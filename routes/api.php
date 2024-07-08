<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\TownController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('countries', CountryController::class);
Route::apiResource('towns', TownController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('product-categories', ProductCategoryController::class);
Route::apiResource('service-categories', ServiceCategoryController::class);

Route::get('/clients', [UserController::class, 'getClients']);
Route::get('/providers', [UserController::class, 'getProviders']);
Route::get('/admins', [UserController::class, 'getAdmins']);
Route::apiResource('services', ServiceController::class);
