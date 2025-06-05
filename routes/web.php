<?php

use Illuminate\Support\Facades\Route;

Route::get('/produk/{id}', function ($id) {
    $produkList = [
        1 => [
            'name' => 'Pulpen',
            'kategori' => 'ATK',
            'image' => 'assets/img/products/samsung-watch-4.png',
            'deskripsi' => 'Pulpen berkualitas tinggi untuk kebutuhan harian.',
            'stock' => 100
        ],
    ];

    if (!array_key_exists($id, $produkList)) {
        abort(404);
    }

    $produk = (object) $produkList[$id];

    return view('layout.produk.detail', compact('produk'));
})->name('produk.detail');

Route::get('/', function () {
    return view('layouts.home'); 
});

Route::get('/kategori', function () {
    return view('layouts.kategori'); 
});

Route::get('/user-login', function () {
    return view('auth.user-login'); 
});

Route::get('/admin-login', function () {
    return view('auth.admin-login'); 
});
