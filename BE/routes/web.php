<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
});
