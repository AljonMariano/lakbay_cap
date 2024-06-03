<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requestors>
 */
class RequestorsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'requestors_id' => fake()->randomNumber(),
            'rq_full_name' => fake()->name(),
            'rq_office' => fake()->company(),
        ];
    }
}
