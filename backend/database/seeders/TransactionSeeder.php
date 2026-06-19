<?php

namespace Database\Seeders;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@financial-analyzer.com')->first();
        if (!$user) {
            return;
        }

        $categories = Category::all()->keyBy('slug');
        $months = 3;

        for ($m = $months; $m >= 0; $m--) {
            $date = Carbon::now()->subMonths($m);

            // Salary
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $categories['salary']->id,
                'description' => 'SALARIO EMPRESA XYZ',
                'amount' => 7500.00,
                'type' => TransactionType::CREDIT,
                'status' => TransactionStatus::APPROVED,
                'date' => $date->copy()->startOfMonth()->addDays(4),
            ]);

            // Food transactions
            $foods = [
                ['IFOOD RESTAURANTE', 45.90],
                ['SUPERMERCADO EXTRA', 320.50],
                ['PADARIA PONTO CHIC', 28.00],
                ['MC DONALDS', 52.80],
                ['RESTAURANTE ITALIANO', 98.50],
            ];
            foreach ($foods as [$desc, $amount]) {
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $categories['food']->id,
                    'description' => $desc,
                    'amount' => $amount,
                    'type' => TransactionType::DEBIT,
                    'status' => TransactionStatus::APPROVED,
                    'date' => $date->copy()->addDays(rand(1, 27)),
                ]);
            }

            // Transportation
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $categories['transportation']->id,
                'description' => 'UBER BRASIL',
                'amount' => 45.50,
                'type' => TransactionType::DEBIT,
                'status' => TransactionStatus::APPROVED,
                'date' => $date->copy()->addDays(rand(1, 27)),
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $categories['transportation']->id,
                'description' => 'POSTO IPIRANGA COMBUSTIVEL',
                'amount' => 180.00,
                'type' => TransactionType::DEBIT,
                'status' => TransactionStatus::APPROVED,
                'date' => $date->copy()->addDays(rand(1, 27)),
            ]);

            // Subscriptions
            $subs = [
                ['NETFLIX', 44.90],
                ['SPOTIFY', 21.90],
                ['AMAZON PRIME', 14.90],
            ];
            foreach ($subs as [$desc, $amount]) {
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $categories['subscription']->id,
                    'description' => $desc,
                    'amount' => $amount,
                    'type' => TransactionType::DEBIT,
                    'status' => TransactionStatus::APPROVED,
                    'date' => $date->copy()->startOfMonth()->addDays(rand(1, 5)),
                ]);
            }

            // Utilities
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $categories['utilities']->id,
                'description' => 'ENEL ENERGIA ELETRICA',
                'amount' => 280.00,
                'type' => TransactionType::DEBIT,
                'status' => TransactionStatus::APPROVED,
                'date' => $date->copy()->addDays(15),
            ]);
        }
    }
}
