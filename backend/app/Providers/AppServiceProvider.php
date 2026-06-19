<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Services\AIAnalysisService;
use App\Services\AnalyticsService;
use App\Services\AuthService;
use App\Services\TransactionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TransactionRepository::class);
        $this->app->bind(CategoryRepository::class);
        $this->app->bind(UserRepository::class);
        $this->app->bind(AIAnalysisService::class);
        $this->app->bind(TransactionService::class);
        $this->app->bind(AnalyticsService::class);
        $this->app->bind(AuthService::class);
    }

    public function boot(): void
    {
        //
    }
}
