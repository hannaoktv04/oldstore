<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserSettingsController;


Route::get('/riwayat-pengajuan', [ItemRequestController::class, 'history'])->name('item_requests.history');

// -------------------------
// CART ROUTES
// -------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/produk/{id}/add-to-cart', [CartController::class, 'store'])->name('produk.addToCart');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    
});



// -------------------------
// AUTHENTICATION ROUTES
// -------------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// -------------------------
// REDIRECT BASED ON ROLE
// -------------------------
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }
    return redirect()->route('login');
})->name('root');

// -------------------------
// ADMIN ROUTES (ADMIN-ONLY)
// -------------------------
Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/pengajuan/status/{status}', [AdminController::class, 'pengajuanByStatus'])->name('admin.pengajuan.status');
});


// -------------------------
// USER ROUTES (LOGGED-IN USERS)
// -------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('layouts.home');
    })->name('home');

    Route::get('/user/setting', function () {
        return view('user.setting');
    })->name('user.setting');

    Route::put('/user/{id}', [UserSettingsController::class, 'update'])->name('user.update');
    Route::post('/user/edit', [UserSettingsController::class, 'edit'])->name('user.edit');
});

// -------------------------
// PUBLIC ROUTES (NO LOGIN REQUIRED)
// -------------------------
Route::get('/kategori', [ItemController::class, 'index'])->name('kategori.index');

Route::controller(ProductController::class)->group(function () {
    Route::get('/produk/{id}', 'show')->name('produk.show');
    Route::get('/search', 'search')->name('search');
});

