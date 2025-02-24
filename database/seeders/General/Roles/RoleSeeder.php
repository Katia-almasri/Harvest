<?php

namespace Database\Seeders\General\Roles;

use App\Enums\General\RoleType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => RoleType::ADMIN,
        ]);

        Role::create([
            'name' => RoleType::CUSTOMER,
        ]);
    }
}
