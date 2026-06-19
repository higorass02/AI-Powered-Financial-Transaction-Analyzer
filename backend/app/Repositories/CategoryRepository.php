<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryRepository
{
    public function all(): Collection
    {
        return Cache::remember('categories_all', 604800, fn () => Category::orderBy('name')->get());
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }
}
