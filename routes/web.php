<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProductController;

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Auth::routes(['register' => false]); // Registration only via admin panel

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Root redirect: Everyone goes to Dashboard first now
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard (Accessible by all roles)
    Route::get('/dashboard', [HomeController::class, 'index'])
        ->name('dashboard')
        ->middleware('web.role:admin,manager,cashier');

    // Role-specific route aliases (to satisfy specific sidebar logic)
    Route::get('/cashier/dashboard', [HomeController::class, 'index'])
        ->name('cashier.dashboard')
        ->middleware('web.role:cashier');

    Route::get('/admin/dashboard', [HomeController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('web.role:admin,manager');

    // POS screen (all roles)
    Route::get('/pos', function () {
        return view('pos');
    })->name('pos');

    // User Management (Admin + Manager only)
    Route::prefix('users')->name('users.')->middleware('web.role:admin,manager')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Product Management (Admin + Manager only)
    Route::resource('products', ProductController::class)->middleware('web.role:admin,manager');
});
