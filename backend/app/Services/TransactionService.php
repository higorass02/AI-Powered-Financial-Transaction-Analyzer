<?php

namespace App\Services;

use App\Events\TransactionCreated;
use App\Exceptions\InvalidTransactionException;
use App\Models\Transaction;
use App\Repositories\CategoryRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TransactionService
{
    public function __construct(
        private TransactionRepository $repository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function list(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $perPage = min($perPage, 100);
        return $this->repository->paginate($userId, $filters, $perPage);
    }

    public function create(int $userId, array $data): Transaction
    {
        $this->validateBusinessRules($data);

        $transaction = $this->repository->create(array_merge($data, ['user_id' => $userId]));

        TransactionCreated::dispatch($transaction);

        return $transaction->load(['category', 'aiAnalysis']);
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $updated = $this->repository->update($transaction, $data);

        // Invalidate AI analysis cache when transaction is edited
        if (isset($data['description']) || isset($data['amount'])) {
            $cacheKey = config('claude.cache.prefix') . $transaction->id;
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        $this->repository->invalidateUserCache($transaction->user_id);

        return $updated;
    }

    public function delete(Transaction $transaction): void
    {
        $this->repository->softDelete($transaction);
        $this->repository->invalidateUserCache($transaction->user_id);
    }

    public function categories(): Collection
    {
        return $this->categoryRepository->all();
    }

    private function validateBusinessRules(array $data): void
    {
        if (isset($data['amount']) && $data['amount'] > 100000) {
            throw new InvalidTransactionException('Amount exceeds the maximum limit of R$ 100,000.');
        }

        if (isset($data['date'])) {
            $date = \Carbon\Carbon::parse($data['date']);
            if ($date->isFuture()) {
                throw new InvalidTransactionException('Transaction date cannot be in the future.');
            }
            if ($date->lt(\Carbon\Carbon::now()->subYears(10))) {
                throw new InvalidTransactionException('Transaction date is too far in the past.');
            }
        }
    }
}
