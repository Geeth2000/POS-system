<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $categories = Category::all();

        if ($categories->count() == 0) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $sampleProducts = [
            ['name' => 'Fresh Milk 1L', 'category' => 'Dairy & Eggs'],
            ['name' => 'White Bread', 'category' => 'Groceries'],
            ['name' => 'Basmati Rice 5kg', 'category' => 'Groceries'],
            ['name' => 'Dishwashing Liquid', 'category' => 'Household'],
            ['name' => 'Shampoo 400ml', 'category' => 'Personal Care'],
            ['name' => 'Coca Cola 1.5L', 'category' => 'Beverages'],
            ['name' => 'Potato Chips', 'category' => 'Snacks'],
            ['name' => 'Cheddar Cheese', 'category' => 'Dairy & Eggs'],
            ['name' => 'Bath Soap', 'category' => 'Personal Care'],
            ['name' => 'Laundry Detergent', 'category' => 'Household'],
            ['name' => 'Apple Juice', 'category' => 'Beverages'],
            ['name' => 'Cooking Oil 2L', 'category' => 'Groceries'],
        ];

        foreach ($sampleProducts as $p) {
            $category = $categories->where('name', $p['category'])->first() ?? $categories->random();
            
            $price = $faker->randomFloat(2, 50, 1500);
            $cost = $price * 0.8; // Assume 20% margin for cost
            $qty = $faker->numberBetween(5, 100);
            $sku = $faker->unique()->ean13();

            Product::create([
                'name'        => $p['name'],
                'category_id' => $category->id,
                'price'       => $price,
                'cost'        => $cost,
                'quantity'    => $qty,    // Legacy field
                'stock_qty'   => $qty,    // New field
                'sku'         => $sku,    // Legacy field
                'item_code'   => $sku,    // New field
                'barcode'     => $sku,    // New field
                'is_active'   => true,
                'description' => $faker->sentence(),
            ]);
        }
    }
}
