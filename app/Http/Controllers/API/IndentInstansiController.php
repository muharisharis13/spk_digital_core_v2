<?php

namespace App\Http\Controllers\API;

use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\IndentInstansi;
use App\Models\IndentInstansiLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IndentInstansiController extends Controller
{
    //

    public function createIndentInstansi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "sales_id" => "required",
                "salesman_name" => "required",
                "indent_instansi_number_po" => "required",
                "indent_instansi_po_date" => "required",
                "indent_instansi_name" => "required",
                "indent_instansi_address" => "required",
                "province_id" => "required",
                "province_name" => "required",
                "city_id" => "required",
                "city_name" => "required",
                "district_id" => "required",
                "district_name" => "required",
                "sub_district_id" => "required",
                "sub_district_name" => "required",
                "indent_instansi_postal_code" => "nullable",
                "indent_instansi_no_telp" => "nullable",
                "indent_instansi_no_hp" => "required",
                "indent_instansi_email" => "nullable",
                "motor_id" => "required",
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $dataLocal = $request->all();

            $dataLocal["indent_instansi_date"] = Carbon::now();
            $dataLocal["dealer_id"] = $getDealerSelected->dealer_id;
            $dataLocal["indent_instansi_number"] = GenerateNumber::generate("INDENT-INSTANSI", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "indent_instansis", "indent_instansi_number");

            $createIndentInstansi = IndentInstansi::create($dataLocal);

            //create instansi log
            $createIndentInstansiLog = IndentInstansiLog::create([
                "indent_instansi_id" => $createIndentInstansi->indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Create Indent Instansi"
            ]);

            // DB::commit();


            $data = [
                "indent_instansi" => $createIndentInstansi,
                "indent_instansi_log" => $createIndentInstansiLog
            ];

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
