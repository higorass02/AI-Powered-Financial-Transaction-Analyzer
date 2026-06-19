<?php

namespace Tests\Unit\Services;

use App\Exceptions\InvalidTransactionException;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->user = User::factory()->create();
        $this->service = app(TransactionService::class);
    }

    public function test_it_creates_transaction(): void
    {
        $data = [
            'description' => 'SUPERMERCADO EXTRA',
            'amount' => 150.00,
            'type' => 'debit',
            'date' => now()->toDateString(),
        ];

        $transaction = $this->service->create($this->user->id, $data);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals('SUPERMERCADO EXTRA', $transaction->description);
        $this->assertEquals(150.00, $transaction->amount);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_it_rejects_future_date(): void
    {
        $this->expectException(InvalidTransactionException::class);

        $this->service->create($this->user->id, [
            'description' => 'TEST',
            'amount' => 10.00,
            'type' => 'debit',
            'date' => now()->addDays(5)->toDateString(),
        ]);
    }

    public function test_it_rejects_amount_over_limit(): void
    {
        $this->expectException(InvalidTransactionException::class);

        $this->service->create($this->user->id, [
            'description' => 'HUGE PURCHASE',
            'amount' => 200000.00,
            'type' => 'debit',
            'date' => now()->toDateString(),
        ]);
    }

    public function test_it_soft_deletes_transaction(): void
    {
        $transaction = Transaction::factory()->create(['user_id' => $this->user->id]);

        $this->service->delete($transaction);

        $this->assertSoftDeleted('transactions', ['id' => $transaction->id]);
    }
}
