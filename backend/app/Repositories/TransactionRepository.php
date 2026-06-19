<?php

namespace App\Repositories;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TransactionRepository
{
    public function paginate(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Transaction::forUser($userId)
            ->with(['category', 'aiAnalysis'])
            ->orderBy($filters['sort_by'] ?? 'date', $filters['sort_order'] ?? 'desc');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('date', '<=', $filters['to_date']);
        }

        if (!empty($filters['search'])) {
            $query->where('description', 'ilike', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::with(['category', 'aiAnalysis', 'user'])->find($id);
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);
        return $transaction->fresh(['category', 'aiAnalysis']);
    }

    public function softDelete(Transaction $transaction): void
    {
        $transaction->delete();
    }

    public function findLastMonth(int $userId): Collection
    {
        return Transaction::forUser($userId)
            ->where('date', '>=', Carbon::now()->startOfMonth())
            ->with('category')
            ->get();
    }

    public function findByPeriod(int $userId, string $from, string $to): Collection
    {
        return Transaction::forUser($userId)
            ->inPeriod($from, $to)
            ->with('category')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getMonthlyStats(int $userId, int $months = 12): Collection
    {
        $cacheKey = "user_{$userId}_monthly_stats_{$months}";

        return Cache::remember($cacheKey, 3600, function () use ($userId, $months) {
            return Transaction::forUser($userId)
                ->where('date', '>=', Carbon::now()->subMonths($months)->startOfMonth())
                ->selectRaw("
                    TO_CHAR(date, 'YYYY-MM') as month,
                    SUM(CASE WHEN type IN ('credit') THEN amount ELSE 0 END) as income,
                    SUM(CASE WHEN type IN ('debit') THEN amount ELSE 0 END) as spending,
                    COUNT(*) as transactions_count
                ")
                ->groupByRaw("TO_CHAR(date, 'YYYY-MM')")
                ->orderByRaw("TO_CHAR(date, 'YYYY-MM') ASC")
                ->get();
        });
    }

    public function getSpendingByCategory(int $userId, string $from, string $to): Collection
    {
        return Transaction::forUser($userId)
            ->inPeriod($from, $to)
            ->where('type', TransactionType::DEBIT)
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->get();
    }

    public function invalidateUserCache(int $userId): void
    {
        Cache::forget("user_{$userId}_monthly_stats_12");
        Cache::forget("user_{$userId}_monthly_stats_6");
        Cache::forget("user_{$userId}_dashboard_summary");
    }
}
