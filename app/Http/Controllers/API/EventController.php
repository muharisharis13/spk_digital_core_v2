<?php

namespace App\Http\Controllers\API;

use App\Enums\EventStatusEnum;
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
                "event_status" => "required|in:cancel,request"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailEvent = Event::where("event_id", $event_id)->first();

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
            $getDetailEvent = Event::where("event_id", $event_id)->with(["dealer", "event_unit.unit.motor"])->first();

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


            $getPaginateEvent = Event::latest()->with(["dealer", "event_unit.unit.motor"])
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->where(function ($query) use ($searchQuery) {
                    $query->where('event_name', 'LIKE', "%$searchQuery%")
                        ->orWhere('event_status', 'LIKE', "%$searchQuery%")
                        ->orWhere('event_address', 'LIKE', "%$searchQuery%")
                        ->orWhere('event_number', 'LIKE', "%$searchQuery%");
                })
                ->when($date, function ($query) use ($date) {
                    return $query->whereDate('event_start', 'LIKE', "%$date%");
                })
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
            $getDetailEvent = Event::where("event_id", $event_id)->first();

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
                "event_start" => "required|date",
                // "event_end" => "required|date",
                "event_name" => "required",
                "event_address" => "required",
                "event_unit.*.unit_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailEvent = Event::where("event_id", $event_id)
                ->with(["dealer", "event_unit.unit.motor"])
                ->first();

            if (!isset($getDetailEvent->event_id)) {
                return ResponseFormatter::error("Event Not Found !", "Bad Request", 400);
            }


            foreach ($request->event_unit as $item) {
                if (!isset($item["event_unit_list_id"])) {
                    $createEventUnit[] = EventListUnit::create([
                        "event_id" => $getDetailEvent->event_id,
                        "unit_id" => $item["unit_id"]
                    ]);
                }
            }

            // update form event
            $getDetailEvent->update([
                "event_name" => $request->event_name,
                "event_address" => $request->event_address,
                "event_start" => $request->event_start,
                "event_description" => $request->event_description,
            ]);
            $user = Auth::user();

            // add event log
            EventLog::create([
                "event_id" => $getDetailEvent->event_id,
                "user_id" => $user->user_id,
                "event_log_action" => EventStatusEnum::create,
                "event_log_note" => "Update Event"
            ]);

            DB::commit();


            $data = [
                "event" => $getDetailEvent,
                "event_unit" => $createEventUnit
            ];


            return ResponseFormatter::success($data, "Successfully updated !");
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
                $createEventUnit[] = EventListUnit::create([
                    "event_id" => $createEvent->event_id,
                    "unit_id" => $item["unit_id"]
                ]);
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
                "event_unit" => $createEventUnit
            ];
            return ResponseFormatter::success($data, "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
