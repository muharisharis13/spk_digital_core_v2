<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\NeqUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NeqReturnController extends Controller
{
    //

    public function getAllUnitNeq(Request $request, $neq_id)
    {
        try {

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);
            $search = $request->input("q");


            $getAllUnitNeq = NeqUnit::latest();

            $getAllUnitNeq = $getAllUnitNeq->with(["unit.motor", "neq" => function ($query) use ($getDealer) {
                $query->where("dealer_id", $getDealer->dealer_id);
            }])
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
}
