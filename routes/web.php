<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CoffeeController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Middleware\AdminMiddleware;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // POS Interface
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);

    // Coffee Menu
    Route::resource('coffees', CoffeeController::class)->except(['create', 'edit', 'show']);

    // Tables
    Route::resource('tables', TableController::class)->except(['create', 'edit', 'show']);

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'edit', 'show']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Receipt
    Route::get('/receipt/{order}', [ReceiptController::class, 'show'])->name('receipt.show');
    Route::get('/receipt/{order}/pdf', [ReceiptController::class, 'downloadPdf'])->name('receipt.pdf');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');

    // Admin Only Routes
    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
