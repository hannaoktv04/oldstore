<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
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

        return view('peri::user.wishlist', compact('wishlists'));
    }

    public function addToWishlist(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        $item = \App\Models\Item::findOrFail($id);

        ItemWishlist::create([
            'user_id'        => auth()->id(),
            'nama_barang'    => $item->nama_barang,
            'deskripsi'      => $item->deskripsi,
            'category_id'    => $item->category_id,
            'qty_diusulkan'  => $request->qty,
            'status'         => 'pending',
            'catatan_admin'  => null,
        ]);

        return redirect()->route('user.wishlist');
    }
}
