<?php

namespace Database\Seeders;

use App\Models\Leasing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeasingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Leasing::create([
            "leasing_name" => "Adira Leasing"
        ]);
    }
}
