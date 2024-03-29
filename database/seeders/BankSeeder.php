<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Bank::create([
            "bank_name" => "Bank BCA",
            "bank_number" => "11111",
            "bank_name_account" => "PT alfa scorpii",
            "dealer_id" => "9ba3ab4f-ef4d-47b9-ad35-245d8b190bce"
        ]);
    }
}
