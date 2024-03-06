<?php

namespace App\Http\Controllers\API;

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
            $searchQuery = $request->input('q');


            $getListPaginateUnit = Unit::with(["motor", "shipping_order.dealer"])
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

                ->when($date, function ($query) use ($date) {
                    return $query->whereDate('unit_received_date', 'LIKE', "%$date%");
                })
                ->paginate($limit);


            return ResponseFormatter::success($getListPaginateUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
