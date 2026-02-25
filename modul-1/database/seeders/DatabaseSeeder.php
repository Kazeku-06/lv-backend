<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $electronic = ProductCategory::firstOrCreate(
            ['name' => 'Electronics'],
            ['description' => 'Electronic devices and accessories']
        );

        $clothing = ProductCategory::firstOrCreate(
            ['name' => 'Clothing'],
            ['description' => 'Apparel and accessories']
        );

        Product::firstOrCreate(
            ['name' => 'Smartphone', 'category_id' => $electronic->id],
            ['price' => 5000000]
        );

        Product::firstOrCreate(
            ['name' => 'Laptop', 'category_id' => $electronic->id],
            ['price' => 12000000]
        );

        Product::firstOrCreate(
            ['name' => 'T-Shirt', 'category_id' => $clothing->id],
            ['price' => 150000]
        );

        Product::firstOrCreate(
            ['name' => 'Jeans', 'category_id' => $clothing->id],
            ['price' => 300000]
        );
    }
}
