<?php

namespace App\Services;

use App\Exceptions\AIAnalysisFailedException;
use App\Models\AIAnalysis;
use App\Models\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIAnalysisService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('claude.base_url'),
            'timeout' => config('claude.timeout'),
            'headers' => [
                'x-api-key' => config('claude.api_key'),
                'anthropic-version' => config('claude.api_version'),
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function analyzeTransaction(Transaction $transaction): AIAnalysis
    {
        $cacheKey = config('claude.cache.prefix') . $transaction->id;

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return AIAnalysis::updateOrCreate(
                ['transaction_id' => $transaction->id],
                $cached
            );
        }

        $result = $this->callClaude($this->buildCategorizationPrompt($transaction));

        $data = [
            'transaction_id' => $transaction->id,
            'category_suggested' => $result['category'] ?? 'Other',
            'confidence' => $result['confidence'] ?? 0,
            'reasoning' => $result['reasoning'] ?? null,
            'needs_review' => ($result['confidence'] ?? 0) < 0.7,
            'prompt_tokens' => $result['usage']['input_tokens'] ?? null,
            'completion_tokens' => $result['usage']['output_tokens'] ?? null,
            'model_used' => config('claude.model'),
        ];

        Cache::put($cacheKey, $data, config('claude.cache.ttl'));

        return AIAnalysis::updateOrCreate(
            ['transaction_id' => $transaction->id],
            $data
        );
    }

    public function detectAnomalies(int $userId, array $recentTransactions): array
    {
        if (empty($recentTransactions)) {
            return [];
        }

        $prompt = $this->buildAnomalyPrompt($recentTransactions);
        $result = $this->callClaude($prompt);

        return $result['anomalies'] ?? [];
    }

    public function generateBudgetRecommendations(int $userId, array $stats): array
    {
        $prompt = $this->buildBudgetPrompt($stats);
        $result = $this->callClaude($prompt);

        return $result['recommendations'] ?? [];
    }

    private function buildCategorizationPrompt(Transaction $transaction): string
    {
        $categories = [
            'Food', 'Transportation', 'Entertainment', 'Utilities',
            'Healthcare', 'Shopping', 'Subscription', 'Transfer', 'Salary', 'Investment', 'Other',
        ];
        $categoriesList = implode(', ', $categories);

        return "You are a financial transaction categorizer. Analyze this transaction and respond ONLY with valid JSON.

Transaction:
- Description: {$transaction->description}
- Amount: R$ {$transaction->amount}
- Type: {$transaction->type->value}
- Date: {$transaction->date}

Respond with this exact JSON structure:
{
  \"category\": \"one of: {$categoriesList}\",
  \"confidence\": 0.95,
  \"reasoning\": \"brief explanation\"
}";
    }

    private function buildAnomalyPrompt(array $transactions): string
    {
        $txJson = json_encode($transactions, JSON_PRETTY_PRINT);

        return "You are a financial anomaly detector. Analyze these transactions and identify anomalies.
Respond ONLY with valid JSON.

Transactions:
{$txJson}

Respond with:
{
  \"anomalies\": [
    {
      \"transaction_id\": 1,
      \"reason\": \"explanation\",
      \"severity\": \"low|medium|high\",
      \"confidence\": 0.9
    }
  ]
}";
    }

    private function buildBudgetPrompt(array $stats): string
    {
        $statsJson = json_encode($stats, JSON_PRETTY_PRINT);

        return "You are a personal finance advisor. Based on spending data, provide budget recommendations.
Respond ONLY with valid JSON.

Spending data:
{$statsJson}

Respond with:
{
  \"recommendations\": [
    {
      \"category\": \"Food\",
      \"action\": \"description\",
      \"current_cost\": 1200.00,
      \"potential_savings\": 300.00,
      \"reasoning\": \"explanation\"
    }
  ]
}";
    }

    private function callClaude(string $prompt): array
    {
        $retries = config('claude.retry.times', 3);
        $lastException = null;

        for ($i = 0; $i < $retries; $i++) {
            try {
                $response = $this->client->post('/messages', [
                    'json' => [
                        'model' => config('claude.model'),
                        'max_tokens' => config('claude.max_tokens'),
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                    ],
                ]);

                $body = json_decode($response->getBody()->getContents(), true);
                $content = $body['content'][0]['text'] ?? '{}';

                // Extract JSON from response (handle markdown code blocks)
                if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                    $content = $matches[1];
                }

                $parsed = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new AIAnalysisFailedException("Invalid JSON response from Claude: {$content}");
                }

                $parsed['usage'] = $body['usage'] ?? [];

                return $parsed;

            } catch (GuzzleException $e) {
                $lastException = $e;
                Log::warning("Claude API attempt {$i} failed: " . $e->getMessage());

                if ($i < $retries - 1) {
                    usleep(config('claude.retry.sleep', 1000) * 1000 * ($i + 1));
                }
            }
        }

        throw new AIAnalysisFailedException(
            "Claude API failed after {$retries} attempts: " . $lastException?->getMessage()
        );
    }
}
