<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

// --- Authenticated Routes ---
Route::middleware(["auth", "verified"])->group(function () {
    
    // Core Navigation
    Route::get("/dashboard", [FolderController::class, "index"])->name("dashboard");
    Route::get("/token-balance", [FolderController::class, "getTokenBalance"])->name("workspace.tokens.balance");
    Route::post("/workspace/select/{folder}", [FolderController::class, "select"])->name("workspace.select");

    // Language Switcher
    Route::get("/lang/{locale}", function ($locale) {
        if (in_array($locale, ["en", "th"])) {
            session(["locale" => $locale]);
        }
        return back();
    })->name("lang.switch");

    // Profile Management
    Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
    Route::delete("/profile", [ProfileController::class, "destroy"])->name("profile.destroy");

    // --- Workspace / Tenant Contextual Routes ---
    Route::middleware([\App\Http\Middleware\IdentifyTenant::class])->prefix("workspace")->group(function () {
        Route::get("/dashboard", [AdminController::class, "dashboard"])->name("workspace.dashboard");
        
        // Slips Management
        Route::get("/slips", [AdminController::class, "slipReader"])->name("workspace.slip.index");
        Route::get("/slips/edit/{slip}", [AdminController::class, "editSlip"])->name("workspace.slip.edit");
        Route::post("/slips/process", [AdminController::class, "processSlip"])->name("workspace.slip.process");
        Route::post("/slips/rescan/{slip}", [AdminController::class, "rescanSlip"])->name("workspace.slip.rescan");
        Route::patch("/slips/update/{slip}", [AdminController::class, "updateSlip"])->name("workspace.slip.update");
        Route::delete("/slips/delete/{slip}", [AdminController::class, "deleteSlip"])->name("workspace.slip.delete");
        Route::post("/slips/bulk-update", [AdminController::class, "bulkUpdateSlips"])->name("workspace.slip.bulk-update");
        
        // Settings & Export
        Route::patch("/slips/export-settings", [AdminController::class, "updateExportSettings"])->name("workspace.slip.export-settings");
        Route::get("/slips/export-excel", [AdminController::class, "exportExcel"])->name("workspace.slip.export");
        Route::get("/slips/export-history", [AdminController::class, "exportHistory"])->name("workspace.slip.export-history");
        Route::post("/slips/suggest-fields", [AdminController::class, "suggestWorkspaceFields"])->name("workspace.slip.suggest-fields");
    });

    // --- Super Admin Routes ---
    Route::middleware(["role:super_admin"])->prefix("admin")->group(function () {
        Route::get("/users", [AdminController::class, "users"])->name("admin.users");
        Route::get("/users/{user}", [AdminController::class, "showUser"])->name("admin.users.show");
        Route::get("/audit-logs", [AdminController::class, "auditLogs"])->name("admin.audit-logs");
        Route::get("/settings", [AdminController::class, "systemSettings"])->name("admin.settings");
        Route::patch("/settings", [AdminController::class, "updateSystemSettings"])->name("admin.settings.update");
        Route::post("/settings/suggest", [AdminController::class, "suggestPrompt"])->name("admin.settings.suggest");

        Route::post("/folders", [AdminController::class, "storeFolderForAdmin"])->name("admin.folders.store");
        Route::get("/folders/{merchant}", [AdminController::class, "showFolder"])->name("admin.folders.show");
        Route::patch("/folders/{merchant}", [AdminController::class, "updateFolderForAdmin"])->name("admin.folders.update");
        Route::patch("/folders/{merchant}/status", [AdminController::class, "updateFolderStatus"])->name("admin.folders.status");
        Route::delete("/folders/{merchant}", [AdminController::class, "destroyFolder"])->name("admin.folders.destroy");
    });
});

require __DIR__."/auth.php";