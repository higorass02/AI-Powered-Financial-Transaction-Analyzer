<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// API Documentation (public)
Route::get('/docs', function () {
    return response()->json([
        'name'    => config('app.name') . ' API',
        'version' => '1.0.0',
        'base_url' => url('/api'),
        'authentication' => [
            'type'   => 'Bearer Token (Laravel Sanctum)',
            'header' => 'Authorization: Bearer {token}',
            'note'   => 'Obtain a token via POST /api/auth/login',
        ],
        'endpoints' => [
            'auth' => [
                ['method' => 'POST', 'path' => '/api/auth/register',   'auth' => false, 'description' => 'Register a new user',         'body' => ['name', 'email', 'password', 'password_confirmation']],
                ['method' => 'POST', 'path' => '/api/auth/login',      'auth' => false, 'description' => 'Login and get access token',   'body' => ['email', 'password']],
                ['method' => 'POST', 'path' => '/api/auth/logout',     'auth' => true,  'description' => 'Revoke current token'],
                ['method' => 'GET',  'path' => '/api/auth/me',         'auth' => true,  'description' => 'Get authenticated user info'],
            ],
            'transactions' => [
                ['method' => 'GET',    'path' => '/api/transactions',                    'auth' => true, 'description' => 'List transactions (paginated)', 'query' => ['category_id', 'type', 'from_date', 'to_date', 'sort_by', 'sort_order', 'search', 'per_page']],
                ['method' => 'POST',   'path' => '/api/transactions',                    'auth' => true, 'description' => 'Create a transaction',           'body' => ['description', 'amount', 'type', 'date', 'category_id', 'notes']],
                ['method' => 'GET',    'path' => '/api/transactions/{id}',               'auth' => true, 'description' => 'Get a single transaction'],
                ['method' => 'PUT',    'path' => '/api/transactions/{id}',               'auth' => true, 'description' => 'Update a transaction'],
                ['method' => 'DELETE', 'path' => '/api/transactions/{id}',               'auth' => true, 'description' => 'Delete a transaction'],
                ['method' => 'POST',   'path' => '/api/transactions/import',             'auth' => true, 'description' => 'Import transactions from CSV/OFX', 'body' => ['file (multipart)', 'auto_categorize']],
                ['method' => 'POST',   'path' => '/api/transactions/{id}/analyze',       'auth' => true, 'description' => 'AI analysis for a transaction',   'body' => ['scope (basic|detailed)']],
            ],
            'analytics' => [
                ['method' => 'GET', 'path' => '/api/dashboard/summary',              'auth' => true, 'description' => 'Dashboard summary (balance, income, spending, budgets)'],
                ['method' => 'GET', 'path' => '/api/analytics/spending-by-category', 'auth' => true, 'description' => 'Spending grouped by category', 'query' => ['from_date', 'to_date']],
                ['method' => 'GET', 'path' => '/api/analytics/monthly-trend',        'auth' => true, 'description' => 'Monthly income/expense trend',  'query' => ['months (1-24)', 'include_forecast']],
            ],
            'categories' => [
                ['method' => 'GET', 'path' => '/api/categories', 'auth' => true, 'description' => 'List all transaction categories'],
            ],
            'ai' => [
                ['method' => 'GET', 'path' => '/api/ai/insights',        'auth' => true, 'description' => 'AI-generated financial insights'],
                ['method' => 'GET', 'path' => '/api/ai/recommendations',  'auth' => true, 'description' => 'AI-generated saving recommendations'],
            ],
        ],
    ]);
});

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
