<?php

namespace App\Http\Controllers\API;

use App\Enums\EventReturnStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\EventReturn;
use App\Models\EventReturnListUnit;
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
            ]);

            foreach ($request->event_return_unit as $item) {
                $createEventReturnUnit[] = EventReturnListUnit::create([
                    'event_return_id' => $createEventReturn->event_return_id,
                    "unit_id" => $item["unit_id"]
                ]);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
