<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPengajuanController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminWishlistController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockAdjustmentController;

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/purchase-orders', [PurchaseOrderController::class, 'index'])->name('admin.purchase_orders.index');
});

Route::delete('/cart/bulk-delete', [CartController::class, 'bulkDelete'])->name('cart.bulkDelete');


Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/produk/{id}/add-to-cart', [CartController::class, 'store'])->name('produk.addToCart');
    Route::post('/produk/{id}/pesanLangsung', [CartController::class, 'pesanLangsung'])->name('produk.pesanLangsung');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/kategori/{id}', [CategoryController::class, 'show'])->name('kategori.show');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/pengajuan/{id}/konfirmasi', [ItemRequestController::class, 'konfirmasiUser'])->name('pengajuan.konfirmasiUser');
    Route::get('/pengajuan/enota/{id}', [ItemRequestController::class, 'showENota'])->name('pengajuan.enota');
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
    Route::post('/pengajuan/{pengajuan}/approve', [AdminPengajuanController::class, 'approve'])->name('pengajuan.approve');
    Route::post('/pengajuan/{pengajuan}/reject', [AdminPengajuanController::class, 'reject'])->name('pengajuan.reject');
    Route::post('/pengajuan/{pengajuan}/deliver', [AdminPengajuanController::class, 'deliver'])->name('pengajuan.deliver');
    Route::post('/pengajuan/{pengajuan}/received', [AdminPengajuanController::class, 'markAsReceived'])->name('pengajuan.received');
     Route::post('/admin/pengajuan/{pengajuan}/deliver',  [AdminPengajuanController::class, 'deliver'])
        ->name('admin.pengajuan.deliver');

    Route::post('/admin/pengajuan/{pengajuan}/received', [AdminPengajuanController::class, 'markAsReceived'])
        ->name('admin.pengajuan.received');   
    Route::get('/admin/pengajuan/{pengajuan}/nota',      [AdminPengajuanController::class, 'nota'])
        ->name('admin.pengajuan.nota');
    Route::get('/pengajuan/{pengajuan}/nota', [AdminPengajuanController::class, 'nota'])->name('pengajuan.nota');
    Route::get('/admin/add-item', [ItemController::class, 'create'])->name('admin.addItem');
    Route::post('/admin/add-item', [ItemController::class, 'store'])->name('admin.storeItem');
    Route::get('/admin/wishlist', [AdminWishlistController::class, 'index'])->name('admin.wishlist.index');
    Route::post('/admin/wishlist/{id}/akomodasi', [AdminWishlistController::class, 'akomodasi'])->name('admin.wishlist.akomodasi');
    Route::post('/admin/wishlist/{id}/tolak', [AdminWishlistController::class, 'tolak'])->name('admin.wishlist.tolak');
    Route::get('/admin/items', [ItemController::class, 'itemList'])->name('admin.items');
    Route::get('/admin/items/{item}/edit', [ItemController::class, 'edit'])->name('admin.items.edit');
    Route::put('/admin/items/{item}', [ItemController::class, 'update'])->name('admin.items.update');
    Route::post('/admin/items/bulk-action', [ItemController::class, 'bulkAction'])->name('admin.items.bulkAction');
    Route::post('admin/items/toggle/{item}', [ItemController::class, 'toggleState'])->name('admin.items.toggle');
    Route::delete('/admin/items/{item}', [ItemController::class, 'destroy'])->name('admin.items.destroy');
    Route::get('/admin/stok/koreksi', [StockAdjustmentController::class, 'create'])->name('admin.stok.koreksi');
    Route::post('/admin/stok/koreksi', [StockAdjustmentController::class, 'store'])->name('admin.stok.koreksi.store');

    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('/admin/categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('admin.categories.bulkDelete');
    Route::delete('/admin/items/images/{image}', [ItemController::class, 'deleteImage'])->name('admin.items.images.destroy');

});

// -------------------------
// USER ROUTES (LOGGED-IN USERS)
// -------------------------
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/riwayat-pengajuan', [ItemRequestController::class, 'history'])->name('user.history');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('user.wishlist');
    Route::post('/wishlist/{id}', [WishlistController::class, 'addToWishlist'])->name('user.wishlist.store');
    Route::post('/update-pengambilan/{id}', [ItemRequestController::class, 'updateTanggalPengambilan']);
    
});

// -------------------------
// PUBLIC ROUTES (NO LOGIN REQUIRED)
// -------------------------
Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori.index');

Route::controller(ProductController::class)->group(function () {
    Route::get('/produk/{id}', 'show')->name('produk.show');
    Route::get('/search', 'search')->name('search');
});