<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Events>
 */
class EventsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        $dateStart = $faker->dateTimeBetween('2024-01-01', '2024-12-31');
        $dateEnd = $faker->dateTimeBetween($dateStart, '2024-12-31');
        $timeStart = $dateStart->format('H:i:s');
        $timeEnd = $dateEnd->format('H:i:s');

        return [
            'ev_name' => $faker->word(),
            'ev_venue' => $faker->address(),
            'ev_date_start' => $dateStart->format('Y-m-d'),
            'ev_time_start' => $timeStart,
            'ev_date_end' => $dateEnd->format('Y-m-d'),
            'ev_time_end' => $timeEnd,
            'ev_date_added' => $faker->date(),
            'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
