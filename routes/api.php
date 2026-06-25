<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScanApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\AuthApiController;

// ======================
// API AUTH
// ======================
Route::post('/auth/login', [AuthApiController::class, 'login']);

// ======================
// API PROTECTED (Sanctum)
// ======================
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/me', [AuthApiController::class, 'me']);

    // ---- SCAN API ----
    Route::get('/scans', [ScanApiController::class, 'index']);
    Route::post('/scans', [ScanApiController::class, 'store']);
    Route::get('/scans/{id}', [ScanApiController::class, 'show']);
    Route::delete('/scans/{id}', [ScanApiController::class, 'destroy']);

    // ---- USER API (Admin only) ----
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserApiController::class, 'index']);
        Route::post('/users', [UserApiController::class, 'store']);
        Route::get('/users/{id}', [UserApiController::class, 'show']);
        Route::put('/users/{id}', [UserApiController::class, 'update']);
        Route::delete('/users/{id}', [UserApiController::class, 'destroy']);
    });
});
