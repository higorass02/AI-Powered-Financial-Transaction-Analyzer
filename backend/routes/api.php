<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Auth routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Transactions
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/', [TransactionController::class, 'store']);
        Route::post('/import', [TransactionController::class, 'import']);
        Route::get('/{transaction}', [TransactionController::class, 'show']);
        Route::put('/{transaction}', [TransactionController::class, 'update']);
        Route::delete('/{transaction}', [TransactionController::class, 'destroy']);
        Route::post('/{transaction}/analyze', [TransactionController::class, 'analyze']);
    });

    // Analytics
    Route::prefix('analytics')->group(function () {
        Route::get('/spending-by-category', [AnalyticsController::class, 'spendingByCategory']);
        Route::get('/monthly-trend', [AnalyticsController::class, 'monthlyTrend']);
    });

    // Dashboard
    Route::get('/dashboard/summary', [AnalyticsController::class, 'dashboardSummary']);

    // Categories
    Route::get('/categories', [TransactionController::class, 'categories']);

    // AI
    Route::prefix('ai')->group(function () {
        Route::get('/insights', [AIController::class, 'insights']);
        Route::get('/recommendations', [AIController::class, 'recommendations']);
    });
});
