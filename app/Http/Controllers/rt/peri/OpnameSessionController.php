<?php

namespace App\Http\Controllers;

use App\Models\OpnameSession;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OpnameSessionController extends Controller
{

    public function index()
    {
        $sessions = OpnameSession::with('opener')->latest()->get();
        return view('admin.stock_opname.index', compact('sessions'));
    }
    public function endSession(Request $request, OpnameSession $stock_opname)
    {
        $request->validate([
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai'
        ]);

        if (!$stock_opname->canBeEnded()) {
            return back()->with('error', 'Hanya sesi aktif yang bisa diakhiri');
        }

        DB::beginTransaction();
        try {
            $stock_opname->update([
                'status' => 'selesai',
                'tanggal_selesai' => $request->tanggal_selesai,
                'block_transaction' => false
            ]);

            $items = $stock_opname->stockOpnames()
                ->withDifference()
                ->with(['item' => function ($query) {
                    $query->with(['stocks', 'adjustments', 'logs']);
                }])
                ->get();

            foreach ($items as $opnameItem) {
                if (!$opnameItem->item || !$opnameItem->item->stocks) {
                    continue;
                }

                $opnameItem->item->stocks()->update([
                    'qty' => $opnameItem->qty_fisik
                ]);

                $opnameItem->item->adjustments()->create([
                    'qty_sebelum' => $opnameItem->qty_sistem,
                    'qty_fisik' => $opnameItem->qty_fisik,
                    'qty_selisih' => $opnameItem->selisih,
                    'tipe_adjustment' => 'opname',
                    'keterangan' => 'Stock opname periode ' . $stock_opname->periode_bulan,
                    'adjusted_by' => auth()->id(),
                    'adjusted_at' => now()
                ]);

                $opnameItem->item->logs()->create([
                    'tipe' => $opnameItem->selisih > 0 ? 'in' : 'out',
                    'qty' => abs($opnameItem->selisih),
                    'sumber' => 'adjustment',
                    'sumber_id' => $stock_opname->id,
                    'deskripsi' => 'Stock opname adjustment'
                ]);

                $opnameItem->update(['status' => 'diajukan']);
            }

            DB::commit();

            return redirect()->route('admin.stock_opname.index')
                ->with('success', 'Sesi stock opname telah diselesaikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan sesi: ' . $e->getMessage());
        }
    }
}
