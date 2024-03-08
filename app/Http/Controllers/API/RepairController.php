<?php

namespace App\Http\Controllers\API;

use App\Enums\RepairLogEnum;
use App\Enums\RepairStatusEnum;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Repair;
use App\Models\RepairLog;
use App\Models\RepairUnitList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RepairController extends Controller
{
    //

    public function getDetailRepair(Request $request, $repair_id)
    {
        try {
            $getDetailRepairUnit = Repair::where("repair_id", $repair_id)
                ->with(["dealer", "main_dealer", "repair_unit.unit", "repair_unit.unit.motor", "repair_log.user"])
                ->first();

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
            $getPaginateRepair = Repair::with(["repair_unit", "repair_log.user", "dealer", "main_dealer"])
                ->where(function ($query) use ($searchQuery) {
                    $query->where('repair_number', 'LIKE', "%$searchQuery%")
                        ->orWhere('repair_status', 'LIKE', "%$searchQuery%")
                        ->whereHas("main_dealer", function ($queryMainDealer) use ($searchQuery) {
                            return $queryMainDealer->where("main_dealer_name", 'LIKE', "%$searchQuery%");
                        });
                })
                ->where("repair_status", "LIKE", "%$repair_status%")
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
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
                "repair_reason" => $request->repair_reason,
                "repair_status" => RepairStatusEnum::create,
                "repair_number" => GenerateNumber::generate("REPAIR", $getDealer->dealer->dealer_name),
                "dealer_id" => $getDealer->dealer_id
            ]);

            if (!isset($createRepair->repair_id)) {
                return ResponseFormatter::error("Gagal Create Repair !", "Bad Request", 400);
            }

            $createRepairLog = RepairLog::create([
                "user_id" => $user->user_id,
                "repair_log_action" => RepairLogEnum::create,
                "repair_log_note" => NULL,
                "repair_id" => $createRepair->repair_id
            ]);

            foreach ($request->repair_unit as $item) {
                $createRepairUnit[] = RepairUnitList::create([
                    "repair_id" => $createRepair->repair_id,
                    "unit_id" => $item["unit_id"]
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
