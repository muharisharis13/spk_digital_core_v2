<?php

namespace App\Http\Controllers\API;

use App\Enums\MotorStatusEnum;
use App\Enums\ShippingOrderStatusEnum;
use App\Enums\UnitLogActionEnum;
use App\Enums\UnitLogStatusEnum;
use App\Enums\UnitStatusEnum;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Motor;
use App\Models\ShippingOrder;
use App\Models\Unit;
use App\Models\UnitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShippingOrderController extends Controller
{
    //
    // public function __construct()
    // {
    //     $this->middleware('auth:api',['except' => ['login']]);
    // }

    public function updateTerimaUnitShippingOrder(Request $request, $unit_id)
    {
        try {

            DB::beginTransaction();



            $user = Auth::user();

            $currentDate = Carbon::now()->format('Y-m-d');

            $updateUnit = Unit::where("unit_id", $unit_id)->update([
                "unit_status" => UnitStatusEnum::on_hand,
                "unit_note" => $request->unit_note,
                "unit_received_date" => $currentDate
            ]);
            $getUnit = Unit::where("unit_id", $unit_id)->with(["shipping_order", "dealer", "event"])->first();

            UnitLog::create([
                "unit_id" => $unit_id,
                "user_id" => $user->user_id,
                "unit_log_number" => isset($getUnit->shipping_order->shipping_order_number) ? $getUnit->shipping_order->shipping_order_number : null,
                "unit_log_dealer_name" => isset($getUnit->dealer->dealer_name) ? $getUnit->dealer->dealer_name : null,
                "unit_log_dealer_neq_name" => isset($getUnit->dealer_neq->dealer_neq_name) ? $getUnit->dealer_neq->dealer_neq_name : null,
                "unit_log_event_name" => isset($getUnit->event->event_name) ? $getUnit->event->event_name : null,
                "unit_log_action" => UnitLogActionEnum::terima_unit,
                "unit_log_status" => UnitLogStatusEnum::ON_HAND
            ]);

            $getUnitByShippingOrderId = Unit::where("shipping_order_id", $getUnit->shipping_order_id)->get();

            $allOnHand = true;
            foreach ($getUnitByShippingOrderId as $unitLoop) {
                if ($unitLoop->unit_status !== 'on_hand') {
                    $allOnHand = false;
                    break; // Jika ada satu unit yang tidak on_hand, hentikan loop
                }
            }

            if ($allOnHand) {
                ShippingOrder::where("shipping_order_id", $getUnit->shipping_order_id)->update([
                    "shipping_order_status" => ShippingOrderStatusEnum::complete
                ]);
            } else {
                ShippingOrder::where("shipping_order_id", $getUnit->shipping_order_id)->update([
                    "shipping_order_status" => ShippingOrderStatusEnum::partly
                ]);
            }



            DB::commit();

            if ($updateUnit === 0) {

                return ResponseFormatter::error("Unit Not Found !", "Bad Request", 400);
            } else {
                return ResponseFormatter::success($updateUnit, "Successfully updated !");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailShippingOrder(Request $request, $shipping_order_id)
    {
        try {
            $getDetailShippingOrder = ShippingOrder::with(["dealer", "unit.motor"])
                ->where("shipping_order_id", $shipping_order_id)
                ->first();

            return
                ResponseFormatter::success($getDetailShippingOrder);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListShippingOrder(Request $request)
    {
        try {


            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);



            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $shipping_order_status = $request->input('shipping_order_status');
            $startDate = $request->input('start_date_shipping');
            $endDate = $request->input('end_date_shipping');
            $searchQuery = $request->input('q');


            $getListShippingOrder = ShippingOrder::with(["dealer", "unit.motor"])
                ->where(function ($query) use ($searchQuery) {
                    $query->where('shipping_order_status', 'LIKE', "%$searchQuery%")
                        ->orWhere('shipping_order_number', 'LIKE', "%$searchQuery%")
                        // Add more columns here as needed
                        ->orWhere('shipping_order_delivery_number', 'LIKE', "%$searchQuery%")
                        ->orWhere('shipping_order_shipping_date', 'LIKE', "%$searchQuery%")
                        ->orWhere('shipping_order_status', 'LIKE', "%$searchQuery%");
                })
                ->where('shipping_order_status', 'LIKE', "%$shipping_order_status%")
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('shipping_order_shipping_date', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('shipping_order_shipping_date', '<=', $endDate);
                })
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->withCount([
                    'unit as unit_received_total' => function ($query) {
                        $query->selectRaw('count(*)')
                            ->where('unit_status', 'on_hand');
                    },
                    'unit as unit_total' => function ($query) {
                        $query->selectRaw('count(*)');
                    },
                ])
                ->orderBy($sortBy, $sortOrder)
                ->paginate($limit);

            return
                ResponseFormatter::success($getListShippingOrder);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }



    public function sycnShippingOrder(Request $request, $city)
    {
        ini_set('max_execution_time', 0);
        try {

            $validator  = Validator::make($request->all(), [
                "targetDate" => "required:date"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            // update data depack dari pusat itu setiap 1 jam sekali
            // format "20240221"
            $data = [
                "targetDate" => date("Ymd", strtotime($request->targetDate))
            ];


            // perulangan untuk input data ke unit_order

            DB::beginTransaction();


            switch ($city) {
                case 'mdn':
                    $shipmentDataDealerMDN = Http::post('https://yimmdpackwebapi.ymcapps.net/dpackweb/api/v1/unitshipment?dealerCd=FA0601&accessToken=MGFkNjQ2MGJhZmZhZDM1ZGIyM2I4NjZhYWZjM2M0YmFhY2I3NDBmNw==', $data);
                    $shipmentDataDealerMDN = $shipmentDataDealerMDN->json();
                    if ($shipmentDataDealerMDN["code"] == 2001) {
                        return ResponseFormatter::error("But there are no data.");
                    }
                    foreach ($shipmentDataDealerMDN["data"] as $itemHeader) {

                        foreach ($itemHeader["detail-datas"] as $itemDetail) {
                            // check dealer
                            $checkDealer = $this->checkDealer($itemHeader["h.customer_code_"]);
                            // check motor
                            $checkMotorByMotorName = $this->checkMotorByMotorName($itemDetail["d.model_name_"]);

                            // jika dealer ada di database maka update datanya
                            if (isset($checkDealer->dealer_id)) {
                                $shippingOrder =  ShippingOrder::firstOrCreate([
                                    "shipping_order_delivery_number" => $itemHeader["h.delivery_no_"]
                                ], [
                                    "shipping_order_number" => $itemDetail["d.sales_order_no_"],
                                    "shipping_order_delivery_number" => $itemHeader["h.delivery_no_"],
                                    "shipping_order_shipping_date" => $this->formatDate($itemHeader["h.shipping_date_"]),
                                    "dealer_id" => $checkDealer->dealer_id,
                                    "shipping_order_status" => ShippingOrderStatusEnum::transit
                                ]);

                                if (isset($checkMotorByMotorName->motor_id)) {

                                    foreach ($itemDetail["subdetail-datas"] as $itemSubDetail) {
                                        Unit::firstOrCreate(
                                            [
                                                "unit_frame" => $itemSubDetail["s.frame_no_"]
                                            ],
                                            [
                                                "unit_color" => $itemDetail["d.color_"],
                                                "unit_frame" => $itemSubDetail["s.frame_no_"],
                                                "unit_engine" => $itemSubDetail["s.engine_no_"],
                                                "shipping_order_id" => $shippingOrder->shipping_order_id,
                                                "motor_id" => $checkMotorByMotorName->motor_id,
                                                "unit_code" => 0,
                                            ]
                                        );
                                    }
                                } else {

                                    $createMotor = Motor::create([
                                        "motor_id" => Str::uuid(),
                                        // "motor_code" => $itemDetail["d.model_code_"],
                                        "motor_name" => $itemDetail["d.model_name_"],
                                        "motor_status" => MotorStatusEnum::ACTIVE
                                    ]);

                                    foreach ($itemDetail["subdetail-datas"] as $itemSubDetail) {
                                        Unit::firstOrCreate(
                                            [
                                                "unit_frame" => $itemSubDetail["s.frame_no_"]
                                            ],
                                            [
                                                "unit_color" => $itemDetail["d.color_"],
                                                "unit_frame" => $itemSubDetail["s.frame_no_"],
                                                "unit_engine" => $itemSubDetail["s.engine_no_"],
                                                "shipping_order_id" => $shippingOrder->shipping_order_id,
                                                "motor_id" => $createMotor->motor_id,
                                                "unit_code" => 0,
                                            ]
                                        );
                                    }
                                }
                            }
                            // jika dealer tidak ada di database maka create datanya
                            else {
                                $createDealer = Dealer::create([
                                    "dealer_id" => Str::uuid(),
                                    "dealer_name" => $itemHeader["h.customer_name_"],
                                    "dealer_code" => $itemHeader["h.customer_code_"],
                                    "dealer_type" => isset($itemHeader["h.consignee_"]) && strpos($itemHeader["h.consignee_"], "ALFA SCORPII") !== false ? 'mds' : 'independent'
                                ]);



                                $shippingOrder =  ShippingOrder::create([
                                    "shipping_order_id" => Str::uuid(),
                                    "shipping_order_number" => $itemDetail["d.sales_order_no_"],
                                    "shipping_order_delivery_number" => $itemHeader["h.delivery_no_"],
                                    "shipping_order_shipping_date" => $this->formatDate($itemHeader["h.shipping_date_"]),
                                    "dealer_id" => $createDealer->dealer_id,
                                    "shipping_order_status" => ShippingOrderStatusEnum::transit
                                ]);

                                if (isset($checkMotorByMotorName->motor_id)) {

                                    foreach ($itemDetail["subdetail-datas"] as $itemSubDetail) {
                                        Unit::firstOrCreate(
                                            [
                                                "unit_frame" => $itemSubDetail["s.frame_no_"]
                                            ],
                                            [
                                                "unit_color" => $itemDetail["d.color_"],
                                                "unit_frame" => $itemSubDetail["s.frame_no_"],
                                                "unit_engine" => $itemSubDetail["s.engine_no_"],
                                                "shipping_order_id" => $shippingOrder->shipping_order_id,
                                                "motor_id" => $checkMotorByMotorName->motor_id,
                                                "unit_code" => 0,
                                            ]
                                        );
                                    }
                                } else {

                                    $createMotor = Motor::create([
                                        "motor_id" => Str::uuid(),
                                        // "motor_code" => $itemDetail["d.model_code_"],
                                        "motor_name" => $itemDetail["d.model_name_"],
                                        "motor_status" => MotorStatusEnum::ACTIVE
                                    ]);

                                    foreach ($itemDetail["subdetail-datas"] as $itemSubDetail) {
                                        Unit::firstOrCreate(
                                            [
                                                "unit_frame" => $itemSubDetail["s.frame_no_"]
                                            ],
                                            [
                                                "unit_color" => $itemDetail["d.color_"],
                                                "unit_frame" => $itemSubDetail["s.frame_no_"],
                                                "unit_engine" => $itemSubDetail["s.engine_no_"],
                                                "shipping_order_id" => $shippingOrder->shipping_order_id,
                                                "motor_id" => $createMotor->motor_id,
                                                "unit_code" => 0,
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                    DB::commit();



                    return ResponseFormatter::success($shipmentDataDealerMDN["data"]);
                    break;
                default:
                    return ResponseFormatter::error('parameter invalid', "Bad request", 400);
                    break;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    protected function checkMotorByMotorName($motor_name)
    {
        $response = Motor::where("motor_name", $motor_name)->get()->first();


        return $response;
    }

    protected function checkMotor($model_code)
    {
        $response = Motor::where("motor_code", $model_code)->get()->first();


        return $response;
    }

    protected function checkDealer($dealer_code)
    {
        $response = Dealer::where("dealer_code", $dealer_code)->get()
            ->first();

        return $response;
    }

    protected function formatDate($date)
    {
        $date = Carbon::createFromFormat('Ymd', $date);
        $formattedDate = $date->format('Y-m-d'); // Menghasilkan: 2024-02-15

        return $formattedDate;
    }
}