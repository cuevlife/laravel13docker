<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CentralController;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'th'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

$domain = env('APP_DOMAIN', 'localhost');

// ============================================
// SUPER ADMIN SUBDOMAIN ROUTES
// ============================================
Route::domain('admin.' . $domain)->middleware(['web', 'auth', 'role:super_admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // === SaaS Owner Features ===
    Route::get('/dashboard', [AdminController::class, 'superAdminDashboard'])->name('admin.dashboard');
    Route::get('/projects', [AdminController::class, 'projects'])->name('admin.projects.index');
    Route::post('/projects', [AdminController::class, 'storeProjectForAdmin'])->name('admin.projects.store');
    Route::get('/projects/{merchant}', [AdminController::class, 'showProject'])->name('admin.projects.show');
    Route::patch('/projects/{merchant}', [AdminController::class, 'updateProjectForAdmin'])->name('admin.projects.update');
    Route::patch('/projects/{merchant}/status', [AdminController::class, 'updateProjectStatus'])->name('admin.projects.status');
    Route::post('/projects/{merchant}/members', [AdminController::class, 'attachProjectMember'])->name('admin.projects.members.attach');
    Route::patch('/projects/{merchant}/members/{user}', [AdminController::class, 'updateProjectMemberRole'])->name('admin.projects.members.update');
    Route::delete('/projects/{merchant}/members/{user}', [AdminController::class, 'detachProjectMember'])->name('admin.projects.members.detach');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.status');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/users/{user}/workspaces', [AdminController::class, 'attachUserWorkspace'])->name('admin.users.workspaces.attach');
    Route::delete('/users/{user}/workspaces/{merchant}', [AdminController::class, 'detachUserWorkspace'])->name('admin.users.workspaces.detach');
    Route::post('/users/{user}/tokens', [AdminController::class, 'adjustTokens'])->name('admin.users.tokens');
    Route::get('/topups', [AdminController::class, 'topupRequests'])->name('admin.topups');
    Route::post('/topups/{topupRequest}/approve', [AdminController::class, 'approveTopupRequest'])->name('admin.topups.approve');
    Route::post('/topups/{topupRequest}/reject', [AdminController::class, 'rejectTopupRequest'])->name('admin.topups.reject');
});

Route::middleware(['web', 'auth', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->to(\App\Support\OwnerUrl::path(request(), 'dashboard'));
    });

    Route::get('/dashboard', [AdminController::class, 'superAdminDashboard'])->name('owner.dashboard');
    Route::get('/projects', [AdminController::class, 'projects'])->name('owner.projects.index');
    Route::post('/projects', [AdminController::class, 'storeProjectForAdmin'])->name('owner.projects.store');
    Route::get('/projects/{merchant}', [AdminController::class, 'showProject'])->name('owner.projects.show');
    Route::patch('/projects/{merchant}', [AdminController::class, 'updateProjectForAdmin'])->name('owner.projects.update');
    Route::patch('/projects/{merchant}/status', [AdminController::class, 'updateProjectStatus'])->name('owner.projects.status');
    Route::post('/projects/{merchant}/members', [AdminController::class, 'attachProjectMember'])->name('owner.projects.members.attach');
    Route::patch('/projects/{merchant}/members/{user}', [AdminController::class, 'updateProjectMemberRole'])->name('owner.projects.members.update');
    Route::delete('/projects/{merchant}/members/{user}', [AdminController::class, 'detachProjectMember'])->name('owner.projects.members.detach');
    Route::get('/users', [AdminController::class, 'users'])->name('owner.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('owner.users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('owner.users.show');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('owner.users.role');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('owner.users.status');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('owner.users.destroy');
    Route::post('/users/{user}/tokens', [AdminController::class, 'adjustTokens'])->name('owner.users.tokens');
    Route::post('/users/{user}/workspaces', [AdminController::class, 'attachUserWorkspace'])->name('owner.users.workspaces.attach');
    Route::delete('/users/{user}/workspaces/{merchant}', [AdminController::class, 'detachUserWorkspace'])->name('owner.users.workspaces.detach');
    Route::get('/topups', [AdminController::class, 'topupRequests'])->name('owner.topups');
    Route::post('/topups/{topupRequest}/approve', [AdminController::class, 'approveTopupRequest'])->name('owner.topups.approve');
    Route::post('/topups/{topupRequest}/reject', [AdminController::class, 'rejectTopupRequest'])->name('owner.topups.reject');
});

