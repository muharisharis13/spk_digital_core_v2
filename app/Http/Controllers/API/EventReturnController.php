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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventReturnController extends Controller
{
    //


    public function createEventReturn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "event_id" => "required",
                "event_return_unit.*.unit_id" => "required",

            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $createEventReturn = EventReturn::create([
                "event_id" => $request->event_id,
                "event_return_number" => GenerateNumber::generate("EVENT-RETURN", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "event_returns", "event_return_number"),
                "event_return_status" => EventReturnStatusEnum::create,
                "dealer_id" => $getDealerSelected->dealer_id
            ]);

            foreach ($request->event_return_unit as $item) {
                $createEventReturnUnit[] = EventReturnListUnit::create([
                    'event_return_id' => $createEventReturn->event_return_id,
                    "unit_id" => $item["unit_id"]
                ]);
            }

            // create log event return
            $eventReturnLog = EventReturnLog::create([
                "event_return_id" => $createEventReturn->event_return_id,
                "user_id" => $user->user_id,
                "event_return_log_action" => EventReturnStatusEnum::create,
                "event_return_log_note" => "Create Event Return"
            ]);


            $data = [
                "event_return" => $createEventReturn,
                "event_return_unit" => $createEventReturnUnit,
                "event_return_log" => $eventReturnLog
            ];

            return ResponseFormatter::success($data, "Successfully created");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getAllUnitEvent(Request $request)
    {
        try {

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);


            $getAllUnitEvent = EventListUnit::latest();

            $getAllUnitEvent = $getAllUnitEvent->with(["event"])
                ->whereHas("repair", function ($query) use ($getDealer) {
                    $query->where("dealer_id", $getDealer->dealer_id)
                        ->where("event_status", EventStatusEnum::approve);
                });




            $getAllUnitEvent = $getAllUnitEvent->get();

            return ResponseFormatter::success($getAllUnitEvent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
