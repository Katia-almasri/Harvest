<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\General\Roles\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //1. Roles
//        $this->call(RoleSeeder::class);
//        //2. Admin
//        $this->call(AdminSeeder::class);

        $this->call(CountrySeeder::class);
        $this->call(CitySeeder::class);


    }
}
