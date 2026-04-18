<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CentralController;
use App\Http\Controllers\FolderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'th'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// ============================================
// PUBLIC & AUTH ROUTES
// ============================================
Route::get('/', function () {
    return redirect()->to('/login');
});

require __DIR__.'/auth.php';

// ============================================
// SUPER ADMIN ROUTES (Control Plane - Pure Path)
// ============================================
Route::middleware(['web', 'auth', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->to('/admin/users');
    });

    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.status');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/users/{user}/tokens', [AdminController::class, 'adjustTokens'])->name('admin.users.tokens');
    Route::post('/users/{user}/workspaces', [AdminController::class, 'attachUserWorkspace'])->name('admin.users.workspaces.attach');
    Route::delete('/users/{user}/workspaces/{merchant}', [AdminController::class, 'detachUserWorkspace'])->name('admin.users.workspaces.detach');
    
    Route::get('/folders', [AdminController::class, 'folders'])->name('admin.folders.index');
    Route::get('/folders/create', [AdminController::class, 'createFolder'])->name('admin.folders.create');
    Route::post('/folders', [AdminController::class, 'storeFolderForAdmin'])->name('admin.folders.store');
    Route::get('/folders/{merchant}', [AdminController::class, 'showFolder'])->name('admin.folders.show');
    Route::patch('/folders/{merchant}', [AdminController::class, 'updateFolderForAdmin'])->name('admin.folders.update');
    Route::patch('/folders/{merchant}/status', [AdminController::class, 'updateFolderStatus'])->name('admin.folders.status');
    Route::post('/folders/{merchant}/members', [AdminController::class, 'attachFolderMember'])->name('admin.folders.members.attach');
    Route::patch('/folders/{merchant}/members/{user}', [AdminController::class, 'updateFolderMemberRole'])->name('admin.folders.members.update');
    Route::delete('/folders/{merchant}/members/{user}', [AdminController::class, 'detachFolderMember'])->name('admin.folders.members.detach');
    
    Route::get('/settings', [AdminController::class, 'systemSettings'])->name('admin.settings');
    Route::patch('/settings', [AdminController::class, 'updateSystemSettings'])->name('admin.settings.update');
    Route::post('/settings/suggest', [AdminController::class, 'suggestPrompt'])->name('admin.settings.suggest');
});

// ============================================
// SHARED WORKSPACE ROUTES (Pure Path)
// ============================================
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Folder Hub (The Profile Chooser)
    Route::get('/dashboard', [FolderController::class, 'index'])->name('dashboard');
    Route::post('/folders', [FolderController::class, 'store'])->name('workspace.folders.store');
    Route::delete('/folders/{id}', [FolderController::class, 'destroy'])->name('workspace.folders.destroy');
    Route::get('/tokens/balance', [FolderController::class, 'getTokenBalance'])->name('workspace.tokens.balance');

    // Entry point to a folder
    Route::get('/folders/open/{folder}', [CentralController::class, 'openFolder'])->name('folders.open');

    // Legacy Redirects
    Route::get('/projects/open/{id}', fn($id) => redirect()->route('folders.open', ['folder' => $id]));
    Route::get('/admin/projects', fn() => redirect()->route('admin.folders.index'));
    Route::get('/admin/projects/create', fn() => redirect()->route('admin.folders.create'));
    Route::get('/admin/projects/{id}', fn($id) => redirect()->route('admin.folders.show', ['merchant' => $id]));

    // Inside a Workspace
    Route::middleware([\App\Http\Middleware\IdentifyTenant::class])->prefix('workspace')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('workspace.dashboard');
        Route::get('/slips', [AdminController::class, 'slipReader'])->name('workspace.slip.index');
        Route::post('/slips/process', [AdminController::class, 'processSlip'])->middleware('tokens:1')->name('workspace.slip.process');
        Route::get('/slips/edit/{slip}', [AdminController::class, 'editSlip'])->name('workspace.slip.edit');
        Route::delete('/slips/delete/{slip}', [AdminController::class, 'deleteSlip'])->name('workspace.slip.delete');
        Route::post('/slips/rescan/{slip}', [AdminController::class, 'rescanSlip'])->name('workspace.slip.rescan');
        Route::post('/slips/bulk', [AdminController::class, 'bulkUpdateSlips'])->name('workspace.slip.bulk');
        Route::get('/slips/export', [AdminController::class, 'exportExcel'])->name('workspace.slip.export');
        Route::get('/slips/export-history', [AdminController::class, 'exportHistory'])->name('workspace.slip.export-history');
        Route::patch('/slips/export-settings', [AdminController::class, 'updateExportSettings'])->name('workspace.slip.export-settings');
        
        // Settings
        
        // Store Info
        Route::get('/stores/{merchant}', [AdminController::class, 'showStore'])->name('workspace.stores.show');
        Route::patch('/stores/{merchant}', [AdminController::class, 'updateStore'])->name('workspace.stores.update');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
