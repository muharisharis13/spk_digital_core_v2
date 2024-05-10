<?php

namespace App\Http\Controllers\API;

use App\Enums\RepairReturnStatusEnum;
use App\Enums\RepairStatusEnum;
use App\Enums\UnitStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\RepairReturn;
use App\Models\RepairReturnUnit;
use App\Models\RepairUnitList;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RepairReturnController extends Controller
{
    //

    public function getPaginateRepairReturn(Request $request)
    {
        try {
            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);

            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $repair_return_status = $request->input("repair_return_status");
            $searchQuery = $request->input('q');


            $getPaginateRepairReturn = RepairReturn::latest()
                ->where("dealer_id", $getDealer->dealer_id)
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->where("repair_return_status", 'LIKE', "%$repair_return_status%")
                ->where(function ($query) use ($searchQuery) {
                    $query->where("repair_return_number", 'LIKE', "%$searchQuery%")
                        ->orWhere("repair_return_STATUS", 'LIKE', "%$searchQuery%");
                })
                ->withCount([
                    'repair_return_unit as repair_return_unit_total' => function ($query) {
                        $query->selectRaw('count(*)');
                    },
                ])
                ->paginate($limit);


            return ResponseFormatter::success($getPaginateRepairReturn);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateStatusRepairReturn(Request $request, $repair_return_id)
    {
        try {

            $validator  = Validator::make($request->all(), [
                "repair_return_status" => "required|in:create,approve,cancel"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            $getDetailRepairReturn = RepairReturn::where("repair_return_id", $repair_return_id)->first();

            DB::beginTransaction();

            if ($request->repair_return_status === "approve") {
                foreach ($getDetailRepairReturn->repair_return_unit as $item) {
                    if (isset($item["repair_unit"]->unit->unit_id)) {
                        Unit::where("unit_id", $item["repair_unit"]->unit->unit_id)->update([
                            "unit_status" => UnitStatusEnum::on_hand
                        ]);
                    }
                }
            }

            $getDetailRepairReturn->update([
                "repair_return_status" => $request->repair_return_status,
                "repair_return_number" => str_replace('TEMP-', "", $getDetailRepairReturn->repair_return_number)
            ]);

            DB::commit();

            return ResponseFormatter::success($getDetailRepairReturn, "Successfully change status repair return !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteRepairReturn(Request $request, $repair_return_id)
    {
        try {
            $getDetailRepairReturn = RepairReturn::where("repair_return_id", $repair_return_id)->first();

            DB::beginTransaction();

            if (!isset($getDetailRepairReturn->repair_return_id)) {
                return ResponseFormatter::error("Repair return not found", "not found", 404);
            }

            foreach ($getDetailRepairReturn->repair_return_unit as $item) {
                // hapus repair return unit

                $getDetailRepairReturnUnit = RepairReturnUnit::where("repair_return_unit_id", $item["repair_return_unit_id"])->first();

                RepairUnitList::where("repair_unit_list_id", $getDetailRepairReturnUnit->repair_unit_list_id)->update([
                    "is_return" => false
                ]);

                $getDetailRepairReturnUnit->delete();
            }

            $getDetailRepairReturn->delete();

            DB::commit();

            return ResponseFormatter::success($getDetailRepairReturn, "Successfully deleted repair return !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteRepairReturnUnit(Request $request, $repair_return_unit_id)
    {
        try {
            $getDetaiRepairReturnUnit = RepairReturnUnit::where("repair_return_unit_id", $repair_return_unit_id)->first();

            DB::beginTransaction();

            RepairUnitList::where("repair_unit_list_id", $getDetaiRepairReturnUnit->repair_unit_list_id)->update([
                "is_return" => false
            ]);
            $getDetaiRepairReturnUnit->delete();

            DB::commit();

            return ResponseFormatter::success($getDetaiRepairReturnUnit, "Successfully delete repair return unit !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getRepairUnit(Request $request)
    {
        try {
            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);

            $searchQuery = $request->input('q');


            $getRepairUnit = RepairUnitList::latest()
                ->where(function ($query) use ($searchQuery) {
                    $query->whereHas("unit", function ($queryUnit) use ($searchQuery) {
                        $queryUnit->where("unit_frame", "LIKE", "%$searchQuery%")
                            ->orWhereHas("motor", function ($queryMotor) use ($searchQuery) {
                                $queryMotor->where("motor_name", "LIKE", "%$searchQuery%");
                            });
                    });
                })
                ->where("is_return", false)
                ->with(["repair", "unit.motor"])
                ->whereHas("repair", function ($query) use ($getDealer) {
                    $query->where("dealer_id", $getDealer->dealer_id)
                        ->where("repair_status", RepairStatusEnum::approve);
                })
                ->get();

            return ResponseFormatter::success($getRepairUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailRepairReturn(Request $request, $repair_return_id)
    {
        try {
            $getDetailRepairReturn = RepairReturn::where("repair_return_id", $repair_return_id)->first();


            if (!$getDetailRepairReturn) {
                return ResponseFormatter::error("Repair return not found !", "Bad request !", 400);
            }

            return ResponseFormatter::success($getDetailRepairReturn);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateRepairReturn(Request $request, $repair_return_id)
    {
        try {

            $validator  = Validator::make($request->all(), [
                "repair_return_unit" => "required|array",
                "repair_return_unit.*.repair_unit_list_id" => "required",
                "repair_return_unit.*.is_delete" => "nullable",
                "repair_return_unit.*.repair_return_unit_id" => "nullable"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            // update repair unit return

            DB::beginTransaction();
            $createRepairReturnUnit = [];

            foreach ($request->repair_return_unit as $item) {

                if (!isset($item["repair_return_unit_id"])) {
                    // merubah status unit di table repair_unit_list
                    RepairUnitList::where("repair_unit_list_id", $item["repair_unit_list_id"])
                        ->update([
                            "is_return" => true
                        ]);

                    $createRepairReturnUnit[] = RepairReturnUnit::create([
                        "repair_return_id" => $repair_return_id,
                        "repair_unit_list_id" => $item["repair_unit_list_id"]
                    ]);
                } else {
                    $getDetailRepairReturnUnit = RepairReturnUnit::where("repair_return_unit_id", $item["repair_return_unit_id"])
                        ->where("repair_unit_list_id", $item["repair_unit_list_id"])
                        ->first();

                    if (!isset($getDetailRepairReturnUnit->repair_return_unit_id)) {
                        DB::rollBack();
                        return ResponseFormatter::error("Repair return not found", "Bad Request", 400);
                    }

                    if ($item["is_delete"] == "true") {
                        $getDetailRepairReturnUnit->delete();
                        RepairUnitList::where("repair_unit_list_id", $item["repair_unit_list_id"])->update([
                            "is_return" => false
                        ]);
                    }
                }
            }

            DB::commit();


            return ResponseFormatter::success($createRepairReturnUnit, "Successfully update repair return!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createRepairReturn(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "repair_return_unit" => "required|array",
                "repair_return_unit.*.repair_unit_list_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);

            DB::beginTransaction();

            $createRepairReturn = RepairReturn::create([
                "repair_return_number" => GenerateNumber::generate("TEMP-REPAIR-RETURN", GenerateAlias::generate($getDealer->dealer->dealer_name), "repair_returns", "repair_return_number"),
                "repair_return_status" => RepairReturnStatusEnum::create,
                "dealer_id" => $getDealer->dealer_id
            ]);

            if (!isset($createRepairReturn->repair_return_id)) {
                return ResponseFormatter::error("Gagal Create Repair Return !", "Bad Request", 400);
            }

            foreach ($request->repair_return_unit as $item) {
                // merubah status unit di table repair_unit_list
                RepairUnitList::where("repair_unit_list_id", $item["repair_unit_list_id"])
                    ->update([
                        "is_return" => true
                    ]);


                $createRepairReturnUnit[] = RepairReturnUnit::create([
                    "repair_return_id" => $createRepairReturn->repair_return_id,
                    "repair_unit_list_id" => $item["repair_unit_list_id"]
                ]);
            }

            DB::commit();

            $data = [
                "repair_return" => $createRepairReturn,
                "repair_return_unit" => $createRepairReturnUnit
            ];


            return ResponseFormatter::success($data, "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
