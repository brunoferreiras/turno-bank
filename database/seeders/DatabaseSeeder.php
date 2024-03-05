<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\UserTypes;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Customer',
            'username' => 'customer',
            'email' => 'customer@turnobank.com',
            'password' => bcrypt('password'),
            'type' => UserTypes::CUSTOMER->value,
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@turnobank.com',
            'password' => bcrypt('password'),
            'type' => UserTypes::ADMIN->value,
        ]);
    }
}
