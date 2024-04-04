<?php

namespace Database\Seeders;

use App\Models\Broker;
use App\Models\Education;
use App\Models\Hobby;
use App\Models\Martial;
use App\Models\MotorBrand;
use App\Models\PricelistMotor;
use App\Models\Residence;
use App\Models\Tenor;
use App\Models\Work;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpkMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //9ba3ab4f-ef4d-47b9-ad35-245d8b190bce =>dealer yang terselect

        Broker::create([
            "broker_name" => "Broker Doni",
            "dealer_id" => "9ba3ab4f-ef4d-47b9-ad35-245d8b190bce"
        ]);

        Education::create([
            "education_name" => "Sarjana 1",
        ]);

        Hobby::create([
            "hobbies_name" => "basket"
        ]);

        Martial::create([
            "martial_name" => "Lajang"
        ]);
        Martial::create([
            "martial_name" => "Menikah"
        ]);

        MotorBrand::create([
            "motor_brand_name" => "Honda"
        ]);
        PricelistMotor::create([
            "motor_id" => "f9bb84a7-fd7e-4818-822e-bac0839ee6f5",
            "off_the_road" => 25000000,
            "bbn" => 500000,
            "commission" => 0,
            "pricelist_location_type" => "dealer",
            "dealer_id" => "9ba3ab4f-ef4d-47b9-ad35-245d8b190bce"
        ]);
        PricelistMotor::create([
            "motor_id" => "f9bb84a7-fd7e-4818-822e-bac0839ee6f5",
            "off_the_road" => 25000000,
            "bbn" => 500000,
            "commission" => 0,
            "pricelist_location_type" => "dealer",
            "dealer_id" => "9ba3ab4f-ef4d-47b9-ad35-245d8b190bce",
            "dealer_neq_id" => "a8727582-7a5e-482d-b40c-1bff0d9fb98c"
        ]);

        Tenor::create([
            "dealer_id" => "9ba3ab4f-ef4d-47b9-ad35-245d8b190bce",
            "tenor_amount_total" => 12
        ]);
        Tenor::create([
            "dealer_id" => "9ba3ab4f-ef4d-47b9-ad35-245d8b190bce",
            "tenor_amount_total" => 24
        ]);
        Tenor::create([
            "dealer_id" => "9ba3ab4f-ef4d-47b9-ad35-245d8b190bce",
            "tenor_amount_total" => 36
        ]);

        Work::create([
            "work_name" => "Karyawan Swasta",

        ]);

        Residence::create([
            "residence_name" => "Rumah Sendiri"
        ]);
    }
}
