<?php

namespace App\Http\Controllers\API;

use App\Enums\IndentStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Indent;
use App\Models\IndentLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IndentController extends Controller
{


    public function getPaginate(Request $request)
    {
        try {
            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $indentStatus = $request->input("indent_status");
            $limit = $request->input("limit", 5);
            $searchQuery = $request->input("q");

            $getPaginateIndent = Indent::latest()
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->when($indentStatus, function ($query) use ($indentStatus) {
                    $query->where("indent_status", $indentStatus);
                })
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $startDate = Carbon::parse($startDate)->startOfDay();
                    $endDate = Carbon::parse($endDate)->endOfDay();
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where("indent_number", "LIKE", "%$searchQuery%")
                        ->orWhere("indent_people_name", "LIKE", "%$searchQuery%");
                })
                ->paginate($limit);


            return ResponseFormatter::success($getPaginateIndent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createIndent(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "motor_id" => "required",
                "color_id" => "required",
                "indent_people_name" => "required",
                "indent_nik" => "nullable",
                "indent_wa_number" => "nullable",
                "indent_phone_number" => "nullable",
                "indent_type" => "required|in:cash,credit",
                "indent_note" => "nullable",
                "amount_total" => "required",
                "sales_id" => "required",
                "micro_finance_id" => "nullable",
                "leasing_id" => "nullable"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            // create new indent
            $createIndent = Indent::create([
                "dealer_id" => $getDealerSelected->dealer_id,
                "motor_id" => $request->motor_id,
                "color_id" => $request->color_id,
                "indent_people_name" => $request->indent_people_name,
                "indent_nik" => $request->indent_nik,
                "indent_wa_number" => $request->indent_wa_number,
                "indent_phone_number" => $request->indent_phone_number,
                "indent_type" => $request->indent_type,
                "indent_status" => IndentStatusEnum::unpaid,
                "indent_note" => $request->indent_note,
                "amount_total" => $request->amount_total,
                "sales_id" => $request->sales_id,
                "micro_finance_id" => $request->micro_finance_id,
                "leasing_id" => $request->leasing_id,
                "indent_number" => GenerateNumber::generate("INDENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "indents", "indent_number")
            ]);

            // create log indent
            $createLogIndent = IndentLog::create([
                "indent_id" => $createIndent->indent_id,
                "user_id" => $user->user_id,
                "indent_log_action" => "Indent " . IndentStatusEnum::unpaid
            ]);

            DB::commit();

            $data = [
                "indent" => $createIndent,
                "indent_log" => $createLogIndent
            ];

            return ResponseFormatter::success($data, "Successfully created indent !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
