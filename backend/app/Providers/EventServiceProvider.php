<?php

namespace App\Providers;

use App\Events\AnomalyDetected;
use App\Events\TransactionCreated;
use App\Listeners\AnalyzeTransactionListener;
use App\Listeners\LogTransactionCreated;
use App\Listeners\NotifyAnomalyDetected;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TransactionCreated::class => [
            LogTransactionCreated::class,
            AnalyzeTransactionListener::class,
        ],
        AnomalyDetected::class => [
            NotifyAnomalyDetected::class,
        ],
    ];
}
