<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\TownController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
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


Route::group(['prefix' => 'auth'], function () {
    Route::post('signup/provider', [AuthController::class, 'signupProvider']);
    Route::post('signup/client', [AuthController::class, 'signupClient']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('password-otp', [AuthController::class, 'passwordOtp']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('towns', TownController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('product-categories', ProductCategoryController::class);
    Route::apiResource('service-categories', ServiceCategoryController::class);
    Route::apiResource('services', ServiceController::class);

    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/update/password', [UserController::class, 'update_password']);
        Route::get('/all', [UserController::class, 'getUsers']);
        Route::get('/clients', [UserController::class, 'getClients']);
        Route::get('/providers', [UserController::class, 'getProviders']);
        Route::get('/admin', [UserController::class, 'getAdmins']);
    });
});
