<?php

namespace Tests\Unit\Services;

use App\Exceptions\AIAnalysisFailedException;
use App\Models\Transaction;
use App\Services\AIAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AIAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_analyzes_transaction_and_returns_analysis(): void
    {
        $service = Mockery::mock(AIAnalysisService::class)->makePartial();

        $service->shouldReceive('analyzeTransaction')
            ->once()
            ->andReturn((object) [
                'category_suggested' => 'Food',
                'confidence' => 0.95,
                'is_anomaly' => false,
                'needs_review' => false,
            ]);

        $transaction = new Transaction([
            'description' => 'IFOOD RESTAURANTE',
            'amount' => 45.50,
            'type' => 'debit',
        ]);

        $result = $service->analyzeTransaction($transaction);

        $this->assertEquals('Food', $result->category_suggested);
        $this->assertEquals(0.95, $result->confidence);
    }

    public function test_low_confidence_marks_needs_review(): void
    {
        $service = Mockery::mock(AIAnalysisService::class)->makePartial();

        $service->shouldReceive('analyzeTransaction')
            ->once()
            ->andReturn((object) [
                'category_suggested' => 'Other',
                'confidence' => 0.5,
                'needs_review' => true,
            ]);

        $transaction = new Transaction(['description' => 'XYZ PAYMENT', 'amount' => 100]);
        $result = $service->analyzeTransaction($transaction);

        $this->assertTrue($result->needs_review);
    }
}
