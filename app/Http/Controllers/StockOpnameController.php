<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOpname;
use App\Models\OpnameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{

    public function formInput(OpnameSession $opname)
    {
        $items = Item::with([
            'stocks',
            'stockOpnames' => function ($q) use ($opname) {
                $q->where('session_id', $opname->id);
            }
        ])->get();

        return view('admin.stock_opname.edit', [
            'session' => $opname,
            'items' => $items,
        ]);
    }

    public function storeInput(Request $request, OpnameSession $opname)
    {
        $itemIds    = $request->input('item_id', []);
        $fisikList  = $request->input('qty_fisik', []);
        $catatanList = $request->input('catatan', []);
        $userId     = Auth::id();
        $tanggal    = now()->toDateString();

        foreach ($itemIds as $index => $itemId) {
            $qtyFisik = $fisikList[$index];

    if ($qtyFisik === null || $qtyFisik === '') {
        continue; 
    }

    $item = Item::find($itemId);
    $qtySistem = optional($item->stock)->qty ?? 0;
    $selisih = $qtyFisik - $qtySistem;

            StockOpname::updateOrCreate([
                'item_id'         => $itemId,
                'tanggal_opname'  => $tanggal,
                'session_id'      => $opname->id,
            ], [
                'qty_sistem'      => $qtySistem,
                'qty_fisik'       => $qtyFisik,
                'selisih'         => $selisih,
                'status'          => 'draft',
                'dilakukan_oleh'  => $userId,
                'catatan'         => $catatanList[$index] ?? null,
            ]);
        }

        return redirect()->route('admin.stock_opname.input.form', $opname->id)
            ->with('success', 'Data berhasil disimpan sebagai draft.');
    }
}
