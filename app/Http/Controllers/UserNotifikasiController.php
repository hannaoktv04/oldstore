<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\StockNotification;
use App\Models\ItemDelivery;
use App\Models\Cart;

class UserNotifikasiController extends Controller
{
    public function index()
    {
        $cartItems = Auth::check() ? Cart::where('user_id', Auth::id())->get() : collect();
        $jumlahKeranjang = $cartItems->count();

        $notifikasiProdukUser = collect();
        $notifikasiPengiriman = collect();

        if (Auth::check() && Auth::user()->hasRole('pegawai')) {
            $notifikasiProdukUser = StockNotification::query()
                ->where('seen', false)
                ->where(function ($q) {
                    $q->where('judul', 'like', '%tersedia%')
                    ->orWhere('judul', 'like', '%produk baru%')
                    ->orWhere('pesan', 'like', '%tersedia%')
                    ->orWhere('pesan', 'like', '%produk baru%')
                    ->orWhere('pesan', 'like', '%update stok%')
                    ->orWhere('pesan', 'like', '%restock%');
                })
                ->where(function ($q) {
                    $q->where('judul', 'not like', '%menipis%')
                    ->where('pesan', 'not like', '%menipis%');
                })
                ->with('item')
                ->latest()
                ->take(10)
                ->get();

            $notifikasiPengiriman = ItemDelivery::whereHas('request', fn($q) => $q->where('user_id', Auth::id()))
                ->whereIn('status', ['assigned', 'on_progress'])
                ->with('request:id,kode_request,user_id')
                ->latest()
                ->take(10)
                ->get();
        }

        $totalNotifUser = $notifikasiProdukUser->count() + $notifikasiPengiriman->count();

        return view('home', compact(
            'cartItems',
            'jumlahKeranjang',
            'notifikasiProdukUser',
            'notifikasiPengiriman',
            'totalNotifUser'
        ));
    }
}
