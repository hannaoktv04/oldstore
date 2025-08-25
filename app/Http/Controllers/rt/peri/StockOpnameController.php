<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use App\Models\Item;
use App\Models\StockOpname;
use App\Models\OpnameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class StockOpnameController extends Controller
{
    public function create()
    {
        $items = Item::with('stocks')->get();
        return view('peri::admin.stock_opname.create', compact('items'));
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
        return view('peri::admin.stock_opname.edit', [
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
            return redirect()->route('admin.stock_opname.index')->with('success', $statusChanged
                ? 'Data opname dan status sesi berhasil diperbarui'
                : 'Data opname berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    public function destroy(OpnameSession $stock_opname)
    {

        try {
            DB::beginTransaction();
            StockOpname::where('session_id', $stock_opname->id)->delete();
            $stock_opname->delete();

            DB::commit();

            return redirect()->route('admin.stock_opname.index')
                ->with('success', 'Sesi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus sesi: ' . $e->getMessage());
        }
    }
    public function show(OpnameSession $stock_opname)
    {
        $allItems = Item::with(['stocks', 'stockOpnames' => function ($q) use ($stock_opname) {
            $q->where('session_id', $stock_opname->id);
        }])->get();

        $stock_opname->load(['opener']);

        return view('peri::admin.stock_opname.show', [
            'session' => $stock_opname,
            'items' => $allItems,
            'currentDate' => Carbon::now()
        ]);
    }

    public function downloadPdf(OpnameSession $stock_opname)
    {
        $allItems = Item::with(['stocks', 'stockOpnames' => function ($query) use ($stock_opname) {
            $query->where('session_id', $stock_opname->id);
        }])->orderBy('nama_barang', 'asc')->get();
        $stock_opname->load(['opener']);

        $data = [
            'session'     => $stock_opname,
            'items'       => $allItems,
            'currentDate' => Carbon::now()
        ];
        $pdf = PDF::loadView('peri::admin.stock_opname.print', $data);
        $pdf->setPaper('a4', 'potrait');
        return $pdf->download('laporan-stock-opname-' . $stock_opname->periode_bulan . '-' . $stock_opname->id . '.pdf');
    }
}