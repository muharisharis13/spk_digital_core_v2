<?php

namespace App\Http\Controllers\API;

use App\Enums\NeqReturnStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\DealerNeq;
use App\Models\NeqReturn;
use App\Models\NeqReturnLog;
use App\Models\NeqReturnUnit;
use App\Models\NeqUnit;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NeqReturnController extends Controller
{
    //

    public function updateStatusNeqReturn(Request $request, $neq_return_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "neq_return_status" => "required|in:cancel,approve"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            DB::beginTransaction();
            $getDetailNeqReturn = NeqReturn::where("neq_return_id", $neq_return_id)
                ->with(["dealer_neq", "dealer_neq.dealer", "neq_return_unit.neq_unit.unit.motor", "delivery_neq_return.delivery"]);

            $getDetailNeqReturn = $getDetailNeqReturn->first();

            if (!$getDetailNeqReturn) {
                return ResponseFormatter::success("neq return not found", "Bad Request", 400);
            }
            foreach ($getDetailNeqReturn->neq_return_unit as $item) {
                // update neq unit
                $getDetailNeqUnit =  NeqUnit::with(["unit"])->where("neq_unit_id", $item["neq_unit_id"])->first();

                Unit::where("unit_id", $getDetailNeqUnit->unit->unit_id)->update([
                    "unit_location_status" => null
                ]);


                $getDetailNeqUnit = $getDetailNeqUnit->update([
                    "is_return" => true
                ]);
            }

            $getDetailNeqReturn = $getDetailNeqReturn->update([
                "neq_return_status" => $request->neq_return_status
            ]);

            DB::commit();

            return ResponseFormatter::success($getDetailNeqReturn, "Successfully update status neq return");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteNeqReturnUnit(Request $request,  $neq_return_unit_id)
    {
        try {
            $getDetailNeqReturnUnit = NeqReturnUnit::where("neq_return_unit_id", $neq_return_unit_id);
            DB::beginTransaction();
            $getDetailNeqReturnUnit = $getDetailNeqReturnUnit->delete();
            DB::commit();
            return ResponseFormatter::success($getDetailNeqReturnUnit, "Successfully deleted unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteNeqReturn(Request $request, $neq_return_id)
    {
        try {
            $getDetailNeqReturn = NeqReturn::where("neq_return_id", $neq_return_id)
                ->with(["dealer_neq", "dealer_neq.dealer", "neq_return_unit.neq_unit.unit.motor", "delivery_neq_return.delivery"]);
            DB::beginTransaction();
            $getDetailNeqReturn = $getDetailNeqReturn->delete();
            DB::commit();
            return ResponseFormatter::success($getDetailNeqReturn, "Successfully deleted neq return");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailNeqReturn(Request $request, $neq_return_id)
    {
        try {
            $getDetailNeqReturn = NeqReturn::latest()->where("neq_return_id", $neq_return_id)
                ->with(["neq_return_unit.neq_unit.unit.motor", "dealer_neq", "delivery_neq_return", "neq_return_log.user"]);
            // $getDetailNeqReturn = NeqReturn::latest()->where("neq_return_id", $neq_return_id)
            //     ->with(["dealer_neq", "dealer_neq.dealer", "neq_return_unit.neq_unit.unit.motor", "delivery_neq_return.delivery", "neq_return_log.user"]);

            $getDetailNeqReturn = $getDetailNeqReturn->first();

            return ResponseFormatter::success($getDetailNeqReturn);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateNeqReturn(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $neq_return_status = $request->input("neq_return_status");
            $start = $request->input("start_date");
            $end = $request->input("end_date");
            $searchQuery = $request->input("q");
            $neq_dealer_id = $request->input("neq_dealer_id");

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);
            // "neq_return_unit.neq_unit.unit.motor",
            $getPaginate = NeqReturn::latest()
                ->with(["dealer_neq"])
                ->withCount([
                    "neq_return_unit as neq_return_unit_total" => function ($query) {
                        $query->selectRaw("count(*)");
                    }
                ])
                ->when($start, function ($query) use ($start) {
                    return $query->whereDate('created_at', '>=', $start);
                })
                ->when($end, function ($query) use ($end) {
                    return $query->whereDate('created_at', '<=', $end);
                })
                ->where("neq_return_status", "LIKE", "%$neq_return_status%")
                ->whereHas("dealer_neq", function ($query) use ($neq_dealer_id) {
                    $query->where("dealer_neq_id", "LIKE", "%$neq_dealer_id%");
                })
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where('neq_return_number', "LIKE", "%$searchQuery%")
                        ->orWhereHas("dealer_neq", function ($query) use ($searchQuery) {
                            $query->where("dealer_neq_name", "LIKE", "%$searchQuery%");
                        });
                })
                ->where("dealer_id", $getDealerSelected->dealer_id);


            $getPaginate = $getPaginate->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateNeqReturn(Request $request, $neq_return_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "neq_return_unit" => "required|array",
                "neq_return_unit.*.neq_unit_id" => "required",
                "neq_return_unit.*.neq_return_unit_id" => "nullable",
                "neq_return_unit.*.is_delete" => "nullable"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            foreach ($request->neq_return_unit as $item) {
                if (isset($item["neq_return_unit_id"])) {
                    $getDetailNeqReturnUnit = NeqReturnUnit::where("neq_return_unit_id", $item["neq_return_unit_id"])
                        ->where("neq_unit_id", $item["neq_unit_id"])->first();

                    if (!isset($getDetailNeqReturnUnit->neq_return_unit_id)) {
                        DB::rollBack();
                        return ResponseFormatter::error("neq return not found", "Bad Request", 400);
                    }

                    if ($item["is_delete"] == "true") {
                        $getDetailNeqReturnUnit->delete();
                    }
                    // continue; // Skip if neq_return_unit_id exists
                }
                if (!isset($request->neq_return_unit_id)) {
                    $createNeqReturnUnit[] = NeqReturnUnit::create([
                        "neq_return_id" => $neq_return_id,
                        "neq_unit_id" => $item["neq_unit_id"]
                    ]);
                }
            }

            $user = Auth::user();


            $createNeqReturnLog = NeqReturnLog::create([
                "neq_return_id" => $neq_return_id,
                "user_id" => $user->user_id,
                "neq_return_log_action" => NeqReturnStatusEnum::create,
            ]);

            DB::commit();

            $getDetailNeqReturn = NeqReturn::where("neq_return_id", $neq_return_id)->first();


            $data = [
                "neq_return" => $getDetailNeqReturn,
                "neq_return_log" => $createNeqReturnLog
            ];

            if (isset($createNeqReturnUnit)) {
                $data["neq_return_unit"] = $createNeqReturnUnit;
            }

            return ResponseFormatter::success($data, "Successfully updated !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createNeqReturn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "dealer_neq_id" => "required",
                "neq_return_unit" => "required|array",
                "neq_return_unit.*.neq_unit_id" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $createNeqReturn = NeqReturn::create([
                "dealer_neq_id" => $request->dealer_neq_id,
                "neq_return_number" =>  GenerateNumber::generate("TF-NEQ-RETURN", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "neq_returns", "neq_return_number"),
                "neq_return_status" => NeqReturnStatusEnum::create,
                "dealer_id" => $getDealerSelected->dealer_id,
                "neq_return_note" => $request->neq_return_note
            ]);



            foreach ($request->neq_return_unit as $item) {
                $createNeqReturnUnit[] = NeqReturnUnit::create([
                    "neq_return_id" => $createNeqReturn->neq_return_id,
                    "neq_unit_id" => $item["neq_unit_id"]
                ]);
            }


            $createNeqReturnLog = NeqReturnLog::create([
                "neq_return_id" => $createNeqReturn->neq_return_id,
                "user_id" => $user->user_id,
                "neq_return_log_action" => NeqReturnStatusEnum::create,
            ]);

            DB::commit();


            $data = [
                "neq_return" => $createNeqReturn,
                "neq_return_unit" => $createNeqReturnUnit,
                "neq_return_log" => $createNeqReturnLog
            ];

            return ResponseFormatter::success($data, "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }



    public function getAllUnitNeq(Request $request, $dealer_neq_id)
    {
        try {

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);
            $search = $request->input("q");


            $getAllUnitNeq = NeqUnit::latest();

            $getAllUnitNeq = $getAllUnitNeq->with(["unit.motor", "neq" => function ($query) use ($getDealer) {
                $query->whereHas("dealer_neq", function ($query) use ($getDealer) {
                    $query->where("dealer_id", $getDealer->dealer_id);
                });
            }])
                ->whereHas("neq", function ($query) use ($getDealer, $dealer_neq_id) {
                    $query
                        ->where("neq_status", 'approve')
                        ->whereHas("dealer_neq", function ($query) use ($getDealer, $dealer_neq_id) {
                            $query->where("dealer_neq_id", $dealer_neq_id);
                            $query->where("dealer_id", $getDealer->dealer_id);
                        });
                })
                ->where("is_return", false)
                ->when($search, function ($query) use ($search) {
                    return $query->whereHas("unit", function ($query) use ($search) {
                        $query->whereHas("motor", function ($query) use ($search) {
                            $query->where("motor_name", "LIKe", "%$search%");
                        });
                    });
                });




            $getAllUnitNeq = $getAllUnitNeq->get();

            return ResponseFormatter::success($getAllUnitNeq);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    // private function checkUnitIsHaveEvent($unit_id)
    // {
    //     $user = Auth::user();
    //     $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);


    //     if (!isset($unit_id)) {
    //         return ResponseFormatter::error("unit id not found", "bad request", 400);
    //     }
    //     $getUnit = Unit::latest()
    //         ->with("event_list_unit")
    //         ->where("unit_id", $unit_id)
    //         ->where("dealer_id", $getDealerSelected->dealer_id)->first();

    //     return isset($getUnit->event_list_unit);
    // }
}
