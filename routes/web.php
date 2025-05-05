<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Auth\SocialiteController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/firebase-users', [FirebaseController::class, 'getUsers']);
Route::get('/login', [AuthApiController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthApiController::class, 'login']);
Route::get('/logout', [AuthApiController::class, 'logout']);
Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirectToProvider']); //ini yg baru
Route::get('/auth/callback/{provider}', [SocialiteController::class, 'handleProviderCallback']);// ini yg baru
//Route::middleware(['isAdmin'])->group(function () {
//    Route::get('/admin/me', [AdminDashboardController::class, 'me']);
//    Route::get('/admin/users', [AdminDashboardController::class, 'users']);


//});







