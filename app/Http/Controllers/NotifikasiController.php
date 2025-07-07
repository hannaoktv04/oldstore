<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockNotification;

class NotifikasiController extends Controller
{
    public function markSeen()
    {
        if (auth()->check() && auth()->user()->role === 'pegawai') {
            StockNotification::where('seen', false)->update(['seen' => true]);
            return back()->with('status', 'Semua notifikasi telah ditandai sebagai dibaca.');
        }

        return back()->with('error', 'Akses ditolak.');
    }
}
