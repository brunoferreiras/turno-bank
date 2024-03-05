<?php

namespace Database\Factories;

use App\Enums\DepositStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deposit>
 */
class DepositFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->numberBetween(0, 1000),
            'image' => fake()->imageUrl(),
            'status' => fake()->randomElement([DepositStatus::PENDING, DepositStatus::ACCEPTED, DepositStatus::REJECTED]),
        ];
    }
}
