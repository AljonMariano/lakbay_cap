<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Drivers>
 */
class DriversFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // "driver_id" => fake()->unique()->randomNumber(4, true),
            "dr_emp_id" => fake()->unique()->randomNumber(8, true),
            "dr_fname" =>  fake()->firstName(),
            "dr_mname" => fake()->lastName(),
            "dr_lname" => fake()->lastName(),
            "off_id" => fake()->numberBetween(1,10),
            "dr_status" => fake()->randomElement(['On Travel', 'Busy','Idle']),
        ];
    }
} 
