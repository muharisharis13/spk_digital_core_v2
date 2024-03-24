<?php

namespace Database\Seeders;

use App\Models\Dealer;
use App\Models\DealerByUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserByDealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //


        $getDealer = Dealer::where("dealer_name", "PT. ALFA SCORPII - AR. HAKIM")->first();
        $getUser = User::first();

        DealerByUser::create([
            "dealer_id" => $getDealer->dealer_id,
            "user_id" => $getUser->user_id,
            "isSelected" => true
        ]);
    }
}
