<?php

namespace App\Http\Controllers\API;

use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Spk;
use App\Models\SpkCustomer;
use App\Models\SpkGeneral;
use App\Models\SpkLog;
use App\Models\SpkTransaction;
use App\Models\SpkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SPKController extends Controller
{
    //

    const validator = [
        "spk_general_location" => "required|in:dealer,neq",
        "indent_id" => "nullable",
        "spk_general_indent_date" => "nullable",
        "sales_id" => "required",
        "spk_general_method_sales" => "required",
        "dealer_id" => "nullable",
        "dealer_neq_id" => "nullable",
        "motor_id" => "required",
        "color_id" => "required",
        "spk_transaction_method_buying" => "required|in:on_the_road,off_the_road",
        "spk_transaction_method_payment" => "required|in:cash,credit",
        "leasing_id" => 'nullable',
        "spk_transaction_down_payment" => "nullable",
        "spk_transaction_tenor" => "nullable",
        "spk_transaction_instalment" => "nullable",
        "spk_transaction_surveyor_name" => "required",
        "micro_finance_id" => "nullable",
        //spk customer
        "spk_customer_nik" => "required",
        "spk_customer_name" => "required",
        "spk_customer_address" => "required",
        "province" => "required",
        "city" => "required",
        "district" => "required",
        "sub_district" => "required",
        "spk_customer_postal_code" => "nullable",
        "spk_customer_birth_place" => "required",
        "spk_customer_birth_date" => "required",
        "spk_customer_gender" => "required|in:man|women",
        "spk_customer_telp" => "nullable",
        "spk_customer_no_wa" => "nullable",
        "spk_customer_no_phone" => "required",
        "spk_customer_religion" => "required",
        "martial_id" => "required",
        "hobbies_id" => "nullable",
        "spk_customer_mother_name" => "nullable",
        "spk_customer_npwp" => 'nullable',
        "spk_customer_email" => "nullable",
        "residence_id" => "required",
        "education_id" => "required",
        "work_id" => "required",
        "spk_customer_length_of_work" => "nullable",
        "spk_customer_income" => "required",
        "spk_customer_outcome" => "required",
        "motor_brand_id" => "nullable",
        "spk_customer_motor_type_before" => "nullable",
        "spk_customer_motor_year_before" => "nullable",
    ];

    function isDealerRequired($validator)
    {
        return $validator->sometimes("dealer_id", 'required', function ($input) {
            return $input->spk_general_location == 'dealer';
        });
    }
    function isDealerNeqRequired($validator)
    {
        return $validator->sometimes(["dealer_neq_id", "dealer_id"], 'required', function ($input) {
            return $input->spk_general_location == 'neq';
        });
    }

    function spk_transaction_method_payment_credit($validator)
    {
        return $validator->sometimes(["leasing_id", "spk_transaction_down_payment", "spk_transaction_tenor", "spk_transaction_instalment"], "required", function ($input) {
            return $input->spk_transaction_method_buying == 'credit';
        });
    }

    function spk_transaction_method_payment_cash($validator)
    {
        return
            $validator->sometimes(["micro_finance_id"], 'required', function ($input) {
                return $input->spk_transaction_method_buying == 'cash';
            });
    }

    function createSPKMaster($dealerSelected)
    {
        return Spk::create([
            "spk_number"
            => GenerateNumber::generate("SPK", GenerateAlias::generate($dealerSelected->dealer->dealer_name), "spks", "spk_number"),
            "dealer_id" => $dealerSelected->dealer_id,
            "spk_status" => "create"
        ]);
    }

    function createSPKGeneral($createSPK, $request)
    {
        return SpkGeneral::create([
            "spk_id" => $createSPK->spk_id,
            "indent_id" => $request->indent_id,
            "spk_general_indent_date" => $request->spk_general_indent_date,
            "spk_general_location" => $request->spk_general_location,
            "sales_id" => $request->sales_id,
            "spk_general_method_sales" => $request->spk_general_method_sales,
            "dealer_id" => $request->dealer_id,
            "dealer_neq_id" => $request->dealer_neq_id
        ]);
    }

    function createSPKUnit($createSPK, $request)
    {
        return SpkUnit::create([
            "motor_id" => $request->motor_id,
            "spk_id" => $createSPK->spk_id,
            "color_id" => $request->color_id
        ]);
    }

    function createSpkLog($createSPK, $user, $action)
    {
        return SpkLog::create([
            "spk_log_action" => $action,
            "user_id" => $user->user_id,
            "spk_id" => $createSPK->spk_id
        ]);
    }

    function createSpkTransaction($createSPK, $request)
    {

        if ($request->spk_transaction_method_payment == "cash") {
            return
                SpkTransaction::create([
                    "spk_id" => $createSPK->spk_id,
                    "spk_transaction_method_buying" => $request->spk_transaction_method_buying,
                    "spk_transaction_method_payment" => $request->spk_transaction_method_payment,
                    "spk_transaction_surveyor_name" => $request->spk_transaction_surveyor_name,
                    "micro_finance_id" => $request->micro_finance_id
                ]);
        } else {
            return SpkTransaction::create([
                "spk_id" => $createSPK->spk_id,
                "spk_transaction_method_buying" => $request->spk_transaction_method_buying,
                "spk_transaction_method_payment" => $request->spk_transaction_method_payment,
                "leasing_id" => $request->leasing_id,
                "spk_transaction_down_payment" => $request->spk_transaction_down_payment,
                "spk_transaction_tenor" => $request->spk_transaction_tenor,
                "spk_transaction_instalment" =>  $request->spk_transaction_instalment,
                "spk_transaction_surveyor_name" => $request->spk_transaction_surveyor_name,
                "micro_finance_id" => $request->micro_finance_id
            ]);
        }
    }

    function createSpkCustomer($createSPK, $request)
    {
        SpkCustomer::create([]);
    }

    public function createSPK(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), self::validator);

            self::isDealerRequired($validator);
            self::isDealerNeqRequired($validator);
            self::spk_transaction_method_payment_credit($validator);
            self::spk_transaction_method_payment_cash($validator);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            DB::beginTransaction();

            // buat spk
            $createSPK = self::createSPKMaster($getDealerSelected);

            //buat spk general
            $createSPKGeneral = self::createSPKGeneral($createSPK, $request);

            //buat spk unit
            $createSPKUnit = self::createSPKUnit($createSPK, $request);

            //buat spk transaction
            $createSPKTransaction = self::createSpkTransaction($createSPK, $request);

            //buat spk log
            $createSPKUnit = self::createSpkLog($createSPK, $user, "Create Spk");

            $data = [
                "spk" => $createSPK,
                "spk_general" => $createSPKGeneral,
                "spk_unit" => $createSPKUnit,
                "spk_transaction" => $createSPKTransaction
            ];

            return ResponseFormatter::success($data, "Successfully created SPK !");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
