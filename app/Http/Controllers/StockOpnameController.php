<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\StockOpname;
use App\Models\OpnameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $sessions = OpnameSession::with('opener')->latest()->get();
        return view('admin.stock_opname.index', compact('sessions'));
    }

    public function create()
    {
        return view('admin.stock_opname.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_bulan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            OpnameSession::where('status', 'aktif')->update([
                'status' => 'selesai',
                'tanggal_selesai' => now()->toDateString()
            ]);

            $session = OpnameSession::create([
                'periode_bulan' => $request->periode_bulan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => null, // Akan diisi saat di-approve
                'status' => 'aktif',
                'block_transaction' => true,
                'dibuka_oleh' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('admin.stock_opname.edit', $session->id)
                ->with('success', 'Sesi stock opname berhasil dimulai! Silakan input barang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memulai sesi: ' . $e->getMessage());
        }
    }
   public function edit(OpnameSession $opname)
{
    $items = Item::with('stocks')->get();
    return view('admin.stock_opname.edit', [
        'session' => $opname,
        'items' => $items
    ]);
}


    public function update(Request $request, OpnameSession $session)
    {
        $data = $request->validate([
            'item_id.*' => 'required|exists:items,id',
            'qty_fisik.*' => 'required|numeric|min:0',
        ]);

        foreach ($request->item_id as $index => $itemId) {
            $item = Item::find($itemId);
            $qtySistem = optional($item->stock)->qty ?? 0;
            $qtyFisik = $request->qty_fisik[$index];
            $selisih = $qtyFisik - $qtySistem;

            StockOpname::updateOrCreate([
                'item_id' => $itemId,
                'tanggal_opname' => now()->toDateString(),
            ], [
                'qty_sistem' => $qtySistem,
                'qty_fisik' => $qtyFisik,
                'selisih' => $selisih,
                'status' => 'draft',
                'dilakukan_oleh' => Auth::id(),
                'catatan' => $request->catatan[$index] ?? null,
            ]);
        }

        return redirect()->route('admin.stock_opname.index')->with('success', 'Data opname disimpan sebagai draft.');
    }

    public function show(OpnameSession $session)
    {
        $opnames = StockOpname::with('item', 'user')
            ->whereDate('tanggal_opname', '>=', is_object($session->tanggal_mulai) ? $session->tanggal_mulai->toDateString() : $session->tanggal_mulai)
            ->whereDate('tanggal_opname', '<=', is_object($session->tanggal_selesai) ? $session->tanggal_selesai->toDateString() : $session->tanggal_selesai)
            ->get();

        return view('admin.stock_opname.show', compact('session', 'opnames'));
    }

    public function endSession(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:opname_sessions,id',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai'
        ]);

        DB::beginTransaction();
        try {
            $session = OpnameSession::findOrFail($request->id);

            // Update data stock opname
            StockOpname::whereDate('tanggal_opname', '>=', $session->tanggal_mulai)
                ->whereNull('tanggal_selesai')
                ->update([
                    'status' => 'disetujui',
                    'updated_at' => now()
                ]);

            // Tutup session
            $session->update([
                'status' => 'selesai',
                'tanggal_selesai' => $request->tanggal_selesai,
                'block_transaction' => false
            ]);

            DB::commit();

            return redirect()->route('admin.stock_opname.index')
                ->with('success', 'Sesi stock opname telah diselesaikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan sesi: ' . $e->getMessage());
        }
    }
    // StockOpnameController.php
    public function history()
    {
        $opnameSessions = OpnameSession::with('opened_by')->orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.opname_sessions.history', compact('opnameSessions'));
    }

    public function detail(OpnameSession $session)
    {
        $session->load('opened_by');
        $opnames = StockOpname::with('item', 'dilakukan_oleh')
            ->whereDate('tanggal_opname', '>=', $session->tanggal_mulai)
            ->whereDate('tanggal_opname', '<=', $session->tanggal_selesai)
            ->get();

        return view('admin.opname_sessions.show', compact('session', 'opnames'));
    }

    // Untuk menambahkan item ke sesi opname
    public function addItem(Request $request, OpnameSession $session)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty_fisik' => 'required|numeric|min:0'
        ]);

        $item = Item::find($request->item_id);

        StockOpname::create([
            'session_id' => $session->id,
            'item_id' => $item->id,
            'qty_sistem' => $item->stock->qty ?? 0,
            'qty_fisik' => $request->qty_fisik,
            'selisih' => $request->qty_fisik - ($item->stock->qty ?? 0),
            'status' => 'draft',
            'dilakukan_oleh' => auth()->id()
        ]);

        return back()->with('success', 'Item berhasil ditambahkan');
    }
}
