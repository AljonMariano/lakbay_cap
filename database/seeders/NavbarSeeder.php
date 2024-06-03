<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Navigation;

class NavbarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = [
            [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'ordering' => 1,
            ],
            [
                'name' => 'Reservation',
                'route' => 'reservation',
                'ordering' => 2,
            ],
            [
                'name' => 'Events',
                'route' => 'events',
                'ordering' => 3,
            ],
            [
                'name' => 'Vehicles',
                'route' => 'vehicles',
                'ordering' => 4,
            ],
            [
                'name' => 'Drivers',
                'route' => 'drivers',
                'ordering' => 5,
            ],
            [
                'name' => 'Requestors',
                'route' => 'requestors',
                'ordering' => 6,
            ]
            
        ];

        foreach ($links as $key => $navbar) {
            Navigation::create($navbar);
        }
    }
}
