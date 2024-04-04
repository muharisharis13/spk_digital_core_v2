<?php

namespace App\Http\Controllers\API;

use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Spk;
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
        "dealer_neq_id"
    ];

    function isDealerRequired($validator)
    {
        return $validator->sometimes("dealer_id", 'required', function ($input) {
            return $input->spk_general_location == 'dealer';
        });
    }
    function isDealerNeqRequired($validator)
    {
        return $validator->sometimes("dealer_neq_id", 'required', function ($input) {
            return $input->spk_general_location == 'neq';
        });
    }

    function createSPKMaster($dealerSelected)
    {
        $createSPK = Spk::create([
            "spk_number"
            => GenerateNumber::generate("TEMP-REPAIR-RETURN", GenerateAlias::generate($dealerSelected->dealer->dealer_name), "repair_returns", "repair_return_number")
        ]);

        return $createSPK;
    }

    function createSPKGeneral()
    {
    }

    public function createSPK(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), self::validator);

            self::isDealerRequired($validator);
            self::isDealerNeqRequired($validator);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            DB::beginTransaction();

            // buat spk
            $createSPK = self::createSPKMaster($getDealerSelected);

            $data = [
                "spk" => $createSPK
            ];
            return ResponseFormatter::success($data, "Successfully created SPK !");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
