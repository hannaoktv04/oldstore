<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemWishlist;

class ProductController extends Controller
{
    public function show($id)
    {
        $produk = Item::findOrFail($id);
        return view('produk.detail', compact('produk'));
    }


    public function search(Request $request)
    {
        $keyword = $request->input('q');

        $produk = Item::where('nama_barang', 'like', '%' . $keyword . '%')->get();

        return view('search.results', compact('produk', 'keyword'));
    }

    public function tambahKeWishlist(Request $request, $id)
    {
        $produk = Item::findOrFail($id);

        ItemWishlist::create([
            'user_id' => Auth::id(),
            'nama_barang' => $produk->nama_barang,
            'deskripsi' => $produk->deskripsi,
            'category_id' => $produk->category_id,
            'qty_diusulkan' => 1,
            'status' => 'pending',
            'catatan_admin' => null,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke wishlist.');
    }
}