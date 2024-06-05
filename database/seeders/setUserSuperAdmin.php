<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class setUserSuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $get_user = User::first();

        $allPermission =  Permission::latest()->get();

        foreach ($allPermission as $item) {
            $get_user->givePermissionTo($item->name);
        }
    }
}
