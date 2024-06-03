<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offices>
 */
class OfficesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'off_id' => fake()->unique()->randomNumber(4, true),
            'off_acr' => strtoupper(fake()->lexify('PGO-???')),
            'off_name' => fake()->company(),
            'off_head' => fake()->name(),
        ];
    }
}
