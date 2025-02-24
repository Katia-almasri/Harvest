<?php

namespace Database\Seeders;

use App\Models\Common\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::create([
            'name' => 'United States',
            'longitude' => -98.35,
            'latitude' => 39.50
        ]);

        Country::create([
            'name' => 'Canada',
            'longitude' => -106.3468,
            'latitude' => 56.1304
        ]);

        Country::create([
            'name' => 'Australia',
            'longitude' => 133.7751,
            'latitude' => -25.2744
        ]);
    }
}
