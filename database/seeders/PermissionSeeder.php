<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $permissions = [
            "read_unit",
            "read_unit_detail",
            "update_unit_status"
        ];

        // Looping untuk membuat permission
        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName, 'guard_name' => 'api']);
        }
    }
}
