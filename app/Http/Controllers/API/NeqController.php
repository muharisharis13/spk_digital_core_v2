<?php

namespace App\Http\Controllers\API;

use App\Enums\NeqStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Neq;
use App\Models\NeqLog;
use App\Models\NeqUnit;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NeqController extends Controller
{

    public function deleteUnitNeq(Request $request, $neq_unit_id)
    {
        try {
            $getDetailUnitNeq = NeqUnit::where("neq_unit_id", $neq_unit_id)->first();

            DB::beginTransaction();

            $getDetailUnitNeq = $getDetailUnitNeq->delete();

            DB::commit();
            return ResponseFormatter::success($getDetailUnitNeq, "Successfully delete unit neq !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteNeq(Request $request, $neq_id)
    {
        try {

            DB::beginTransaction();


            $getDetailDealerNeq = Neq::latest()
                ->with(["dealer_neq", "dealer", "neq_unit.unit.motor"])
                ->withCount([
                    "neq_unit as neq_unit_total" => function ($query) {
                        $query->selectRaw("count(*)");
                    }
                ])
                ->where("neq_id",  $neq_id)
                ->first();

            foreach ($getDetailDealerNeq->neq_unit as $item) {
                // delete neq unit

                NeqUnit::where("neq_unit_id", $item["neq_unit_id"])->delete();
            }


            $getDetailDealerNeq = $getDetailDealerNeq->delete();

            DB::commit();

            return ResponseFormatter::success($getDetailDealerNeq, "Successfully delete neq !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function updateStatusNeq(Request $request, $neq_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "neq_status" => "required|in:approve,cancel",
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();
            $user = Auth::user();

            $getDetailNeq = Neq::latest()
                ->with(["dealer_neq", "dealer", "neq_unit.unit.motor"])
                ->withCount([
                    "neq_unit as neq_unit_total" => function ($query) {
                        $query->selectRaw("count(*)");
                    }
                ])
                ->where("neq_id",  $neq_id)
                ->first();


            // create log neq
            $creaetLogNeq = NeqLog::create([
                "neq_id" => $getDetailNeq->neq_id,
                "user_id" => $user->user_id,
                "neq_log_action" => "update"
            ]);


            $getDetailNeq = $getDetailNeq->update([
                "neq_status" => $request->neq_status
            ]);

            DB::commit();

            $data = [
                "neq" => $getDetailNeq,
                "neq_log" => $creaetLogNeq
            ];

            return ResponseFormatter::success($data, "Successfully update status neq !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function getDetailNeq(Request $request, $neq_id)
    {
        try {
            $getDetailDealerNeq = Neq::latest()
                ->with(["dealer_neq", "dealer", "neq_unit.unit.motor", "neq_log.user", "deliver_neq"])
                ->withCount([
                    "neq_unit as neq_unit_total" => function ($query) {
                        $query->selectRaw("count(*)");
                    }
                ])
                ->where("neq_id",  $neq_id);

            $getDetailDealerNeq = $getDetailDealerNeq->first();


            return ResponseFormatter::success($getDetailDealerNeq);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateNeq(Request $request)
    {
        try {

            $limit = $request->input("limit", 5);
            $neq_status = $request->input("neq_status");
            $dealer_neq_id = $request->input("dealer_neq_id");
            $start = $request->input("start_date");
            $end = $request->input("end_date");
            $searchQuery = $request->input("q");

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $getPaginateNeq = Neq::latest()
                ->with(["dealer_neq", "dealer"])
                ->withCount([
                    "neq_unit as neq_unit_total" => function ($query) {
                        $query->selectRaw("count(*)");
                    }
                ])
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    return $query->where("neq_number", "LIKE", "%$searchQuery%")
                        ->orWhere("neq_status", "LIKE", "%$searchQuery%")
                        ->orWhereHas("dealer", function ($query) use ($searchQuery) {
                            $query->where("dealer_name", "LIKE", "%$searchQuery%");
                        })
                        ->orWhereHas("dealer_neq", function ($query) use ($searchQuery) {
                            $query->where("dealer_neq_name", "LIKE", "%$searchQuery%");
                        });
                })
                ->when($start, function ($query) use ($start) {
                    return $query->whereDate('created_at', '>=', $start);
                })
                ->when($end, function ($query) use ($end) {
                    return $query->whereDate('created_at', '<=', $end);
                })
                ->where("dealer_neq_id", "LIKE", "%$dealer_neq_id%")
                ->where("neq_status", "LIKE", "%$neq_status%")
                ->where("dealer_id", $getDealerSelected->dealer_id);


            $getPaginateNeq = $getPaginateNeq->paginate($limit);
            return ResponseFormatter::success($getPaginateNeq);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateNeq(Request $request, $neq_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "neq_shipping_date" => "required",
                "dealer_neq_id" => "required",
                "neq_note" => "nullable",
                "neq_unit" => "required|array",
                "neq_unit.*.unit_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();

            $getDetailNeq = Neq::where("neq_id", $neq_id)->first();

            $getDetailNeq->update([
                "neq_note" => $request->neq_note,
                "neq_shipping_date" => $request->neq_shipping_date,
                "dealer_neq_id" => $request->dealer_neq_id,
            ]);

            // create log neq
            $creaetLogNeq = NeqLog::create([
                "neq_id" => $getDetailNeq->neq_id,
                "user_id" => $user->user_id,
                "neq_log_action" => "update"
            ]);



            foreach ($request->neq_unit as $item) {
                if (isset($item["neq_unit_id"])) {
                    continue; // Skip if neq_unit_id exists
                }
                if (!isset($item["neq_unit_id"])) {
                    if ($this->checkUnitIsHaveEvent($item["unit_id"])) {
                        DB::rollBack();
                        return ResponseFormatter::error("Unit $item->unit_id sudah memiliki event harap di return dahulu untuk tersedia di transfer ke NEQ", "Bad request !", 400);
                    }
                    $createNeqUnit[] = NeqUnit::create([
                        "neq_id" => $getDetailNeq->neq_id,
                        "unit_id" => $item["unit_id"],
                    ]);
                }
            }

            DB::commit();

            $data = [
                "neq" => $getDetailNeq,
                "neq_log" => $creaetLogNeq
            ];

            if ($createNeqUnit) {

                $data["neq_unit"] = $createNeqUnit;
            }


            return ResponseFormatter::success($data, "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function createNeq(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "neq_shipping_date" => "required",
                "dealer_neq_id" => "required",
                "neq_note" => "nullable",
                "neq_unit" => "required|array",
                "neq_unit.*.unit_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $createNeq = Neq::create([
                "neq_note" => $request->neq_note,
                "neq_shipping_date" => $request->neq_shipping_date,
                "dealer_neq_id" => $request->dealer_neq_id,
                "dealer_id" => $getDealerSelected->dealer_id,
                "neq_number" => GenerateNumber::generate("TF-NEQ", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "neqs", "neq_number"),
                "neq_status" => NeqStatusEnum::create
            ]);

            // create log neq
            $creaetLogNeq = NeqLog::create([
                "neq_id" => $createNeq->neq_id,
                "user_id" => $user->user_id,
                "neq_log_action" => "create"
            ]);

            foreach ($request->neq_unit as $item) {
                if ($this->checkUnitIsHaveEvent($item["unit_id"])) {
                    DB::rollBack();
                    return ResponseFormatter::error("Unit $item->unit_id sudah memiliki event harap di return dahulu untuk tersedia di transfer ke NEQ", "Bad request !", 400);
                }
                $createNeqUnit[] = NeqUnit::create([
                    "neq_id" => $createNeq->neq_id,
                    "unit_id" => $item["unit_id"],
                ]);
            }

            DB::commit();

            $data = [
                "neq" => $createNeq,
                "neq_unit" => $createNeqUnit,
                "neq_log" => $creaetLogNeq
            ];

            return ResponseFormatter::success($data, "Successfully created !");

            // return ResponseFormatter::success($this->checkUnitIsHaveEvent(), "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    private function checkUnitIsHaveEvent()
    {
        $user = Auth::user();
        $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);


        $getUnit = Unit::latest()
            ->with("event_list_unit")
            ->where("unit_id", "9b9b8bf9-70ea-46d5-8024-5ebfa2566c5a")
            ->where("dealer_id", $getDealerSelected->dealer_id)->first();

        return isset($getUnit->event_list_unit);
    }
}
