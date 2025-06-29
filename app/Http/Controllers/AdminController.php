<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemLog;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ItemWishlist;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $pengajuanBaru = ItemRequest::where('status', 'submitted')->count();
        $perluDikirim = ItemRequest::where('status', 'approved')->count();
        $pengajuanSelesai = ItemRequest::where('status', 'received')->count();
        $pembatalan = ItemRequest::where('status', 'rejected')->count();

        $tahunDipilih = $request->input('tahun', date('Y'));
        $bulanDipilih = $request->input('bulan', date('m'));

        $barangKeluarPerBulan = ItemLog::where('tipe', 'out')
            ->whereYear('created_at', $tahunDipilih)
            ->selectRaw('MONTH(created_at) as bulan, SUM(qty) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $barangKeluarHarian = ItemLog::where('tipe', 'out')
            ->whereYear('created_at', $tahunDipilih)
            ->whereMonth('created_at', $bulanDipilih)
            ->selectRaw('DATE(created_at) as tanggal, SUM(qty) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('tanggal')
            ->pluck('total', 'tanggal')
            ->toArray();

        $topProduk = ItemRequestDetail::whereYear('created_at', $tahunDipilih)
            ->selectRaw('item_id, SUM(qty_requested) as total')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->with('item.category', 'item.photo')
            ->take(12)
            ->get();
        
        $topWishlist = ItemWishlist::whereYear('created_at', $tahunDipilih)
            ->selectRaw('nama_barang, category_id, COUNT(*) as total')
            ->groupBy('nama_barang', 'category_id')
            ->orderByDesc('total')
            ->with('category')
            ->take(12)
            ->get();


        return view('admin.dashboard', compact(
            'pengajuanBaru', 'perluDikirim', 'pengajuanSelesai', 'pembatalan',
            'barangKeluarPerBulan', 'barangKeluarHarian', 'tahunDipilih', 'bulanDipilih', 'topProduk', 'topWishlist'
        ));
    }

    public function pengajuanByStatus($status)
    {
        $pengajuans = ItemRequest::where('status', $status)->get();
        return view('admin.pengajuan-status', compact('pengajuans', 'status'));
    }
}
