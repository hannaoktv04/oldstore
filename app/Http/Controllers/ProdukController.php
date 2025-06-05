<?php

use App\Models\Item; 

class ProdukController extends Controller
{
    public function show($id)
    {
        $produk = Item::findOrFail($id);
        return view('produk.detail', compact('produk'));
    }
}
