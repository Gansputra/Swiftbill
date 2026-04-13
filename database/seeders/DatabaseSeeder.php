<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin SwiftBill',
            'email' => 'admin@swiftbill.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Cashier SwiftBill',
            'email' => 'cashier@swiftbill.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);

        // Categories
        $food = \App\Models\Category::create(['name' => 'Food', 'slug' => 'food', 'description' => 'Solid food items']);
        $drink = \App\Models\Category::create(['name' => 'Beverage', 'slug' => 'beverage', 'description' => 'Liquid drink items']);

        // Suppliers
        $sup = \App\Models\Supplier::create([
            'name' => 'Global Distribution Inc.',
            'email' => 'contact@globaldist.com',
            'phone' => '08123456789',
            'address' => 'Business Center Block A'
        ]);

        // Products
        \App\Models\Product::create([
            'name' => 'Organic Coffee Beans',
            'sku' => 'CF-001',
            'category_id' => $drink->id,
            'supplier_id' => $sup->id,
            'buy_price' => 50000,
            'sell_price' => 85000,
            'stock' => 50,
            'min_stock' => 10,
        ]);

        \App\Models\Product::create([
            'name' => 'Chocolate Protein Bar',
            'sku' => 'SN-005',
            'category_id' => $food->id,
            'supplier_id' => $sup->id,
            'buy_price' => 12000,
            'sell_price' => 20000,
            'stock' => 100,
            'min_stock' => 20,
        ]);
    }
}
