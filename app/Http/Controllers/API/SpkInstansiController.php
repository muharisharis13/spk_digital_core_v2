<?php

namespace App\Http\Controllers\API;

use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\SpkInstansi;
use App\Models\SpkInstansiDelivery;
use App\Models\SpkInstansiDeliveryFile;
use App\Models\SpkInstansiGeneral;
use App\Models\SpkInstansiLegal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpkInstansiController extends Controller
{
    //

    public function getPaginate(Request $request)
    {
        try {
            $getPaginate = SpkInstansi::latest()
                ->paginate(5);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    const validator = [
        "sales_id" => "required",
        "sales_name" => "required",
        "indent_instansi_id" => "nullable",
        "po_number" => "required",
        "po_date" => "required",
        "instansi_name" => "required",
        "instansi_address" => "required",
        "province" => "required",
        "city" => "required",
        "district" => "required",
        "sub_district" => "required",
        "postal_code" => "required",
        "no_telp" => "nullable",
        "no_hp" => "required",
        "email" => "nullable",
        "instansi_name_legal" => "required",
        "instansi_address_legal" => "required",
        "province_legal" => "required",
        "city_legal" => "required",
        "district_legal" => "required",
        "sub_district_legal" => "required",
        "postal_code_legal" => "required",
        "no_telp_legal" => "nullable",
        "no_hp_legal" => "required",
        "delivery_type" => "required|in:ktp,dealer,neq,domicile",

    ];


    function isSelectedDeliveryTypeKTP($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_address", "delivery_city", "delivery_no_hp",
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "ktp";
            }
        )->sometimes(["delivery_no_telp"], "nullable", function ($input) {
            return $input->delivery_type === "ktp";
        });
    }
    function isSelectedDeliveryTypeDealer($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_no_hp",
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "dealer";
            }
        );
    }
    function isSelectedDeliveryTypeNeq($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_no_hp", "dealer_neq_id"
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "neq";
            }
        );
    }
    function isSelectedDeliveryTypeDomicile($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_address", "city",
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "domicile";
            }
        )->sometimes(
            ["file_sk.*"],
            "array|nullable|mimes:pdf,jpg,png,pdf|max:5120",
            function ($input) {
                return $input->delivery_type === 'domicile';
            }
        );
    }


    protected function createSpkMaster($dealerSelected)
    {


        $result = SpkInstansi::create([
            "spk_instansi_number" => GenerateNumber::generate("SPK-INSTANSI", GenerateAlias::generate($dealerSelected->dealer->dealer_name), "spk_instansis", "spk_instansi_number"),
            "dealer_id" => $dealerSelected->dealer_id,
            "spk_instansi_status" => "create",
        ]);

        return $result;
    }

    protected function createSpkGeneral($createSpk, $request)
    {


        $result = SpkInstansiGeneral::create([
            "spk_instansi_id" => $createSpk->spk_instansi_id,
            "sales_name" => $request->sales_name,
            "sales_id" => $request->sales_id,
            "po_number" => $request->po_number,
            "po_date" => $request->po_date,
            "instansi_name" => $request->instansi_name,
            "instansi_address" => $request->instansi_address,
            "province" => $request->province,
            "province_id" => $request->province_id,
            "city" => $request->city,
            "city_id" => $request->city_id,
            "district" => $request->district,
            "district_id" => $request->district_id,
            "sub_district" => $request->sub_district,
            "sub_district_id" => $request->sub_district_id,
            "postal_code" => $request->postal_code,
            "no_telp" => $request->no_telp,
            "no_hp" => $request->no_hp,
            "email" => $request->email,
        ]);

        return $result;
    }

    protected function createSpkLegal($createSpk, $request)
    {

        return SpkInstansiLegal::create([
            "spk_instansi_id" => $createSpk->spk_instansi_id,
            "instansi_name" => $request->instansi_name_legal,
            "instansi_address" => $request->instansi_address_legal,
            "province" => $request->province_legal,
            "province_id" => $request->province_id_legal,
            "city" => $request->city_legal,
            "city_id" => $request->city_id_legal,
            "district" => $request->district_legal,
            "district_id" => $request->district_id_legal,
            "sub_district" => $request->sub_district_legal,
            "sub_district_id" => $request->sub_district_id_legal,
            "postal_code" => $request->postal_code_legal,
            "no_telp" => $request->no_telp_legal,
            "no_hp" => $request->no_hp_legal,
        ]);
    }

    protected function createSpkDelivery($createSpk, $request)
    {
        $dataDelivery = [
            "spk_instansi_id" => $createSpk->spk_instansi_id,
            "delivery_type" => $request->delivery_type,
        ];

        if ($request->delivery_type === "ktp") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["address"] = $request->delivery_address;
            $dataDelivery["city"] = $request->delivery_city;
            $dataDelivery["no_telp"] = $request->delivery_no_telp;
            $dataDelivery["no_hp"] = $request->delivery_no_hp;
        }
        if ($request->delivery_type === "dealer") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["no_hp"] = $request->delivery_no_hp;
        }
        if ($request->delivery_type === "neq") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["no_hp"] = $request->delivery_no_hp;
            $dataDelivery["dealer_neq_id"] = $request->dealer_neq_id;
        }

        if ($request->delivery_type === "domicile") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["address"] = $request->delivery_address;
            $dataDelivery["city"] = $request->delivery_city;
            $dataDelivery["is_domicile"] = true;
        }

        $result = SpkInstansiDelivery::create($dataDelivery);

        return $result;
    }
    protected function createSpkDeliveryFile($createSpkDelivery, $request)
    {
        $createSpkDeliveryFile = [];

        if ($request->file_sk) {
            foreach ($request->file("file_sk") as $item) {
                $imagePath = $item->store("spk_instansi", "public");

                $createSpkDeliveryFile[] = SpkInstansiDeliveryFile::create([
                    "spk_instansi_delivery_id" => $createSpkDelivery->spk_instansi_delivery_id,
                    "files" => $imagePath
                ]);
            }
        }

        return $createSpkDeliveryFile;
    }


    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), self::validator);

            self::isSelectedDeliveryTypeKTP($validator);
            self::isSelectedDeliveryTypeDealer($validator);
            self::isSelectedDeliveryTypeNeq($validator);
            self::isSelectedDeliveryTypeDomicile($validator);

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            DB::beginTransaction();

            $createSpk = self::createSpkMaster($getDealerSelected);
            $createSpkGeneral = self::createSpkGeneral($createSpk, $request);
            $createSpkLegal = self::createSpkLegal($createSpk, $request);
            $createSpkDelivery = self::createSpkDelivery($createSpk, $request);
            $createSpkDeliveryFile = null;
            if ($request->delivery_type === "domicile") {
                $createSpkDeliveryFile = self::createSpkDeliveryFile($createSpkDelivery, $request);
            }

            $data = [
                "spk_instansi" => $createSpk,
                "spk_instansi_general" => $createSpkGeneral,
                "spk_instansi_legal" => $createSpkLegal,
                "spk_instansi_delivery" => $createSpkDelivery,
                "spk_instansi_delivery_file" => $createSpkDeliveryFile,
            ];

            DB::commit();

            return ResponseFormatter::success($data, "Successfully created SPK !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }
}
