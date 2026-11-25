<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    Route::resource('categories', CategoryController::class)
        ->except('show');

    Route::resource('users', UserController::class)
        ->except('show');

    Route::resource('roles', RoleController::class)
        ->except('show');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/medicines', [MedicineController::class, 'index'])
        ->name('medicines.index');

    Route::get('/customers', [CustomerController::class, 'index'])
        ->name('customers.index');

    Route::get('/sales', [SaleController::class, 'index'])
        ->name('sales.index');

    Route::get('/sales/create', [SaleController::class, 'create'])
        ->name('sales.create');

    Route::post('/sales', [SaleController::class, 'store'])
        ->name('sales.store');

    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])
        ->name('sales.destroy');
});
