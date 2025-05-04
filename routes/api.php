<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminReportController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AdminDashboardController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/admin/login', [AuthApiController::class, 'login']);
Route::middleware(['auth.admin'])->get('/admin/me', [AuthApiController::class, 'me']);
Route::middleware(['auth.admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index']);
});
Route::middleware(['auth.admin'])->group(function () {
    Route::put('/admin/users/{id}/status', [AdminUserController::class, 'updateStatus']);
    Route::get('/admin/users', [AdminUserController::class, 'index']);
});
Route::middleware('auth.admin')->get('/admin/profile', [AdminController::class, 'profile']);
Route::middleware('auth.admin')->prefix('admin')->group(function () {
    Route::get('/reports', [AdminReportController::class, 'index']);
    Route::get('/reports/{id}', [AdminReportController::class, 'show']);
    Route::put('/reports/{id}', [AdminReportController::class, 'update']);
    Route::delete('/reports/{id}', [AdminReportController::class, 'destroy']);
});
Route::middleware(['auth.admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);
});






