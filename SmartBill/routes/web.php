<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // AI Slip Reader
    Route::get('/admin/slip-reader', [AdminController::class, 'slipReader'])->name('admin.slip-reader');
    Route::post('/admin/slip-reader', [AdminController::class, 'processSlip'])->name('admin.slip-process');
    Route::get('/admin/slip-export', [AdminController::class, 'exportExcel'])->name('admin.slip-export');

    // Management
    Route::get('/admin/merchants', [AdminController::class, 'merchants'])->name('admin.merchants');
    Route::post('/admin/merchants', [AdminController::class, 'storeMerchant'])->name('admin.merchants.store');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
