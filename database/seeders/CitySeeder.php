<?php

namespace Database\Seeders;


use App\Models\Common\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create([
            'name' => 'California',
            'country_id' => 1,
            'longitude' => rand(-180, 180),
            'latitude' => rand(-90, 90)
        ]);

        City::create([
            'name' => 'Otawa',
            'country_id' => 2,
            'longitude' => rand(-180, 180),
            'latitude' => rand(-90, 90)
        ]);

        City::create([
            'name' => 'Cydney',
            'country_id' => 3,
            'longitude' => rand(-180, 180),
            'latitude' => rand(-90, 90)
        ]);
    }
}
