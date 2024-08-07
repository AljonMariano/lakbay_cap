<?php

namespace Database\Factories;
use App\Models\Events;
use App\Models\Driver;
use App\Models\Drivers;
use App\Models\Requestors;
use App\Models\Vehicles;
use App\Models\Offices;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservations>
 */
class ReservationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rs_purpose' => fake()->randomNumber(),
            'rs_passengers' => fake()->numberBetween(1,20),
            'rs_travel_type'=> fake()->randomElement(['Daily Travel', 'Outside Province']),
            'rs_approval_status' => fake()->randomElement(['Pending', 'Approved', 'Rejected']),
            'rs_status' => fake()->randomElement(['Active', 'Inactive']),
            'destination_activity' => fake()->numberBetween(1,50), 
            'requestor_id' => fake()->numberBetween(1,10), 
            'off_id' => Offices::factory(), 
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
            
        ];
    }
}