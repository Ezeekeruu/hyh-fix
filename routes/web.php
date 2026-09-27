<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.dashboard-index');
});

Route::get('/pos', function () {
    return view('pos.pos-index');
});

Route::get('/transaction-history', function () {
    return view('transactions.transactions-index');
})->name('transaction.index');

Route::get('/pos', [SaleController::class, 'create'])->name('pos.index');
Route::post('/pos', [SaleController::class, 'store'])->name('sales.store');

// Inventory Management
Route::get('/inventory', [ProductController::class, 'index'])->name('products.index');
Route::get('/inventory/add', [ProductController::class, 'create'])->name('products.create');
Route::post('/inventory', [ProductController::class, 'store'])->name('products.store');
Route::get('/inventory/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/inventory/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/inventory/{id}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/inventory/{id}', [ProductController::class, 'destroy'])->name('products.destroy');


// Reports
Route::get('/reports', function () {
    return view('reports.reports-index');
});

// User Management
Route::get('/user-management', [UserController::class, 'index'])->name('users.index');
Route::get('/user-management/add', [UserController::class, 'create'])->name('users.create');
Route::post('/user-management', [UserController::class, 'store'])->name('users.store');
