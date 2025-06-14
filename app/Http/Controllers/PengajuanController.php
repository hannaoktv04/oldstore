<?php

use App\Models\ItemLog;
use App\Models\ItemRequest;
use App\Models\ItemStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengajuanController
{
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $pengajuan = ItemRequest::with(['details.item', 'user'])->findOrFail($id);

            foreach ($pengajuan->details as $detail) {
                $item = $detail->item;
                $qty = $detail->qty_requested;

                $stock = ItemStock::where('item_id', $item->id)->first();

                if (!$stock || $stock->qty < $qty || $item->stok_minimum < $qty) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Stok '{$item->nama_barang}' tidak mencukupi.");
                }
            }

            foreach ($pengajuan->details as $detail) {
                $item = $detail->item;
                $qty = $detail->qty_requested;

                $stock = ItemStock::where('item_id', $item->id)->first();
                $stock->qty -= $qty;
                $stock->save();

                $item->stok_minimum -= $qty;
                $item->save();

                ItemLog::create([
                    'item_id' => $item->id,
                    'tipe' => 'out',
                    'qty' => $qty,
                    'sumber' => 'request',
                    'sumber_id' => $pengajuan->id,
                    'deskripsi' => "Pengeluaran barang oleh permintaan user: {$pengajuan->user->nama}",
                ]);
            }

            $pengajuan->status = 'approved';
            $pengajuan->approved_by = auth()->id();
            $pengajuan->approved_at = now();
            $pengajuan->save();

            DB::commit();

            return redirect()->back()->with('success', 'Pesanan berhasil di-approve.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

}
