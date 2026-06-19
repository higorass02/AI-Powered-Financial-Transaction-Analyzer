<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepository;
use App\Services\AIAnalysisService;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(
        private AIAnalysisService $aiService,
        private AnalyticsService $analyticsService,
        private TransactionRepository $transactionRepository,
    ) {}

    public function insights(Request $request): JsonResponse
    {
        $request->validate([
            'period' => ['nullable', 'in:day,week,month'],
            'include' => ['nullable', 'string'],
        ]);

        $period = $request->get('period', 'month');
        $now = Carbon::now();

        $from = match($period) {
            'day' => $now->copy()->startOfDay()->toDateString(),
            'week' => $now->copy()->startOfWeek()->toDateString(),
            default => $now->copy()->startOfMonth()->toDateString(),
        };

        $transactions = $this->transactionRepository
            ->findByPeriod($request->user()->id, $from, $now->toDateString())
            ->toArray();

        $anomalies = $this->aiService->detectAnomalies($request->user()->id, $transactions);
        $stats = $this->analyticsService->spendingByCategory(
            $request->user()->id,
            $from,
            $now->toDateString()
        );

        $recommendations = $this->aiService->generateBudgetRecommendations(
            $request->user()->id,
            $stats
        );

        return response()->json([
            'data' => [
                'anomalies' => $anomalies,
                'recommendations' => $recommendations,
            ],
        ]);
    }

    public function recommendations(Request $request): JsonResponse
    {
        $request->validate([
            'period' => ['nullable', 'in:month,quarter,year'],
            'depth' => ['nullable', 'in:basic,detailed'],
        ]);

        $now = Carbon::now();
        $stats = $this->analyticsService->spendingByCategory(
            $request->user()->id,
            $now->copy()->startOfMonth()->toDateString(),
            $now->toDateString()
        );

        $recommendations = $this->aiService->generateBudgetRecommendations(
            $request->user()->id,
            $stats
        );

        return response()->json(['data' => ['recommendations' => $recommendations]]);
    }
}
