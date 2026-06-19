<?php

namespace App\Events;

use App\Models\AIAnalysis;
use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnomalyDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Transaction $transaction,
        public readonly AIAnalysis $analysis,
    ) {}
}
