<?php

namespace Database\Seeders;

use App\Models\Sales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Sales::create([
            "sales_name" => "Doni sales",
            "sales_nip" => "12111"
        ]);
        Sales::create([
            "sales_name" => "Muharis sales",
            "sales_nip" => "121112222"
        ]);
    }
}
