<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\ItemRequest;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pengajuanBaru = ItemRequest::where('status', 'submitted')->count();
        $perluDikirim = ItemRequest::where('status', 'approved')->count();
        $pengajuanSelesai = ItemRequest::where('status', 'received')->count();
        $pembatalan = ItemRequest::where('status', 'rejected')->count();
        
        return view('admin.dashboard', compact('pengajuanBaru', 'perluDikirim','pengajuanSelesai', 'pembatalan'));
    }

    public function pengajuanByStatus($status)
    {
        $pengajuans = ItemRequest::where('status', $status)->get();

        return view('admin.pengajuan-status', compact('pengajuans', 'status'));
    }

}
