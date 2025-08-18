<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockAdjustment;
use App\Models\ItemLog;
use App\Models\StockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentController extends Controller
{
    public function create()
    {
        $items = Item::with('stocks')->get();
        return view('peri::admin.koreksi_stok', compact('items'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $itemId = $request->input('item_id');
            $qtyFisik = $request->input('qty_fisik');

            $stock = ItemStock::where('item_id', $itemId)->first();
            $qtySebelum = $stock->qty ?? 0;
            $qtySelisih = $qtyFisik - $qtySebelum;

            $adjustment = StockAdjustment::create(attributes: [
                'item_id' => $itemId,
                'qty_sebelum' => $qtySebelum,
                'qty_fisik' => $qtyFisik,
                'qty_selisih' => $qtySelisih,
                'tipe_adjustment' => 'koreksi',
                'keterangan' => 'koreksi stok manual',
                'adjusted_by' => Auth::id(),
                'adjusted_at' => now(),
            ]);


            $stock->update(['qty' => $qtyFisik]);

            ItemLog::create([
                'item_id' => $itemId,
                'tipe' => $qtySelisih >= 0 ? 'in' : 'out',
                'qty' => abs($qtySelisih),
                'sumber' => 'adjustment',
                'sumber_id' => $adjustment->id,
                'deskripsi' => 'Koreksi stok',
            ]);

            if ($qtySebelum <= 0 && $qtyFisik > 0) {
                StockNotification::create([
                    'item_id' => $itemId,
                    'seen' => false,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Koreksi stok berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal koreksi stok: ' . $e->getMessage());
        }
    }
}