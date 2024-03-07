<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\UserTypes;
use App\Models\Deposit;
use App\Models\Purchase;
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
        Deposit::factory()->count(100)->create([
            'account_id' => 1
        ]);
        Purchase::factory()->count(50)->create([
            'account_id' => 1
        ]);
        $this->call(AdminUserSeeder::class);
    }
}
