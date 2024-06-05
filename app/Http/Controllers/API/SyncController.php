<?php

namespace App\Http\Controllers\API;

use App\Enums\UsersStatusEnum;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ApiSecret;
use App\Models\Dealer;
use App\Models\DealerByUser;
use App\Models\DealerNeq;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class SyncController extends Controller
{
    //

    public function checkDealer(Request $request)
    {
        try {
            $getDealer = Dealer::get();


            return ResponseFormatter::success($getDealer);
        } catch (\Throwable $e) {

            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function syncData(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "api_secret_key" => "required",
                "dealers" => "required|array",
                "dealers.*.dealer_name" => "required",
                "dealers.*.dealer_id" => "required",
                "dealers.*.dealer_code" => "required",
                "dealers.*.dealer_phone_number" => "nullable",
                "dealers.*.dealer_type" => "required",
                "dealers.*.dealer_address" => "nullable",
                "dealers.*.dealer_neq" => "array",
                "dealers.*.dealer_neq.*.dealer_neq_name" => "required",
                "dealers.*.dealer_neq.*.dealer_neq_address" => "required",
                "dealers.*.dealer_neq.*.dealer_neq_phone_number" => "nullable",
                "dealers.*.dealer_neq.*.dealer_neq_code" => "required",
                "dealers.*.dealer_neq.*.dealer_neq_city" => "nullable",
                "colors" => "required|array",
                "colors.*.color_name" => "required",
                "motors" => "required|array",
                "motors.*.motor_name" => "required",
                "user" => "required"
                // tambahkan list color, type motor

            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $createApiSecret = ApiSecret::create([
                "api_secret_key" => $request->api_secret_key
            ]);


            $createUser = User::create([
                "username" => $request->user->username,
                "password" => $request->user->password,
                "user_status" => UsersStatusEnum::ACTIVE
            ]);

            $getAllPermission = Permission::latest()->get();

            foreach ($getAllPermission as $item) {
                $createUser->givePermissionTo($item->name);
            }




            foreach ($request->dealers as $dealerData) {
                $dealer = Dealer::create([
                    'dealer_id' => $dealerData['dealer_id'],
                    'dealer_name' => $dealerData['dealer_name'],
                    'dealer_code' => $dealerData['dealer_code'],
                    'dealer_phone_number' => $dealerData['dealer_phone_number'],
                    'dealer_type' => $dealerData['dealer_type'],
                    'dealer_address' => $dealerData['dealer_address'],
                ]);

                // add semua dealer ke dealer by user

                DealerByUser::create([
                    "dealer_id" => $dealer->dealer_id,
                    "user_id" => $createUser->user_id,
                ]);

                foreach ($dealerData['dealer_neq'] as $neqData) {
                    DealerNeq::create([
                        "dealer_neq_name" => $neqData["dealer_neq_name"],
                        "dealer_neq_address" => $neqData["dealer_neq_address"],
                        "dealer_neq_phone_number" => $neqData["dealer_neq_phone_number"],
                        "dealer_neq_code" => $neqData["dealer_neq_code"],
                        "dealer_neq_city" => $neqData["dealer_neq_city"],
                        "dealer_id" => $dealer->dealer_id
                    ]);
                }

                foreach ($dealerData['motors'] as $motorData) {
                    Sales::create(
                        [
                            "motor_name" => $motorData["motor_name"],
                            "motor_status" => "active"
                        ]
                    );
                }
                foreach ($dealerData['colors'] as $colorData) {
                    Sales::create(
                        [
                            "color_name" => $colorData["color_name"],
                        ]
                    );
                }
            }

            DealerByUser::first()->update([
                "isSelected" => 1
            ]);

            DB::commit();

            return ResponseFormatter::success("success sync data");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
