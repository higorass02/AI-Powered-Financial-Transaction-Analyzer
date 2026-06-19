<?php

namespace App\Actions;

use App\Models\AIAnalysis;
use App\Models\Transaction;
use App\Repositories\CategoryRepository;
use App\Services\AIAnalysisService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyzeTransactionAction
{
    public function __construct(
        private AIAnalysisService $aiService,
        private CategoryRepository $categoryRepository,
    ) {}

    public function execute(Transaction $transaction, string $scope = 'basic'): AIAnalysis
    {
        return DB::transaction(function () use ($transaction, $scope) {
            $analysis = $this->aiService->analyzeTransaction($transaction);

            if ($analysis->category_suggested && $analysis->confidence >= 0.7) {
                $category = $this->categoryRepository->findBySlug(
                    strtolower(str_replace(' ', '-', $analysis->category_suggested))
                );

                if ($category && !$transaction->category_id) {
                    $transaction->update(['category_id' => $category->id]);
                }
            }

            Log::info("Transaction {$transaction->id} analyzed. Category: {$analysis->category_suggested}, Confidence: {$analysis->confidence}");

            return $analysis;
        });
    }
}
