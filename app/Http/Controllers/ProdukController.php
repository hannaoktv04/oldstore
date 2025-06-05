<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function showDetail()
    {
        $product = [
            'name' => 'Samsung Watch 4',
            'category' => 'Wearables',
            'stock' => 469,
            'image' => asset('assets/img/products/samsung-watch-4.png')
        ];

        return view('layout.produk.detail', compact('product'));
    }
}