<?php

namespace App\Http\Controllers\API;

use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\IndentInstansi;
use App\Models\IndentInstansiLog;
use App\Models\IndentInstansiPayment;
use App\Models\IndentInstansiPaymentImage;
use App\Models\IndentInstansiPaymentRefund;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IndentInstansiController extends Controller
{
    //

    public function cancelIndentInstansi(Request $request, $indent_instansi_id)
    {
        try {
            $getListPayment = IndentInstansiPayment::where("indent_instansi_id", $indent_instansi_id)->get();

            if ($getListPayment->count() > 0) {
                return ResponseFormatter::error("Tidak dapat melakukan pembatalan indent di karenakan payment masih ada. coba kembali !", "Bad request", 400);
            }

            DB::beginTransaction();

            $getDetailIndent = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)->first();

            $getDetailIndent->update([
                "indent_status" => "cancel"
            ]);

            $user = Auth::user();
            //create instansi log
            $createLog = IndentInstansiLog::create([
                "indent_instansi_id" => $indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Refund all Payment"
            ]);

            // DB::commit();

            $data = [
                "indent_instansi" => $getDetailIndent,
                "indent_log" => $createLog
            ];

            return ResponseFormatter::success($data, "Successfully canceled indent instansi");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function refundAllPayment(Request $request, $indent_instansi_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "indent_instansi_payment_refund_note" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            // mendapatkan total payment yang sudah terjadi
            $totalIndentPayment = IndentInstansiPayment::where("indent_instansi_id", $indent_instansi_id)->sum('indent_instansi_payment_amount');

            DB::beginTransaction();
            $createIndentPaymentRefund = IndentInstansiPaymentRefund::create([
                "indent_instansi_id" => $indent_instansi_id,
                "indent_instansi_payment_refund_total" => intval($totalIndentPayment),
                "indent_instansi_payment_refund_note" => $request->indent_instansi_payment_refund_note,
                "refund_number" => GenerateNumber::generate("REFUND-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "indent_instansi_payment_refunds", "refund_number")
            ]);

            // melakukan penghapusan indent payment secara keseluruhan
            IndentInstansiPayment::where("indent_instansi_id", $indent_instansi_id)->delete();

            // melakukan update ident status menjadi unpaid
            IndentInstansi::where("indent_instansi_id", $indent_instansi_id)->update([
                "indent_instansi_status" => "unpaid"
            ]);

            $user = Auth::user();
            //create instansi log
            IndentInstansiLog::create([
                "indent_instansi_id" => $indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Refund all Payment"
            ]);

            DB::commit();

            return ResponseFormatter::success($createIndentPaymentRefund, "Successfully refund all indent payment");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deletePayment(Request $request, $indent_instansi_payment_id)
    {
        try {
            $getDetailIndentInstansiPayment = IndentInstansiPayment::where("indent_instansi_payment_id", $indent_instansi_payment_id)->first();

            DB::beginTransaction();

            $getDetailIndentInstansi = IndentInstansi::where("indent_instansi_id", $getDetailIndentInstansiPayment->indent_instansi_id)->first();


            if ($getDetailIndentInstansi->indent_instansi_status !== "unpaid") {
                return ResponseFormatter::error("Tidak dapat melakukan delete payment karena status tidak unpaid", "Bad Request", 400);
            }

            IndentInstansiPaymentImage::where("indent_instansi_payment_id", $indent_instansi_payment_id)->delete();

            $getDetailIndentInstansiPayment->delete();

            $user = Auth::user();
            //create instansi log
            $createIndentInstansiLog = IndentInstansiLog::create([
                "indent_instansi_id" => $getDetailIndentInstansi->indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Delete Payment " . $getDetailIndentInstansiPayment->indent_instansi_payment_amount
            ]);

            DB::commit();

            $data = [
                "indent_instansi" => $getDetailIndentInstansi,
                "indent_refund" => $getDetailIndentInstansiPayment,
                "indent_log" => $createIndentInstansiLog
            ];


            return ResponseFormatter::success($data, "successfully refund indent !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function addPayment(Request $request, $indent_instansi_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "indent_instansi_payment_image" => "array|nullable",
                "indent_instansi_payment_image.*.img" => "required|mimes:png,jpg,pdf|max:5120",
                "indent_instansi_payment_method" => "required|in:cash,bank_transfer,giro",
                "bank_id" => "nullable",
                "indent_instansi_payment_amount" => "integer|required|min:1",
                "indent_instansi_payment_date" => "date|required",
                "indent_instansi_payment_note" => "nullable",
            ]);

            $validator->sometimes(["bank_id"], "required", function ($input) {
                return $input->indent_instansi_payment_method === "bank";
            });

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();
            $getDetailIndentInstansi = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)->first();


            if ($getDetailIndentInstansi->indent_instansi_status !== "unpaid") {
                return ResponseFormatter::error("status indent intansi harus ke posisi unpaid untuk bisa di lakukan penambahan payment", "bad request", 400);
            }



            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $dataLocal = [
                "indent_instansi_payment_method" => $request->indent_instansi_payment_method,
                "bank_id" => $request->bank_id,
                "indent_instansi_payment_amount" => $request->indent_instansi_payment_amount,
                "indent_instansi_payment_date" => $request->indent_instansi_payment_date,
                "indent_instansi_payment_note" => $request->indent_instansi_payment_note,
                "indent_instansi_id" => $indent_instansi_id,
                "indent_instansi_payment_number" => GenerateNumber::generate("PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "indent_instansi_payments", "indent_instansi_payment_number")
            ];

            $createPayment = IndentInstansiPayment::create($dataLocal);


            $createPaymentImg = [];

            if ($request->indent_instansi_payment_image) {
                foreach ($request->file("indent_instansi_payment_image") as $item) {
                    $imagePath = $item["img"]->store("indentInstansi", "public");

                    $createPaymentImg[] = IndentInstansiPaymentImage::create([
                        "idnt_instansi_payment_id" => $createPayment->indent_instansi_payment_id,
                        "image" => $imagePath
                    ]);
                }
            }


            // melakukan pengecekan apakah pembayaran sudah lunas apa belum dari total list indent payment
            $totalIndentPayment = IndentInstansiPayment::where("indent_instansi_id", $indent_instansi_id)->sum('indent_instansi_payment_amount');
            $getDetailIndentInstansi = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)->first();

            // melakukan penjumlahan data lama dengan data baru
            // $totalIndentPayment = $totalIndentPayment + $request->indent_payment_amount;

            if (intval($totalIndentPayment) >  $getDetailIndentInstansi->indent_instansi_nominal) {
                DB::rollBack();
                return ResponseFormatter::error("Payment Harus sama besar dengan total amount", "Bad Request", 400);
            }

            $user = Auth::user();
            //create instansi log
            $createIndentInstansiLog = IndentInstansiLog::create([
                "indent_instansi_id" => $indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Update Status to $request->indent_instansi_status"
            ]);

            $data = [
                "indent_instansi" => $getDetailIndentInstansi,
                "indent_instansi_payment" => $createPayment,
                "indent_instansi_payment_image" => $createPaymentImg,
                "totalIndentPayment" => intval($totalIndentPayment),
                "indent_instansi_log" => $createIndentInstansiLog
            ];

            DB::commit();


            return ResponseFormatter::success($data, "Successfully created indent payment !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateStatus(Request $request, $indent_instansi_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "indent_instansi_status" => "required|in:cashier_check,finance_check"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }
            DB::beginTransaction();

            $getDetail = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)
                ->with(["dealer", "motor"])
                ->first();

            if (!$getDetail->indent_instansi_status == "paid") {
                return ResponseFormatter::error("indent instansi status tidak dapat di update  dengan kondisi belum paid", "Bad Request",  400);
            }

            $getDetail->update([
                "indent_instansi_status" => $request->indent_instansi_status
            ]);
            $user = Auth::user();

            //create instansi log
            $createIndentInstansiLog = IndentInstansiLog::create([
                "indent_instansi_id" => $getDetail->indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Update Status to $request->indent_instansi_status"
            ]);

            DB::commit();

            $data = [
                "indent_instansi" => $getDetail,
                "indent_instansi_log" => $createIndentInstansiLog
            ];


            return ResponseFormatter::success($data, "Successfully updated status");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginate(Request $request)
    {
        try {

            $limit = $request->input("limit", 5);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $indentInstansiStatus = $request->input("indent_instansi_status");
            $q = $request->input("q");

            $getPaginate = IndentInstansi::latest()
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->when($indentInstansiStatus, function ($query) use ($indentInstansiStatus) {
                    return $query->where("indent_instansi_status", $indentInstansiStatus);
                })
                ->when($q, function ($query) use ($q) {
                    return $query->where("indent_instansi_date", "LIKE", "%$q%")
                        ->orWhere("indent_instansi_number", "LIKE", "%$q%")
                        ->orWhere("indent_instansi_number_po", "LIKE", "%$q%");
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetail(Request $request, $indent_instansi_id)
    {
        try {
            $getDetail = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)
                ->with(["dealer", "motor"])
                ->first();


            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateIndentInstansi(Request $request, $indent_instansi_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "sales_id" => "required",
                "salesman_name" => "required",
                "indent_instansi_number_po" => "required",
                "indent_instansi_po_date" => "required",
                "indent_instansi_name" => "required",
                "indent_instansi_address" => "required",
                "province_id" => "required",
                "province_name" => "required",
                "city_id" => "required",
                "city_name" => "required",
                "district_id" => "required",
                "district_name" => "required",
                "sub_district_id" => "required",
                "sub_district_name" => "required",
                "indent_instansi_postal_code" => "nullable",
                "indent_instansi_no_telp" => "nullable",
                "indent_instansi_no_hp" => "required",
                "indent_instansi_email" => "nullable",
                "motor_id" => "required",
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetail = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)
                ->with(["dealer", "motor"])
                ->first();

            $dataLocal = $request->all();

            $getDetail->update($dataLocal);
            $user = Auth::user();


            //create instansi log
            $createIndentInstansiLog = IndentInstansiLog::create([
                "indent_instansi_id" => $getDetail->indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Update Indent Instansi"
            ]);


            $data = [
                "indent_instansi" => $getDetail,
                "indent_instansi_log" => $createIndentInstansiLog
            ];

            return ResponseFormatter::success($data, "Successfully updated Data");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createIndentInstansi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "sales_id" => "required",
                "salesman_name" => "required",
                "indent_instansi_number_po" => "required",
                "indent_instansi_po_date" => "required",
                "indent_instansi_name" => "required",
                "indent_instansi_address" => "required",
                "province_id" => "required",
                "province_name" => "required",
                "city_id" => "required",
                "city_name" => "required",
                "district_id" => "required",
                "district_name" => "required",
                "sub_district_id" => "required",
                "sub_district_name" => "required",
                "indent_instansi_postal_code" => "nullable",
                "indent_instansi_no_telp" => "nullable",
                "indent_instansi_no_hp" => "required",
                "indent_instansi_email" => "nullable",
                "motor_id" => "required",
                "indent_instansi_nominal" => "required",
                "indent_instansi_note" => "nullable"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $dataLocal = $request->all();

            $dataLocal["indent_instansi_date"] = Carbon::now();
            $dataLocal["indent_instansi_status"] = "unpaid";
            $dataLocal["dealer_id"] = $getDealerSelected->dealer_id;
            $dataLocal["indent_instansi_number"] = GenerateNumber::generate("INDENT-INSTANSI", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "indent_instansis", "indent_instansi_number");

            $createIndentInstansi = IndentInstansi::create($dataLocal);

            //create instansi log
            $createIndentInstansiLog = IndentInstansiLog::create([
                "indent_instansi_id" => $createIndentInstansi->indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "Create Indent Instansi"
            ]);

            DB::commit();


            $data = [
                "indent_instansi" => $createIndentInstansi,
                "indent_instansi_log" => $createIndentInstansiLog
            ];

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
