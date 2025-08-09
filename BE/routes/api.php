<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Authentication Routes
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
    
    // User Profile Routes
    Route::get('/user/profile', [App\Http\Controllers\API\UserController::class, 'getProfile']);
    Route::put('/user/profile', [App\Http\Controllers\API\UserController::class, 'updateProfile']);
    Route::get('/user/dashboard', [App\Http\Controllers\API\UserController::class, 'getDashboardStats']);
    
    // AI Routes
    Route::post('/ai/process', [App\Http\Controllers\API\AiController::class, 'process']);
    Route::post('/ai/survey', [App\Http\Controllers\API\AiController::class, 'processSurvey']);
    Route::get('/consultation/history', [App\Http\Controllers\API\AiController::class, 'getConsultationHistory']);
    Route::get('/consultation/{id}', [App\Http\Controllers\API\AiController::class, 'getConsultationResult']);
    
    // Admin Routes (có thể thêm middleware admin sau)
    Route::get('/admin/statistics', [App\Http\Controllers\API\AdminController::class, 'getConsultationStatistics']);
    Route::get('/admin/consultations', [App\Http\Controllers\API\AdminController::class, 'getConsultationList']);
    Route::post('/admin/training-data', [App\Http\Controllers\API\AdminController::class, 'updateTrainingData']);
    Route::get('/admin/export-consultations', [App\Http\Controllers\API\AdminController::class, 'exportConsultations']);
});
