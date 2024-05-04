<?php

namespace App\Http\Controllers\API;

use App\Enums\EventReturnStatusEnum;
use App\Enums\EventStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\EventListUnit;
use App\Models\EventReturn;
use App\Models\EventReturnListUnit;
use App\Models\EventReturnLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventReturnController extends Controller
{
    //

    public function deleteEventReturnUnit(Request $request, $event_return_list_unit_id)
    {
        try {

            $getDetailEventReturnUnit = EventReturnListUnit::where("event_return_list_unit_id", $event_return_list_unit_id)->first();
            DB::beginTransaction();
            if (!$getDetailEventReturnUnit) {
                DB::rollBack();
                return ResponseFormatter::error("Event return unit not found !", "Bad Request", 400);
            }

            // update event unit di kembalikan menjadi false
            EventListUnit::where("event_list_unit_id", $getDetailEventReturnUnit["event_list_unit_id"])->update([
                "is_return" => false
            ]);

            $getDetailEventReturnUnit->delete();

            DB::commit();

            return ResponseFormatter::success($getDetailEventReturnUnit, "Successfully delete event return unit !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function deleteEventReturn(Request $request, $event_return_id)
    {
        try {
            $getDetail = EventReturn::latest()
                ->with(["master_event", "event_return_unit", "event_return_log.user", "delivery_event_return.delivery"])
                ->withCount([
                    "event_return_unit as event_return_unit_total" => function ($query) {
                        $query
                            ->selectRaw('count(*)');
                    },
                ])
                ->where("event_return_id", $event_return_id)
                ->first();


            DB::beginTransaction();

            if ($getDetail->event_return_status === "approve") {
                DB::rollBack();
                return ResponseFormatter::error("Tidak Dapat Delete event return yang sudah approve", "Bad Request", 400);
            }

            foreach ($getDetail->event_return_unit as $item) {
                // kembalikan status is return dari true ke false

                EventListUnit::where("event_list_unit_id", $item["event_list_unit_id"])->update([
                    "is_return" => false
                ]);
                // delete unit event retuurn dlu

                EventReturnListUnit::where("event_return_list_unit_id", $item["event_return_list_unit_id"])->delete();
            }

            $getDetail->delete();


            DB::commit();

            return ResponseFormatter::success($getDetail, "Successfully deleted Event Return !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateStatusEventReturn(Request $request, $event_return_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "event_return_status" => "required|in:cancel,approve"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();
            $getDetailEventReturn = EventReturn::with(["master_event", "event_return_unit", "event_return_log.user"])->where("event_return_id", $event_return_id)->first();

            if (!$getDetailEventReturn) {
                return ResponseFormatter::success("event return not found", "Bad Request", 400);
            }

            if ($request->event_return_status === 'approve') {

                foreach ($getDetailEventReturn->event_return_unit as $item) {
                    // update event unit ke is return true

                    $getDetailEventListUnit = EventListUnit::with(["unit"])->where("event_list_unit_id", $item["event_list_unit_id"])->first();

                    Unit::where("unit_id", $getDetailEventListUnit->unit->unit_id)->update([
                        "unit_location_status" => null
                    ]);

                    if ($getDetailEventListUnit->is_return === true) {
                        DB::rollBack();
                        return ResponseFormatter::error("unit " . $getDetailEventListUnit->unit->unit_id . " sudah di return harap hapus.");
                    }




                    $getDetailEventListUnit = $getDetailEventListUnit->update([
                        "is_return" => true
                    ]);
                }

                $user = Auth::user();
                // create event return log
                $createEventLog = EventReturnLog::create([
                    "event_return_id" => $getDetailEventReturn->event_return_id,
                    "user_id" => $user->user_id,
                    "event_return_log_action" => EventReturnStatusEnum::create,
                    "event_return_log_note" => "create event return"
                ]);

                $getDetailEventReturn->update([
                    "event_return_status" => $request->event_return_status
                ]);

                DB::commit();

                return ResponseFormatter::success($getDetailEventReturn, "Success Update Status !");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailEventReturn(Request $request, $event_return_id)
    {
        try {
            $getDetail = EventReturn::latest()
                ->with(["master_event", "event_return_unit", "event_return_log.user", "delivery_event_return.delivery"])
                ->withCount([
                    "event_return_unit as event_return_unit_total" => function ($query) {
                        $query
                            ->selectRaw('count(*)');
                    },
                ])
                ->where("event_return_id", $event_return_id);


            $getDetail = $getDetail->first();

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateEventReturn(Request $request)
    {
        try {
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $date = $request->input('date');
            $event_return_status = $request->input("event_return_status");
            $searchQuery = $request->input('q');

            $getPaginateEventReturn = EventReturn::latest()
                ->with(["master_event"])
                ->withCount([
                    "event_return_unit as event_return_unit_total" => function ($query) {
                        $query
                            ->selectRaw('count(*)');
                    },
                ])
                ->when($date, function ($query) use ($date) {
                    return $query->whereDate('created_at', 'LIKE', "%$date%");
                })
                ->where("event_return_status", "LIKE", "%$event_return_status%")
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    return $query->where('event_return_number', 'LIKE', "%$searchQuery%")
                        ->orWhere("event_return_status", "LIKE", "%$searchQuery%")
                        ->orWhereHas("master_event", function ($query) use ($searchQuery) {
                            $query->where("master_event_name", "LIKE", "%$searchQuery%")
                                ->orWhere("master_event_location", "LIKE", "%$searchQuery%");
                        });
                });


            $getPaginateEventReturn = $getPaginateEventReturn->paginate($limit);

            return ResponseFormatter::success($getPaginateEventReturn);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateEventReturn(Request $request, $event_return_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "event_return_unit" => "required|array",
                "event_return_unit.*.event_list_unit_id" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            foreach ($request->event_return_unit as $item) {

                if (!isset($item["event_return_list_unit_id"])) {
                    // merubah status unit di table repair_unit_list
                    // EventListUnit::where("event_list_unit_id", $item["event_list_unit_id"])
                    //     ->update([
                    //         "is_return" => true
                    //     ]);

                    EventReturnListUnit::create([
                        "event_return_id" => $event_return_id,
                        "event_list_unit_id" => $item["event_list_unit_id"]
                    ]);
                }
            }
            $user = Auth::user();

            // create event return log
            EventReturnLog::create([
                "event_return_id" => $event_return_id,
                "user_id" => $user->user_id,
                "event_return_log_action" => EventReturnStatusEnum::create,
                "event_return_log_note" => "update event return"
            ]);

            DB::commit();

            $getDetail = EventReturn::latest()
                ->with(["master_event", "event_return_unit", "event_return_log.user"])
                ->withCount([
                    "event_return_unit as event_return_unit_total" => function ($query) {
                        $query
                            ->selectRaw('count(*)');
                    },
                ])
                ->where("event_return_id", $event_return_id);


            $getDetail = $getDetail->first();

            return ResponseFormatter::success($getDetail, "Successfully updated !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createEventReturn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "master_event_id" => "required",
                "event_return_unit" => "required|array",
                "event_return_unit.*.event_list_unit_id" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $createEventReturn = EventReturn::create([
                "master_event_id" => $request->master_event_id,
                "event_return_number" => GenerateNumber::generate("EVENT-RETURN", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "event_returns", "event_return_number"),
                "event_return_status" => EventReturnStatusEnum::create,
                "dealer_id" => $getDealerSelected->dealer_id
            ]);

            foreach ($request->event_return_unit as $item) {
                // update event unit ke is return true

                // EventListUnit::where("event_list_unit_id", $item["event_list_unit_id"])->update([
                //     "is_return" => true
                // ]);

                $createEventReturnUnit[] = EventReturnListUnit::create([
                    "event_return_id" => $createEventReturn->event_return_id,
                    "event_list_unit_id" => $item["event_list_unit_id"]
                ]);
            }

            // create event return log
            $createEventLog = EventReturnLog::create([
                "event_return_id" => $createEventReturn->event_return_id,
                "user_id" => $user->user_id,
                "event_return_log_action" => EventReturnStatusEnum::create,
                "event_return_log_note" => "create event return"
            ]);

            DB::commit();

            $data = [
                "event_return" => $createEventReturn,
                "event_return_unit" => $createEventReturnUnit,
                "event_return_log" => $createEventLog
            ];

            return ResponseFormatter::success($data, "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function getAllUnitEvent(Request $request, $master_event_id)
    {
        try {

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);
            $search = $request->input("q");


            $getAllUnitEvent = EventListUnit::latest();

            $getAllUnitEvent = $getAllUnitEvent->with(["event.master_event", "unit.motor", "event" => function ($query) use ($getDealer) {
                $query->whereHas("master_event", function ($query) use ($getDealer) {
                    $query->where("dealer_id", $getDealer->dealer_id);
                });
            }])
                ->whereHas("event", function ($query) use ($getDealer, $master_event_id) {
                    $query
                        ->where("event_status", EventStatusEnum::approve)
                        ->whereHas("master_event", function ($query) use ($getDealer, $master_event_id) {
                            $query->where("master_event_id", $master_event_id);
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




            $getAllUnitEvent = $getAllUnitEvent->get();

            return ResponseFormatter::success($getAllUnitEvent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
