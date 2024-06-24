<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'super-admin']);
    }
}
