<?php

namespace App\Http\Controllers;

use App\Models\OpnameSession;
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
            $status = Carbon::parse($request->tanggal_mulai)->isToday() || Carbon::parse($request->tanggal_mulai)->isPast()
                ? 'aktif' : 'menunggu';

            OpnameSession::where('status', 'aktif')->update([
                'status' => 'selesai',
                'tanggal_selesai' => now()->toDateString()
            ]);

            $session = OpnameSession::create([
                'periode_bulan' => $request->periode_bulan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => null,
                'status' => $status,
                'catatan' => $request->catatan,
                'block_transaction' => true,
                'dibuka_oleh' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('admin.stock_opname.input.form', $session->id)
                ->with('success', 'Sesi stock opname berhasil dimulai!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memulai sesi: ' . $e->getMessage());
        }
    }

    public function edit(OpnameSession $stock_opname)
    {
        return view('admin.stock_opname.edit', ['session' => $stock_opname]);
    }

    public function update(Request $request, OpnameSession $stock_opname)
    {
        $request->validate([
            'periode_bulan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            if ($request->tanggal_mulai != $stock_opname->tanggal_mulai) {
                $status = Carbon::parse($request->tanggal_mulai)->isToday() || Carbon::parse($request->tanggal_mulai)->isPast()
                    ? 'aktif' : 'menunggu';

                $stock_opname->update([
                    'periode_bulan' => $request->periode_bulan,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'status' => $status,
                    'catatan' => $request->catatan
                ]);
            } else {
                $stock_opname->update([
                    'periode_bulan' => $request->periode_bulan,
                    'catatan' => $request->catatan
                ]);
            }

            DB::commit();
            return redirect()->route('admin.stock_opname.index')
                ->with('success', 'Sesi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui sesi: ' . $e->getMessage());
        }
    }
    public function destroy(OpnameSession $stock_opname)
    {
        DB::beginTransaction();
        try {
            $stock_opname->items()->detach();
            $stock_opname->delete();

            DB::commit();

            return redirect()->route('admin.stock_opname.index')
                ->with('success', 'Sesi stock opname berhasil dihapus beserta semua datanya!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus sesi: ' . $e->getMessage());
        }
    }

    public function endSession(Request $request, OpnameSession $stock_opname)
    {
        $request->validate([
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai'
        ]);

        DB::beginTransaction();
        try {
            $stock_opname->update([
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
}