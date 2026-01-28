<?php

use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\rt\peri\AdminController; 
use App\Http\Controllers\rt\peri\AdminPengajuanController;
use App\Http\Controllers\rt\peri\ItemController;
use App\Http\Controllers\rt\peri\ProductController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\rt\peri\ItemRequestController;
use App\Http\Controllers\rt\peri\CartController;
use App\Http\Controllers\rt\peri\HomeController;
use App\Http\Controllers\rt\peri\UserSettingsController;
use App\Http\Controllers\rt\peri\CategoryController;
use App\Http\Controllers\rt\peri\AdminWishlistController;
use App\Http\Controllers\rt\peri\WishlistController;
use App\Http\Controllers\rt\peri\PurchaseOrderController;
use App\Http\Controllers\rt\peri\StockAdjustmentController;
use App\Http\Controllers\rt\peri\PengajuanController;
use App\Http\Controllers\rt\peri\OpnameSessionController;
use App\Http\Controllers\rt\peri\StockOpnameController;
use App\Models\OpnameSession;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Http\Controllers\rt\peri\StaffPengirimanController;
use App\Http\Controllers\rt\peri\NotifikasiController;
use App\Http\Controllers\rt\peri\SatuanController;
use App\Http\Controllers\rt\peri\UserController;
use App\Http\Controllers\rt\peri\DataTableController;
use App\Http\Controllers\rt\peri\UserNotifikasiController;
use App\Http\Controllers\rt\peri\ProfileController;

Route::get('/phpinfo', function () {
    phpinfo();
});


Route::get('/home', function () {
    return view('peri::layouts.home');
})->name('layouts.home');


Route::get('/tes-barcode', function () {
    $generator = new BarcodeGeneratorPNG();
    $barcode = base64_encode($generator->getBarcode('1234567890', $generator::TYPE_CODE_128));

    return '
        <h2>Barcode dari Picqer</h2>
        <img src="data:image/png;base64,' . $barcode . '" />
    ';
});

Route::post('/notifikasi/mark-seen', [NotifikasiController::class, 'markSeen'])
    ->name('notifikasi.markSeen')
    ->middleware('auth');

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
            ? redirect()->route('admin.dashboard.index')
            : redirect()->route('home');
    }
    return redirect()->route('login');
})->name('root');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'getNotifications']);
    Route::post('/notifikasi/baca/{id?}', [NotifikasiController::class, 'markAsRead']);
});

// -------------------------
// ADMIN ROUTES (ADMIN-ONLY)
// -------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard.index');

    Route::get('/pengajuan/status/{status}', action: [AdminController::class, 'pengajuanByStatus'])->name('admin.pengajuan.status');
    Route::get('/pengajuan/{pengajuan}/nota', [AdminPengajuanController::class, 'nota'])->name('admin.pengajuan.nota');
    Route::post('/pengajuan/{pengajuan}/received', [AdminPengajuanController::class, 'markAsReceived'])->name('admin.pengajuan.received');
    Route::post('/pengajuan/{pengajuan}/assign', [AdminPengajuanController::class, 'assignStaff'])->name('admin.pengajuan.assign');
    Route::post('/pengajuan/{pengajuan}/approve', [AdminPengajuanController::class, 'approve'])->name('admin.pengajuan.approve');
    Route::post('/pengajuan/{pengajuan}/reject', [AdminPengajuanController::class, 'reject'])->name('admin.pengajuan.reject');

    Route::resource('/items', ItemController::class, ['as' => 'admin']);
    Route::get('/items-data', [ItemController::class, 'data'])->name('admin.items.data');
    Route::post('/items/bulk-action', [ItemController::class, 'bulkAction'])->name('admin.items.bulkAction');
    Route::post('/items/{item}/toggle-archive', [ItemController::class, 'toggleArchive'])->name('admin.items.toggleArchive');
    Route::delete('/items/images/{image}', [ItemController::class, 'deleteImage'])->name('admin.items.images.delete');

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

    Route::resource('purchase-orders', PurchaseOrderController::class)->names('admin.purchase_orders');
    Route::prefix('purchase-orders')->name('admin.purchase_orders.')->group(function () {
        Route::post('{purchaseOrder}/submit', [PurchaseOrderController::class, 'submit'])->name('submit');
        Route::get('{purchaseOrder}/receive', [PurchaseOrderController::class, 'receiveForm'])->name('receive');
        Route::post('{purchaseOrder}/receive', [PurchaseOrderController::class, 'processReceive'])->name('processReceive');
        Route::get('{id}/pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('downloadPdf');
    });

    Route::controller(OpnameSessionController::class)->prefix('stock-opname')->name('admin.stock_opname.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('{stock_opname}/end', 'endSession')->name('endSession');
    });

    Route::resource('stock-opname', StockOpnameController::class)->except(['index'])->names('admin.stock_opname');
    Route::get('stock-opname/{stock_opname}/download', [StockOpnameController::class, 'downloadPdf'])
        ->name('admin.stock_opname.downloadPdf');

    Route::resource('satuan', SatuanController::class)->except(['show'])->names('admin.satuan');

    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
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

