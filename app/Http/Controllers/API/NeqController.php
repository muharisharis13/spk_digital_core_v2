<?php

namespace App\Http\Controllers\API;

use App\Enums\NeqStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Neq;
use App\Models\NeqLog;
use App\Models\NeqUnit;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NeqController extends Controller
{

    public function getPaginateNeq(Request $request)
    {
        try {

            $getPaginateNeq = Neq::latest();


            $getPaginateNeq = $getPaginateNeq->paginate(5);
            return ResponseFormatter::success($getPaginateNeq);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createNeq(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "neq_shipping_date" => "required",
                "dealer_neq_id" => "required",
                "neq_note" => "nullable",
                "neq_unit" => "required|array",
                "neq_unit.*.unit_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $createNeq = Neq::create([
                "neq_note" => $request->neq_note,
                "neq_shipping_date" => $request->neq_shipping_date,
                "dealer_neq_id" => $request->dealer_neq_id,
                "dealer_id" => $getDealerSelected->dealer_id,
                "neq_number" => GenerateNumber::generate("TF-NEQ", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "neqs", "neq_number"),
                "neq_status" => NeqStatusEnum::create
            ]);

            // create log neq
            $creaetLogNeq = NeqLog::create([
                "neq_id" => $createNeq->neq_id,
                "user_id" => $user->user_id,
                "neq_log_action" => "Create TF Neq"
            ]);

            foreach ($request->neq_unit as $item) {
                if ($this->checkUnitIsHaveEvent($item->unit_id)) {
                    DB::rollBack();
                    return ResponseFormatter::error("Unit $item->unit_id sudah memiliki event harap di return dahulu untuk tersedia di transfer ke NEQ", "Bad request !", 400);
                }
                $createNeqUnit[] = NeqUnit::create([
                    "neq_id" => $createNeq->neq_id,
                    "unit_id" => $item->unit_id,
                ]);
            }

            DB::commit();

            $data = [
                "neq" => $createNeq,
                "neq_unit" => $createNeqUnit,
                "neq_log" => $creaetLogNeq
            ];

            return ResponseFormatter::success($data, "Successfully created !");

            // return ResponseFormatter::success($this->checkUnitIsHaveEvent(), "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    private function checkUnitIsHaveEvent()
    {
        $user = Auth::user();
        $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);


        $getUnit = Unit::latest()
            ->with("event_list_unit")
            ->where("unit_id", "9b9b8bf9-70ea-46d5-8024-5ebfa2566c5a")
            ->where("dealer_id", $getDealerSelected->dealer_id)->first();

        return isset($getUnit->event_list_unit);
    }
}
