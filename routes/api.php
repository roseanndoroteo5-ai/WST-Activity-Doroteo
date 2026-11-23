<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HumanResourcesPayrollController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Product1Controller;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});

Route::apiResource('products1', Product1Controller::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/payroll', [HumanResourcesPayrollController::class, 'payroll']);

Route::post('/payroll/calculate', [
    PayrollController::class,
    'calculate',
]);

Route::apiResource('products', ProductController::class)->names('legacy.products');
