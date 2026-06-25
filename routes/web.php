<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;

// ======================
// AUTH ROUTES
// ======================
Route::get('/', fn() => redirect('/login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ======================
// DASHBOARD (semua role)
// ======================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PROFIL pengguna
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    // SCAN - print result
    Route::get('/scan/{id}/print', [ScanController::class, 'print'])->name('scan.print');

    // ======================
    // ROLE: ADMIN
    // ======================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Manajemen Pengguna (CRUD)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Laporan dan Export
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
        Route::get('/export', [DashboardController::class, 'export'])->name('export');

        // Pengaturan Sistem
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    });

    // ======================
    // ROLE: PETUGAS
    // ======================
    Route::middleware('role:petugas,admin')->prefix('petugas')->name('petugas.')->group(function () {
        // Input data manual
        Route::post('/scan/manual', [ScanController::class, 'storeManual'])->name('scan.manual');
    });

    // ======================
    // SCAN DARI WEBSOCKET (ESP32)
    // ======================
    Route::middleware('role:petugas,admin')->post('/scan/save', [ScanController::class, 'store'])->name('scan.save');
});
