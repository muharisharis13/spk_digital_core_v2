<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\SubDistrict;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProvinceMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $getProvince = Http::get('https://api.binderbyte.com/wilayah/provinsi?api_key=7a2b1fd83f501ba6e776b74499f448a155c9eee5268aa82584baea671db240ea');

        $getProvince = $getProvince->json();

        foreach ($getProvince["value"] as $item) {
            $createProvince = Province::create([
                "province_name" => $item["name"],
            ]);

            $getCity = Http::get('https://api.binderbyte.com/wilayah/kabupaten?api_key=7a2b1fd83f501ba6e776b74499f448a155c9eee5268aa82584baea671db240ea&id_provinsi=' . $item["id"]);

            $getCity = $getCity->json();

            foreach ($getCity["value"] as $itemCity) {
                $createCity = City::create([
                    "city_name" => $itemCity["name"],
                    "province_id" => $createProvince->province_id
                ]);

                $getDistrict = Http::get('https://api.binderbyte.com/wilayah/kecamatan?api_key=7a2b1fd83f501ba6e776b74499f448a155c9eee5268aa82584baea671db240ea&id_kabupaten=' . $itemCity["id"]);

                $getDistrict = $getDistrict->json();

                foreach ($getDistrict["value"] as $itemDistrict) {
                    $createDistrict =  District::create([
                        "district_name" => $itemDistrict["name"],
                        "city_id" => $createCity->city_id
                    ]);

                    $getSubDistrict = Http::get('https://api.binderbyte.com/wilayah/kelurahan?api_key=8e49f28e0f2f2cf56393c352613eec358e85fb7077ce6f7f453ebb826a7b1f6d&id_kecamatan=' . $itemDistrict["id"]);

                    $getSubDistrict = $getSubDistrict->json();

                    foreach ($getSubDistrict["value"]  as $itemSubDistrict) {
                        SubDistrict::create([
                            "sub_district_name" => $itemSubDistrict["name"],
                            "district_id" => $createDistrict->district_id
                        ]);
                    }
                }
            }
        }
    }
}
