<?php

namespace Database\Seeders;

use App\Helpers\PermmissionList;
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

        $permissions = PermmissionList::AllPermission();

        // Looping untuk membuat permission
        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName, 'guard_name' => 'api']);
        }
    }
}
