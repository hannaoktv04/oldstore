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
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\StockOpnameController;

Route::delete('/cart/bulk-delete', [CartController::class, 'bulkDelete'])->name('cart.bulkDelete');

Route::middleware(['auth', 'block.opname'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/produk/{id}/add-to-cart', [CartController::class, 'store'])->name('produk.addToCart');
    Route::post('/produk/{id}/pesanLangsung', [CartController::class, 'pesanLangsung'])->name('produk.pesanLangsung');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/pengajuan/{id}/konfirmasi', [ItemRequestController::class, 'konfirmasiUser'])->name('pengajuan.konfirmasiUser');
    Route::get('/pengajuan/enota/{id}', [ItemRequestController::class, 'showENota'])->name('pengajuan.enota');
    Route::get('/pengajuan/{id}/download', [PengajuanController::class, 'downloadNota'])->name('pengajuan.downloadNota');
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
Route::middleware(['auth', 'can:isAdmin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/pengajuan/status/{status}', [AdminController::class, 'pengajuanByStatus'])->name('admin.pengajuan.status');
    Route::get('/pengajuan/{pengajuan}/nota', [AdminPengajuanController::class, 'nota'])->name('admin.pengajuan.nota');
    Route::post('/pengajuan/{pengajuan}/deliver', [AdminPengajuanController::class, 'deliver'])->name('admin.pengajuan.deliver');
    Route::post('/pengajuan/{pengajuan}/received', [AdminPengajuanController::class, 'markAsReceived'])->name('admin.pengajuan.received');
    Route::post('/pengajuan/{pengajuan}/approve', [AdminPengajuanController::class, 'approve'])->name('admin.pengajuan.approve');
    Route::post('/pengajuan/{pengajuan}/reject', [AdminPengajuanController::class, 'reject'])->name('admin.pengajuan.reject');

    Route::get('/add-item', [ItemController::class, 'create'])->name('admin.addItem');
    Route::post('/add-item', [ItemController::class, 'store'])->name('admin.storeItem');
    Route::get('/items', [ItemController::class, 'itemList'])->name('admin.items');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('admin.items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('admin.items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('admin.items.destroy');
    Route::post('/items/bulk-action', [ItemController::class, 'bulkAction'])->name('admin.items.bulkAction');
    Route::post('/items/toggle/{item}', [ItemController::class, 'toggleState'])->name('admin.items.toggle');
    Route::delete('/items/images/{image}', [ItemController::class, 'deleteImage'])->name('admin.items.images.destroy');

    Route::get('/wishlist', [AdminWishlistController::class, 'index'])->name('admin.wishlist.index');
    Route::post('/wishlist/{id}/akomodasi', [AdminWishlistController::class, 'akomodasi'])->name('admin.wishlist.akomodasi');
    Route::post('/wishlist/{id}/tolak', [AdminWishlistController::class, 'tolak'])->name('admin.wishlist.tolak');

    Route::get('/stok/koreksi', [StockAdjustmentController::class, 'create'])->name('admin.stok.koreksi');
    Route::post('/stok/koreksi', [StockAdjustmentController::class, 'store'])->name('admin.stok.koreksi.store');

    Route::get('/categories', [CategoryController::class, 'publicView'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('/categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('admin.categories.bulkDelete');

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('admin.purchase_orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('admin.purchase_orders.createPO');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('admin.purchase_orders.store');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('admin.purchase_orders.showPO');
    Route::post('/purchase-orders/{purchaseOrder}/submit', [PurchaseOrderController::class, 'submit'])->name('admin.purchase_orders.submit');
    Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('admin.purchase_orders.destroy');
    Route::get('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receiveForm'])->name('admin.purchase_orders.receive');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'processReceive'])->name('admin.purchase_orders.processReceive');
    Route::get('/purchase_orders/{purchase_order}/edit', [PurchaseOrderController::class, 'edit'])->name('admin.purchase_orders.edit');
    Route::put('/purchase_orders/{purchase_order}', [PurchaseOrderController::class, 'update'])->name('admin.purchase_orders.update');

    Route::prefix('stock-opname')->name('admin.stock_opname.')->group(function () {
    Route::get('/', [StockOpnameController::class, 'index'])->name('index');
    Route::get('/create', [StockOpnameController::class, 'create'])->name('create');
    Route::post('/', [StockOpnameController::class, 'store'])->name('store');

    // jangan prefix nested lagi
    Route::get('/{opname}/edit', [StockOpnameController::class, 'edit'])->name('edit');
    Route::put('/{opname}', [StockOpnameController::class, 'update'])->name('update');

    Route::post('/{opname}/end', [StockOpnameController::class, 'endSession'])->name('end');
    Route::get('/{opname}', [StockOpnameController::class, 'show'])->name('show');
});

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
    Route::get('/kategori/{id}', [CategoryController::class, 'show'])->name('kategori.show');
});

// -------------------------
// PUBLIC ROUTES (NO LOGIN REQUIRED)
// -------------------------
Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori.index');

Route::controller(ProductController::class)->group(function () {
    Route::get('/produk/{id}', 'show')->name('produk.show');
    Route::get('/search', 'search')->name('search');
});
