<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DeleteAllUserPermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus semua permissions dan roles dari users
        User::all()->each(function ($user) {
            $user->syncPermissions([]);
        });

        // Hapus semua permissions dan roles
        Permission::query()->delete();
    }
}
