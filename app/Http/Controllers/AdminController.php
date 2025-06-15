<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemRequest;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pengajuanBaru = ItemRequest::where('status', 'submitted')->count();
        $perubahan = ItemRequest::where('status', 'revised')->count();
        $pembatalan = ItemRequest::where('status', 'rejected')->count();

        return view('admin.dashboard', compact('pengajuanBaru', 'perubahan', 'pembatalan'));
    }

    public function pengajuanByStatus($status)
    {
        $pengajuans = ItemRequest::where('status', $status)->get();

        return view('admin.pengajuan-status', compact('pengajuans', 'status'));
    }
}
