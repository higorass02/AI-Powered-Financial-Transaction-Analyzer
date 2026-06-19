<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Food', 'slug' => 'food', 'icon' => 'utensils', 'color' => '#FF6B6B'],
            ['name' => 'Transportation', 'slug' => 'transportation', 'icon' => 'car', 'color' => '#4ECDC4'],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'icon' => 'film', 'color' => '#45B7D1'],
            ['name' => 'Utilities', 'slug' => 'utilities', 'icon' => 'zap', 'color' => '#96CEB4'],
            ['name' => 'Healthcare', 'slug' => 'healthcare', 'icon' => 'heart', 'color' => '#FFEAA7'],
            ['name' => 'Shopping', 'slug' => 'shopping', 'icon' => 'shopping-bag', 'color' => '#DDA0DD'],
            ['name' => 'Subscription', 'slug' => 'subscription', 'icon' => 'repeat', 'color' => '#98D8C8'],
            ['name' => 'Transfer', 'slug' => 'transfer', 'icon' => 'arrow-right-left', 'color' => '#B0C4DE'],
            ['name' => 'Salary', 'slug' => 'salary', 'icon' => 'banknote', 'color' => '#90EE90'],
            ['name' => 'Investment', 'slug' => 'investment', 'icon' => 'trending-up', 'color' => '#FFD700'],
            ['name' => 'Other', 'slug' => 'other', 'icon' => 'more-horizontal', 'color' => '#D3D3D3'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
