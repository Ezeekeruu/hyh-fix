<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\RepairTicketController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->to(AuthController::homeFor(auth()->user()));
})->name('home');

// Login (also fixes the route('login') link in welcome.blade.php).
// No public registration: only an admin creates staff accounts.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {

    // Dashboards. Admins land on /dashboard, staff on /staff/dashboard.
    // Each action redirects cross-role visits to the correct home.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');

    // POS — both roles can process sales.
    Route::get('/pos', [SaleController::class, 'create'])->name('pos.index');
    Route::post('/pos', [SaleController::class, 'store'])->name('sales.store');

    // Repair Management — both roles (unchanged behavior).
    Route::get('/repair-management', [RepairTicketController::class, 'index'])->name('repair-tickets.index');
    Route::get('/repair-management/create', [RepairTicketController::class, 'create'])->name('repair-tickets.create');
    Route::post('/repair-management', [RepairTicketController::class, 'store'])->name('repair-tickets.store');
    Route::get('/repair-management/{id}', [RepairTicketController::class, 'show'])->name('repair-tickets.show');
    Route::get('/repair-management/{id}/edit', [RepairTicketController::class, 'edit'])->name('repair-tickets.edit');
    Route::put('/repair-management/{id}', [RepairTicketController::class, 'update'])->name('repair-tickets.update');
    Route::delete('/repair-management/{id}', [RepairTicketController::class, 'destroy'])->name('repair-tickets.destroy');

    // Transaction History — both roles, read-only UI (receipt view only).
    Route::get('/transaction-history', [SaleController::class, 'transactionHistory'])
        ->name('transaction.history');

    Route::get('/sales/{id}', [SaleController::class, 'show'])
        ->name('sales.show');

    // Inventory list — both roles. Staff sees it read-only
    // (action buttons hidden in the view, writes blocked below).
    Route::get('/inventory', [ProductController::class, 'index'])->name('products.index');

    // Admin-only: everything that writes inventory, plus cost/profit
    // reports and staff account management. Staff gets 403 here.
    Route::middleware('role:admin')->group(function () {
        Route::get('/inventory/add', [ProductController::class, 'create'])->name('products.create');
        Route::post('/inventory', [ProductController::class, 'store'])->name('products.store');
        Route::get('/inventory/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/inventory/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/inventory/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/inventory/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Master data — each with its own module page + table.
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/add', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/add', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('/service-types', [ServiceTypeController::class, 'index'])->name('service-types.index');
        Route::get('/service-types/add', [ServiceTypeController::class, 'create'])->name('service-types.create');
        Route::post('/service-types', [ServiceTypeController::class, 'store'])->name('service-types.store');
        Route::get('/service-types/{id}/edit', [ServiceTypeController::class, 'edit'])->name('service-types.edit');
        Route::put('/service-types/{id}', [ServiceTypeController::class, 'update'])->name('service-types.update');
        Route::delete('/service-types/{id}', [ServiceTypeController::class, 'destroy'])->name('service-types.destroy');

        Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('/user-management', [UserController::class, 'index'])->name('users.index');
        Route::get('/user-management/add', [UserController::class, 'create'])->name('users.create');
        Route::post('/user-management', [UserController::class, 'store'])->name('users.store');
        Route::get('/user-management/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/user-management/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/user-management/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
