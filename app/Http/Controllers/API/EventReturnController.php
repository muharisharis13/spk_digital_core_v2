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


    public function getAllUnitEvent(Request $request, $master_event_id)
    {
        try {

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);


            $getAllUnitEvent = EventListUnit::latest();

            $getAllUnitEvent = $getAllUnitEvent->with(["event.master_event", "event" => function ($query) use ($getDealer) {
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
                });




            $getAllUnitEvent = $getAllUnitEvent->get();

            return ResponseFormatter::success($getAllUnitEvent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
