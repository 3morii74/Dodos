<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1/auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
    Route::post('/verifyResetCode', [AuthController::class, 'verifyResetCode']);
    Route::put('/resetPassword', [AuthController::class, 'resetPassword']);
});
