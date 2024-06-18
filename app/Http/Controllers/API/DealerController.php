<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DealerController extends Controller
{
    //

    public function getDetailDealer(Request $request, $dealer_id)
    {
        try {
            $getDetail = Dealer::where("dealer_id", $dealer_id)->with(["dealer_logo"])->first();


            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function createLogoDealer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "logo" => "required",
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            DB::beginTransaction();
            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);

            if ($request->hasFile("logo")) {
                $logo = $request->file("logo")->store("logo", "public");
            }

            $storeLogo = DealerLogo::updateOrCreate([
                "dealer_id" => $getDealerByUserSelected->dealer_id
            ], [
                "logo" => $logo,
                "dealer_id" => $getDealerByUserSelected->dealer_id
            ]);

            DB::commit();

            return ResponseFormatter::success($storeLogo);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
}
