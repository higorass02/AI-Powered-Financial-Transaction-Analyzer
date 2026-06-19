<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    public function __construct(
        private TransactionRepository $repository,
    ) {}

    public function dashboardSummary(int $userId): array
    {
        $cacheKey = "user_{$userId}_dashboard_summary";

        return Cache::remember($cacheKey, 3600, function () use ($userId) {
            $now = Carbon::now();
            $from = $now->copy()->startOfMonth()->toDateString();
            $to = $now->copy()->endOfMonth()->toDateString();

            $transactions = $this->repository->findByPeriod($userId, $from, $to);

            $income = $transactions
                ->where('type', TransactionType::CREDIT)
                ->sum('amount');

            $spending = $transactions
                ->where('type', TransactionType::DEBIT)
                ->sum('amount');

            $allTransactions = Transaction::forUser($userId)->get();
            $totalBalance = $allTransactions->where('type', TransactionType::CREDIT)->sum('amount')
                - $allTransactions->where('type', TransactionType::DEBIT)->sum('amount');

            return [
                'balance' => [
                    'total' => max(0, round($totalBalance, 2)),
                    'currency' => 'BRL',
                ],
                'current_month' => [
                    'income' => round($income, 2),
                    'spending' => round($spending, 2),
                    'net' => round($income - $spending, 2),
                ],
                'budget' => [
                    'total' => round($income, 2),
                    'spent' => round($spending, 2),
                    'percentage' => $income > 0 ? round(($spending / $income) * 100) : 0,
                ],
                'period' => $now->format('Y-m'),
            ];
        });
    }

    public function spendingByCategory(int $userId, string $from, string $to): array
    {
        $data = $this->repository->getSpendingByCategory($userId, $from, $to);
        $totalSpending = $data->sum('total');

        return $data->map(function ($item) use ($totalSpending) {
            return [
                'category_id' => $item->category_id,
                'category_name' => $item->category?->name ?? 'Unknown',
                'amount' => round($item->total, 2),
                'percentage' => $totalSpending > 0 ? round(($item->total / $totalSpending) * 100) : 0,
                'transactions_count' => $item->count,
                'average_transaction' => $item->count > 0 ? round($item->total / $item->count, 2) : 0,
            ];
        })->values()->toArray();
    }

    public function monthlyTrend(int $userId, int $months = 12, bool $includeForecast = false): array
    {
        $data = $this->repository->getMonthlyStats($userId, $months);

        $trend = $data->map(fn ($item) => [
            'month' => $item->month,
            'income' => round($item->income, 2),
            'spending' => round($item->spending, 2),
            'net' => round($item->income - $item->spending, 2),
            'transactions_count' => $item->transactions_count,
        ])->values()->toArray();

        $result = ['data' => $trend];

        if ($includeForecast && count($trend) >= 3) {
            $recentSpending = array_slice(array_column($trend, 'spending'), -3);
            $forecast = array_sum($recentSpending) / count($recentSpending);
            $nextMonth = Carbon::now()->addMonth()->format('Y-m');

            $result['forecast'] = [
                ['month' => $nextMonth, 'forecast_spending' => round($forecast, 2)],
            ];
        }

        return $result;
    }
}
