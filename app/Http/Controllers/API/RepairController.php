<?php

namespace App\Http\Controllers\API;

use App\Enums\RepairLogEnum;
use App\Enums\RepairStatusEnum;
use App\Enums\UnitLogActionEnum;
use App\Enums\UnitLogStatusEnum;
use App\Enums\UnitStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Repair;
use App\Models\RepairLog;
use App\Models\RepairUnitList;
use App\Models\Unit;
use App\Models\UnitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RepairController extends Controller
{
    //

    public function updateStatusRepair(Request $request, $repair_id)
    {
        try {

            $validator = Validator::make($request->all(), [
                "repair_status" => "required|in:approve,cancel"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();

            DB::beginTransaction();

            $updateRepairStatus = Repair::where("repair_id", $repair_id)->first();

            // create log
            RepairLog::create([
                "user_id" => $user->user_id,
                "repair_log_action" => $request->repair_status,
                "repair_log_note" => $request->reason,
                "repair_id" => $updateRepairStatus->repair_id
            ]);


            if ($request->repair_status === "cancel") {
                $getListRepairUnit = RepairUnitList::where("repair_id", $repair_id)->get();

                foreach ($getListRepairUnit as $itemRepairUnit) {
                    Unit::where("unit_id", $itemRepairUnit->unit_id)->update([
                        "unit_status" => UnitStatusEnum::on_hand
                    ]);
                }
            }

            if ($request->repair_status === "approve") {
                $getListRepairUnit = RepairUnitList::where("repair_id", $repair_id)->get();
                foreach ($getListRepairUnit as $itemRepairUnit) {
                    Unit::where("unit_id", $itemRepairUnit->unit_id)->update([
                        "unit_status" => UnitStatusEnum::repair
                    ]);
                }
            }


            // update status
            $updateRepairStatus->update([
                'repair_status' => $request->repair_status,
                'repair_number' => str_replace('TEMP-', "", $updateRepairStatus->repair_number)
            ]);

            DB::commit();

            return ResponseFormatter::success($updateRepairStatus, "Successfully Updated !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteRepairUnit(Request $request, $repair_unit_id)
    {
        try {
            DB::beginTransaction();
            $deteleteRepairUnit = RepairUnitList::where("repair_unit_list_id", $repair_unit_id)
                ->with(["repair"])
                ->first();

            $user = Auth::user();


            Unit::where("unit_id", $deteleteRepairUnit["unit_id"])->update([
                "unit_status" => UnitStatusEnum::on_hand
            ]);
            UnitLog::create([
                "unit_id" => $deteleteRepairUnit["unit_id"],
                "user_id" => $user->user_id,
                "unit_log_number" => $deteleteRepairUnit->repair->repair_number,
                "unit_log_action" => UnitLogActionEnum::repair,
                "unit_log_status" => UnitLogStatusEnum::ON_HAND
            ]);

            $deteleteRepairUnit->delete();
            DB::commit();

            return ResponseFormatter::success($deteleteRepairUnit);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteRepair(Request $request, $repair_id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $deleteRepair = Repair::where("repair_id", $repair_id)
                ->where("repair_status", RepairStatusEnum::create)
                ->with(["dealer", "repair_unit.unit", "repair_unit.unit.motor", "repair_log.user"])
                ->first();


            foreach ($deleteRepair->repair_unit as $item) {
                Unit::where("unit_id", $item["unit_id"])->update([
                    "unit_status" => UnitStatusEnum::on_hand
                ]);
                UnitLog::create([
                    "unit_id" => $item["unit_id"],
                    "user_id" => $user->user_id,
                    "unit_log_number" => $deleteRepair->repair_number,
                    "unit_log_action" => UnitLogActionEnum::repair,
                    "unit_log_status" => UnitLogStatusEnum::ON_HAND
                ]);
                RepairUnitList::where("repair_id", $repair_id)->delete();
            }
            RepairLog::where("repair_id", $repair_id)->delete();

            $deleteRepair->delete();



            DB::commit();

            return ResponseFormatter::success($deleteRepair);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateRepair(Request $request, $repair_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "main_dealer_id" => "required",
                "main_dealer_name" => "required",
                "repair_reason" => "required",
                "repair_unit" => "required|array",
                "repair_unit.*.unit_id" => "required",
                "repair_unit.*.repair_unit_id" => "nullable",
                "repair_unit.*.is_delete" => "nullable"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();

            Repair::where("repair_id", $repair_id)->update([
                "main_dealer_id" => $request->main_dealer_id,
                "main_dealer_name" => $request->main_dealer_name,
                "repair_reason" => $request->repair_reason,
            ]);
            $getDetailRepair = Repair::where("repair_id", $repair_id)
                ->with(["dealer", "repair_unit.unit", "repair_unit.unit.motor", "repair_log.user"])
                ->first();

            if (!isset($getDetailRepair->repair_id)) {
                return
                    ResponseFormatter::error("Repair Not Found !", "Bad Request", 400);
            }

            RepairLog::create([
                "user_id" => $user->user_id,
                "repair_log_action" => RepairLogEnum::create,
                "repair_log_note" => "update repair",
                "repair_id" => $getDetailRepair->repair_id
            ]);

            foreach ($request->repair_unit as $item) {

                // jika repair_unit_id
                if (!isset($item["repair_unit_id"])) {
                    $createRepairUnit[] = RepairUnitList::create([
                        "repair_id" => $getDetailRepair->repair_id,
                        "unit_id" => $item["unit_id"]
                    ]);
                    Unit::where("unit_id", $item["unit_id"])->update([
                        "unit_status" => UnitStatusEnum::hold
                    ]);
                    UnitLog::create([
                        "unit_id" => $item["unit_id"],
                        "user_id" => $user->user_id,
                        "unit_log_number" => $getDetailRepair->repair_number,
                        "unit_log_action" => UnitLogActionEnum::repair,
                        "unit_log_status" => UnitLogStatusEnum::HOLD
                    ]);
                } else {
                    //cari unit di list
                    $getDetailUnitRepair = RepairUnitList::where("repair_unit_list_id", $item["repair_unit_id"])
                        ->where("unit_id", $item["unit_id"])
                        ->first();

                    if ($item["is_delete"] == "true") {
                        if (!isset($getDetailUnitRepair->repair_unit_list_id)) {
                            DB::rollBack();
                            return ResponseFormatter::error("repair unit not found", "Bad Request", 400);
                        }
                        $getDetailUnitRepair->delete();
                        Unit::where("unit_id", $item["unit_id"])->update([
                            "unit_status" => UnitStatusEnum::on_hand
                        ]);
                        UnitLog::create([
                            "unit_id" => $item["unit_id"],
                            "user_id" => $user->user_id,
                            "unit_log_number" => $getDetailRepair->repair_number,
                            "unit_log_action" => UnitLogActionEnum::repair,
                            "unit_log_status" => UnitLogStatusEnum::ON_HAND
                        ]);
                    }
                }
            }


            DB::commit();

            $getDetailRepair = Repair::where("repair_id", $repair_id)
                ->with(["dealer", "repair_unit.unit", "repair_unit.unit.motor", "repair_log.user"])
                ->first();


            return ResponseFormatter::success($getDetailRepair, "Successfully updated !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailRepair(Request $request, $repair_id)
    {
        try {
            $getDetailRepairUnit = Repair::where("repair_id", $repair_id)
                ->with(["dealer",  "repair_unit.unit", "repair_unit.unit.motor", "repair_log.user", "delivery_repair.delivery"])
                ->first();

            if (!$getDetailRepairUnit) {
                return ResponseFormatter::error("Repair not found !", "Bad request !", 400);
            }

            return ResponseFormatter::success($getDetailRepairUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateRepairUnit(Request $request)
    {
        try {
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $repair_status = $request->input("repair_status");
            $searchQuery = $request->input('q');
            $sortBy = $request->input('sort_by', 'repair_number');
            $sortOrder = $request->input('sort_order', 'asc');


            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $getPaginateRepair = Repair::latest()->with(["repair_unit" => function ($query) {
                $query->where("is_return", false);
            }, "repair_log.user", "dealer"])
                ->where(function ($query) use ($searchQuery) {
                    $query->where('repair_number', 'LIKE', "%$searchQuery%")
                        ->orWhere('repair_status', 'LIKE', "%$searchQuery%")
                        ->orWhere('main_dealer_name', 'LIKE', "%$searchQuery%");
                })
                ->where("repair_status", "LIKE", "%$repair_status%")
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->withCount([
                    "repair_unit as repair_unit_total" => function ($query) {
                        $query
                            ->selectRaw('count(*)');
                    },
                    "repair_unit as repair_return_unit_total" => function ($query) {
                        $query
                            ->where("is_return", true)
                            ->selectRaw('count(*)');
                    },
                ])
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->orderBy($sortBy, $sortOrder)
                ->paginate($limit);

            return ResponseFormatter::success($getPaginateRepair);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function createRepair(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "main_dealer_id" => "required",
                "main_dealer_name" => "required",
                "repair_reason" => "required",
                "repair_unit" => "required|array",
                "repair_unit.*.unit_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);

            $createRepair = Repair::create([
                "main_dealer_id" => $request->main_dealer_id,
                "main_dealer_name" => $request->main_dealer_name,
                "repair_reason" => $request->repair_reason,
                "repair_status" => RepairStatusEnum::create,
                "repair_number" => GenerateNumber::generate("TEMP-REPAIR", GenerateAlias::generate($getDealer->dealer->dealer_name), "repairs", "repair_number"),
                "dealer_id" => $getDealer->dealer_id
            ]);

            if (!isset($createRepair->repair_id)) {
                return ResponseFormatter::error("Gagal Create Repair !", "Bad Request", 400);
            }

            $createRepairLog = RepairLog::create([
                "user_id" => $user->user_id,
                "repair_log_action" => RepairLogEnum::create,
                "repair_log_note" => "create new repair",
                "repair_id" => $createRepair->repair_id
            ]);


            foreach ($request->repair_unit as $item) {
                $createRepairUnit[] = RepairUnitList::create([
                    "repair_id" => $createRepair->repair_id,
                    "unit_id" => $item["unit_id"]
                ]);
                Unit::where("unit_id", $item["unit_id"])->update([
                    "unit_status" => UnitStatusEnum::hold
                ]);
                UnitLog::create([
                    "unit_id" => $item["unit_id"],
                    "user_id" => $user->user_id,
                    "unit_log_number" => $createRepair->repair_number,
                    "unit_log_action" => UnitLogActionEnum::repair,
                    "unit_log_status" => UnitLogStatusEnum::HOLD
                ]);
            }



            DB::commit();

            $data = [
                "repair" => $createRepair,
                "repair_uni" => $createRepairUnit,
                "reapair_log" => $createRepairLog
            ];

            return ResponseFormatter::success($data, "Successfully Created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
