<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Motor;
use App\Models\ShippingOrder;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class shippingOrderController extends Controller
{
    //


    public function sycnShippingOrder(Request $request, $city)
    {
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
                            $checkDealer = $this->checkDealer($itemHeader["h.customer_code_"]);

                            if (isset($checkDealer->dealer_id)) {
                                $shippingOrder =  ShippingOrder::updateOrCreate([
                                    "shipping_order_delivery_number" => $itemHeader["h.delivery_no_"]
                                ], [
                                    "shipping_order_number" => $itemDetail["d.sales_order_no_"],
                                    "shipping_order_delivery_number" => $itemHeader["h.delivery_no_"],
                                    "shipping_order_shipping_date" => $this->formatDate($itemHeader["h.shipping_date_"]),
                                    "dealer_id" => $checkDealer->dealer_id
                                ]);

                                 $checkMotor = $this->checkMotor($itemDetail["d.model_code_"]);

                                   
                                // sampe di sini
                                foreach ($itemDetail["subdetail-datas"] as $itemSubDetail) {
                                    if(isset($checkMotor->motor_code)){
                                        Unit::updateOrCreate([
                                            "unit_frame" => $itemSubDetail["s.frame_no_"]
                                        ],
                                    [
                                        "unit_color" =>$itemDetail["d.color_"],
                                        "unit_frame"=>$itemSubDetail["s.frame_no_"],
                                        "unit_engine" => $itemSubDetail["s.engine_no_"],
                                        "shipping_order_id"=> $shippingOrder->unitOrderId,
                                        "motor_id" => $checkMotor->motor_id
                                    ]);
                                    }
                                  
                                   
                                }
                            } else {
                                $createDealer = Dealer::create([
                                    "company_name" => $itemHeader["h.customer_name_"],
                                    "company_code" => $itemHeader["h.customer_code_"],
                                    "company_type" => isset($itemHeader["h.consignee_"]) && strpos($itemHeader["h.consignee_"], "ALFA SCORPII") !== false ? 'mds' : 'independent'
                                ]);

                                $shippingOrder =  ShippingOrder::updateOrCreate([
                                    "unit_order_delivery_number" => $itemHeader["h.delivery_no_"]
                                ], [
                                    "unit_order_sales_order_number" => $itemDetail["d.sales_order_no_"],
                                    "unit_order_delivery_number" => $itemHeader["h.delivery_no_"],
                                    "unit_order_shipping_date" => $this->formatDate($itemHeader["h.shipping_date_"]),
                                    "companySubId" => $createDealer->companySubId
                                ]);

                                foreach ($itemDetail["subdetail-datas"] as $itemSubDetail) {
                                    Unit::updateOrCreate([
                                        "unit_detail_frame" => $itemSubDetail["s.frame_no_"]
                                    ], [
                                        "unit_detail_model_name" => $itemDetail["d.model_name_"],
                                        "unit_detail_color" => $itemDetail["d.color_"],
                                        "unit_detail_code" => $itemDetail["d.model_code_"],
                                        "unit_detail_frame" => $itemSubDetail["s.frame_no_"],
                                        "unit_detail_engine" => $itemSubDetail["s.engine_no_"],
                                        "unitOrderId" => $shippingOrder->unitOrderId,
                                    ]);
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

    protected function checkMotor($model_code){
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
