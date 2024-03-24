<?php

namespace App\Http\Controllers\API;

use App\Enums\EventStatusEnum;
use App\Enums\NeqStatusEnum;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitContoller extends Controller
{
    //

    public function getDetailUnit(Request $request, $unit_id)
    {
        try {
            $getDetailUnit = Unit::with(["motor", "shipping_order.dealer", "unit_log.user"])
                ->where("unit_id", $unit_id)->first();

            return ResponseFormatter::success($getDetailUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function getListPaginateUnit(Request $request)
    {
        try {

            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $date = $request->input('date');
            $motor = $request->input("motor");
            $location = $request->input("location");
            $unit_status = $request->input("unit_status");
            $unit_frame = $request->input("unit_frame");
            $motor_id = $request->input("motor_id");
            $searchQuery = $request->input('q');
            $sortBy = $request->input('sort_by', 'unit_id');
            $sortOrder = $request->input('sort_order', 'asc');
            $has_event = $request->input("has_event", "true");


            $getListPaginateUnit = Unit::with(["motor", "shipping_order.dealer", "event_list_unit", "event_list_unit.event.master_event", "neq_unit.neq", "neq_unit"])
                ->whereNotNull("unit_status")
                ->where(function ($query) use ($searchQuery) {
                    $query->where('unit_color', 'LIKE', "%$searchQuery%")
                        ->orWhere('unit_engine', 'LIKE', "%$searchQuery%")
                        ->orWhere('unit_status', 'LIKE', "%$searchQuery%")
                        ->orWhere('unit_frame', 'LIKE', "%$searchQuery%");
                })
                ->where(function ($query) use ($location) {
                    $query->where('dealer_id', "LIKE", "%$location%")
                        ->orWhere('dealer_neq_id', "LIKE", "%$location%")
                        ->orWhereNull('dealer_id')
                        ->orWhereNull('dealer_neq_id');
                })
                ->whereHas("shipping_order", function ($query) use ($getDealerByUserSelected) {
                    $query->where("dealer_id", $getDealerByUserSelected->dealer_id);
                })
                ->whereHas("motor", function ($query) use ($motor) {
                    $query->where("motor_name", "LIKE", "%$motor%");
                })
                ->where("unit_status", "LIKE", "%$unit_status%")
                ->where("motor_id", "LIKE", "%$motor_id%")
                ->where("unit_frame", "LIKE", "%$unit_frame%")
                ->when($date, function ($query) use ($date) {
                    return $query->whereDate('unit_received_date', 'LIKE', "%$date%");
                })
                ->whereHas("event_list_unit", function ($query) {
                    $query->whereHas("event", function ($query) {
                        $query->where("event_status", EventStatusEnum::approve)->where("is_return", false);
                    });
                })
                ->whereHas("neq_unit", function ($query) {
                    $query->whereHas("neq", function ($query) {
                        $query->where("neq_status", NeqStatusEnum::approve)->where("is_return", false);
                    });
                })
                ->orderBy($sortBy, $sortOrder);

            // if ($has_event === "true") {
            //     $getListPaginateUnit->whereHas("event_list_unit", function ($query) {
            //         $query->whereNotNull("event_id");
            //     });
            // } elseif ($has_event === "false") {
            //     $getListPaginateUnit->whereDoesntHave("event_list_unit");
            // }

            $getListPaginateUnit = $getListPaginateUnit->paginate($limit);


            return ResponseFormatter::success($getListPaginateUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
