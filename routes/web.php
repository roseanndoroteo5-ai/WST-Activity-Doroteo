<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/local-variable', [PostController::class, 'index']);
Route::get('/read', [PostController::class, 'read']);
Route::get('/greet/{name}', [PostController::class, 'greet']);
