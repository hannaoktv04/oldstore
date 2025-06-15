<?php

namespace App\Http\Controllers;
use App\Models\ItemStock;
use App\Models\ItemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\ItemRequest;

class AdminPengajuanController extends Controller
{
    public function status($status)
    {
        $pengajuans = ItemRequest::with([
            'user',
            'details.item.photo',
            'details.item.category',
        ])
        ->where('status', $status)
        ->orderByDesc('created_at')
        ->get();

        return view('admin.pengajuan-status', compact('pengajuans', 'status'));
    }

public function approve(Request $request, ItemRequest $pengajuan)
{
    DB::beginTransaction();
    try {
        foreach ($pengajuan->details as $detail) {

            $approvedQty = $request->input('approved_qty')[$detail->id] ?? 0;

            if ($approvedQty < 0 || $approvedQty > $detail->qty_requested) {
                throw new \Exception("Jumlah disetujui tidak valid.");
            }

            $availableStock = $detail->item->stocks()->sum('qty');
            if ($approvedQty > $availableStock) {
                throw new \Exception("Stok tidak mencukupi untuk {$detail->item->nama_barang}");
            }

            $detail->update(['qty_approved' => $approvedQty]);
            $detail->item->decrement('stok_minimum', $approvedQty);
            $stock = ItemStock::where('item_id', $detail->item_id)->first();

            if ($stock) {
                $stock->decrement('qty', $approvedQty);
            } else {
                ItemStock::create([
                    'item_id' => $detail->item_id,
                    'qty'     => 0 - $approvedQty,
                ]);
            }

            ItemLog::create([
                'item_id'   => $detail->item_id,
                'tipe'      => 'out',
                'qty'       => $approvedQty,
                'sumber'    => 'request',
                'sumber_id' => $pengajuan->id,
                'deskripsi' => 'Pengeluaran permintaan #' . $pengajuan->id,
            ]);
        }

        $pengajuan->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'keterangan'  => $request->input('admin_note'),
        ]);

        DB::commit();
        return back()->with('success', 'Pengajuan disetujui');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

public function reject(Request $request, ItemRequest $pengajuan)
{
    DB::beginTransaction();
    try {
        $pengajuan->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'keterangan'  => $request->input('admin_note'),
        ]);

        DB::commit();
        return back()->with('rejected', 'Pengajuan ditolak');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menolak: '.$e->getMessage());
    }
}



}