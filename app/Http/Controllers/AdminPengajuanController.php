<?php
namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\ItemDelivery;
use App\Models\ItemReceipt;
use App\Models\ItemStock;
use App\Models\ItemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;

class AdminPengajuanController extends Controller
{
    public function status($status)
    {
        $pengajuans = ItemRequest::with([
            'user',
            'details.item.photo',
            'details.item.category',
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
    public function deliver(Request $request, ItemRequest $pengajuan)
    {
        $request->validate([
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $buktiPath = null;
            if ($request->hasFile('bukti_foto')) {
                $buktiPath = $request->file('bukti_foto')
                                     ->store('bukti_pengiriman', 'public');
            }

            ItemDelivery::updateOrCreate(
                ['item_request_id' => $pengajuan->id],
                [
                    'operator_id'   => auth()->id(),
                    'tanggal_kirim' => now(),
                    'bukti_foto'    => $buktiPath,
                    'status'        => 'in_progress',
                ]
            );

            $pengajuan->update(['status' => 'delivered']);

            DB::commit();
            return back()->with('success', 'Pengajuan ditandai dikirim.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($buktiPath)) Storage::disk('public')->delete($buktiPath);
            return back()->with('error', 'Gagal kirim: '.$e->getMessage());
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

            // Simpan gambar ke public storage
            $buktiPath = $request->file('bukti_foto')->store('bukti_penerimaan', 'public');

            // Buat record ItemReceipt
            ItemReceipt::create([
                'item_delivery_id' => $delivery->id,
                'received_by' => auth()->id(),
                'tanggal_terima' => now(),
                'catatan' => $request->input('catatan'),
            ]);

            // Update delivery
            $delivery->update([
                'status' => 'completed',
                'bukti_foto' => $buktiPath,
            ]);

            // Update pengajuan
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

}
