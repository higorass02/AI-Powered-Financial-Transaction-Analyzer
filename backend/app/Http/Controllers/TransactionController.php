<?php

namespace App\Http\Controllers;

use App\Actions\AnalyzeTransactionAction;
use App\Actions\ImportTransactionsAction;
use App\Exceptions\InvalidTransactionException;
use App\Http\Requests\AnalyzeRequest;
use App\Http\Requests\ImportTransactionsRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private ImportTransactionsAction $importAction,
        private AnalyzeTransactionAction $analyzeAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'category_id', 'type', 'from_date', 'to_date',
            'sort_by', 'sort_order', 'search',
        ]);

        $perPage = (int) $request->get('per_page', 20);
        $transactions = $this->transactionService->list($request->user()->id, $filters, $perPage);

        return response()->json($transactions);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->create(
                $request->user()->id,
                $request->validated()
            );

            return response()->json(['data' => $transaction], 201);
        } catch (InvalidTransactionException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Transaction $transaction, Request $request): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $transaction->load(['category', 'aiAnalysis']);
        return response()->json(['data' => $transaction]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $updated = $this->transactionService->update($transaction, $request->validated());
        return response()->json(['data' => $updated]);
    }

    public function destroy(Transaction $transaction, Request $request): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $this->transactionService->delete($transaction);
        return response()->json(null, 204);
    }

    public function import(ImportTransactionsRequest $request): JsonResponse
    {
        $result = $this->importAction->execute(
            $request->user()->id,
            $request->file('file'),
            $request->boolean('auto_categorize', true)
        );

        return response()->json(['data' => $result], 201);
    }

    public function analyze(AnalyzeRequest $request, Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $scope = $request->get('scope', 'basic');
        $analysis = $this->analyzeAction->execute($transaction, $scope);

        return response()->json([
            'data' => [
                'transaction_id' => $transaction->id,
                'analysis' => $analysis,
            ],
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = $this->transactionService->categories();
        return response()->json(['data' => $categories]);
    }
}
