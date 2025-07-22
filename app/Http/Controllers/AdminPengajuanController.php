<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\ItemDelivery;
use App\Models\ItemReceipt;
use App\Models\ItemStock;
use App\Models\ItemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminPengajuanController extends Controller
{
    public function status($status)
    {
        $pengajuans = ItemRequest::with([
            'user',
            'details.item.photo',
            'details.item.category',
            'itemDelivery'
        ])
        ->where('status', $status);

        if ($status === 'submitted') {
            $pengajuans->orderBy('created_at', 'asc');
        } elseif ($status === 'approved') {
            $pengajuans->orderBy('tanggal_pengiriman', 'asc')
                       ->orderBy('id', 'asc');
        } else {
            $pengajuans->orderBy('created_at', 'desc');
        }

        $pengajuans = $pengajuans->get();

        $staff_pengiriman = User::whereHas('roles', function($q){
            $q->where('name', 'staff_pengiriman');
        })->get();

        return view('admin.pengajuan.index', compact('pengajuans', 'status', 'staff_pengiriman'));
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

    public function markAsReceived(Request $request, ItemRequest $pengajuan)
    {
        $request->validate([
            'bukti_foto' => 'required|image|max:2048',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $delivery = $pengajuan->itemDelivery;
            if (!$delivery) {
                throw new \Exception('Pengiriman tidak ditemukan.');
            }

            $buktiPath = $request->file('bukti_foto')->store('bukti_penerimaan', 'public');

            ItemReceipt::create([
                'item_delivery_id' => $delivery->id,
                'received_by' => auth()->id(),
                'tanggal_terima' => now(),
                'catatan' => $request->input('catatan'),
            ]);

            $delivery->update([
                'status' => 'completed',
                'bukti_foto' => $buktiPath,
            ]);

            $pengajuan->update([
                'status' => 'received',
            ]);

            DB::commit();
            return back()->with('success', 'Barang berhasil ditandai sebagai diterima.');

        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($buktiPath)) {
                Storage::disk('public')->delete($buktiPath);
            }
            return back()->with('error', 'Gagal menyimpan penerimaan: '.$e->getMessage());
        }
    }

    public function assignStaff(Request $request, ItemRequest $pengajuan)
    {
        $request->validate([
            'staff_pengiriman' => 'required|exists:users,id',
            'tanggal_pengiriman' => 'required|date|after_or_equal:today',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $delivery = ItemDelivery::firstOrNew(['item_request_id' => $pengajuan->id]);

            if ($delivery->staff_pengiriman) {
                throw new \Exception('Pengiriman sudah diassign ke staff: ' . $delivery->staff_pengiriman);
            }

            $staff = User::findOrFail($request->staff_pengiriman);

            $delivery->fill([
                'operator_id'     => auth()->id(),
                'tanggal_kirim'   => $request->tanggal_pengiriman,
                'status'          => 'in_progress',
                'staff_pengiriman'=> $staff->nama,
            ])->save();

            $pengajuan->update([
                'status' => 'approved',
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'keterangan' => $request->catatan,
            ]);

            DB::commit();
            return back()->with('success', 'Staff pengiriman berhasil diassign.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal assign staff: ' . $e->getMessage());
        }
    }

}