<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::post('auth/register', 'register');
});

Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth.is.admin')->group(function () {
        Route::get('deposits/pendings', [DepositController::class, 'pendings']);
        Route::patch('deposits/{deposit}/status', [DepositController::class, 'updateStatus']);
    });

    Route::middleware('auth.is.customer')->group(function () {
        Route::post('deposits', [DepositController::class, 'newDeposit']);

        Route::get('accounts/balance', [AccountController::class, 'balance']);
        Route::get('accounts/transactions', [AccountController::class, 'transactions']);

        Route::post('purchases', [PurchaseController::class, 'store']);
    });
});
