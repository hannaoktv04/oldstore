<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
// use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return File::get(public_path('index.html'));
});

// Route::get('/', [AuthController::class, 'login'])->name('login');
// Route::post('/do-login', [AuthController::class, 'do_login'])->name('do-login');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
   Route::prefix('dashboard')->name('dashboard.')->group(function () {
      Route::get('/', [DashboardController::class, 'index'])->name('index');
   });
});
