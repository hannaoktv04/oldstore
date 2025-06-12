<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemWishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = ItemWishlist::with('category')
                      ->where('user_id', auth()->id())
                      ->orderByDesc('created_at')
                      ->get();

        return view('user.wishlist', compact('wishlists'));
    }
}
