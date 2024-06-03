<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReservationVehicle>
 */
class ReservationVehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => fake()->numberBetween(1,50), 
            'driver_id' => fake()->numberBetween(1,50), 
            'vehicle_id' => fake()->numberBetween(1,50), 
        ];
    }
}
