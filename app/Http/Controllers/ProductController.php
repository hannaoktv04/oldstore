<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

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
}