<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/transactions')->assertStatus(401);
    }

    public function test_it_lists_transactions_paginated(): void
    {
        Transaction::factory()->count(5)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson('/api/transactions')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_it_creates_transaction(): void
    {
        $payload = [
            'description' => 'UBER BRASIL',
            'amount' => 45.50,
            'type' => 'debit',
            'date' => now()->toDateString(),
        ];

        $this->actingAs($this->user)
            ->postJson('/api/transactions', $payload)
            ->assertCreated()
            ->assertJsonFragment(['description' => 'UBER BRASIL']);
    }

    public function test_it_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/transactions', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['description', 'amount', 'type', 'date']);
    }

    public function test_it_shows_single_transaction(): void
    {
        $transaction = Transaction::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->getJson("/api/transactions/{$transaction->id}")
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'description', 'amount', 'type', 'date']]);
    }

    public function test_it_cannot_view_other_users_transaction(): void
    {
        $otherUser = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->user)
            ->getJson("/api/transactions/{$transaction->id}")
            ->assertStatus(403);
    }

    public function test_it_updates_transaction(): void
    {
        $transaction = Transaction::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->putJson("/api/transactions/{$transaction->id}", ['description' => 'Updated Description'])
            ->assertOk()
            ->assertJsonFragment(['description' => 'Updated Description']);
    }

    public function test_it_soft_deletes_transaction(): void
    {
        $transaction = Transaction::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/transactions/{$transaction->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('transactions', ['id' => $transaction->id]);
    }
}
