<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BalanceController;
use App\Http\Controllers\Api\TransactionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('balance')->group(function () {
        Route::get('/', [BalanceController::class, 'show']);
        Route::post('deposit', [BalanceController::class, 'deposit']);
        Route::post('withdraw', [BalanceController::class, 'withdraw']);
    });
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/recent', [TransactionController::class, 'recent']);
});
