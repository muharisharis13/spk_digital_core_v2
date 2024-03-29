<?php

namespace Database\Seeders;

use App\Models\MicroFinance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MicroFinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        MicroFinance::create([
            "micro_finance_name" => "Bank BCA Micro Finance"
        ]);
    }
}
