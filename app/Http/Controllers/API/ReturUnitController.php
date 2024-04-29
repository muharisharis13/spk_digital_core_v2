<?php

namespace App\Http\Controllers\API;

use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ReturUnit;
use App\Models\ReturUnitList;
use App\Models\ReturUnitLog;
use App\Models\Unit;
use App\Models\UnitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReturUnitController extends Controller
{
    //

    public function getDetailReturUnit(Request $request, $retur_unit_id)
    {
        try {
            $getDetailReturUnit = ReturUnit::where("retur_unit_id", $retur_unit_id)
                ->with(["dealer", "retur_unit_list", "retur_unit_log"])
                ->first();

            return ResponseFormatter::success($getDetailReturUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateReturUnit(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $retur_unit_status = $request->input("retur_unit_status");
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $getPaginateReturUnit = ReturUnit::latest()
                ->with(["dealer"])
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->where("retur_unit_status", "LIKE", "%$retur_unit_status%")
                ->paginate($limit);

            return ResponseFormatter::success($getPaginateReturUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createReturUnit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "main_dealer_name" => "required",
                "main_dealer_id" => "required",
                "dealer_type" => "required|in:mds,independent",
                "dealer_id" => "required",
                "retur_unit_reason" => "nullable",
                "units" => "array|required",
                "unit.*.unit_id" => "required"
            ]);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();


            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);

            $createReturUnit = ReturUnit::create([
                "retur_unit_status" => "create",
                "dealer_type" => $request->dealer_type,
                "dealer_id" => $request->dealer_id,
                "retur_unit_reason" => $request->retur_unit_reason,
                "main_dealer_name" => $request->main_dealer_name,
                "main_dealer_id" => $request->main_dealer_id,
                "retur_unit_number" => GenerateNumber::generate("RETUR-UNIT", GenerateAlias::generate($getDealer->dealer->dealer_name), "retur_units", "retur_unit_number")
            ]);

            $createReturUnitList = [];

            foreach ($request->units as $item) {
                $createReturUnitList[] = ReturUnitList::create([
                    "retur_unit_id" => $createReturUnit->retur_unit_id,
                    "unit_id" => $item["unit_id"],
                ]);

                $updateUnit = Unit::where("unit_id", $item["unit_id"])->first();

                $updateUnit->update([
                    "unit_status" => "hold"
                ]);
                UnitLog::create([
                    "unit_id" => $updateUnit->unit_id,
                    "user_id" => $user->user_id,
                    "unit_log_number" => GenerateNumber::generate("RETUR-UNIT", GenerateAlias::generate($getDealer->dealer->dealer_name), "unit_logs", "unit_log_number"),
                    "unit_log_action" => "update status to hold",
                    "unit_log_status" => "hold",

                ]);
            }

            //create log unit retur
            $createReturUnitLog = ReturUnitLog::create([
                "retur_unit_id" => $createReturUnit->retur_unit_id,
                "user_id" => $user->user_id,
                "retur_unit_log_action" => "create retur unit"
            ]);

            DB::commit();

            $data = [
                "retur_unit" => $createReturUnit,
                "retur_unit_list" => $createReturUnitList,
                "retur_unit_log" => $createReturUnitLog
            ];

            return ResponseFormatter::success($data, "Successfully create retur unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
