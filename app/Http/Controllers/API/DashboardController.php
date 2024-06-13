<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Spk;
use App\Models\SpkInstansi;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //

    public function getCountTotal(Request $request)
    {
        try {
            $user = Auth::user();
            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $getCountSpk = Spk::where("spk_status", "spk")
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->count();
            $getCountSpkInstansi = SpkInstansi::where("spk_instansi_status", "publish")
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->count();
            $getCountUnit = Unit::where("unit_status", "on_hand")
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->count();

            $data = [
                "spk" => $getCountSpk,
                "unit" => $getCountUnit,
                "spk_instansi" => $getCountSpkInstansi
            ];

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
