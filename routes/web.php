<?php

use Illuminate\Support\Facades\Route;

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
});

Route::get('/inventory', function () {
    return view('inventory.inventory-index');
});

Route::get('/reports', function () {
    return view('reports.reports-index');
});

Route::get('/user-management', function () {
    return view('users.users-index');
});