// ============================================
// TENANT SUBDOMAIN ROUTES
// ============================================
Route::domain('{subdomain}.' . $domain)->middleware(['web', \App\Http\Middleware\IdentifyTenant::class])->group(function () {
    Route::get('/', function ($subdomain) {
        return redirect()->route('login');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('tenant.dashboard');

        // === Slip Registry (Historical Data) ===
        Route::get('/slips', [AdminController::class, 'slipReader'])->name('admin.slip.index');
        Route::get('/exports', [AdminController::class, 'exportCenter'])->name('admin.exports.index');
        Route::post('/slips/batches', [AdminController::class, 'storeSlipBatch'])->name('admin.slip.batches.store');
        Route::patch('/slips/batches/{batch}', [AdminController::class, 'updateSlipBatch'])->name('admin.slip.batches.update');
        Route::post('/slips/bulk', [AdminController::class, 'bulkUpdateSlips'])->name('admin.slip.bulk');
        Route::get('/slips/edit/{slip}', [AdminController::class, 'editSlip'])->name('admin.slip.edit');
        Route::post('/slips/process', [AdminController::class, 'processSlip'])->middleware('tokens:1')->name('admin.slip.process');
        Route::post('/slips/update/{slip}', [AdminController::class, 'updateSlip'])->name('admin.slip.update');
        Route::patch('/slips/{slip}/workflow', [AdminController::class, 'updateSlipWorkflow'])->name('admin.slip.workflow');
        Route::patch('/slips/{slip}/archive', [AdminController::class, 'toggleSlipArchive'])->name('admin.slip.archive');
        Route::delete('/slips/delete/{slip}', [AdminController::class, 'deleteSlip'])->name('admin.slip.delete');
        Route::get('/slips/export', [AdminController::class, 'exportExcel'])->name('admin.slip.export');
        // === Extraction Profiles (The "Intelligence Profiles") ===
        Route::get('/templates', [AdminController::class, 'merchants'])->name('admin.templates.index');
        Route::get('/templates/{merchant}/edit', [AdminController::class, 'editMerchant'])->name('admin.templates.edit');
        Route::post('/templates/suggest', [AdminController::class, 'suggestPrompt'])->name('admin.templates.suggest');
        Route::post('/templates/store', [AdminController::class, 'storeMerchant'])->name('admin.templates.store');
        Route::patch('/templates/update/{merchant}', [AdminController::class, 'updateMerchantMapping'])->name('admin.templates.update');
        Route::delete('/templates/delete/{merchant}', [AdminController::class, 'deleteMerchant'])->name('admin.templates.delete');
    });
});

// ============================================
// SESSION-BASED WORKSPACE ROUTES
// ============================================
Route::middleware(['web', 'auth', 'verified', \App\Http\Middleware\IdentifyTenant::class])->prefix('workspace')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('workspace.dashboard');

    Route::get('/slips', [AdminController::class, 'slipReader'])->name('workspace.slip.index');
    Route::get('/exports', [AdminController::class, 'exportCenter'])->name('workspace.exports.index');
    Route::post('/slips/batches', [AdminController::class, 'storeSlipBatch'])->name('workspace.slip.batches.store');
    Route::patch('/slips/batches/{batch}', [AdminController::class, 'updateSlipBatch'])->name('workspace.slip.batches.update');
    Route::post('/slips/bulk', [AdminController::class, 'bulkUpdateSlips'])->name('workspace.slip.bulk');
    Route::get('/slips/edit/{slip}', [AdminController::class, 'editSlip'])->name('workspace.slip.edit');
    Route::post('/slips/process', [AdminController::class, 'processSlip'])->middleware('tokens:1')->name('workspace.slip.process');
    Route::post('/slips/update/{slip}', [AdminController::class, 'updateSlip'])->name('workspace.slip.update');
    Route::patch('/slips/{slip}/workflow', [AdminController::class, 'updateSlipWorkflow'])->name('workspace.slip.workflow');
    Route::patch('/slips/{slip}/archive', [AdminController::class, 'toggleSlipArchive'])->name('workspace.slip.archive');
    Route::delete('/slips/delete/{slip}', [AdminController::class, 'deleteSlip'])->name('workspace.slip.delete');
    Route::get('/slips/export', [AdminController::class, 'exportExcel'])->name('workspace.slip.export');

    Route::get('/templates', [AdminController::class, 'merchants'])->name('workspace.templates.index');
    Route::get('/templates/{merchant}/edit', [AdminController::class, 'editMerchant'])->name('workspace.templates.edit');
    Route::post('/templates/suggest', [AdminController::class, 'suggestPrompt'])->name('workspace.templates.suggest');
    Route::post('/templates/store', [AdminController::class, 'storeMerchant'])->name('workspace.templates.store');
    Route::patch('/templates/update/{merchant}', [AdminController::class, 'updateMerchantMapping'])->name('workspace.templates.update');
    Route::delete('/templates/delete/{merchant}', [AdminController::class, 'deleteMerchant'])->name('workspace.templates.delete');
});

// ============================================
// CENTRAL DOMAIN ROUTES (Fallback)
// ============================================
Route::middleware('web')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        // Workspace Selector
        Route::get('/dashboard', [CentralController::class, 'dashboard'])->name('dashboard');
        Route::get('/projects/open/{project}', [CentralController::class, 'openProject'])->name('projects.open');

        // Global Billing
        Route::get('/billing', [AdminController::class, 'billing'])->name('billing');
        Route::post('/billing/topups', [AdminController::class, 'submitTopupRequest'])->name('billing.topups.store');

        // === Store Management (The "Brands") ===
        Route::get('/stores', [AdminController::class, 'stores'])->name('admin.stores.index');
        Route::post('/stores', [AdminController::class, 'storeStore'])->name('admin.stores.store');
        Route::get('/stores/{merchant}', [AdminController::class, 'showStore'])->name('admin.stores.show');
        Route::patch('/stores/{merchant}', [AdminController::class, 'updateStore'])->name('admin.stores.update');
        Route::delete('/stores/{merchant}', [AdminController::class, 'deleteStore'])->name('admin.stores.delete');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
