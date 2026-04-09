<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CentralController;

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
    
    Route::get('/projects', [AdminController::class, 'projects'])->name('admin.projects.index');
    Route::get('/projects/create', [AdminController::class, 'createProject'])->name('admin.projects.create');
    Route::post('/projects', [AdminController::class, 'storeProjectForAdmin'])->name('admin.projects.store');
    Route::get('/projects/{merchant}', [AdminController::class, 'showProject'])->name('admin.projects.show');
    Route::patch('/projects/{merchant}', [AdminController::class, 'updateProjectForAdmin'])->name('admin.projects.update');
    Route::patch('/projects/{merchant}/status', [AdminController::class, 'updateProjectStatus'])->name('admin.projects.status');
    Route::post('/projects/{merchant}/members', [AdminController::class, 'attachProjectMember'])->name('admin.projects.members.attach');
    Route::patch('/projects/{merchant}/members/{user}', [AdminController::class, 'updateProjectMemberRole'])->name('admin.projects.members.update');
    Route::delete('/projects/{merchant}/members/{user}', [AdminController::class, 'detachProjectMember'])->name('admin.projects.members.detach');
    
    Route::get('/topups', [AdminController::class, 'topupRequests'])->name('admin.topups');
    Route::post('/topups/{topupRequest}/approve', [AdminController::class, 'approveTopupRequest'])->name('admin.topups.approve');
    Route::post('/topups/{topupRequest}/reject', [AdminController::class, 'rejectTopupRequest'])->name('admin.topups.reject');

    Route::get('/settings', [AdminController::class, 'systemSettings'])->name('admin.settings');
    Route::patch('/settings', [AdminController::class, 'updateSystemSettings'])->name('admin.settings.update');
});

// ============================================
// SHARED WORKSPACE ROUTES (Pure Path)
// ============================================
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Folder Hub (The Profile Chooser)
    Route::get('/dashboard', [\App\Http\Controllers\ProjectController::class, 'index'])->name('dashboard');
    Route::post('/projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('workspace.projects.store');
    Route::delete('/projects/{id}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('workspace.projects.destroy');
    Route::get('/tokens/balance', [\App\Http\Controllers\ProjectController::class, 'getTokenBalance'])->name('workspace.tokens.balance');

    // Entry point to a folder
    Route::get('/projects/open/{project}', [CentralController::class, 'openProject'])->name('projects.open');

    // Inside a Workspace
    Route::middleware([\App\Http\Middleware\IdentifyTenant::class])->prefix('workspace')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('workspace.dashboard');
        Route::get('/slips', [AdminController::class, 'slipReader'])->name('workspace.slip.index');
        Route::post('/slips/process', [AdminController::class, 'processSlip'])->middleware('tokens:1')->name('workspace.slip.process');
        Route::get('/slips/edit/{slip}', [AdminController::class, 'editSlip'])->name('workspace.slip.edit');
        Route::delete('/slips/delete/{slip}', [AdminController::class, 'deleteSlip'])->name('workspace.slip.delete');
        Route::post('/slips/bulk', [AdminController::class, 'bulkUpdateSlips'])->name('workspace.slip.bulk');
        Route::get('/slips/export', [AdminController::class, 'exportExcel'])->name('workspace.slip.export');
        
        // Settings
        
        // Store Info
        Route::get('/stores/{merchant}', [AdminController::class, 'showStore'])->name('workspace.stores.show');
        Route::patch('/stores/{merchant}', [AdminController::class, 'updateStore'])->name('workspace.stores.update');
    });

    // Global Billing
    Route::get('/billing', [AdminController::class, 'billing'])->name('billing');
    Route::post('/billing/topups', [AdminController::class, 'submitTopupRequest'])->name('billing.topups.store');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
