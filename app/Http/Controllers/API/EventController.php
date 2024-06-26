<?php

namespace App\Http\Controllers\API;

use App\Enums\EventStatusEnum;
use App\Enums\UnitLocationStatusEnum;
use App\Enums\UnitLogStatusEnum;
use App\Helpers\FormatDate;
use App\Helpers\FormateDate;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventListUnit;
use App\Models\EventLog;
use App\Models\Unit;
use App\Models\UnitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    //

    public function deleteUnitEvent(Request $request, $event_list_unit_id)
    {
        try {
            $getEventUnit = EventListUnit::where("event_list_unit_id", $event_list_unit_id)->first();

            $getEventUnit->delete();

            return ResponseFormatter::success($getEventUnit, "Successfully delete unit event !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateStatusEvent(Request $request, $event_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "event_status" => "required|in:cancel,approve"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailEvent = Event::with(["event_unit.event.master_event"])->where("event_id", $event_id)->first();
            $user = Auth::user();
            // check apakah unit tersebut sudah approve di event lain apa belum
            if (isset($getDetailEvent->event_unit) && $getDetailEvent->event_unit->count() > 0) {
                foreach ($getDetailEvent->event_unit as $eventUnit) {

                    // check unit apakah unit location status tidak null
                    $getUnitDetail = Unit::where("unit_id", $eventUnit->unit_id)->first();

                    if ($getUnitDetail->unit_location_status != null) {
                        DB::rollBack();
                        return ResponseFormatter::error("Unit dengan ID {$eventUnit->unit_id} sudah terdaftar dalam unit location status.", "Bad Request", 400);
                    }


                    $unit = $eventUnit->unit_id;


                    $checkDuplicate = EventListUnit::whereHas('event', function ($query) use ($unit) {
                        $query->where('event_status', 'approve');
                    })
                        ->where('unit_id', $unit)
                        ->where('event_id', $event_id)
                        ->exists();

                    if ($checkDuplicate) {
                        // Jika unit tersebut sudah di-approve di event lain
                        DB::rollBack();
                        return ResponseFormatter::error("Unit already approved in another event", "Bad Request", 400);
                    }

                    // update unit location status

                    $getDetailUnit =  Unit::where("unit_id", $unit)->update([
                        "unit_location_status" => UnitLocationStatusEnum::event,
                        "event_id" => $getDetailEvent->event_id
                    ]);

                    UnitLog::create([
                        "unit_id" => $unit,
                        "user_id" => $user->user_id,
                        "unit_log_number" => "-",
                        "unit_log_action" => "on_hand",
                        "unit_log_status" => UnitLogStatusEnum::EVENT
                    ]);
                }
            } else {
                DB::rollBack();
                return
                    ResponseFormatter::error("Paling tidak memiliki satu unit untuk di approve", "Bad Request", 400);
            }


            // add event log
            EventLog::create([
                "event_id" => $getDetailEvent->event_id,
                "user_id" => $user->user_id,
                "event_log_action" => EventStatusEnum::create,
                "event_log_note" => "Create new event"
            ]);

            $getDetailEvent->update([
                "event_status" => $request->event_status
            ]);

            DB::commit();

            return ResponseFormatter::success($getDetailEvent, "Success Update Status");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailEvent(Request $request, $event_id)
    {
        try {
            $getDetailEvent = Event::where("event_id", $event_id)->with(["event_unit.unit.event_list_unit.event", "event_unit.unit.motor", "master_event", "event_log.user", "delivery_event.event.master_event", "delivery_event.delivery"])->first();

            return ResponseFormatter::success($getDetailEvent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateEvent(Request $request)
    {
        try {
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $date = $request->input('date');
            $searchQuery = $request->input('q');


            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $getPaginateEvent = Event::latest()
                ->with(["master_event", "event_unit.unit.motor"])
                ->whereHas("master_event", function ($query) use ($getDealerByUserSelected) {
                    $query->where("dealer_id", $getDealerByUserSelected->dealer_id);
                })
                ->where("master_event_id", "!=", null)
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($query) use ($searchQuery) {
                        $query->whereDate('created_at', 'LIKE', "%$searchQuery%")
                            ->orWhere("event_number", "LIKE", "%$searchQuery%")
                            ->orWhereHas("master_event", function ($query) use ($searchQuery) {
                                $query->where('master_event_name', 'LIKE', "%$searchQuery%")
                                    ->orWhere('master_event_location', 'LIKE', "%$searchQuery%")
                                    ->orWhere('master_event_date', 'LIKE', "%$searchQuery%");
                            });
                    });
                })
                ->withCount([
                    "event_unit as event_unit_total" => function ($query) {
                        $query->selectRaw('count(*)');
                    },
                    "event_unit as event_unit_return_total" => function ($query) {
                        $query->where("is_return", true)
                            ->selectRaw('count(*)');
                    },
                ])
                ->paginate($limit);


            return ResponseFormatter::success($getPaginateEvent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteEvent(Request $request, $event_id)
    {
        try {
            DB::beginTransaction();
            $getDetailEvent = Event::where("event_id", $event_id)->with(["event_unit"])->first();

            if (!isset($getDetailEvent->event_id)) {
                return ResponseFormatter::success("Event not found !", "Bad request", 400);
            }


            foreach ($getDetailEvent->event_unit as $item) {
                EventListUnit::where("event_id", $item["event_id"])->delete();
            }

            $getDetailEvent->delete();

            DB::commit();

            return ResponseFormatter::success($getDetailEvent, "Delete Successfully");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateEvent(Request $request, $event_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "master_event_id" => "required",
                "event_unit" => "required|array",
                "event_unit.*.unit_id" => "required",
                "event_unit.*.event_list_unit_id" => "nullable",
                "event_unit.*.is_delete" => "nullable"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();



            $getDetailEvent = Event::where("event_id", $event_id)->with(["event_unit", "master_event"])->first();


            // add unit ke event list unit

            foreach ($request->event_unit as $item) {
                // if (isset($item["event_list_unit_id"])) {
                //     $getDetailEventListUnit = EventListUnit::where("event_list_unit_id", $item["event_list_unit_id"])
                //         ->where("unit_id", $item["unit_id"])
                //         ->first();

                //     if (!isset($getDetailEventListUnit->event_list_unit_id)) {
                //         DB::rollBack();
                //         return ResponseFormatter::error("event unit not found", "Bad Request", 400);
                //     }

                //     $getDetailEventListUnit->delete();
                //     // continue; // Skip if neq_unit_id exists
                // }
                if (!isset($item['event_list_unit_id'])) {
                    if ($this->checkUnitIsHaveNEQ($item['unit_id'])) {
                        DB::rollBack();
                        return ResponseFormatter::error("Unit " . $item['unit_id'] . " sudah memiliki neq, harap di-return dahulu untuk tersedia di transfer ke event", "Bad request !", 400);
                    }
                    // check unit apakah sudah ada di event

                    $checkUnit = Unit::where("unit_id", $item["unit_id"])->with("event_list_unit.event.master_event")->first();
                    if (!isset($checkUnit->event_list_unit->event->master_event->master_event_id)) {
                        $createEventUnit[] = EventListUnit::create([
                            "event_id" => $getDetailEvent->event_id,
                            "unit_id" => $item["unit_id"]
                        ]);
                    } else {
                        DB::rollBack();
                        return ResponseFormatter::error("Unit dengan ID {$item['unit_id']} sudah terdaftar dalam event.", "Bad Request", 400);
                    }
                } else {
                    $getDetailEventListUnit = EventListUnit::where("event_list_unit_id", $item["event_list_unit_id"])
                        ->where("unit_id", $item["unit_id"])
                        ->first();

                    if (!isset($getDetailEventListUnit->event_list_unit_id)) {
                        DB::rollBack();
                        return ResponseFormatter::error("event unit not found", "Bad Request", 400);
                    }

                    if ($item["is_delete"] == "true") {
                        $getDetailEventListUnit->delete();
                    }
                }
            }

            // add event log
            EventLog::create([
                "event_id" => $getDetailEvent->event_id,
                "user_id" => $user->user_id,
                "event_log_action" => EventStatusEnum::create,
                "event_log_note" => "Update Event"
            ]);

            $getDetailEvent->update([
                "master_event_id" => $request->master_event_id
            ]);


            DB::commit();


            // $data = [
            //     "event" => $getDetailEvent,
            // ];
            return ResponseFormatter::success($getDetailEvent, "Successfully updated !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createEvent(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "master_event_id" => "required",
                "event_unit" => "required|array",
                "event_unit.*.unit_id" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);



            $createEvent = Event::create([
                "master_event_id" => $request->master_event_id,
                "event_status" => EventStatusEnum::create,
                "event_description" => $request->event_description,
                "event_number" => GenerateNumber::generate("EVENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "event", "event_number")
            ]);


            // add unit ke event list unit

            foreach ($request->event_unit as $item) {
                if ($this->checkUnitIsHaveNEQ($item['unit_id'])) {
                    DB::rollBack();
                    return ResponseFormatter::error("Unit " . $item['unit_id'] . " sudah memiliki neq / event, harap di-return dahulu untuk tersedia di transfer ke event", "Bad request !", 400);
                }
                $createEventUnit[] = EventListUnit::create([
                    "event_id" => $createEvent->event_id,
                    "unit_id" => $item["unit_id"]
                ]);
                // check unit apakah sudah ada di event

                // $checkUnit = Unit::where("unit_id", $item["unit_id"])->with("event_list_unit.event.master_event")->first();

                // if (!isset($checkUnit->event_list_unit->event->master_event->master_event_id)) {
                //     $createEventUnit[] = EventListUnit::create([
                //         "event_id" => $createEvent->event_id,
                //         "unit_id" => $item["unit_id"]
                //     ]);
                // } else {
                //     DB::rollBack();
                //     return ResponseFormatter::error("Unit dengan ID {$item['unit_id']} sudah terdaftar dalam event.", "Bad Request", 400);
                // }
            }

            // add event log
            EventLog::create([
                "event_id" => $createEvent->event_id,
                "user_id" => $user->user_id,
                "event_log_action" => EventStatusEnum::create,
                "event_log_note" => "Create new event"
            ]);


            DB::commit();


            $data = [
                "event" => $createEvent,
            ];

            if (!empty($createEventUnit)) {
                $data["event_unit"] =  $createEventUnit;
            }
            return ResponseFormatter::success($data, "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    private function checkUnitIsHaveNEQ($unit_id)
    {
        $user = Auth::user();
        $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);


        if (!isset($unit_id)) {
            return ResponseFormatter::error("unit id not found", "bad request", 400);
        }
        $getUnit = Unit::latest()
            ->with(["neq_unit"])
            ->where("unit_id", $unit_id)
            ->where("dealer_id", $getDealerSelected->dealer_id)->first();

        return isset($getUnit->unit_location_status);
    }
}
