<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AssignAllRolesToUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $user = User::where("username", "arhakim")->first();

        $permission  = Permission::all();

        // Mengassign semua role ke user
        foreach ($permission as $item) {
            $user->givePermissionTo($item->name);
        }
    }
}