Route::middleware(['auth', 'role:staff_pengiriman'])->prefix('staff-pengiriman')->group(function () {
    Route::get('/dashboard', [StaffPengirimanController::class, 'index'])->name('staff-pengiriman.dashboard');
    Route::get('/konfirmasi/{kodeResi}', [StaffPengirimanController::class, 'show'])->name('staff-pengiriman.konfirmasi');
    Route::post('/konfirmasi/{kodeResi}', [StaffPengirimanController::class, 'submit'])->name('staff-pengiriman.konfirmasi.submit');
    Route::get('/on-progress', [StaffPengirimanController::class, 'onProgress'])->name('staff-pengiriman.onprogress');
    Route::get('/waiting', [StaffPengirimanController::class, 'waiting'])->name('staff-pengiriman.waiting');
    Route::get('/selesai', [StaffPengirimanController::class, 'selesai'])->name('staff-pengiriman.selesai');
    Route::post('/assign/{id}', [StaffPengirimanController::class, 'assignToMe'])->name('staff-pengiriman.assign');
});


Route::get('/portal', function () {
    return view('peri::portal.index');
})->name('portal')->middleware('auth');


Route::get('/portal', function () {
    return view('peri::portal.index');
})->name('portal')->middleware('auth');


Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi/user/produk', [UserNotifikasiController::class, 'produk'])->name('user.notif.produk');
    Route::get('/notifikasi/user/pengiriman', [UserNotifikasiController::class, 'pengiriman'])->name('user.notif.pengiriman');

    Route::post('/notifikasi/user/produk/baca/{id?}', [UserNotifikasiController::class, 'markProdukAsRead'])->name('user.notif.produk.baca');
    Route::post('/notifikasi/user/pengiriman/baca/{id?}', [UserNotifikasiController::class, 'markPengirimanAsRead'])->name('user.notif.pengiriman.baca');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/pengajuan/{id}/ttd', [PengajuanController::class, 'signature'])
        ->name('pengajuan.signature');

    Route::post('/pengajuan/{id}/ttd', [PengajuanController::class, 'signatureStore'])
        ->name('pengajuan.signature.store');
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/store/{id}', [CartController::class, 'store'])->name('cart.store');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/delete/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/bulk-delete', [CartController::class, 'bulkDelete'])->name('cart.bulkDelete');

    // checkout
    Route::get('/checkout', [CartController::class, 'checkoutPage'])->name('cart.checkoutPage');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // invoice
    Route::get('/invoice/{id}', [CartController::class, 'invoice'])->name('cart.invoice');

    // ongkir
    Route::post('/calc-ongkir', [CartController::class, 'calcOngkir'])
        ->name('cart.calcOngkir');
});

Route::prefix('cart/api/regions')->group(function () {
    Route::get('/provinces', [CartController::class, 'getProvinces']);
    Route::get('/cities/{province_code}', [CartController::class, 'getCities']);
    Route::get('/districts/{city_code}', [CartController::class, 'getDistricts']);
    Route::get('/villages/{district_code}', [CartController::class, 'getVillages']);
});

Route::post('/api/midtrans-callback', [CartController::class, 'handleNotification']);

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/setting', [ProfileController::class, 'edit'])->name('user.setting');
    Route::patch('/setting/update', [ProfileController::class, 'update'])->name('profile.update');
});