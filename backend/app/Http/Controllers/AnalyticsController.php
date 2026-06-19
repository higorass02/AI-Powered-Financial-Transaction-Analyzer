<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function dashboardSummary(Request $request): JsonResponse
    {
        $summary = $this->analyticsService->dashboardSummary($request->user()->id);
        return response()->json(['data' => $summary]);
    }

    public function spendingByCategory(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $now = Carbon::now();
        $from = $request->get('from_date', $now->copy()->startOfMonth()->toDateString());
        $to = $request->get('to_date', $now->copy()->endOfMonth()->toDateString());

        $data = $this->analyticsService->spendingByCategory($request->user()->id, $from, $to);
        return response()->json(['data' => $data]);
    }

    public function monthlyTrend(Request $request): JsonResponse
    {
        $request->validate([
            'months' => ['nullable', 'integer', 'min:1', 'max:24'],
            'include_forecast' => ['nullable', 'boolean'],
        ]);

        $months = (int) $request->get('months', 12);
        $forecast = $request->boolean('include_forecast', false);

        $data = $this->analyticsService->monthlyTrend($request->user()->id, $months, $forecast);
        return response()->json($data);
    }
}
