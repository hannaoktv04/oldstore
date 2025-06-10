<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;

class AdminPengajuanController extends Controller
{
    public function status($status)
    {
        $pengajuans = ItemRequest::with(['details.item', 'user'])
                        ->where('status', $status)
                        ->orderByDesc('created_at')
                        ->get();

        return view('admin.pengajuan-status', compact('pengajuans', 'status'));
    }
}