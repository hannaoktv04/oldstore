<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemWishlist;

class AdminWishlistController extends Controller
{
    public function index()
    {
        $wishlists = ItemWishlist::with('user')->orderByDesc('created_at')->get();
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
        $wishlist = ItemWishlist::findOrFail($id);
        $wishlist->status = 'ditolak';
        $wishlist->catatan_admin = $request->input('catatan_admin', 'Permintaan ditolak');
        $wishlist->save();

        return redirect()->back()->with('success', 'Permintaan telah ditolak.');
    }
}
