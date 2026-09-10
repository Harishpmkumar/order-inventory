<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([

            [
                'name' => 'Milk 1L',
                'code' => 'MILK001',
                'price' => 65.00,
                'tax_percentage' => 5,
                'stock_on_hand' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Bread',
                'code' => 'BREAD001',
                'price' => 40.00,
                'tax_percentage' => 5,
                'stock_on_hand' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Cooking Oil 1L',
                'code' => 'OIL001',
                'price' => 180.00,
                'tax_percentage' => 12,
                'stock_on_hand' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Rice 5Kg',
                'code' => 'RICE001',
                'price' => 420.00,
                'tax_percentage' => 5,
                'stock_on_hand' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sugar 1Kg',
                'code' => 'SUGAR001',
                'price' => 55.00,
                'tax_percentage' => 5,
                'stock_on_hand' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Tea Powder',
                'code' => 'TEA001',
                'price' => 150.00,
                'tax_percentage' => 12,
                'stock_on_hand' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Coffee Powder',
                'code' => 'COFFEE001',
                'price' => 220.00,
                'tax_percentage' => 12,
                'stock_on_hand' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Soap',
                'code' => 'SOAP001',
                'price' => 38.00,
                'tax_percentage' => 18,
                'stock_on_hand' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Toothpaste',
                'code' => 'TOOTH001',
                'price' => 95.00,
                'tax_percentage' => 18,
                'stock_on_hand' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Shampoo',
                'code' => 'SHAM001',
                'price' => 240.00,
                'tax_percentage' => 18,
                'stock_on_hand' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Biscuits',
                'code' => 'BISC001',
                'price' => 30.00,
                'tax_percentage' => 12,
                'stock_on_hand' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Soft Drink 750ml',
                'code' => 'DRINK001',
                'price' => 95.00,
                'tax_percentage' => 18,
                'stock_on_hand' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
