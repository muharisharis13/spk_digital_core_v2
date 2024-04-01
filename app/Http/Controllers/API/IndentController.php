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
use App\Models\IndentPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IndentController extends Controller
{

    public function cancelIndent(Request $request, $indent_id)
    {
        try {
            // melakukan pengecekan indent payment sudah refund semua atau belum
            $getIndentPayment = IndentPayment::where("indent_id", $indent_id)->where("indent_payment_type", "payment")->get();

            if ($getIndentPayment->count() > 0) {
                return ResponseFormatter::error("Tidak dapat melakukan pembatalan indent di karenakan payment masih ada. coba kembali !", "Bad request", 400);
            }

            DB::beginTransaction();

            $getDetailIndent = Indent::where("indent_id", $indent_id)->first();

            $getDetailIndent->update([
                "indent_status" => IndentStatusEnum::cancel
            ]);

            // create log
            $user = Auth::user();
            $createLogIndent = IndentLog::create([
                "indent_id" => $getDetailIndent->indent_id,
                "user_id" => $user->user_id,
                "indent_log_action" => "cancel Indent " . $indent_id
            ]);

            DB::commit();


            $data = [
                "indent" => $getDetailIndent,
                "indent_log" => $createLogIndent
            ];



            return ResponseFormatter::success($data, "Successfully canceled indent");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function refundAllPayment(Request $request, $indent_id)
    {
        try {

            DB::beginTransaction();
            $getIndentListPayment = IndentPayment::where("indent_id", $indent_id)->delete();

            // create log
            $user = Auth::user();
            IndentLog::create([
                "indent_id" => $indent_id,
                "user_id" => $user->user_id,
                "indent_log_action" => "Refund all Payment"
            ]);
            DB::commit();

            return ResponseFormatter::success($getIndentListPayment, "Successfully refund all indent payment");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function refundPayment(Request $request, $indent_payment_id)
    {
        try {
            // melakukan pengenchekan SPK tapi blm ada tablenya ongoing

            $getDetailIndentPayment = IndentPayment::where("indent_payment_id", $indent_payment_id)->first();

            DB::beginTransaction();

            // melakukan penggatian payment dari paid ke unpaid atau dari cashier check ke unpaid dan seterusnya

            $getDetailIndent = Indent::where("indent_id", $getDetailIndentPayment->indent_id)->first();
            $getDetailIndent->update([
                "indent_status" => IndentStatusEnum::unpaid
            ]);

            $getDetailIndentPayment->update([
                "indent_payment_type" => "refund"
            ]);

            // create log
            $user = Auth::user();
            $createLogIndent = IndentLog::create([
                "indent_id" => $getDetailIndent->indent_id,
                "user_id" => $user->user_id,
                "indent_log_action" => "Refund Payment " . $getDetailIndentPayment->indent_payment_amount
            ]);


            DB::commit();

            $data = [
                "indent" => $getDetailIndent,
                "indent_refund" => $getDetailIndentPayment,
                "indent_log" => $createLogIndent
            ];

            return ResponseFormatter::success($data, "successfully refund indent !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    // public function refundPayment(Request $request, $indent_id)
    // {
    //     try {
    //         // melakukan pengenchekan SPK tapi blm ada tablenya ongoing


    //         DB::beginTransaction();

    //         // melakukan penggatian payment dari paid ke unpaid atau dari cashier check ke unpaid dan seterusnya

    //         $getDetailIndent = Indent::with(["indent_payment"])->where("indent_id", $indent_id)->first();
    //         $getDetailIndent->update([
    //             "indent_status" => IndentStatusEnum::unpaid
    //         ]);

    //         foreach ($getDetailIndent as $item) {
    //             IndentPayment::where("indent_id", $item["indent_id"])->delete();
    //         }


    //         // create log
    //         $user = Auth::user();
    //         $createLogIndent = IndentLog::create([
    //             "indent_id" => $getDetailIndent->indent_id,
    //             "user_id" => $user->user_id,
    //             "indent_log_action" => "Refund Payment " . $indent_id
    //         ]);


    //         DB::commit();

    //         $data = [
    //             "indent" => $getDetailIndent,
    //             "indent_log" => $createLogIndent
    //         ];

    //         return ResponseFormatter::success($data, "successfully refund indent !");
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return ResponseFormatter::error($e->getMessage(), "internal server", 500);
    //     }
    // }

    public function updateStatusIndent(Request $request, $indent_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "indent_status" => "required|in:cashier_check,finance_check"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();


            $getDetailIndent = Indent::where("indent_id", $indent_id)->first();

            if ($getDetailIndent->indent_status == "unpaid") {
                return ResponseFormatter::error("Tidak dapat merubah status jika masih unpaid", "Bad request", 400);
            }

            $getDetailIndent->update([
                "indent_status" => $request->indent_status
            ]);
            $user = Auth::user();

            // create log indent
            $createLogIndent = IndentLog::create([
                "indent_id" => $indent_id,
                "user_id" => $user->user_id,
                "indent_log_action" => "Indent update status to " . $request->indent_status
            ]);


            DB::commit();

            $data = [
                "indent" => $getDetailIndent,
                "indent_log" => $createLogIndent
            ];

            return ResponseFormatter::success($data, "successfully update status indent !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function addPayment(Request $request, $indent_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "indent_payment_img" => "nullable|mimes:png,jpg|max:2048",
                "indent_payment_method" => "required|in:cash,bank_transfer,giro",
                "bank_id" => "nullable",
                "indent_payment_amount" => "integer|required",
                "indent_payment_date" => "date|required",
                "indent_payment_note" => "nullable",
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            if ($request->indent_payment_method == "cash" && $request->bank_id) {
                DB::rollBack();
                return
                    ResponseFormatter::error("Please Delete bank id for payment method cash", "Bad Request", 400);
            }

            $imagePath = $request->file('indent_payment_img')->store('indent', 'public');


            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $createIndentPayment = IndentPayment::create([
                "indent_id" => $indent_id,
                "indent_payment_img" => $imagePath,
                "indent_payment_method" => $request->indent_payment_method,
                "bank_id" => $request->bank_id,
                "indent_payment_amount" => $request->indent_payment_amount,
                "indent_payment_date" => $request->indent_payment_date,
                "indent_payment_note" => $request->indent_payment_note,
                "indent_payment_number" => GenerateNumber::generate("PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "indent_payments", "indent_payment_number")
            ]);

            // melakukan pengecekan apakah pembayaran sudah lunas apa belum dari total list indent payment
            $totalIndentPayment = IndentPayment::where("indent_id", $indent_id)->where("indent_payment_type", "payment")->sum('indent_payment_amount');
            $getDetailIndent = Indent::where("indent_id", $indent_id)->first();

            // melakukan penjumlahan data lama dengan data baru
            $totalIndentPayment = $totalIndentPayment + $request->indent_payment_amount;

            // if ($totalIndentPayment >= $getDetailIndent->amount_total) {
            //     $getDetailIndent->update([
            //         "indent_status" => IndentStatusEnum::paid
            //     ]);
            // }


            $user = Auth::user();

            // create log indent
            $createLogIndent = IndentLog::create([
                "indent_id" => $indent_id,
                "user_id" => $user->user_id,
                "indent_log_action" => "Payment"
            ]);

            DB::commit();


            $data = [
                "indent" => $getDetailIndent,
                "indent_payment" => $createIndentPayment,
                "indent_log" => $createLogIndent,
                "totalIndentPayment" => $totalIndentPayment
            ];
            return ResponseFormatter::success($data, "Successfully created indent payment !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailInden(Request $request, $indent_id)
    {
        try {

            $getDetailIndent = Indent::where("indent_id", $indent_id)
                ->with(["sales", "motor", "color", "indent_log.user", "indent_payment.bank"])
                ->first();


            if ($getDetailIndent->indent_type == 'credit') {
                $getDetailIndent->load(["leasing"]);
            }

            if ($getDetailIndent->indent_type == 'cash') {
                $getDetailIndent->load(["micro_finance"]);
            }

            $response = [];

            if ($getDetailIndent) {
                $response = $getDetailIndent->toArray();

                // Ambil semua data indent_payment
                $indentPayments = $getDetailIndent->indent_payment;

                // Filter data indent_payment berdasarkan status refund
                $indentPaymentRefund = $indentPayments->filter(function ($payment) {
                    return $payment->indent_payment_type === 'refund';
                });

                // Filter data indent_payment berdasarkan status payment
                $indentPaymentPayment = $indentPayments->filter(function ($payment) {
                    return $payment->indent_payment_type === 'payment';
                });

                // Konversi koleksi menjadi array untuk kedua jenis payment
                $paymentRefundArray = $indentPaymentRefund->toArray();
                $paymentPaymentArray = $indentPaymentPayment->toArray();

                // Masukkan data indent_payment ke dalam respons sebagai array of object
                $response['indent_payment'] = array_values($paymentPaymentArray);

                // Masukkan data indent_payment_refund ke dalam respons sebagai array of object
                $response['indent_payment_refund'] = array_values($paymentRefundArray);
            }

            return ResponseFormatter::success($response);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

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
                ->with(["motor", "color"])
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->when($indentStatus, function ($query) use ($indentStatus) {
                    $query->where("indent_status", $indentStatus);
                })
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
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

    public function updateIndent(Request $request, $indent_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "motor_id" => "required",
                "color_id" => "required",
                "indent_people_name" => "required",
                "indent_nik" => "nullable",
                "indent_wa_number" => "nullable",
                "indent_phone_number" => "required",
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

            // melakukan pengecekan pembayaran apakah sudah 0 apa belum pembyaran indent

            $getListPaymentIndent = IndentPayment::where("indent_id", $indent_id)->where("indent_payment_type", "payment")->get();

            if ($getListPaymentIndent->count() > 0) {
                DB::rollBack();
                return ResponseFormatter::error("Harap lakukan penghapusan pembayaran dahulu sebelum melakukan update datat indent !", "Bad Request", 400);
            }

            $getDetailIndent = Indent::where("indent_id", $indent_id)->first();

            $getDetailIndent->update([
                "motor_id" => $request->motor_id,
                "color_id" => $request->color_id,
                "indent_people_name" => $request->indent_people_name,
                "indent_nik" => $request->indent_nik,
                "indent_wa_number" => $request->indent_wa_number,
                "indent_phone_number" => $request->indent_phone_number,
                "indent_type" => $request->indent_type,
                "indent_note" => $request->indent_note,
                "amount_total" => $request->amount_total,
                "sales_id" => $request->sales_id,
                "micro_finance_id" => $request->micro_finance_id,
                "leasing_id" => $request->leasing_id,
            ]);

            $user = Auth::user();

            // create log indent
            $createLogIndent = IndentLog::create([
                "indent_id" => $indent_id,
                "user_id" => $user->user_id,
                "indent_log_action" => "Update Indent " . $getDetailIndent->indent_people_name
            ]);

            DB::commit();

            $data = [
                "indent" => $getDetailIndent,
                "indent_log" => $createLogIndent
            ];

            return ResponseFormatter::success($data, "Successfully updated indent !");
        } catch (\Throwable $e) {
            DB::rollBack();
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
                "indent_phone_number" => "required",
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
