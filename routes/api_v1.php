<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);

Route::get('products/queries', [ProductController::class, 'queries']);

Route::get('products/stats', [ProductController::class, 'stats']);

// Creating a product requires a signed-in session; the rest stay public
Route::middleware('auth:sanctum')->post('products', [ProductController::class, 'store'])->name('products.store');

Route::apiResource('products', ProductController::class)->except('store');

Route::apiResource('orders', OrderController::class);

Route::apiResource('order-products', OrderProductController::class);

Route::apiResource('addresses', AddressController::class);

Route::apiResource('reviews', ReviewController::class);
