<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'th'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function() {
        return redirect()->route('admin.slip.index');
    })->name('dashboard');
    
    // === Slip Registry (Historical Data) ===
    Route::get('/slips', [AdminController::class, 'slipReader'])->name('admin.slip.index'); 
    Route::get('/slips/edit/{slip}', [AdminController::class, 'editSlip'])->name('admin.slip.edit');
    Route::post('/slips/process', [AdminController::class, 'processSlip'])->name('admin.slip.process');
    Route::post('/slips/update/{slip}', [AdminController::class, 'updateSlip'])->name('admin.slip.update');
    Route::delete('/slips/delete/{slip}', [AdminController::class, 'deleteSlip'])->name('admin.slip.delete');
    Route::get('/slips/export', [AdminController::class, 'exportExcel'])->name('admin.slip.export');

    // === Store Management (The "Brands") ===
    Route::get('/stores', [AdminController::class, 'stores'])->name('admin.stores.index');
    Route::post('/stores', [AdminController::class, 'storeStore'])->name('admin.stores.store');
    Route::get('/stores/{merchant}', [AdminController::class, 'showStore'])->name('admin.stores.show');
    Route::patch('/stores/{merchant}', [AdminController::class, 'updateStore'])->name('admin.stores.update');
    Route::delete('/stores/{merchant}', [AdminController::class, 'deleteStore'])->name('admin.stores.delete');

    // === Extraction Profiles (The "AI Prompt Templates") ===
    Route::get('/templates', [AdminController::class, 'merchants'])->name('admin.templates.index');
    Route::get('/templates/{merchant}/edit', [AdminController::class, 'editMerchant'])->name('admin.templates.edit');
    Route::post('/templates/suggest', [AdminController::class, 'suggestPrompt'])->name('admin.templates.suggest');
    Route::post('/templates/store', [AdminController::class, 'storeMerchant'])->name('admin.templates.store');
    Route::patch('/templates/update/{merchant}', [AdminController::class, 'updateMerchantMapping'])->name('admin.templates.update');
    Route::delete('/templates/delete/{merchant}', [AdminController::class, 'deleteMerchant'])->name('admin.templates.delete');

    // === Admin Only Features ===
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
