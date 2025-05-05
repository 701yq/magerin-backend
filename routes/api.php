<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminReportController;
use App\Http\Controllers\Api\AdminDashboardController;

Route::prefix('admin')->group(function () {

    // 🔐 Login (tanpa middleware)
    Route::post('/login', [AuthApiController::class, 'login']);

    // 🔐 Middleware untuk admin
    Route::middleware(['auth.admin'])->group(function () {
        // ✅ Profil admin
        Route::get('/me', [AuthApiController::class, 'me']);

        // ✅ Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // ✅ Users
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{id}/status', [AdminUserController::class, 'updateStatus']);

        // ✅ Reports
        Route::get('/reports', [AdminReportController::class, 'index']);
        Route::get('/reports/{id}', [AdminReportController::class, 'show']);
        Route::put('/reports/{id}', [AdminReportController::class, 'update']);
        Route::delete('/reports/{id}', [AdminReportController::class, 'destroy']);
    });

});
