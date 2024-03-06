<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerNeq;
use App\Models\Motor;
use Illuminate\Http\Request;

class Master extends Controller
{
    //

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

    public function getListDealerMDS(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");

            if ($paginate === "true") {
                $getListAllMDSMD = Dealer::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_code", "LIKE", "%$searchQuery%");
                })->paginate($limit);
            } else {
                $getListAllMDSMD = Dealer::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_code", "LIKE", "%$searchQuery%");
                })->get();
            }

            return ResponseFormatter::success($getListAllMDSMD);
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
