<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTransactionCreated implements ShouldQueue
{
    public function handle(TransactionCreated $event): void
    {
        AuditLog::create([
            'user_id' => $event->transaction->user_id,
            'action' => 'transaction.created',
            'auditable_type' => 'transaction',
            'auditable_id' => $event->transaction->id,
            'new_values' => $event->transaction->toArray(),
        ]);
    }
}
