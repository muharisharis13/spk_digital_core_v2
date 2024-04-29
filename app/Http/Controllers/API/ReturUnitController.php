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

    public function confirmStatusReturUnit(Request $request, $retur_unit_id)
    {
        try {
            # code...
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteRetur(Request $request, $retur_unit_id)
    {
        try {
            DB::beginTransaction();
            $getDetailReturUnit = ReturUnit::where("retur_unit_id", $retur_unit_id)->first();


            if ($getDetailReturUnit->retur_unit_status === "create") {
                ReturUnitList::where("retur_unit_id", $retur_unit_id)->delete();
                $getDetailReturUnit->delete();
            } else {
                DB::rollBack();
                return ResponseFormatter::error("Tidak dapat melakukan hapus retur unit selain status create", "Bad Request", 400);
            }

            DB::commit();

            return ResponseFormatter::success("Berhasil hapus retur unit", "Successfully delete retur unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteReturUnitList(Request $request, $retur_unit_list_id)
    {
        try {
            $getDetailReturUnitList = ReturUnitList::where("retur_unit_list_id", $retur_unit_list_id)->with(["retur_unit"])->first();
            $user = Auth::user();
            DB::beginTransaction();

            //kembalikan status ke semula on_hand di unitnya
            Unit::where("unit_id", $getDetailReturUnitList->unit_id)->first()->update([
                "unit_status" => "on_hand"
            ]);

            UnitLog::create([
                "unit_id" => $getDetailReturUnitList->unit_id,
                "user_id" => $user->user_id,
                "unit_log_number" => $getDetailReturUnitList->retur_unit->retur_unit_number,
                "unit_log_action" => "update status to on_hand",
                "unit_log_status" => "on_hand",

            ]);


            //delete unit yang ada di detail retur
            $getDetailReturUnitList->delete();

            //create log unit retur
            $createReturUnitLog = ReturUnitLog::create([
                "retur_unit_id" => $getDetailReturUnitList->retur_unit->retur_unit_id,
                "user_id" => $user->user_id,
                "retur_unit_log_action" => "delete retur unit list $retur_unit_list_id"
            ]);

            DB::commit();

            $data = [
                "retur_unit_list" => $getDetailReturUnitList,
                "retur_unit_log" => $createReturUnitLog
            ];

            return ResponseFormatter::success($data, "Successfully deleted retur unit list");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function editReturUNit(Request $request, $retur_unit_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "main_dealer_name" => "required",
                "main_dealer_id" => "required",
                "dealer_type" => "required|in:mds,independent",
                "dealer_id" => "required",
                "retur_unit_reason" => "nullable",
                "units" => "array|required",
                "unit.*.unit_id" => "required",
                "unit.*.retur_unit_list_id" => "nullable",
            ]);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailReturUnit = ReturUnit::where("retur_unit_id", $retur_unit_id)->first();

            $getDetailReturUnit->update([
                "dealer_type" => $request->dealer_type,
                "dealer_id" => $request->dealer_id,
                "retur_unit_reason" => $request->retur_unit_reason,
                "main_dealer_name" => $request->main_dealer_name,
                "main_dealer_id" => $request->main_dealer_id,
            ]);

            $user = Auth::user();
            // $getDealer = GetDealerByUserSelected::GetUser($user->user_id);


            $createReturUnitList = [];

            //update jika ada unit_id baru yang mau di retur

            foreach ($request->units as $item) {
                if (!isset($item["retur_unit_list_id"])) {

                    $createReturUnitList[] = ReturUnitList::create([
                        "retur_unit_id" => $retur_unit_id,
                        "unit_id" => $item["unit_id"],
                    ]);

                    $updateUnit = Unit::where("unit_id", $item["unit_id"])->first();

                    $updateUnit->update([
                        "unit_status" => "hold"
                    ]);
                    UnitLog::create([
                        "unit_id" => $updateUnit->unit_id,
                        "user_id" => $user->user_id,
                        "unit_log_number" => $getDetailReturUnit->retur_unit_number,
                        "unit_log_action" => "update status to hold",
                        "unit_log_status" => "hold",

                    ]);
                }
            }

            //create log unit retur
            $createReturUnitLog = ReturUnitLog::create([
                "retur_unit_id" => $retur_unit_id,
                "user_id" => $user->user_id,
                "retur_unit_log_action" => "update retur unit"
            ]);

            DB::commit();

            $data = [
                "retur_unit" => $getDetailReturUnit,
                "retur_unit_list" => $createReturUnitList,
                "retur_unit_log" => $createReturUnitLog
            ];

            return ResponseFormatter::success($data, "Successfully edited retur unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

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
                    "unit_log_number" => $createReturUnit->retur_unit_id,
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