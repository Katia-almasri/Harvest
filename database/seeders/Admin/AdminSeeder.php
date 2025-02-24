<?php

namespace Database\Seeders\Admin;

use App\Enums\General\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = User::factory()->count(2)->create();

        foreach ($admins as $_admin) {
            $_admin->assignRole(RoleType::ADMIN);
        }
    }
}
