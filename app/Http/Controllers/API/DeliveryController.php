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
use App\Models\DeliveryEvent;
use App\Models\deliveryLog;
use App\Models\DeliveryRepair;
use App\Models\DeliveryRepairReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    //

    public function deleteDelivery(Request $request, $delivery_id)
    {
        try {
            $getDetailDelivery = delivery::where("delivery_id", $delivery_id)->first();

            if ($getDetailDelivery->delivery_status === DeliveryStatusEnum::request) {
                return ResponseFormatter::error("Cannot delete delivery because delivery status request");
            }

            DB::beginTransaction();

            $getDetailDelivery->delete();
            DB::commit();

            return ResponseFormatter::success($getDetailDelivery, "Successfully Deleted Delivery");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function changeStatusDelivery(Request $request, $delivery_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "delivery_status" => "required|in:approve,cancel"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();

            $updateStatusDelivery = Delivery::where("delivery_id", $delivery_id)->first();

            // update log
            $createDeliveryLog = deliveryLog::create([
                "delivery_id" => $updateStatusDelivery->delivery_id,
                "user_id" => $user->user_id,
                "delivery_log_action" =>  $request->delivery_status,
                "delivery_note" => "Change status to " . $request->delivery_status
            ]);

            $updateStatusDelivery->update([
                "delivery_status" => $request->delivery_status,
                'delivery_number' => str_replace('TEMP-', "", $updateStatusDelivery->delivery_number)
            ]);

            DB::commit();

            $data = [
                "delivery" => $updateStatusDelivery,
                "delivery_log" => $createDeliveryLog
            ];

            return ResponseFormatter::success($data, "Success Change Status !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function DetailDelivery(Request $request, $delivery_id)
    {
        try {
            $getDetailDelivery = Delivery::with(["delivery_repair.repair.main_dealer", "delivery_repair.repair.repair_unit" => function ($query) {
                $query->where("is_return", false);
            }, "delivery_repair.repair.repair_unit.unit.motor", "dealer", "event.event_unit.unit.motor", "delivery_log" => function ($query) {

                $query->latest();
            }, "delivery_repair_return.repair_return"])
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
            $sortBy = $request->input('sort_by', 'delivery_id');
            $sortOrder = $request->input('sort_order', 'asc');
            $delivery_type = $request->input("delivery_type");
            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);



            $getPaginateDelivery = Delivery::latest();




            $getPaginateDelivery->with(["dealer"])
                ->where(function ($query) use ($searchQuery) {
                    $query->where('delivery_driver_name', 'LIKE', "%$searchQuery%")
                        ->orWhere('delivery_number', 'LIKE', "%$searchQuery%")
                        ->orWhereHas("delivery_repair", function ($delivery_repair) use ($searchQuery) {
                            return $delivery_repair->whereHas("repair", function ($queryRepair) use ($searchQuery) {
                                $queryRepair->where("repair_number", 'LIKE', "%$searchQuery%")
                                    ->orWhereHas("main_dealer", function ($queryMainDealer) use ($searchQuery) {
                                        $queryMainDealer->where("main_dealer_name", 'LIKE', "%$searchQuery%");
                                    });
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
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->orderBy($sortBy, $sortOrder);

            if ($delivery_type === 'repair') {
                $getPaginateDelivery->with(["delivery_repair.repair.main_dealer", "delivery_repair.repair.repair_unit"]);
            } else if ($delivery_type === 'repair_return') {
                $getPaginateDelivery->with(["delivery_repair_return.repair_return"]);
            } else if ($delivery_type === 'event') {
                $getPaginateDelivery->with(["delivery_event.event.master_event", "delivery_event.event.event_unit"]);
            }

            $getPaginateDelivery = $getPaginateDelivery->paginate($limit);

            return ResponseFormatter::success($getPaginateDelivery);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateDelivery(Request $request, $delivery_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "delivery_driver_name" => "required",
                "delivery_vehicle" => "required",
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            DB::beginTransaction();

            $getDetailDelivery = delivery::where("delivery_id", $delivery_id)->first();

            $user = Auth::user();

            if ($getDetailDelivery->delivery_status === DeliveryStatusEnum::request) {
                return ResponseFormatter::error("Cannot update delivery because delivery status request");
            }

            $getDetailDelivery->update([
                "delivery_driver_name" => $request->delivery_driver_name,
                "delivery_vehicle" => $request->delivery_vehicle,
                "delivery_note" => $request->delivery_note,
                "delivery_completeness" => $request->delivery_completeness,
            ]);

            $createLogDelivery = deliveryLog::create([
                "user_id" => $user->user_id,
                "delivery_log_action" => DeliveryLogActionEnum::create,
                "delivery_note" => "update Delivery",
                "delivery_id" => $getDetailDelivery->delivery_id
            ]);

            DB::commit();


            $data = [
                "delivery" => $getDetailDelivery,
                "delivery_log" => $createLogDelivery
            ];

            return ResponseFormatter::success($data, "Successfully updated !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function CreateDelivery(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "delivery_driver_name" => "required",
                "delivery_vehicle" => "required",
                // "repair_id" => "required",
                // "delivery_type" => "required|in:repair,retur,event,spk"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);


            $deliveryType = "";
            if ($request->repair_return_id) {
                $deliveryType = DeliveryTypeEnum::repair_return;
            } elseif ($request->repair_id) {
                $deliveryType = DeliveryTypeEnum::repair;
            } elseif ($request->event_id) {
                $deliveryType = DeliveryTypeEnum::event;
            }

            DB::beginTransaction();
            $createDelivery = delivery::create([
                "delivery_driver_name" => $request->delivery_driver_name,
                "delivery_vehicle" => $request->delivery_vehicle,
                "delivery_note" => $request->delivery_note,
                "delivery_completeness" => $request->delivery_completeness,
                "delivery_number" => GenerateNumber::generate("TEMP-DELIVERY", GenerateAlias::generate($getDealer->dealer->dealer_name), "deliveries", "delivery_number"),
                "dealer_id" => $getDealer->dealer_id,
                // "repair_id" => $request->repair_id,
                // "event_id" => $request->event_id,
                "delivery_status" => DeliveryStatusEnum::create,
                "delivery_type" => $deliveryType
            ]);

            $createDeliveryLog = deliveryLog::create([
                "user_id" => $user->user_id,
                "delivery_log_action" => DeliveryLogActionEnum::create,
                "delivery_note" => "Create Delivery",
                "delivery_id" => $createDelivery->delivery_id
            ]);

            $data = [
                "delivery" => $createDelivery,
                "delivery_log" => $createDeliveryLog
            ];


            if (isset($request->repair_id)) {
                $createDeliveryRepair = DeliveryRepair::create([
                    "delivery_id" => $createDelivery->delivery_id,
                    "repair_id" => $request->repair_id
                ]);

                $data['delivery_repair'] = $createDeliveryRepair;
            } else if (isset($request->repair_return_id)) {
                $createDeliveryRepair = DeliveryRepairReturn::create([
                    "delivery_id" => $createDelivery->delivery_id,
                    "repair_return_id" => $request->repair_return_id
                ]);
                $data['delivery_repair'] = $createDeliveryRepair;
            } else if (isset($request->event_id)) {
                $createDeliveryEvent = DeliveryEvent::create([
                    "event_id" => $request->event_id,
                    "delivery_id" => $createDelivery->delivery_id
                ]);
                $data['delivery_event'] = $createDeliveryEvent;
            }



            DB::commit();




            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
