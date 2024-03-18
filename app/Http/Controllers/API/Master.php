<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerByUser;
use App\Models\DealerNeq;
use App\Models\MainDealer;
use App\Models\MasterEvent;
use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Master extends Controller
{
    //

    public function getEventPaginate(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListEvent = MasterEvent::with(["event.event_unit"])->where(function ($query) use ($searchQuery) {
                    $query->where("master_event_name", "LIKE", "%$searchQuery%")
                        ->orWhere("master_event_location", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListEvent = MasterEvent::with(["event.event_unit"])->where(function ($query) use ($searchQuery) {
                    $query->where("master_event_name", "LIKE", "%$searchQuery%")
                        ->orWhere("master_event_location", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListEvent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createEvent(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "master_event_date" => "required|date",
                "master_event_name" => "required",
                "master_event_location" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $createEvent = MasterEvent::create([
                "master_event_date" => $request->master_event_date,
                "master_event_name" => $request->master_event_name,
                "master_event_location" => $request->master_event_location,
                "dealer_id" => $getDealerByUserSelected->dealer_id,
                "master_event_note" => $request->master_event_note
            ]);

            DB::commit();

            return ResponseFormatter::success($createEvent, "Successfully created event !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function GelistPaginateMainDealer(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListMainDealer = MainDealer::where(function ($query) use ($searchQuery) {
                    $query->where("main_dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("main_dealer_identifier", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListMainDealer = MainDealer::where(function ($query) use ($searchQuery) {
                    $query->where("main_dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("main_dealer_identifier", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
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
            $sortBy = $request->input('sort_by', 'motor_name');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListMotor = Motor::where(function ($query) use ($searchQuery) {
                    $query->where("motor_name", "LIKE", "%$searchQuery%")
                        ->orWhere("motor_code", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListMotor = Motor::where(function ($query) use ($searchQuery) {
                    $query->where("motor_name", "LIKE", "%$searchQuery%")
                        ->orWhere("motor_code", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
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
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            $getDealerByUser = DealerByUser::where("user_id", $user->user_id)
                ->with(["dealer"])
                ->where("isSelected", 1)
                ->orderBy($sortBy, $sortOrder)
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
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            $getListAllDealer = DealerByUser::with(["dealer"])
                ->orderBy($sortBy, $sortOrder)->get();

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
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListAllMDSMD = DealerNeq::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_neq_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_neq_code", "LIKE", "%$searchQuery%");
                })
                    ->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListAllMDSMD = DealerNeq::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_neq_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_neq_code", "LIKE", "%$searchQuery%");
                })
                    ->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListAllMDSMD);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
