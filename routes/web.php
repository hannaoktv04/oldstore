<?php

use Illuminate\Support\Facades\Route;

Route::get('/kategori', function () {
    return view('layouts.kategori'); 
});

Route::get('/user-login', function () {
    return view('auth.user-login'); 
});

Route::get('/admin-login', function () {
    return view('auth.admin-login'); 
});

Route::get('/dashboard', function () {
    return view('admin.dashboard'); 
});
