<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(50)->create();
        \App\Models\Offices::factory(50)->create();
        \App\Models\Drivers::factory(50)->create();
        \App\Models\Vehicles::factory(50)->create();
        \App\Models\Events::factory(50)->create();
        \App\Models\Requestors::factory(50)->create();
        \App\Models\Reservations::factory(50)->create();
        \App\Models\ReservationVehicle::factory(50)->create();

        // Create a specific user for testing
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
