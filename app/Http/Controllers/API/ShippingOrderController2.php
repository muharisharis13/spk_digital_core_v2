<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ApiSecret;
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
        try {


            $validator = Validator::make($request->all(), [
                "shipping_date" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }



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
            ])->post("http://103.165.240.34:9003/api/v1" . $url, $data);

            $syncDataShipping = $syncDataShipping->json();

            return $syncDataShipping;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
