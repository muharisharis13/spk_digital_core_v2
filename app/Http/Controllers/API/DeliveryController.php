<?php

namespace App\Http\Controllers\API;

use App\Enums\DeliveryLogActionEnum;
use App\Enums\DeliveryStatusEnum;
use App\Enums\DeliveryTypeEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\delivery;
use App\Models\deliveryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    //

    public function DetailDelivery(Request $request, $delivery_id)
    {
        try {
            $getDetailDelivery = Delivery::with(["repair.main_dealer", "repair.repair_unit", "dealer"])
                ->where("delivery_id", $delivery_id)
                ->first();

            return ResponseFormatter::success($getDetailDelivery);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function GetListPagianteDelivery(Request $request)
    {
        try {
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $delivery_status = $request->input("delivery_status");
            $searchQuery = $request->input('q');
            $sortBy = $request->input('sort_by', 'repair_id');
            $sortOrder = $request->input('sort_order', 'asc');
            $delivery_type = $request->input("delivery_type");

            $getPaginateDelivery = Delivery::with(["repair.main_dealer", "repair.repair_unit", "dealer"])
                ->where(function ($query) use ($searchQuery) {
                    $query->where('delivery_driver_name', 'LIKE', "%$searchQuery%")
                        ->orWhere('delivery_number', 'LIKE', "%$searchQuery%")
                        ->orWhereHas("repair", function ($queryRepair) use ($searchQuery) {
                            return $queryRepair->where("repair_number", 'LIKE', "%$searchQuery%");
                        })
                        ->orWhereHas("repair", function ($queryRepair) use ($searchQuery) {
                            return $queryRepair->whereHas("main_dealer", function ($queryMainDealer) use ($searchQuery) {
                                return $queryMainDealer->where("main_dealer_name", "LIKE", "%$searchQuery%");
                            });
                        });
                })
                ->where("delivery_type", $delivery_type)
                ->where("delivery_status", "LIKE", "%$delivery_status%")
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->orderBy($sortBy, $sortOrder)
                ->paginate($limit);

            return ResponseFormatter::success($getPaginateDelivery);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function CreateDelivery(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "delivery_driver_name" => "required",
                "delivery_vehicle" => "required",
                "repair_id" => "required",
                "delivery_type" => "required|in:repair,retur,event,spk"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);


            DB::beginTransaction();
            $createDelivery = delivery::create([
                "delivery_driver_name" => $request->delivery_driver_name,
                "delivery_vehicle" => $request->delivery_vehicle,
                "delivery_note" => $request->delivery_note,
                "delivery_completeness" => $request->delivery_completeness,
                "delivery_number" => GenerateNumber::generate("TEMP-DELIVERY", GenerateAlias::generate($getDealer->dealer->dealer_name), "deliveries", "delivery_number"),
                "dealer_id" => $getDealer->dealer_id,
                "repair_id" => $request->repair_id,
                "delivery_status" => DeliveryStatusEnum::create,
                "delivery_type" => $request->delivery_type
            ]);

            $createDeliveryLog = deliveryLog::create([
                "user_id" => $user->user_id,
                "delivery_log_action" => DeliveryLogActionEnum::create,
                "delivery_note" => "Create Delivery",
                "delivery_id" => $createDelivery->delivery_id
            ]);

            DB::commit();

            $data = [
                "delivery" => $createDelivery,
                "delivery_log" => $createDeliveryLog
            ];

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
