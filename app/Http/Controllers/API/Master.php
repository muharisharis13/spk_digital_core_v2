<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerByUser;
use App\Models\DealerNeq;
use App\Models\MainDealer;
use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Master extends Controller
{
    //

    public function GelistPaginateMainDealer(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");

            if ($paginate === "true") {
                $getListMainDealer = MainDealer::where(function ($query) use ($searchQuery) {
                    $query->where("main_dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("main_dealer_identifier", "LIKE", "%$searchQuery%");
                })->paginate($limit);
            } else {
                $getListMainDealer = MainDealer::where(function ($query) use ($searchQuery) {
                    $query->where("main_dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("main_dealer_identifier", "LIKE", "%$searchQuery%");
                })->get();
            }

            return ResponseFormatter::success($getListMainDealer);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function getListPaginateMotor(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");

            if ($paginate === "true") {
                $getListMotor = Motor::where(function ($query) use ($searchQuery) {
                    $query->where("motor_name", "LIKE", "%$searchQuery%")
                        ->orWhere("motor_code", "LIKE", "%$searchQuery%");
                })->paginate($limit);
            } else {
                $getListMotor = Motor::where(function ($query) use ($searchQuery) {
                    $query->where("motor_name", "LIKE", "%$searchQuery%")
                        ->orWhere("motor_code", "LIKE", "%$searchQuery%");
                })->get();
            }

            return ResponseFormatter::success($getListMotor);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListLocationByUserLogin(Request $request)
    {
        try {
            $user = Auth::user();

            $getDealerByUser = DealerByUser::where("user_id", $user->user_id)
                ->with(["dealer"])
                ->where("isSelected", 1)
                ->first();

            $dealerNeqList = DealerNeq::where("dealer_id", $getDealerByUser->dealer_id)->get();

            $data = [
                "dealer" => $getDealerByUser,
                "dealer_neq" => $dealerNeqList
            ];

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListDealerMDS(Request $request)
    {
        try {

            $getListAllDealer = DealerByUser::with(["dealer"])
                ->get();

            return ResponseFormatter::success($getListAllDealer);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListDealerNeq(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");

            if ($paginate === "true") {
                $getListAllMDSMD = DealerNeq::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_neq_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_neq_code", "LIKE", "%$searchQuery%");
                })->paginate($limit);
            } else {
                $getListAllMDSMD = DealerNeq::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_neq_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_neq_code", "LIKE", "%$searchQuery%");
                })->get();
            }

            return ResponseFormatter::success($getListAllMDSMD);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
