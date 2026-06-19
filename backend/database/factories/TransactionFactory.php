<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'description' => fake()->company() . ' ' . fake()->word(),
            'amount' => fake()->randomFloat(2, 1, 5000),
            'type' => fake()->randomElement(TransactionType::cases())->value,
            'status' => TransactionStatus::APPROVED->value,
            'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }

    public function debit(): static
    {
        return $this->state(['type' => TransactionType::DEBIT->value]);
    }

    public function credit(): static
    {
        return $this->state(['type' => TransactionType::CREDIT->value]);
    }
}
