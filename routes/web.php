<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::middleware('role:admin')->group(function () {
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Items
    Route::get('items', [ItemController::class, 'index'])->name('items.index');
    Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show')->whereNumber('item');
    Route::middleware('role:admin')->group(function () {
        Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('items', [ItemController::class, 'store'])->name('items.store');
        Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit')->whereNumber('item');
        Route::put('items/{item}', [ItemController::class, 'update'])->name('items.update')->whereNumber('item');
        Route::delete('items/{item}', [ItemController::class, 'destroy'])->name('items.destroy')->whereNumber('item');
    });

    // Stock In (Admin & Petugas)
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('stock-ins', [StockInController::class, 'index'])->name('stock-ins.index');
        Route::get('stock-ins/create', [StockInController::class, 'create'])->name('stock-ins.create');
        Route::post('stock-ins', [StockInController::class, 'store'])->name('stock-ins.store');
        Route::get('stock-ins/{stockIn}', [StockInController::class, 'show'])->name('stock-ins.show');
    });

    // Stock Out (Admin & Petugas)
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('stock-outs', [StockOutController::class, 'index'])->name('stock-outs.index');
        Route::get('stock-outs/create', [StockOutController::class, 'create'])->name('stock-outs.create');
        Route::post('stock-outs', [StockOutController::class, 'store'])->name('stock-outs.store');
        Route::get('stock-outs/{stockOut}', [StockOutController::class, 'show'])->name('stock-outs.show');
    });

    // Stock Movements (All roles)
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');

    // Reports (All roles)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('stock-in', [ReportController::class, 'stockIn'])->name('stock-in');
        Route::get('stock-out', [ReportController::class, 'stockOut'])->name('stock-out');
        Route::get('movements', [ReportController::class, 'movements'])->name('movements');
    });

    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
