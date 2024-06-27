<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ApiSecret;
use App\Models\Color;
use App\Models\Dealer;
use App\Models\Motor;
use App\Models\ShippingOrder;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ShippingOrderController2 extends Controller
{
    //

    public function syncShippingOrderMD(Request $request)
    {
        ini_set('max_execution_time', 0);
        try {


            $validator = Validator::make($request->all(), [
                "shipping_date" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            DB::beginTransaction();
            $getApiKeySecret = ApiSecret::get()->first();
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $url = '/secret/shipping-order/sync';

            $data = [
                "shipping_date" => $request->shipping_date
            ];

            $syncDataShipping = Http::withHeaders([
                'ALFA-API-KEY' => $getApiKeySecret->api_secret_key,
                'ALFA-DEALER-CODE' => $getDealerSelected->dealer->dealer_code,
                // ])->post(env('API_MD_BASE') . $url, $data);
            ])->post("http://103.165.240.34:9003/api/v1" . $url, $data);

            $syncDataShipping = $syncDataShipping->json();


            if ($syncDataShipping["meta"]["code"] === 401) {
                return $syncDataShipping;
            }

            if (count($syncDataShipping["data"]) > 0) {
                foreach ($syncDataShipping["data"] as $item) {

                    //cari nama dealer berdasarkan nama dealer
                    $getDetailDealer = Dealer::where("dealer_code", $item["dealer"]["dealer_code"])->first();

                    // return $getDetailDealer;


                    //simpan data shipping order
                    $createShippingOrder = ShippingOrder::firstOrCreate([
                        "shipping_order_delivery_number" => $item["shipping_order_delivery_number"]
                    ], [
                        "shipping_order_number" => $item["shipping_order_number"],
                        "shipping_order_delivery_number" => $item["shipping_order_delivery_number"],
                        "shipping_order_status" => $item["shipping_order_status"],
                        "shipping_order_shipping_date" => $item["shipping_order_shipping_date"],
                        "dealer_id" => $getDetailDealer->dealer_id,
                    ]);

                    //simpan data unit
                    foreach ($item["unit"] as $itemUnit) {
                        //check color jika ada buat jika tidak maka tidak dibuat

                        $createColor = Color::firstOrCreate([
                            "color_name" => $itemUnit["unit_color"]
                        ], [
                            "color_name" => $itemUnit["unit_color"]
                        ]);

                        $createMotor = Motor::firstOrCreate([
                            "motor_name" => $itemUnit["motor"]["motor_name"]
                        ], [
                            "motor_name" => $itemUnit["motor"]["motor_name"],
                            "motor_status" => "active"
                        ]);

                        //check unit jika blm ada maka di simpan jika ada maka tidak akan di simpan,

                        $shipping_order_delivery_number = $item["shipping_order_delivery_number"];

                        Unit::with(["shipping_order"])
                            ->whereHas("shipping_order", function ($query) use ($shipping_order_delivery_number) {
                                return $query->where("shipping_order_delivery_number", $shipping_order_delivery_number);
                            })
                            ->firstOrCreate([
                                "unit_frame" => $itemUnit["unit_frame"],
                                "unit_engine" => $itemUnit["unit_engine"]
                            ], [
                                "unit_frame" => $itemUnit["unit_frame"],
                                "unit_engine" => $itemUnit["unit_engine"],
                                "shipping_order_id" => $createShippingOrder->shipping_order_id,
                                "motor_id" => $createMotor->motor_id,
                                "dealer_id" => $getDetailDealer->dealer_id,
                                "color_id" => $createColor->color_id,
                                "unit_code" => 0
                            ]);
                    }
                }
            }

            DB::commit();

            return $syncDataShipping;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
