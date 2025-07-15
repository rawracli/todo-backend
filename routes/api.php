<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\SubTaskController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PlanController;
use Illuminate\Support\Facades\Route;

// Route::prefix('v1')->group(function () {
//     Route::prefix('auth')->group(function () {
//         Route::post('/register', [AuthController::class, 'register']);
//         Route::post('/login', [AuthController::class, 'login']);
//         Route::get('/oauth/google', [AuthController::class, 'oAuthUrl']);
//         Route::get('/oauth/google/callback', [AuthController::class, 'oAuthCallback']);
//     });
// });

Route::prefix('v1')->group(function () {
    // Rute Otentikasi
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/oauth/google', [AuthController::class, 'oAuthUrl']);
        Route::get('/oauth/google/callback', [AuthController::class, 'oAuthCallback']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // Public plans
    Route::get('plans', [PlanController::class, 'index']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Tasks
        Route::apiResource('tasks', TaskController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::post('tasks/{id}', [TaskController::class, 'update']);
        Route::post('/subtasks/change-status', [SubtaskController::class, 'changeStatus']);
        Route::apiResource('subtasks', SubtaskController::class)->only(['index', 'store', 'destroy']);
        Route::post('subtasks', [SubtaskController::class, 'store']); // CREATE
        Route::post('subtasks/{id}', [SubtaskController::class, 'update']); // UPDATE
        Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::apiResource('payments', PaymentController::class)->only(['index', 'store', 'show']);
    });
    Route::post('/payments/callback', [PaymentController::class, 'callback']);
});
