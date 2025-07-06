<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOpname;
use App\Models\OpnameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    public function create()
    {
        $items = Item::with('stocks')->get();
        return view('admin.stock_opname.create', compact('items'));
    }
    public function store(Request $request)
    {
        try {
            if (!$request->periode_bulan || !$request->tanggal_mulai) {
                throw new \Exception('Data periode dan tanggal harus diisi');
            }
            $session = OpnameSession::create([
                'periode_bulan' => $request->periode_bulan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'status' => Carbon::parse($request->tanggal_mulai)->isToday() || Carbon::parse($request->tanggal_mulai)->isPast() ? 'aktif' : 'menunggu',
                'dibuka_oleh' => auth()->id(),
                'catatan' => $request->catatan,
                'block_transaction' => true
            ]);
            return redirect()->route('admin.stock_opname.edit', $session)
                ->with('success', 'Sesi awal berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function edit(OpnameSession $stock_opname)
    {
        $stock_opname->load(['stockOpnames.item']);
        $items = Item::with(['stocks', 'stockOpnames' => function ($q) use ($stock_opname) {
            $q->where('session_id', $stock_opname->id);
        }])->get();
        return view('admin.stock_opname.edit', [
            'session' => $stock_opname,
            'items' => $items
        ]);
    }
    public function update(Request $request, OpnameSession $stock_opname)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'periode_bulan' => 'required|string|max:255',
                'tanggal_mulai' => 'required|date',
                'catatan' => 'nullable|string'
            ]);

            $updateData = [
                'periode_bulan' => $request->periode_bulan,
                'catatan' => $request->catatan
            ];
            $statusChanged = false;
            $newDate = Carbon::parse($request->tanggal_mulai);

            if ($newDate->format('Y-m-d') != $stock_opname->tanggal_mulai->format('Y-m-d')) {
                $updateData['tanggal_mulai'] = $newDate;
                $updateData['status'] = $newDate->isToday() || $newDate->isPast()
                    ? 'aktif'
                    : 'menunggu';
                $statusChanged = true;
            }
            $stock_opname->update($updateData);
            foreach ($request->items as $itemId => $itemData) {
                if (empty($itemData['qty_fisik']) && $itemData['qty_fisik'] !== '0') {
                    continue;
                }

                $item = Item::with('stocks')->findOrFail($itemId);
                $qtySistem = $item->stocks ? (float)$item->stocks->qty : 0;
                $qtyFisik = (float)$itemData['qty_fisik'];
                $selisih = $qtyFisik - $qtySistem;

                StockOpname::updateOrCreate(
                    ['item_id' => $itemId, 'session_id' => $stock_opname->id],
                    [
                        'qty_sistem' => $qtySistem,
                        'qty_fisik' => $qtyFisik,
                        'selisih' => $selisih,
                        'catatan' => $itemData['catatan'] ?? null,
                        'status' => 'draft',
                        'dilakukan_oleh' => auth()->id(),
                        'tanggal_opname' => now()->toDateString()
                    ]
                );
            }

            DB::commit();

            return back()->with('success', $statusChanged
                ? 'Data opname dan status sesi berhasil diperbarui'
                : 'Data opname berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
