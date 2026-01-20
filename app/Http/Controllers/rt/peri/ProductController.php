<?php

namespace App\Http\Controllers\rt\peri;

use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemWishlist;

class ProductController extends Controller
{
    /**
     * Menampilkan detail produk
     * Mengarah ke: resources/views/rt/peri/produk/detail.blade.php
     */
    public function show($id)
    {
        $produk = Item::with(['category'])->findOrFail($id);
        
        // Menggunakan field 'stok' sesuai standar database Anda
        $stokQty = $produk->stok ?? 0;

        // Jika menggunakan modular, pastikan path peri::produk.detail benar
        return view('peri::produk.detail', compact('produk', 'stokQty')); 
    }

    /**
     * Mencari produk berdasarkan nama
     * Mengarah ke file yang Anda upload: resources/views/rt/peri/search/results.blade.php
     */
   public function search(Request $request)
    {
        
        $keyword = $request->input('keyword') ?? $request->input('q');

        $produk = Item::with(['category'])
            ->where('nama_barang', 'ILIKE', '%' . $keyword . '%')
            ->get();

        return view('peri::search.results', compact('produk', 'keyword'));
    }
    /**
     * Menambahkan produk ke wishlist (ketika stok habis)
     */
    public function tambahWishlist($id, Request $request)
    {
        $produk = Item::findOrFail($id);

        ItemWishlist::create([
            'user_id'       => auth()->id(),
            'nama_barang'   => $produk->nama_barang,
            'deskripsi'     => $produk->deskripsi,
            'category_id'   => $produk->category_id,
            'qty_diusulkan' => $request->qty ?? 1,
            'status'        => 'pending',
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke wishlist.');
    }
}