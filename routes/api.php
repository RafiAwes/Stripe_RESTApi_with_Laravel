<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\userController;
use App\Http\Controllers\Api\paymentController;
use App\Http\Controllers\Api\TransferController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('user')->group(function () {
Route::post('/login', [userController::class, 'loginUser']);
Route::post('/register', [userController::class, 'registerUser']);
Route::post('create-account', [userController::class, 'createAccount']);
})->middleware('guest');

Route::prefix('payment')->group(function () {
    Route::post('/create', [paymentController::class, 'create']);
    Route::post('/confirm', [paymentController::class, 'confirm']);
    Route::post('/release', [TransferController::class, 'releaseToSupplier']);
});

Route::get('/create', [userController::class, 'createAccount']);