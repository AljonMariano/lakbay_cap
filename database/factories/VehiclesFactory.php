<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicles>
 */
class VehiclesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // "vehicle_id" => fake()->unique()->randomNumber(5, true),
        "vh_type" => fake()->unique()->randomNumber(5, true),
        "vh_capacity"=>fake()->numberBetween(1,20),
        "vh_plate" => fake()->name(),
        "vh_brand" => fake()->name(),
        "vh_year" => 2020,
        "vh_fuel_type" => fake()->randomElement(['Gasoline', 'Diesel','Electric']),
        "vh_condition"  => fake()->randomElement(['Perfect', 'Good','Bad']),
        "vh_status" => fake()->randomElement(['Available', 'Not Available','For Maintenance']),
        ];
    }
}
