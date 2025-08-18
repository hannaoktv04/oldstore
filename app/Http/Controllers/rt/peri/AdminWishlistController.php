<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemWishlist;

class AdminWishlistController extends Controller
{
    public function index()
    {
        $wishlists = ItemWishlist::with(['user', 'category'])
            ->orderByDesc('created_at')
            ->paginate(10); // Menambahkan pagination dengan 10 item per halaman
        
        return view('admin.wishlist', compact('wishlists'));
    }

    public function akomodasi($id)
    {
        $wishlist = ItemWishlist::findOrFail($id);
        $wishlist->status = 'diakomodasi';
        $wishlist->catatan_admin = 'Permintaan diakomodasi';
        $wishlist->save();

        return redirect()->back()->with('success', 'Permintaan telah diakomodasi.');
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:255'
        ]);

        $wishlist = ItemWishlist::findOrFail($id);
        $wishlist->status = 'ditolak';
        $wishlist->catatan_admin = $request->catatan_admin;
        $wishlist->save();

        return redirect()->back()->with('success', 'Permintaan telah ditolak.');
    }
}