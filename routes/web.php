<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\RepairTicketController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// POS
Route::get('/pos', [SaleController::class, 'create'])->name('pos.index');
Route::post('/pos', [SaleController::class, 'store'])->name('sales.store');

// Repair Management
Route::get('/repair-management', [RepairTicketController::class, 'index'])->name('repair-tickets.index');
Route::get('/repair-management/create', [RepairTicketController::class, 'create'])->name('repair-tickets.create');
Route::post('/repair-management', [RepairTicketController::class, 'store'])->name('repair-tickets.store');
Route::get('/repair-management/{id}', [RepairTicketController::class, 'show'])->name('repair-tickets.show');
Route::get('/repair-management/{id}/edit', [RepairTicketController::class, 'edit'])->name('repair-tickets.edit');
Route::put('/repair-management/{id}', [RepairTicketController::class, 'update'])->name('repair-tickets.update');
Route::delete('/repair-management/{id}', [RepairTicketController::class, 'destroy'])->name('repair-tickets.destroy');

// Transaction History
Route::get('/transaction-history', function () {
    return view('transactions.transactions-index');
})->name('transaction.index');

// Inventory Management
Route::get('/inventory', [ProductController::class, 'index'])->name('products.index');
Route::get('/inventory/add', [ProductController::class, 'create'])->name('products.create');
Route::post('/inventory', [ProductController::class, 'store'])->name('products.store');
Route::get('/inventory/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/inventory/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/inventory/{id}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/inventory/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::post('/service-types',[RepairTicketController::class, 'storeServiceType'])->name('service-types.store');

// Category Routes
Route::resource('categories', CategoryController::class);

// Supplier Routes
Route::resource('suppliers', SupplierController::class);

// Transaction History
Route::get('/transaction-history', [SaleController::class, 'transactionHistory'])
    ->name('transaction.history');

Route::get('/sales/{id}', [SaleController::class, 'show'])
    ->name('sales.show');

// Reports
Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// User Management
Route::get('/user-management', [UserController::class, 'index'])->name('users.index');
Route::get('/user-management/add', [UserController::class, 'create'])->name('users.create');
Route::post('/user-management', [UserController::class, 'store'])->name('users.store');
Route::get('/user-management/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/user-management/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/user-management/{id}', [UserController::class, 'destroy'])->name('users.destroy');
