<?php

namespace App\Listeners;

use App\Events\AnomalyDetected;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyAnomalyDetected implements ShouldQueue
{
    public function handle(AnomalyDetected $event): void
    {
        Log::warning("Anomaly detected for transaction {$event->transaction->id}", [
            'user_id' => $event->transaction->user_id,
            'description' => $event->transaction->description,
            'amount' => $event->transaction->amount,
            'reason' => $event->analysis->anomaly_reason,
            'level' => $event->analysis->anomaly_level?->value,
        ]);

        AuditLog::create([
            'user_id' => $event->transaction->user_id,
            'action' => 'anomaly.detected',
            'auditable_type' => 'transaction',
            'auditable_id' => $event->transaction->id,
            'new_values' => [
                'anomaly_reason' => $event->analysis->anomaly_reason,
                'anomaly_level' => $event->analysis->anomaly_level?->value,
                'confidence' => $event->analysis->confidence,
            ],
        ]);
    }
}
