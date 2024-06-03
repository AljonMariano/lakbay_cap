<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the appli`cation's database.
     */
    public function run(): void

    {   
        \App\Models\User::factory(50)->create();
        \App\Models\Offices::factory(50)->create();
        \App\Models\Drivers::factory(50)->create();
        \App\Models\Vehicles::factory(50)->create();
        \App\Models\Events::factory(50)->create();
        \App\Models\Requestors::factory(50)->create();
        \App\Models\Reservations::factory(50)->create();
        \App\Models\ReservationVehicle::factory(50)->create();
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
