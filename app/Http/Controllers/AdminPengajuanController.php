<?php

namespace App\Http\Controllers;
use App\Models\ItemStock;
use App\Models\ItemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
