<?php

namespace Database\Seeders;

use App\Helpers\PermmissionList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            Permission::firstOrCreate(['name' => $permissionName["name"], 'guard_name' => 'api', "alias_name" => $permissionName["alias_name"], "group_name" => $permissionName["group_name"]]);
        }
    }
}
