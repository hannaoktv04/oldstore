<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemWishlist;

class ProductController extends Controller
{
    public function show($id)
    {
        $produk = Item::with(['photo', 'category'])->findOrFail($id);
        return view('peri::produk.detail', compact('produk'));
    }


    public function search(Request $request)
    {
        $keyword = $request->input('q');

        $produk = Item::where('nama_barang', 'like', '%' . $keyword . '%')->get();

        return view('peri::search.results', compact('produk', 'keyword'));
    }

    public function tambahWishlist($id, Request $request)
    {
        $produk = Item::findOrFail($id);

        ItemWishlist::create([
            'user_id'       => auth()->id(),
            'nama_barang'   => $produk->nama_barang,
            'deskripsi'     => $produk->deskripsi,
            'category_id'   => $produk->category_id,
            'qty_diusulkan' => 1,
            'status'        => 'pending',
        ]);

        return redirect()->back()->with('success', 'Produk ditambahkan ke wishlist karena stok habis.');
    }

}
