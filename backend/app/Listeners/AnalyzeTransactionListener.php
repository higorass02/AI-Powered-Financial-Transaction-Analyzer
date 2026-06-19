<?php

namespace App\Listeners;

use App\Actions\AnalyzeTransactionAction;
use App\Events\AnomalyDetected;
use App\Events\TransactionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AnalyzeTransactionListener implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $timeout = 90;

    public function __construct(
        private AnalyzeTransactionAction $action,
    ) {}

    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        try {
            $analysis = $this->action->execute($transaction);

            if ($analysis->is_anomaly) {
                AnomalyDetected::dispatch($transaction, $analysis);
            }
        } catch (\Exception $e) {
            Log::error("Failed to analyze transaction {$transaction->id}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
