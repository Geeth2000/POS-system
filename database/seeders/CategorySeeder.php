<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Groceries',
            'Beverages',
            'Household',
            'Personal Care',
            'Snacks',
            'Dairy & Eggs',
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category]);
        }
    }
}
