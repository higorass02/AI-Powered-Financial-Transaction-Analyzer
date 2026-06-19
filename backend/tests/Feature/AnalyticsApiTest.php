<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_it_returns_dashboard_summary(): void
    {
        Transaction::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson('/api/dashboard/summary')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'balance' => ['total', 'currency'],
                    'current_month' => ['income', 'spending', 'net'],
                    'budget' => ['total', 'spent', 'percentage'],
                    'period',
                ],
            ]);
    }

    public function test_it_returns_spending_by_category(): void
    {
        Transaction::factory()->count(5)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson('/api/analytics/spending-by-category')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_it_returns_monthly_trend(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/analytics/monthly-trend')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }
}
