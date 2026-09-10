<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::insert([
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Robert Wilson',
                'email' => 'robert@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
