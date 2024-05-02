<?php

namespace App\Http\Controllers\API;

use App\Enums\UnitStatusEnum;
use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Spk;
use App\Models\SpkAdditionalDocument;
use App\Models\SpkAdditionalDocumentAnother;
use App\Models\SpkCustomer;
use App\Models\SpkDeliveryDealer;
use App\Models\SpkDeliveryDomicile;
use App\Models\SpkDeliveryFileSk;
use App\Models\SpkDeliveryKtp;
use App\Models\SpkDeliveryNeq;
use App\Models\SpkExcessFunds;
use App\Models\SpkGeneral;
use App\Models\SpkLegal;
use App\Models\SpkLog;
use App\Models\SpkPayment;
use App\Models\SpkPaymentList;
use App\Models\SpkPaymentListImage;
use App\Models\SpkPaymentListRefund;
use App\Models\SpkPaymentLog;
use App\Models\SpkPricing;
use App\Models\SpkPricingAccecories;
use App\Models\SpkPricingAdditional;
use App\Models\SpkPurchaseOrder;
use App\Models\SpkPurchaseOrderFile;
use App\Models\SpkTransaction;
use App\Models\SpkUnit;
use App\Models\Unit;
use App\Models\UnitLog;
use App\Models\Indent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SPKController extends Controller
{
    //

    public function getDetailExcessPayment(Request $request, $spk_excess_fund_id)
    {
        try {
            $getDetail = SpkExcessFunds::where("spk_excess_fund_id", $spk_excess_fund_id)->with(["spk"])->first();


            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function getpaginateExcessPayment(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $getPaginate = SpkExcessFunds::latest()
                ->paginate($limit);
            // Mengubah nilai negatif menjadi positif
            foreach ($getPaginate->items() as $item) {
                $item->spk_excess_fund_amount_total = abs($item->spk_excess_fund_amount_total);
            }

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateStatusPaymentList(Request $request, $spk_payment_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "spk_payment_status" => "required|in:cashier_check,finance_check"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailSpkPaymentDetail = SpkPayment::where("spk_payment_id", $spk_payment_id)->first();


            $getDetailSpkPaymentDetail->update([
                "spk_payment_status" => $request->spk_payment_status
            ]);

            $user = Auth::user();

            // buat log spk payment
            $createLog = SpkPaymentLog::create([
                "user_id"
                => $user->user_id,
                "spk_payment_log_action" => "Update Status SPK Payment to $request->spk_payment_status",
                "spk_payment_id" => $spk_payment_id
            ]);

            DB::commit();

            $data = [
                "spk_payment" => $getDetailSpkPaymentDetail,
                "spk_payment_log" => $createLog
            ];

            return ResponseFormatter::success($data, "successfully update status SPK Payment !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function refundAllPaymentt(Request $request, $spk_payment_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "spk_payment_list_refund_note" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            // mendapatkan total payment yang sudah terjadi
            $totalSpkPaymentList = SpkPaymentList::where("spk_payment_id", $spk_payment_id)->sum('spk_payment_list_amount');


            DB::beginTransaction();
            $createtPaymentListRefund = SpkPaymentListRefund::create([
                "spk_payment_id" => $spk_payment_id,
                "spk_payment_list_refund_amount_total" => intval($totalSpkPaymentList),
                "spk_payment_list_refund_number" => GenerateNumber::generate("REFUND-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "spk_payment_list_refunds", "spk_payment_list_refund_number"),
                "spk_payment_list_refund_note" => $request->spk_payment_list_refund_note
            ]);


            //melakukan penghapusan spk payment list secara keseluruhan
            SpkPaymentList::where("spk_payment_id", $spk_payment_id)->delete();


            //melakukan update spk payment status menjadi unpaid
            SpkPayment::where("spk_payment_id", $spk_payment_id)->update([
                "spk_payment_status" => "unpaid"
            ]);


            $user = Auth::user();

            // buat log spk payment
            SpkPaymentLog::create([
                "user_id"
                => $user->user_id,
                "spk_payment_log_action" => "Refund All Payment SPK",
                "spk_payment_id" => $spk_payment_id
            ]);


            DB::commit();


            return ResponseFormatter::success($createtPaymentListRefund, "Successfully refund all spk payment");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deletePayment(Request $request, $spk_payment_list_id)
    {
        try {
            $getDetailPaymentList = SpkPaymentList::where("spk_payment_list_id", $spk_payment_list_id)->first();

            DB::beginTransaction();





            $user = Auth::user();

            // buat log spk payment
            $createSpkPaymentLog =  SpkPaymentLog::create([
                "user_id"
                => $user->user_id,
                "spk_payment_log_action" => "delete payment " . $getDetailPaymentList->spk_payment_list_amount,
                "spk_payment_id" => $getDetailPaymentList->spk_payment_id
            ]);

            //delete image file
            SpkPaymentListImage::where("spk_payment_list_id", $spk_payment_list_id)->delete();

            $getDetailPaymentList->delete();


            $data = [
                "spk_payment_list" => $getDetailPaymentList,
                "spk_payment_log" => $createSpkPaymentLog
            ];


            DB::commit();


            return ResponseFormatter::success($data, "Successfully deleted payment");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function addSpkPayment(Request $request, $spk_payment_id)
    {
        try {

            $validator = Validator::make($request->all(), [
                "spk_payment_img" => "nullable|array",
                "spk_payment_img.*.img" => "required|mimes:png,jpg,pdf|max:5120",
                "spk_payment_list_method" => "required",
                "spk_payment_list_amount" => "required|integer",
                "spk_payment_list_note" => "nullable",
                "spk_payment_list_date" => "required"
            ]);

            $validator->sometimes(["bank_id",], 'required', function ($input) {
                return $input->spk_payment_list_method == 'bank_transfer';
            });
            $validator->sometimes(["bank_id",], 'required', function ($input) {
                return $input->spk_payment_list_method == 'giro';
            });


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            if ($request->spk_payment_list_method == "cash" && isset($request->bank_id)) {
                DB::rollBack();
                return
                    ResponseFormatter::error("Please Delete bank id for payment method cash", "Bad Request", 400);
            }


            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            // create payment

            $spk_payment_list_method = strtoupper($request->spk_payment_list_method);
            $alias = GenerateAlias::generate($getDealerSelected->dealer->dealer_name);
            $number = "SPK-$spk_payment_list_method-PAYMENT";

            $createPayment = SpkPaymentList::create([
                "spk_payment_id" => $spk_payment_id,
                "spk_payment_list_method" => $request->spk_payment_list_method,
                "bank_id" => $request->bank_id,
                "spk_payment_list_amount" => $request->spk_payment_list_amount,
                "spk_payment_list_date" => $request->spk_payment_list_date,
                "spk_payment_list_note" => $request->spk_payment_list_note,
                "spk_payment_list_number" => GenerateNumber::generate($number, $alias, "spk_payment_lists", "spk_payment_list_number")
            ]);

            $createPaymentImg = [];

            if ($request->spk_payment_img) {
                foreach ($request->file("spk_payment_img") as $item) {
                    $imagePath = $item["img"]->store("spk", "public");

                    $createPaymentImg[] = SpkPaymentListImage::create([
                        "spk_payment_list_id" => $createPayment->spk_payment_list_id,
                        "spk_payment_list_img" => $imagePath
                    ]);
                }
            }


            // melakukan pengecekan apakah pembayaran sudah lunas apa belum dari total list spk payment
            $totalSpkPayment = SpkPaymentList::where("spk_payment_id", $spk_payment_id)->sum('spk_payment_list_amount');

            $getDetail = SpkPayment::latest()
                ->with(["spk"])
                ->where("spk_payment_id", $spk_payment_id)
                ->first();


            if ($getDetail->spk_payment_type === "dp") {

                $spk_payment_amount_total = self::sumAmountTotalDp($getDetail);
            }
            if ($getDetail->spk_payment_type === "leasing") {

                $spk_payment_amount_total = self::sumAmountTotalLeasing($getDetail);
            }
            if ($getDetail->spk_payment_type === "cash") {

                $spk_payment_amount_total = self::sumAmountTotalCash($getDetail);
            }

            // melakukan penjumlahan data lama dengan data baru
            // $totalSpkPayment = $totalSpkPayment + $request->indent_payment_amount;

            // if (intval($totalSpkPayment) >  $spk_payment_amount_total) {
            //     DB::rollBack();
            //     return ResponseFormatter::error("Payment Harus sama besar dengan total amount", "Bad Request", 400);
            // }


            // buat log spk payment
            $createSpkPaymentLog =  SpkPaymentLog::create([
                "user_id"
                => $user->user_id,
                "spk_payment_log_action" => "add payment",
                "spk_payment_id" => $spk_payment_id
            ]);

            DB::commit();

            $data = [
                "spk_payment_list" => $createPayment,
                "spk_payment_list_img" => $createPaymentImg,
                "spk_payment_log" => $createSpkPaymentLog
            ];

            return ResponseFormatter::success($data, "Successfully add payment");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    function sumAmountTotalDp($getDetail)
    {
        $result = null;
        $spk = $getDetail->spk;

        $result = ($spk->spk_transaction->spk_transaction_down_payment ?? 0) -
            ($spk->spk_pricing->spk_pricing_indent_nominal ?? 0) -
            ($spk->spk_pricing->spk_pricing_discount ?? 0) - ($spk->spk_pricing->spk_pricing_over_discount ?? 0) - ($spk->spk_pricing->spk_pricing_subsidi ?? 0);

        return $result;
    }

    function sumAmountTotalLeasing($getDetail)
    {
        $spk = $getDetail->spk;

        $onTheRoad = ($spk->spk_pricing->spk_pricing_on_the_road ?? 0);


        return $onTheRoad - $spk->spk_transaction->spk_transaction_down_payment;
    }


    function sumAmountTotalCash($getDetail)
    {
        $result = null;
        $spk = $getDetail->spk;

        $result =
            ($spk->spk_pricing->spk_pricing_on_the_road ?? 0) -
            ($spk->spk_pricing->spk_pricing_indent_nominal ?? 0) -
            ($spk->spk_pricing->spk_pricing_discount ?? 0) -
            ($spk->spk_pricing->spk_pricing_over_discount ?? 0) -
            ($spk->spk_pricing->spk_pricing_subsidi ?? 0);

        return $result;
    }

    public function getDetailSpkPayment(Request $request, $spk_payment_id)
    {
        try {

            $getDetail = SpkPayment::latest()
                ->with(["spk"])
                ->where("spk_payment_id", $spk_payment_id)
                ->first();

            if ($getDetail->spk_payment_type === "dp") {

                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalDp($getDetail);
            }
            if ($getDetail->spk_payment_type === "leasing") {

                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalLeasing($getDetail);
            }
            if ($getDetail->spk_payment_type === "cash") {

                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalCash($getDetail);
            }



            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateSpkPayment(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $transaction_type = $request->input("transaction_type");
            $payment_type = $request->input("payment_type");
            $payment_status = $request->input("payment_status");
            $q = $request->input("q");
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $getPaginate = SpkPayment::latest()
                ->with(["spk"])
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->whereHas("spk", function ($query) use ($transaction_type) {
                    return $query->whereHas("spk_transaction", function ($queryTransaction) use ($transaction_type) {
                        return $queryTransaction->where("spk_transaction_method_payment", "LIKE", "%$transaction_type%");
                    });
                })
                ->where("spk_payment_type", "LIKE", "%$payment_type%")
                ->when($payment_status, function ($query) use ($payment_status) {
                    return $query->where("spk_payment_status", $payment_status);
                })
                ->when($q, function ($query) use ($q) {
                    return $query->whereHas("spk", function ($querySpk) use ($q) {
                        return $querySpk->where("spk_number", "LIKE", "%$q%")
                            ->orWhereHas("spk_legal", function ($querySpkLegal) use ($q) {
                                return $querySpkLegal->where("spk_legal_name", "LIKE", "%$q%");
                            })
                            ->orWhereHas("spk_customer", function ($queryCustomer) use ($q) {
                                return $queryCustomer->where("spk_customer_name", "LIKE", "%$q%");
                            })
                            ->orWhereHas("spk_transaction", function ($queryTransaction) use ($q) {
                                return $queryTransaction->where("spk_transaction_method_payment", "LIKE", "%$q%");
                            });
                    })
                        ->orWhere("spk_payment_status", "LIKE", "%$query%");
                })
                ->where("dealer_id", $getDealerSelected->dealer_id)
                ->paginate($limit);


            foreach ($getPaginate as $item) {
                if (isset($item->spk_payment_type) && $item->spk_payment_type === "dp") {

                    $item["spk_payment_amount_total"] = self::sumAmountTotalDp($item);
                }
                if (isset($item->spk_payment_type) &&  $item->spk_payment_type === "leasing") {

                    $item["spk_payment_amount_total"] = self::sumAmountTotalLeasing($item);
                }
                if (isset($item->spk_payment_type) &&  $item->spk_payment_type === "cash") {

                    $item["spk_payment_amount_total"] = self::sumAmountTotalCash($item);
                }
            }

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteSPK(Request $request, $spk_id)
    {
        try {
            DB::beginTransaction();

            Spk::where("spk_id", $spk_id)->delete();
            SpkAdditionalDocument::where("spk_id")->delete();
            SpkAdditionalDocumentAnother::where("spk_id", $spk_id)->delete();
            SpkCustomer::where("spk_id", $spk_id)->delete();
            SpkDeliveryDealer::where("spk_id", $spk_id)->delete();
            SpkDeliveryDomicile::where("spk_id", $spk_id)->delete();
            SpkDeliveryKtp::where("spk_id", $spk_id)->delete();
            SpkDeliveryNeq::where("spk_id", $spk_id)->delete();
            SpkGeneral::where("spk_id", $spk_id)->delete();
            SpkLegal::where("spk_id", $spk_id)->delete();
            SpkLog::where("spk_id", $spk_id)->delete();
            SpkPricing::where("spk_id", $spk_id)->delete();
            SpkPricingAccecories::where("spk_id", $spk_id)->delete();
            SpkPricingAdditional::where("spk_id", $spk_id)->delete();
            SpkPurchaseOrder::where("spk_id", $spk_id)->delete();
            SpkTransaction::where("spk_id", $spk_id)->delete();
            SpkUnit::where("spk_id", $spk_id)->delete();

            DB::commit();

            return ResponseFormatter::success("Berhasil Hapus");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function addCRO(Request $request, $spk_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "is_cro_check" => "nullable",
                "spk_cro_check_note" => "nullable"
            ]);



            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getSpk = Spk::where("spk_id", $spk_id)->first();


            $getSpk->update([
                "is_cro_check" => $request->is_cro_check,
                "spk_cro_check_note" => $request->spk_cro_check_note
            ]);


            $user = Auth::user();


            //buat spk log
            $createSPKLog =
                SpkLog::create([
                    "spk_log_action" => "add CRO SPK",
                    "user_id" => $user->user_id,
                    "spk_id" => $spk_id
                ]);


            $data = [
                "spk" => $getSpk,
                "spk_log" => $createSPKLog
            ];

            DB::commit();


            return ResponseFormatter::success($data, "Successfully add CRO Check");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    function generateSpkPayment($detailTransaction, $spk_id, $getDealerSelected, $user)
    {



        if ($detailTransaction->spk_transaction_method_payment === "cash") {

            $createSpkPayment = SpkPayment::create([
                "spk_payment_number" =>
                GenerateNumber::generate("SPK-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "spk_payments", "spk_payment_number"),
                "spk_id" => $spk_id,
                "spk_payment_for" => "customer",
                "dealer_id" => $getDealerSelected->dealer_id,
                "spk_payment_type" => "cash",
            ]);

            // buat log spk payment
            SpkPaymentLog::create([
                "user_id"
                => $user->user_id,
                "spk_payment_log_action" => "create SPK Payment",
                "spk_payment_id" => $createSpkPayment->spk_payment_id
            ]);
        } else {
            $createSpkPaymentCustomer =  SpkPayment::create([
                "spk_payment_number" =>
                GenerateNumber::generate("SPK-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "spk_payments", "spk_payment_number"),
                "spk_id" => $spk_id,
                "spk_payment_for" => "customer",
                "dealer_id" => $getDealerSelected->dealer_id,
                "spk_payment_type" => "dp"
            ]);

            // buat log spk payment
            SpkPaymentLog::create([
                "user_id"
                => $user->user_id,
                "spk_payment_log_action" => "create SPK Payment",
                "spk_payment_id" => $createSpkPaymentCustomer->spk_payment_id
            ]);

            // Penundaan selama satu menit di sini
            sleep(30); // Satu menit


            if (isset($createSpkPaymentCustomer->spk_payment_id)) {

                $createSpkPaymentLeasing = SpkPayment::create([
                    "spk_payment_number" =>
                    GenerateNumber::generate("SPK-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "spk_payments", "spk_payment_number"),
                    "spk_id" => $spk_id,
                    "spk_payment_for" => "leasing",
                    "dealer_id" => $getDealerSelected->dealer_id,
                    "spk_payment_type" => "leasing"
                ]);
                // buat log spk payment
                SpkPaymentLog::create([
                    "user_id"
                    => $user->user_id,
                    "spk_payment_log_action" => "create SPK Payment",
                    "spk_payment_id" => $createSpkPaymentLeasing->spk_payment_id
                ]);
            }
        }
    }


    function generateExcessFund($spk_id, $getDealerSelected, $amount_total)
    {
        $createExcessFund = SpkExcessFunds::create([
            "spk_id" => $spk_id,
            "spk_excess_fund_number" => GenerateNumber::generate("SPK-EXCESS-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "spk_excess_funds", "spk_excess_fund_number"),
            "spk_excess_fund_amount_total" => $amount_total
        ]);

        return $createExcessFund;
    }

    public function addShipment(Request $request, $spk_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "unit_id" => "required|uuid",
                "unit_year" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            DB::beginTransaction();

            //update status spk
            Spk::where("spk_id", $spk_id)->update([
                "spk_status" => "spk"
            ]);

            $getSpkUnit = SpkUnit::where("spk_id", $spk_id)->first();

            $getSpkUnit->update([
                "unit_id" => $request->unit_id,
                "spk_unit_year" => $request->unit_year
            ]);

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            // update status unit menjadi spk

            $updateUnit = Unit::where("unit_id", $request->unit_id)->first();



            $updateUnit->update([
                "unit_status" => UnitStatusEnum::spk
            ]);


            $getSpk = Spk::where("spk_id", $spk_id)->first();

            //update status indent menjadi spk

            $getIndent = Indent::where("indent_id", $getSpk->spk_general->indent_id)->first();

            if (isset($getIndent)) {
                if ($getIndent->indent_status === "spk") {
                    DB::rollBack();
                    return
                        ResponseFormatter::error("Indent Sudah memiliki spk harap ganti indent", "bad request", 400);
                }

                $getIndent->update([
                    "indent_status" => "spk"
                ]);
            } else {
                DB::rollback();
                return ResponseFormatter::error("Indent Tidak ditemukan", "bad request", 400);
            }

            //create log unit
            $createUnitLog = UnitLog::create([
                "unit_id" => $request->unit_id,
                "user_id" => $user->user_id,
                "unit_log_number" => "NULL",
                "unit_log_action" => "update status to SPK",
                "unit_log_status" => UnitStatusEnum::spk
            ]);




            //buat spk log
            $createSPKLog =
                SpkLog::create([
                    "spk_log_action" => "add shipment SPK",
                    "user_id" => $user->user_id,
                    "spk_id" => $spk_id
                ]);


            // mendapatkan spk transaction
            $getDetailSpkTransaction = SpkTransaction::where("spk_id", $spk_id)->first();
            // mendapatkan spk pricing
            $getDetailSpkPricing = SpkPricing::where("spk_id", $spk_id)->first();

            // generate spk payment

            self::generateSpkPayment($getDetailSpkTransaction, $spk_id, $getDealerSelected, $user);

            // generate spk excess payment
            $total_amount = $getDetailSpkTransaction->spk_transaction_down_payment - $getDetailSpkPricing->spk_pricing_indent_nominal - $getDetailSpkPricing->spk_pricing_discount - $getDetailSpkPricing->spk_pricing_subsidi - ($getDetailSpkPricing->spk_pricing_over_discount ?? 0);

            if ($total_amount < 0) {
                self::generateExcessFund($spk_id, $getDealerSelected, $total_amount);
            }

            DB::commit();

            $data = [
                "spk_unit" => $getSpkUnit,
                "spk_log" => $createSPKLog,
                "unit" => $updateUnit,
                "unit_log" => $createUnitLog
            ];

            return
                ResponseFormatter::success($data, "Successfully add shipment");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }



    public function updateStatusSpk(Request $request, $spk_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "spk_status" => "required|in:finance_check,cancel"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            DB::beginTransaction();

            $getSpk = Spk::where("spk_id", $spk_id)->first();

            if ($getSpk->spk_status === "finance_check" && $request->spk_status === "cancel") {
                //melakukan cancel spk
                $getSpk->update([
                    "spk_status" => $request->spk_status
                ]);

                //melakukan update indent menjadi finance check

                $getDetailIndent = Indent::where("indent_id", $getSpk->spk_general->indent_id)->first();

                if (isset($getDetailIndent)) {
                    $getDetailIndent->update([
                        "indent_status" => "finance_check"
                    ]);
                } else {
                    DB::rollBack();
                    return ResponseFormatter::error("Indent Tidak ditemukan", "bad request!", 400);
                }

                //melakukan update unit menjadi on hand kembali

                $getDetailUnit =  Unit::where("unit_id", $getSpk->spk_unit->unit_id)->first();

                if (isset($getDetailUnit)) {
                    $getDetailUnit->update([
                        "unit_status" => "on_hand"
                    ]);
                } else {
                    DB::rollBack();
                    return ResponseFormatter::error("Unit Tidak ditemukan", "bad request!", 400);
                }

                //update payment menjadi cancel
                //logika nyaa => check dlu pembayaran apakah status sudah unpaid -> jika sudah unpaid maka table spk payment menjadi status cancel

                $getDetailSpkPayment = SpkPayment::where("spk_id", $spk_id)->first();

                if ($getDetailSpkPayment->spk_payment_status === "unpaid") {
                    $getDetailSpkPayment->update([
                        "spk_payment_status" => "cancel"
                    ]);
                } else {
                    DB::rollBack();
                    return ResponseFormatter::error("pembayaran harus di unpaid kan dahulu sebelum melakukan pembatalan", "bad request!", 400);
                }
            } else {
                $getSpk->update([
                    "spk_status" => $request->spk_status
                ]);
            }

            $user = Auth::user();


            //buat spk log
            $createSPKLog =
                SpkLog::create([
                    "spk_log_action" => "update status SPK",
                    "user_id" => $user->user_id,
                    "spk_id" => $spk_id
                ]);

            $data = [
                "spk" => $getSpk,
                "spk_log" => $createSPKLog
            ];

            DB::commit();

            return ResponseFormatter::success($data, "Successfully update Status");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateActualPurchase(Request $request, $spk_purchase_order_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "spk_purchase_order_act_tac" => "required",
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $updatePurchaseOrder = SpkPurchaseOrder::where("spk_purchase_order_id", $spk_purchase_order_id)->first();

            $updatePurchaseOrder->update([
                "spk_purchase_order_act_tac" => $request->spk_purchase_order_act_tac
            ]);

            $user = Auth::user();

            //buat spk log
            $createSPKLog =
                SpkLog::create([
                    "spk_log_action" => "update purchase actual order",
                    "user_id" => $user->user_id,
                    "spk_id" => $updatePurchaseOrder->spk_id
                ]);

            $data = [
                "spk_purchase_order" => $updatePurchaseOrder,
                "spk_log" => $createSPKLog
            ];
            DB::commit();

            return ResponseFormatter::success($data, "Successfully updated actual tac !");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function deletePurchaseOrder(
        Request $request,
        $spk_purchase_order_id
    ) {
        try {

            DB::beginTransaction();
            $getSpkPo = SpkPurchaseOrder::where("spk_purchase_order_id", $spk_purchase_order_id)->first();


            //delete file purchase order
            SpkPurchaseOrderFile::where("spk_purchase_order_id", $getSpkPo->spk_purchase_order_id)->delete();


            $user = Auth::user();

            //buat spk log
            $createSPKLog =
                SpkLog::create([
                    "spk_log_action" => "reset purchase order",
                    "user_id" => $user->user_id,
                    "spk_id" => $getSpkPo->spk_id
                ]);

            $getSpkPo->delete();

            DB::commit();
            $data = [
                "spk_purchase_order" => $getSpkPo,
                "spk_log" => $createSPKLog
            ];


            return
                ResponseFormatter::success($data, "Successfully reset !");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createPurchaseOrder(Request $request, $spk_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "spk_purchase_order_date" => "required",
                "spk_purchase_order_no" => "required",
                "spk_purchase_order_type" => "required",
                "spk_purchase_order_tac" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $createPurchaseOrder = SpkPurchaseOrder::create([
                "spk_id" => $spk_id,
                "spk_purchase_order_date" => $request->spk_purchase_order_date,
                "spk_purchase_order_no" => $request->spk_purchase_order_no,
                "spk_purchase_order_type" => $request->spk_purchase_order_type,
                "spk_purchase_order_tac" => $request->spk_purchase_order_tac,
                "spk_purchase_order_act_tac" => 0
            ]);

            $createSpkPurchaseOrderFile = [];
            if ($request->spk_purchase_order_files) {
                foreach ($request->file("spk_purchase_order_files") as $item) {
                    $imagePath = $item->store('spk', 'public');

                    $createSpkPurchaseOrderFile[] = SpkPurchaseOrderFile::create([
                        "spk_purchase_order_id" => $createPurchaseOrder->spk_purchase_order_id,
                        "spk_purchase_order_file_path" => $imagePath
                    ]);
                }
            }
            $user = Auth::user();

            //buat spk log
            $createSPKLog =
                SpkLog::create([
                    "spk_log_action" => "create purchase order",
                    "user_id" => $user->user_id,
                    "spk_id" => $spk_id
                ]);

            DB::commit();

            $data = [
                "spk_purchase_order" => $createPurchaseOrder,
                "spk_purchase_order_file" => $createSpkPurchaseOrderFile,
                "spk_log" => $createSPKLog
            ];

            return ResponseFormatter::success($data, "Successfully created !");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deletePriceAccessories(Request $request, $spk_pricing_accecories_id)
    {
        try {
            $find = SpkPricingAccecories::where("spk_pricing_accecories_id", $spk_pricing_accecories_id)->first();
            DB::beginTransaction();

            $find->delete();
            DB::commit();

            return
                ResponseFormatter::success("Berhasil Hapus Pricing Accessories", "Successfully deleted item");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteFileDocumentSK(Request $request, $id)
    {
        try {
            $find = SpkDeliveryFileSk::where("spk_delivery_file_sk_id", $id)->first();
            DB::beginTransaction();

            $find->delete();
            DB::commit();

            return ResponseFormatter::success("Berhasil Hapus document file SK", "Successfully deleted item");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteFileDocumentAnother(Request $request, $id)
    {
        try {
            $find = SpkAdditionalDocumentAnother::where("spk_additional_document_another_id", $id)->first();
            DB::beginTransaction();

            $find->delete();
            DB::commit();

            return ResponseFormatter::success("Berhasil Hapus document another", "Successfully deleted item");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteFileDocumentKK(Request $request, $spk_additional_document_id)
    {
        try {
            $find = SpkAdditionalDocument::where("spk_additional_document_id", $spk_additional_document_id)->first();
            DB::beginTransaction();

            $find->update([
                "spk_additional_document_kk" => "NULL"
            ]);

            DB::commit();

            return ResponseFormatter::success("Berhasil Hapus document KK", "Successfully deleted item");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function deleteFileDocumentKtp(Request $request, $spk_additional_document_id)
    {
        try {
            $find = SpkAdditionalDocument::where("spk_additional_document_id", $spk_additional_document_id)->first();
            DB::beginTransaction();

            $find->update([
                "spk_additional_document_ktp" => "NULL"
            ]);
            DB::commit();

            return ResponseFormatter::success("Berhasil Hapus document KTP", "Successfully deleted item");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    function findSPK($spk_id)
    {
        return Spk::where("spk_id", $spk_id)->first();
    }

    function updateSpkGeneral($spk_id, $request)
    {
        $findGeneral = SpkGeneral::where("spk_id", $spk_id)->first();
        if (!$findGeneral) {
            DB::rollBack();
            throw new \Exception("spk_general not found!", 400);
        }

        $findGeneral->update([
            "indent_id" => $request->indent_id,
            "spk_general_date" => $request->spk_general_date,
            "spk_general_location" => $request->spk_general_location,
            "sales_name" => $request->sales_name,
            "sales_id" => $request->sales_id,
            "spk_general_method_sales" => $request->spk_general_method_sales,
            "dealer_id" => $request->dealer_id,
            "dealer_neq_id" => $request->dealer_neq_id
        ]);
        return $findGeneral;
    }

    function updateSpkUnit($spk_id, $request)
    {
        $findUnit = SpkUnit::where("spk_id", $spk_id)->first();

        if (!$findUnit) {
            DB::rollBack();
            throw new \Exception("spk_unit not found!", 400);
        }

        $findUnit->update([
            "motor_id" => $request->motor_id,
            "color_id" => $request->color_id
        ]);
        return $findUnit;
    }


    function updateSpkTransaction($spk_id, $request)
    {
        $findTransaction = SpkTransaction::where("spk_id", $spk_id)->first();
        if (!$findTransaction) {
            DB::rollBack();
            throw new \Exception("spk_transaction not found!", 400);
        }

        if ($request->spk_transaction_method_payment == "cash") {
            $findTransaction->update([
                "spk_transaction_method_buying" => $request->spk_transaction_method_buying,
                "spk_transaction_method_payment" => $request->spk_transaction_method_payment,
                "spk_transaction_surveyor_name" => $request->spk_transaction_surveyor_name,
                "microfinance_name" => $request->microfinance_name,
                "micro_finance_id" => $request->micro_finance_id,
            ]);
        } else {
            $findTransaction->update([
                "spk_transaction_method_buying" => $request->spk_transaction_method_buying,
                "spk_transaction_method_payment" => $request->spk_transaction_method_payment,
                "leasing_name" => $request->leasing_name,
                "leasing_id" => $request->leasing_id,
                "spk_transaction_down_payment" => $request->spk_transaction_down_payment,
                "spk_transaction_tenor" => $request->spk_transaction_tenor,
                "spk_transaction_instalment" =>  $request->spk_transaction_instalment,
                "spk_transaction_surveyor_name" => $request->spk_transaction_surveyor_name,
                "microfinance_name" => $request->microfinance_name,
                "micro_finance_id" => $request->micro_finance_id,
            ]);
        }

        return $findTransaction;
    }

    function updateSpkCustomer($spk_id, $request)
    {
        $findCustomer = SpkCustomer::where("spk_id", $spk_id)->first();
        if (!$findCustomer) {
            DB::rollBack();
            throw new \Exception("spk_customer not found!", 400);
        }

        $findCustomer->update([
            "spk_customer_nik" => $request->spk_customer_nik,
            "spk_customer_name" => $request->spk_customer_name,
            "spk_customer_address" => $request->spk_customer_address,
            "province" => $request->province,
            "province_id" => $request->province_id,
            "city" => $request->city,
            "city_id" => $request->city_id,
            "district" => $request->district,
            "district_id" => $request->district_id,
            "sub_district" => $request->sub_district,
            "sub_district_id" => $request->sub_district_id,
            "spk_customer_postal_code" => $request->spk_customer_postal_code,
            "spk_customer_birth_place" => $request->spk_customer_birth_place,
            "spk_customer_birth_date" => $request->spk_customer_birth_date,
            "spk_customer_gender" => $request->spk_customer_gender,
            "spk_customer_telp" => $request->spk_customer_telp,
            "spk_customer_no_wa" => $request->spk_customer_no_wa,
            "spk_customer_no_phone" => $request->spk_customer_no_phone,
            "spk_customer_religion" => $request->spk_customer_religion,
            "marital_id" => $request->marital_id,
            "hobbies_id" => $request->hobbies_id,
            "marital_name" => $request->marital_name,
            "hobbies_name" => $request->hobbies_name,
            "spk_customer_mother_name" => $request->spk_customer_mother_name,
            "spk_customer_npwp" => $request->spk_customer_npwp,
            "spk_customer_email" => $request->spk_customer_email,
            "residence_id" => $request->residence_id,
            "education_id" => $request->education_id,
            "work_id" => $request->work_id,
            "residence_name" => $request->residence_name,
            "education_name" => $request->education_name,
            "work_name" => $request->work_name,
            "spk_customer_length_of_work" => $request->spk_customer_length_of_work,
            "spk_customer_income" => $request->spk_customer_income,
            "spk_customer_outcome" => $request->spk_customer_outcome,
            "motor_brand_id" => $request->motor_brand_id,
            "motor_brand_name" => $request->motor_brand_name,
            "spk_customer_motor_type_before" => $request->spk_customer_motor_type_before,
            "spk_customer_motor_year_before" => $request->spk_customer_motor_year_before

        ]);

        return $findCustomer;
    }

    function updateSpkLegal($spk_id, $request)
    {
        $findLegal = SpkLegal::where("spk_id", $spk_id)->first();
        if (!$findLegal) {
            DB::rollBack();
            throw new \Exception("spk_legal not found!", 400);
        }

        $findLegal->update([
            "spk_legal_nik" => $request->spk_legal_nik,
            "spk_legal_name" => $request->spk_legal_name,
            "spk_legal_address" => $request->spk_legal_address,
            "province" => $request->spk_legal_province,
            "province_id" => $request->spk_legal_province_id,
            "city" => $request->spk_legal_city,
            "city_id" => $request->spk_legal_city_id,
            "district" => $request->spk_legal_district,
            "district_id" => $request->spk_legal_district_id,
            "sub_district" => $request->spk_legal_sub_district,
            "sub_district_id" => $request->spk_legal_sub_district_id,
            "spk_legal_postal_code" => $request->spk_legal_postal_code,
            "spk_legal_birth_place" => $request->spk_legal_birth_place,
            "spk_legal_birth_date" => $request->spk_legal_birth_date,
            "spk_legal_gender" => $request->spk_legal_gender,
            "spk_legal_telp" => $request->spk_legal_telp,
            "spk_legal_no_phone" => $request->spk_legal_no_phone
        ]);

        return $findLegal;
    }

    function updateSpkDocument($spk_id, $request)
    {
        $findAdditionaDocument = SpkAdditionalDocument::where("spk_id", $spk_id)->first();
        if ($request->hasFile('spk_additional_document_ktp')) {
            $imagePathKtp = $request->file('spk_additional_document_ktp')->store('spk', 'public');

            if (!$findAdditionaDocument) {
                DB::rollBack();
                throw new \Exception("spk_additional_document ktp not found!", 400);
            }

            $findAdditionaDocument->update([
                "spk_additional_document_ktp" => $imagePathKtp,
            ]);
        }
        if ($request->hasFile('spk_additional_document_kk')) {
            $imagePathKK = $request->file('spk_additional_document_kk')->store('spk', 'public');
            if (!$findAdditionaDocument) {
                DB::rollBack();
                throw new \Exception("spk_additional_document kk not found!", 400);
            }
            $findAdditionaDocument->update([
                "spk_additional_document_kk" => $imagePathKK,
            ]);
        }



        return $findAdditionaDocument;
    }

    function updateSpkDocumentAnother($spk_id, $request)
    {
        $createSpkDocument = [];
        if ($request->spk_additional_document_another) {
            foreach ($request->file("spk_additional_document_another") as $item) {
                $imagePath = $item->store('spk', 'public');

                $createSpkDocument[] = SpkAdditionalDocumentAnother::create([
                    "spk_id" => $spk_id,
                    "spk_additional_document_another_name" => $imagePath
                ]);
            }
        }

        return $createSpkDocument;
    }

    function updateSpkPricing($spk_id, $request)
    {
        $findPricing = SpkPricing::where("spk_id", $spk_id)->first();
        if (!$findPricing) {
            DB::rollBack();
            throw new \Exception("spk_pricing not found!", 400);
        }

        $findPricing->update([
            "spk_pricing_off_the_road" => $request->spk_pricing_off_the_road,
            "spk_pricing_bbn" => $request->spk_pricing_bbn,
            "spk_pricing_on_the_road" => $request->spk_pricing_on_the_road,
            "spk_pricing_indent_nominal" => $request->spk_pricing_indent_nominal,
            "spk_pricing_discount" => $request->spk_pricing_discount,
            "spk_pricing_subsidi" => $request->spk_pricing_subsidi,
            "spk_pricing_booster" => $request->spk_pricing_booster,
            "spk_pricing_commission" => $request->spk_pricing_commission,
            "spk_pricing_commission_surveyor" => $request->spk_pricing_commission_surveyor,
            "broker_id" => $request->broker_id,
            "spk_pricing_broker_name" => $request->spk_pricing_broker_name,
            "spk_pricing_broker_commission" => $request->spk_pricing_broker_commission,
            "spk_pricing_cashback" => $request->spk_pricing_cashback,
            "spk_pricing_delivery_cost" => $request->spk_pricing_delivery_cost, "spk_pricing_on_the_road_note" => $request->spk_pricing_on_the_road_note,
            "spk_pricing_indent_note" => $request->spk_pricing_indent_note,
            "spk_pricing_discount_note" => $request->spk_pricing_discount_note,
            "spk_pricing_subsidi_note" => $request->spk_pricing_subsidi_note,
            "spk_pricing_booster_note" => $request->spk_pricing_booster_note,
            "spk_pricing_commission_note" => $request->spk_pricing_commission_note,
            "spk_pricing_surveyor_commission_note" => $request->spk_pricing_surveyor_commission_note,
            "spk_pricing_broker_note" => $request->spk_pricing_broker_note,
            "spk_pricing_broker_commission_note" => $request->spk_pricing_broker_commission_note,
            "spk_pricing_cashback_note" => $request->spk_pricing_cashback_note,
            "spk_pricing_delivery_cost_note" => $request->spk_pricing_delivery_cost_note,

        ]);

        return $findPricing;
    }

    function updateSpkPricingAccecories($spk_id, $request)
    {
        $updatedItems = [];

        // Pastikan $request->spk_pricing_accecories_price adalah array dan memiliki elemen
        if (is_array($request->spk_pricing_accecories_price) && count($request->spk_pricing_accecories_price) > 0) {
            foreach ($request->spk_pricing_accecories_price as $item) {
                // Periksa apakah 'spk_pricing_accecories_id' disetel dalam setiap item
                if (!isset($item["spk_pricing_accecories_id"])) {
                    $updatedItems[] = SpkPricingAccecories::create([
                        "spk_id" => $spk_id,
                        "spk_pricing_accecories_price" => $item["price"],
                        "spk_pricing_accecories_note" => isset($item["note"]) ? $item["note"] : null
                    ]);
                } else {

                    // Perbarui entri SpkPricingAccecories sesuai dengan spk_pricing_accecories_id
                    $accecories = SpkPricingAccecories::where("spk_pricing_accecories_id", $item["spk_pricing_accecories_id"])->first();

                    // Periksa apakah entri ditemukan
                    if ($accecories) {
                        $accecories->update([
                            "spk_pricing_accecories_price" => $item["price"],
                            "spk_pricing_accecories_note" => isset($item["note"]) ? $item["note"] : null
                        ]);
                        // Tambahkan entri yang telah diperbarui ke dalam array
                        $updatedItems[] = $accecories;
                    }
                }
            }
        }

        return $updatedItems;
    }

    function updateSpkPricingAdditional($spk_id, $request)
    {
        $updatedItems = [];

        // Pastikan $request->spk_pricing_additional_price adalah array dan memiliki elemen
        if (is_array($request->spk_pricing_additional_price) && count($request->spk_pricing_additional_price) > 0) {
            foreach ($request->spk_pricing_additional_price as $item) {
                // Periksa apakah 'spk_pricing_additional_id' disetel dalam setiap item
                if (!isset($item["spk_pricing_additional_id"])) {
                    // Jika 'spk_pricing_additional_id' tidak disetel, buat entri baru
                    $create = SpkPricingAdditional::create([
                        "spk_id" => $spk_id,
                        "spk_pricing_additional_price" => $item["price"],
                        "spk_pricing_additional_note" => isset($item["note"]) ? $item["note"] : null
                    ]);
                    $updatedItems[] = $create;
                } else {
                    // Jika 'spk_pricing_additional_id' disetel, perbarui entri yang ada
                    $accessory = SpkPricingAdditional::find($item["spk_pricing_additional_id"]);

                    // Periksa apakah entri ditemukan
                    if ($accessory) {
                        $accessory->update([
                            "spk_pricing_additional_price" => $item["price"],
                            "spk_pricing_additional_note" => isset($item["note"]) ? $item["note"] : null
                        ]);
                        // Tambahkan entri yang telah diperbarui ke dalam array
                        $updatedItems[] = $accessory;
                    } else {
                        // Handle error gracefully if entry not found
                        // Misalnya, lewati entri ini atau tangani kesalahan sesuai kebutuhan Anda
                    }
                }
            }
        }

        return $updatedItems;
    }

    function updateDeliveryKtp($spk_id, $request)
    {
        $findDeliveryKtp = SpkDeliveryKtp::where("spk_id", $spk_id)->first();
        if (!$findDeliveryKtp) {
            return SpkDeliveryKtp::create([
                "spk_id" => $spk_id, "spk_delivery_ktp_customer_name" => $request->spk_delivery_ktp_customer_name,
                "spk_delivery_ktp_customer_address" => $request->spk_delivery_ktp_customer_address,
                "spk_delivery_ktp_city" => $request->spk_delivery_ktp_city,
                "spk_delivery_ktp_no_phone" => $request->spk_delivery_ktp_no_phone,
                "spk_delivery_ktp_no_telp" => $request->spk_delivery_ktp_no_telp

            ]);
        } else {

            $findDeliveryKtp->update([
                "spk_delivery_ktp_customer_name" => $request->spk_delivery_ktp_customer_name,
                "spk_delivery_ktp_customer_address" => $request->spk_delivery_ktp_customer_address,
                "spk_delivery_ktp_city" => $request->spk_delivery_ktp_city,
                "spk_delivery_ktp_no_phone" => $request->spk_delivery_ktp_no_phone,
                "spk_delivery_ktp_no_telp" => $request->spk_delivery_ktp_no_telp
            ]);

            return $findDeliveryKtp;
        }
    }

    function updateDeliveryNeq($spk_id, $request)
    {
        $findDeliveryNeq = SpkDeliveryNeq::where("spk_id", $spk_id)->first();
        if (!isset($findDeliveryNeq->spk_id)) {
            $createNew =
                SpkDeliveryNeq::create([
                    "spk_id" => $spk_id,
                    "dealer_neq_id" => $request->dealer_delivery_neq_id,
                    "dealer_delivery_neq_customer_name" => $request->dealer_delivery_neq_customer_name,
                    "dealer_delivery_neq_customer_no_phone" => $request->dealer_delivery_neq_customer_no_phone,
                ]);
            return $createNew;
        } else {
            $findDeliveryNeq->update([
                "dealer_neq_id" => $request->dealer_delivery_neq_id,
                "dealer_delivery_neq_customer_name" => $request->dealer_delivery_neq_customer_name,
                "dealer_delivery_neq_customer_no_phone" => $request->dealer_delivery_neq_customer_no_phone,
            ]);

            return $findDeliveryNeq;
        }
    }

    function updateDeliveryDomicile($spk_id, $request)
    {
        $findDeliveryDomicile = SpkDeliveryDomicile::where("spk_id", $spk_id)->first();
        if (!$findDeliveryDomicile) {
            return SpkDeliveryDomicile::create([
                "spk_id" => $spk_id,
                "spk_delivery_domicile_customer_name" => $request->spk_delivery_domicile_customer_name,
                "spk_delivery_domicile_address" => $request->spk_delivery_domicile_address,
                "spk_delivery_domicile_city" => $request->spk_delivery_domicile_city,
                "spk_delivery_file_sk" => "null"
            ]);
        } else {
            $findDeliveryDomicile->update([
                "spk_delivery_domicile_customer_name" => $request->spk_delivery_domicile_customer_name,
                "spk_delivery_domicile_address" => $request->spk_delivery_domicile_address,
                "spk_delivery_domicile_city" => $request->spk_delivery_domicile_city,
                "spk_delivery_file_sk" => "null"
            ]);

            return $findDeliveryDomicile;
        }
    }


    function updateDeliveryDealer($spk_id, $request)
    {
        $findDeliveryDealer = SpkDeliveryDealer::where("spk_id", $spk_id)->first();
        if (!$findDeliveryDealer) {
            return SpkDeliveryDealer::create([
                "spk_id" => $spk_id,
                "spk_delivery_dealer_customer_name" => $request->spk_delivery_dealer_customer_name,
                "spk_delivery_dealer_no_phone" => $request->spk_delivery_dealer_no_phone
            ]);
        } else {
            $findDeliveryDealer->update([
                "spk_delivery_dealer_customer_name" => $request->spk_delivery_dealer_customer_name,
                "spk_delivery_dealer_no_phone" => $request->spk_delivery_dealer_no_phone
            ]);

            return $findDeliveryDealer;
        }
    }

    function createFileSKupdate($createSpkDelivery, $request)
    {

        $createSpkDocument = [];
        if ($request->spk_delivery_file_sk) {
            // if (!isset($createSpkDelivery->spk_delivery_domicile_id)) {
            //     DB::rollBack();
            //     throw new \Exception("uuid spk_delivery_domicile_id not found!", 400);
            // }
            foreach ($request->file("spk_delivery_file_sk") as $item) {
                $imagePath = $item->store('spk', 'public');

                $createSpkDocument[] = SpkDeliveryFileSk::create([
                    "spk_delivery_domicile_id" => $createSpkDelivery->spk_delivery_domicile_id,
                    "file" => $imagePath
                ]);
            }
        }

        return $createSpkDocument;
    }

    public function updateSpk(Request $request, $spk_id)
    {
        try {
            $validator  = Validator::make($request->all(), self::validatorUpdate);
            self::isDealerRequired($validator);
            self::isDealerNeqRequired($validator);
            self::spk_transaction_method_payment_credit($validator);
            self::spk_transaction_method_payment_cash($validator);
            self::isSelectedSpkDeliveryKtp($validator);
            self::isSelectedSpkDeliveryNeq($validator);
            self::isSelectedSpkDeliveryDomicile($validator);
            self::isSelectedSpkDeliveryDealer($validator);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            DB::beginTransaction();

            $findSpk = self::findSPK($spk_id);

            $findSpk->update([
                "spk_delivery_type" => $request->spk_delivery_type
            ]);

            //update SPK General
            $updateSPKGeneral = self::updateSpkGeneral($spk_id, $request);

            //update SPK unit
            $updateSPKUnit = self::updateSpkUnit($spk_id, $request);

            //update SPK transaction
            $updateSPKTransaction = self::updateSpkTransaction($spk_id, $request);

            //update SPK customer
            $updateSPKCustomer = self::updateSpkCustomer($spk_id, $request);

            //update SPK legal
            $updateSPKLegal = self::updateSpkLegal($spk_id, $request);

            // update spk document
            $updateSpkDocument = self::updateSpkDocument($spk_id, $request);

            // update spk document another
            $updateSpkDocumentAnother = self::updateSpkDocumentAnother($spk_id, $request);

            // update spk pricing
            $updateSpkPricing = self::updateSpkPricing($spk_id, $request);

            // update spk pricing accecories
            $updateSpkPricingAccecories = self::updateSpkPricingAccecories($spk_id, $request);

            // update spk pricing additional
            $updateSpkPricingAdditional = self::updateSpkPricingAdditional($spk_id, $request);

            //buat spk log
            $createSPKLog = self::createSpkLog($findSpk, $user, "update Spk");

            //buat spk delivery berdasarkan type
            $createSPKDelivery = null;
            if ($request->spk_delivery_type === "ktp") {
                $createSPKDelivery = self::updateDeliveryKtp($spk_id, $request);
            }
            if ($request->spk_delivery_type === "neq") {
                $createSPKDelivery = self::updateDeliveryNeq($spk_id, $request);
            }
            if ($request->spk_delivery_type === "dealer") {
                $createSPKDelivery = self::updateDeliveryDealer($spk_id, $request);
            }
            if ($request->spk_delivery_type === "domicile") {
                $createSPKDelivery = self::updateDeliveryDomicile($spk_id, $request);
                $createFileSK = null;
                if (isset($createSPKDelivery->spk_delivery_domicile_id)) {
                    $createFileSK = self::createFileSKupdate($createSPKDelivery, $request);
                }
            }

            $data = [
                "spk" => $findSpk,
                "spk_general" => $updateSPKGeneral,
                "spk_log" => $createSPKLog,
                "spk_unit" => $updateSPKUnit,
                "spk_transaction" => $updateSPKTransaction,
                "spk_customer" => $updateSPKCustomer,
                "spk_legal" => $updateSPKLegal,
                "spk_document" => $updateSpkDocument,
                "spk_document_another" => $updateSpkDocumentAnother,
                "spk_pricing" => $updateSpkPricing,
                "spk_pricing_accecories" => $updateSpkPricingAccecories,
                "spk_pricing_additional" => $updateSpkPricingAdditional,
                "spk_delivery" => $createSPKDelivery
            ];

            if ($request->spk_delivery_type === "domicile") {
                $data["file_sk"] = $createFileSK;
            }

            DB::commit();

            return ResponseFormatter::success($data, "Successfully updated SPK !");
        } catch (\Throwable $e) {
            $statusCode = $e->getCode() === 0 ? 400 : $e->getCode();
            DB::rollback();

            if ($statusCode === 1000) {
                return ResponseFormatter::error("The HTTP status code \"1000\" is not valid.", "invalid status", $statusCode);
            }

            return ResponseFormatter::error($e->getMessage(), $statusCode == 400 ? "bad request" : "internal server", $statusCode);
        }
    }

    public function getDetailSpk(Request $request, $spk_id)
    {
        try {

            $getDetail = Spk::where("spk_id", $spk_id)->first();

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getPaginateSpk(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $q = $request->input("q");
            $sales_name = $request->input("sales_name");
            $spk_transaction_method_payment = $request->input("spk_transaction_method_payment");
            $motor_name = $request->input("motor_name");
            $spk_status = $request->input("spk_status");
            $spk_general_location = $request->input("spk_general_location");
            $dealer_id = $request->input("dealer_id");
            $dealer_neq_id = $request->input("dealer_neq_id");
            $is_cro_check = $request->input("is_cro_check");
            $date = $request->input("date");

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);



            $getPaginate = Spk::latest()
                ->where("dealer_id", $getDealerSelected->dealer_id)
                ->when($is_cro_check === "null", function ($query) {
                    return $query->whereNull("is_cro_check");
                })
                ->when($is_cro_check, function ($query) use ($is_cro_check) {
                    return $query->where("is_cro_check", "LIKE", "%$is_cro_check%");
                })
                ->where("spk_status", "LIKE", "%$spk_status%")
                ->when($date, function ($query) use ($date) {
                    return $query->whereDate('created_at', 'LIKE', "%$date%");
                })
                ->whereHas("spk_general", function ($query) use ($sales_name) {
                    return $query->where("sales_name", "LIKE", "%$sales_name%");
                })
                ->whereHas("spk_transaction", function ($query) use ($spk_transaction_method_payment) {
                    return $query->where("spk_transaction_method_payment", "LIKE", "%$spk_transaction_method_payment%");
                })
                ->whereHas("spk_unit", function ($query) use ($motor_name) {
                    return $query->whereHas("motor", function ($queryMotor) use ($motor_name) {
                        return $queryMotor->where("motor_name", "LIKE", "%$motor_name%");
                    });
                })
                ->whereHas("spk_general", function ($query) use ($spk_general_location) {
                    return $query->where("dealer_id", "LIKE", "%$spk_general_location%")
                        ->orWhere("dealer_neq_id", "LIKE", "%$spk_general_location%");
                })
                ->when($q, function ($query) use ($q) {
                    return $query->where("spk_number", "LIKE", "%$q%")
                        ->orWhereHas("spk_transaction", function ($queryTransaction) use ($q) {
                            return $queryTransaction->where("spk_transaction_method_payment", "LIKE", "%$q%");
                        })
                        ->orWhereHas("spk_customer", function ($queryCustomer) use ($q) {
                            return $queryCustomer->where("spk_customer_name", "LIKE", "%$q%");
                        })
                        ->orWhereHas("spk_unit", function ($queryMotor) use ($q) {
                            return $queryMotor->whereHas("motor", function ($queryMotor2) use ($q) {
                                return $queryMotor2->where("motor_name", "LIKE", "%$q%");
                            })
                                ->orWhereHas("unit", function ($queryUnit) use ($q) {
                                    return $queryUnit->where("unit_frame", "LIKE", "%$q%");
                                });
                        })
                        ->orWhereHas("spk_general", function ($query) use ($q) {
                            return $query->where("sales_name", "LIKE", "%$q%");
                        });
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    // validator untuk update
    const validatorUpdate = [
        "spk_general_location" => "required|in:dealer,neq",
        "indent_id" => "nullable",
        "spk_general_date" => "nullable",
        "sales_name" => "required",
        "sales_id" => "required",
        "spk_general_method_sales" => "required",
        "dealer_id" => "nullable",
        "dealer_neq_id" => "nullable",
        "motor_id" => "required",
        "color_id" => "required",
        "spk_transaction_method_buying" => "required|in:on_the_road,off_the_road",
        "spk_transaction_method_payment" => "required|in:cash,credit",
        "leasing_name" => 'nullable',
        "leasing_id" => 'nullable',
        "spk_transaction_down_payment" => "nullable",
        "spk_transaction_tenor" => "nullable",
        "spk_transaction_instalment" => "nullable",
        // "spk_transaction_surveyor_name" => "nullable",
        "microfinance_name" => "nullable",
        "micro_finance_id" => "nullable",
        //spk customer
        "spk_customer_nik" => "required",
        "spk_customer_name" => "required",
        "spk_customer_address" => "required",
        "province" => "required",
        "province_id" => "required",
        "city" => "required",
        "city_id" => "required",
        "district" => "required",
        "district_id" => "required",
        "sub_district" => "required",
        "sub_district_id" => "required",
        "spk_customer_postal_code" => "nullable",
        "spk_customer_birth_place" => "required",
        "spk_customer_birth_date" => "required",
        "spk_customer_gender" => "required|in:man,woman",
        "spk_customer_telp" => "nullable",
        "spk_customer_no_phone" => "required",
        "spk_customer_no_wa" => "nullable",
        "spk_customer_religion" => "required",
        "marital_id" => "required",
        "marital_name" => "required",
        "hobbies_id" => "nullable",
        "hobbies_name" => "nullable",
        "spk_customer_mother_name" => "nullable",
        "spk_customer_npwp" => 'nullable',
        "spk_customer_email" => "nullable",
        "residence_id" => "required",
        "education_id" => "required",
        "work_id" => "required",
        "residence_name" => "required",
        "education_name" => "required",
        "work_name" => "required",
        "spk_customer_length_of_work" => "nullable",
        "spk_customer_income" => "required",
        "spk_customer_outcome" => "required",
        "motor_brand_id" => "nullable",
        "motor_brand_name" => "nullable",
        "spk_customer_motor_type_before" => "nullable",
        "spk_customer_motor_year_before" => "nullable",

        // spk legal
        "spk_legal_nik" => "required",
        "spk_legal_name" => "required",
        "spk_legal_address" => "required",
        "spk_legal_province" => "required",
        "spk_legal_province_id" => "required",
        "spk_legal_city" => "required",
        "spk_legal_city_id" => "required",
        "spk_legal_district" => "required",
        "spk_legal_district_id" => "required",
        "spk_legal_sub_district" => "required",
        "spk_legal_sub_district_id" => "required",
        "spk_legal_postal_code" => "nullable",
        "spk_legal_birth_place" => "required",
        "spk_legal_birth_date" => "required",
        "spk_legal_gender" => "required|in:man,woman",
        "spk_legal_telp" => "nullable",
        "spk_legal_no_phone" => "required",

        //spk document

        "spk_additional_document_ktp" => "nullable|mimes:png,jpg,pdf|max:5120",
        "spk_additional_document_kk" => "nullable|mimes:png,jpg,pdf|max:5120",
        "spk_additional_document_another.*" => 'nullable|mimes:jpg,png,pdf|max:5120',


        //spk pricing

        "spk_pricing_off_the_road" => "required",
        "spk_pricing_bbn" => "required",
        "spk_pricing_on_the_road" => "required",
        "spk_pricing_indent_nominal" => "nullable",
        "spk_pricing_discount" => "nullable",
        "spk_pricing_subsidi" => "nullable",
        "spk_pricing_booster" => "nullable",
        "spk_pricing_commission" => "nullable",
        "spk_pricing_commission_surveyor" => "nullable",
        "broker_id" => "nullable",
        "spk_pricing_broker_name" => "nullable",
        "spk_pricing_broker_commission" => "nullable",
        "spk_pricing_cashback" => "nullable",
        "spk_pricing_delivery_cost" => "nullable",
        "spk_pricing_on_the_road_note" => "nullable",
        "spk_pricing_indent_note" => "nullable",
        "spk_pricing_discount_note" => "nullable",
        "spk_pricing_subsidi_note" => "nullable",
        "spk_pricing_booster_note" => "nullable",
        "spk_pricing_commission_note" => "nullable",
        "spk_pricing_surveyor_commission_note" => "nullable",
        "spk_pricing_broker_note" => "nullable",
        "spk_pricing_broker_commission_note" => "nullable",
        "spk_pricing_cashback_note" => "nullable",
        "spk_pricing_delivery_cost_note" => "nullable",



        //spk accecories

        "spk_pricing_accecories_price" => "nullable|array",
        "spk_pricing_accecories_price.*.price" => "nullable",
        "spk_pricing_accecories_price.*.note" => "nullable",

        //spk accecories additional

        "spk_pricing_additional_price" => "nullable|array",
        "spk_pricing_additional_price.*.price" => "nullable",
        "spk_pricing_additional_price.*.note" => "nullable",

        //spk delivery
        "spk_delivery_type" => "required|in:ktp,neq,domicile,dealer"

    ];

    const validator = [
        "spk_general_location" => "required|in:dealer,neq",
        "indent_id" => "nullable",
        "spk_general_date" => "nullable",
        "sales_name" => "required",
        "sales_id" => "required",
        "spk_general_method_sales" => "required",
        "dealer_id" => "nullable",
        "dealer_neq_id" => "nullable",
        "motor_id" => "required",
        "color_id" => "required",
        "spk_transaction_method_buying" => "required|in:on_the_road,off_the_road",
        "spk_transaction_method_payment" => "required|in:cash,credit",
        "leasing_name" => 'nullable',
        "leasing_id" => 'nullable',
        "spk_transaction_down_payment" => "nullable",
        "spk_transaction_tenor" => "nullable",
        "spk_transaction_instalment" => "nullable",
        // "spk_transaction_surveyor_name" => "nullable",
        "microfinance_name" => "nullable",
        "micro_finance_id" => "nullable",
        //spk customer
        "spk_customer_nik" => "required",
        "spk_customer_name" => "required",
        "spk_customer_address" => "required",
        "province" => "required",
        "province_id" => "required",
        "city" => "required",
        "city_id" => "required",
        "district" => "required",
        "district_id" => "required",
        "sub_district" => "required",
        "sub_district_id" => "required",
        "spk_customer_postal_code" => "nullable",
        "spk_customer_birth_place" => "required",
        "spk_customer_birth_date" => "required",
        "spk_customer_gender" => "required|in:man,woman",
        "spk_customer_telp" => "nullable",
        "spk_customer_no_phone" => "required",
        "spk_customer_no_wa" => "nullable",
        "spk_customer_religion" => "required",
        "marital_id" => "required",
        "marital_name" => "required",
        "hobbies_id" => "nullable",
        "hobbies_name" => "nullable",
        "spk_customer_mother_name" => "nullable",
        "spk_customer_npwp" => 'nullable',
        "spk_customer_email" => "nullable",
        "residence_id" => "required",
        "education_id" => "required",
        "work_id" => "required",
        "residence_name" => "required",
        "education_name" => "required",
        "work_name" => "required",
        "spk_customer_length_of_work" => "nullable",
        "spk_customer_income" => "required",
        "spk_customer_outcome" => "required",
        "motor_brand_id" => "nullable",
        "motor_brand_name" => "nullable",
        "spk_customer_motor_type_before" => "nullable",
        "spk_customer_motor_year_before" => "nullable",

        // spk legal
        "spk_legal_nik" => "required",
        "spk_legal_name" => "required",
        "spk_legal_address" => "required",
        "spk_legal_province" => "required",
        "spk_legal_province_id" => "required",
        "spk_legal_city" => "required",
        "spk_legal_city_id" => "required",
        "spk_legal_district" => "required",
        "spk_legal_district_id" => "required",
        "spk_legal_sub_district" => "required",
        "spk_legal_sub_district_id" => "required",
        "spk_legal_postal_code" => "nullable",
        "spk_legal_birth_place" => "required",
        "spk_legal_birth_date" => "required",
        "spk_legal_gender" => "required|in:man,woman",
        "spk_legal_telp" => "nullable",
        "spk_legal_no_phone" => "required",

        //spk document

        "spk_additional_document_ktp" => "required|mimes:png,jpg,pdf|max:5120",
        "spk_additional_document_kk" => "required|mimes:png,jpg,pdf|max:5120",
        "spk_additional_document_another.*" => 'nullable|mimes:jpg,png,pdf|max:5120',


        //spk pricing

        "spk_pricing_off_the_road" => "required",
        "spk_pricing_bbn" => "required",
        "spk_pricing_on_the_road" => "required",
        "spk_pricing_indent_nominal" => "nullable",
        "spk_pricing_discount" => "nullable",
        "spk_pricing_subsidi" => "nullable",
        "spk_pricing_booster" => "nullable",
        "spk_pricing_commission" => "nullable",
        "spk_pricing_commission_surveyor" => "nullable",
        "broker_id" => "nullable",
        "spk_pricing_broker_name" => "nullable",
        "spk_pricing_broker_commission" => "nullable",
        "spk_pricing_cashback" => "nullable",
        "spk_pricing_delivery_cost" => "nullable",
        "spk_pricing_on_the_road_note" => "nullable",
        "spk_pricing_indent_note" => "nullable",
        "spk_pricing_discount_note" => "nullable",
        "spk_pricing_subsidi_note" => "nullable",
        "spk_pricing_booster_note" => "nullable",
        "spk_pricing_commission_note" => "nullable",
        "spk_pricing_surveyor_commission_note" => "nullable",
        "spk_pricing_broker_note" => "nullable",
        "spk_pricing_broker_commission_note" => "nullable",
        "spk_pricing_cashback_note" => "nullable",
        "spk_pricing_delivery_cost_note" => "nullable",



        //spk accecories

        "spk_pricing_accecories_price" => "nullable|array",
        "spk_pricing_accecories_price.*.price" => "nullable",
        "spk_pricing_accecories_price.*.note" => "nullable",

        //spk accecories additional

        "spk_pricing_additional_price" => "nullable|array",
        "spk_pricing_additional_price.*.price" => "nullable",
        "spk_pricing_additional_price.*.note" => "nullable",

        //spk delivery
        "spk_delivery_type" => "required|in:ktp,neq,domicile,dealer"

    ];


    function isSelectedSpkDeliveryDealer($validator)
    {
        return
            $validator->sometimes(
                ["spk_delivery_dealer_customer_name", "spk_delivery_dealer_no_phone"],
                "required",
                function ($input) {
                    return $input->spk_delivery_type === 'dealer';
                }
            );
    }

    function isSelectedSpkDeliveryDomicile($validator)
    {
        return
            $validator->sometimes(
                ["spk_delivery_domicile_customer_name", "spk_delivery_domicile_address", "spk_delivery_domicile_city"],
                "required",
                function ($input) {
                    return $input->spk_delivery_type === 'domicile';
                }
            )->sometimes(
                ["spk_delivery_file_sk.*"],
                "nullable|mimes:pdf,jpg,png,pdf|max:5120",
                function ($input) {
                    return $input->spk_delivery_type === 'domicile';
                }
            );
    }
    function isSelectedSpkDeliveryNeq($validator)
    {
        return
            $validator->sometimes(
                ["dealer_delivery_neq_id", "dealer_delivery_neq_customer_name", "dealer_delivery_neq_customer_no_phone"],
                "required",
                function ($input) {
                    return $input->spk_delivery_type === 'neq';
                }
            );
    }


    function isSelectedSpkDeliveryKtp($validator)
    {
        return $validator->sometimes(
            ["spk_delivery_ktp_customer_name", "spk_delivery_ktp_customer_address", "spk_delivery_ktp_city", "spk_delivery_ktp_no_phone"],
            "required",
            function ($input) {
                return $input->spk_delivery_type === 'ktp';
            }
        )->sometimes(
            'spk_delivery_ktp_no_telp',
            'nullable',
            function ($input) {
                return $input->spk_delivery_type === 'ktp';
            }
        );
    }

    function isDealerRequired($validator)
    {
        return $validator->sometimes("dealer_id", 'required', function ($input) {
            return $input->spk_general_location == 'dealer';
        });
    }
    function isDealerNeqRequired($validator)
    {
        return $validator->sometimes(["dealer_neq_id", "dealer_id"], 'required', function ($input) {
            return $input->spk_general_location == 'neq';
        });
    }

    function spk_transaction_method_payment_credit($validator)
    {
        return $validator->sometimes(["leasing_name", "leasing_id", "spk_transaction_down_payment", "spk_transaction_tenor", "spk_transaction_instalment", "spk_transaction_surveyor_name"], "required", function ($input) {
            return $input->spk_transaction_method_buying == 'credit';
        });
    }

    function spk_transaction_method_payment_cash($validator)
    {
        return
            $validator->sometimes(["microfinance_name", "micro_finance_id"], 'nullable', function ($input) {
                return $input->spk_transaction_method_buying == 'cash';
            });
    }

    function createSPKMaster($dealerSelected, $request)
    {
        return Spk::create([
            "spk_number"
            => GenerateNumber::generate("SPK", GenerateAlias::generate($dealerSelected->dealer->dealer_name), "spks", "spk_number"),
            "dealer_id" => $dealerSelected->dealer_id,
            "spk_status" => "create",
            "spk_delivery_type" => $request->spk_delivery_type
        ]);
    }

    function createSPKGeneral($createSPK, $request)
    {
        return SpkGeneral::create([
            "spk_id" => $createSPK->spk_id,
            "indent_id" => $request->indent_id,
            "spk_general_date" => $request->spk_general_date,
            "spk_general_location" => $request->spk_general_location,
            "sales_name" => $request->sales_name,
            "sales_id" => $request->sales_id,
            "spk_general_method_sales" => $request->spk_general_method_sales,
            "dealer_id" => $request->dealer_id,
            "dealer_neq_id" => $request->dealer_neq_id
        ]);
    }

    function createSPKUnit($createSPK, $request)
    {
        return SpkUnit::create([
            "motor_id" => $request->motor_id,
            "spk_id" => $createSPK->spk_id,
            "color_id" => $request->color_id
        ]);
    }

    function createSpkLog($createSPK, $user, $action)
    {
        return SpkLog::create([
            "spk_log_action" => $action,
            "user_id" => $user->user_id,
            "spk_id" => $createSPK->spk_id
        ]);
    }

    function createSpkTransaction($createSPK, $request)
    {

        if ($request->spk_transaction_method_payment == "cash") {
            return
                SpkTransaction::create([
                    "spk_id" => $createSPK->spk_id,
                    "spk_transaction_method_buying" => $request->spk_transaction_method_buying,
                    "spk_transaction_method_payment" => $request->spk_transaction_method_payment,
                    "spk_transaction_surveyor_name" => $request->spk_transaction_surveyor_name,
                    "microfinance_name" => $request->microfinance_name,
                    "micro_finance_id" => $request->micro_finance_id,
                ]);
        } else {
            return SpkTransaction::create([
                "spk_id" => $createSPK->spk_id,
                "spk_transaction_method_buying" => $request->spk_transaction_method_buying,
                "spk_transaction_method_payment" => $request->spk_transaction_method_payment,
                "leasing_name" => $request->leasing_name,
                "leasing_id" => $request->leasing_id,
                "spk_transaction_down_payment" => $request->spk_transaction_down_payment,
                "spk_transaction_tenor" => $request->spk_transaction_tenor,
                "spk_transaction_instalment" =>  $request->spk_transaction_instalment,
                "spk_transaction_surveyor_name" => $request->spk_transaction_surveyor_name,
                "microfinance_name" => $request->microfinance_name,
                "micro_finance_id" => $request->micro_finance_id,
            ]);
        }
    }

    function createSpkCustomer($createSPK, $request)
    {
        return  SpkCustomer::create([
            "spk_id" => $createSPK->spk_id,
            "spk_customer_nik" => $request->spk_customer_nik,
            "spk_customer_name" => $request->spk_customer_name,
            "spk_customer_address" => $request->spk_customer_address,
            "province" => $request->province,
            "province_id" => $request->province_id,
            "city" => $request->city,
            "city_id" => $request->city_id,
            "district" => $request->district,
            "district_id" => $request->district_id,
            "sub_district" => $request->sub_district,
            "sub_district_id" => $request->sub_district_id,
            "spk_customer_postal_code" => $request->spk_customer_postal_code,
            "spk_customer_birth_place" => $request->spk_customer_birth_place,
            "spk_customer_birth_date" => $request->spk_customer_birth_date,
            "spk_customer_gender" => $request->spk_customer_gender,
            "spk_customer_telp" => $request->spk_customer_telp,
            "spk_customer_no_wa" => $request->spk_customer_no_wa,
            "spk_customer_no_phone" => $request->spk_customer_no_phone,
            "spk_customer_religion" => $request->spk_customer_religion,
            "marital_id" => $request->marital_id,
            "hobbies_id" => $request->hobbies_id,
            "marital_name" => $request->marital_name,
            "hobbies_name" => $request->hobbies_name,
            "spk_customer_mother_name" => $request->spk_customer_mother_name,
            "spk_customer_npwp" => $request->spk_customer_npwp,
            "spk_customer_email" => $request->spk_customer_email,
            "residence_id" => $request->residence_id,
            "education_id" => $request->education_id,
            "work_id" => $request->work_id,
            "residence_name" => $request->residence_name,
            "education_name" => $request->education_name,
            "work_name" => $request->work_name,
            "spk_customer_length_of_work" => $request->spk_customer_length_of_work,
            "spk_customer_income" => $request->spk_customer_income,
            "spk_customer_outcome" => $request->spk_customer_outcome,
            "motor_brand_id" => $request->motor_brand_id,
            "motor_brand_name" => $request->motor_brand_name,
            "spk_customer_motor_type_before" => $request->spk_customer_motor_type_before,
            "spk_customer_motor_year_before" => $request->spk_customer_motor_year_before
        ]);
    }


    function createSpkLegal($createSPK, $request)
    {
        return SpkLegal::create([
            "spk_id" => $createSPK->spk_id,
            "spk_legal_nik" => $request->spk_legal_nik,
            "spk_legal_name" => $request->spk_legal_name,
            "spk_legal_address" => $request->spk_legal_address,
            "province" => $request->spk_legal_province,
            "province_id" => $request->spk_legal_province_id,
            "city" => $request->spk_legal_city,
            "city_id" => $request->spk_legal_city_id,
            "district" => $request->spk_legal_district,
            "district_id" => $request->spk_legal_district_id,
            "sub_district" => $request->spk_legal_sub_district,
            "sub_district_id" => $request->spk_legal_sub_district_id,
            "spk_legal_postal_code" => $request->spk_legal_postal_code,
            "spk_legal_birth_place" => $request->spk_legal_birth_place,
            "spk_legal_birth_date" => $request->spk_legal_birth_date,
            "spk_legal_gender" => $request->spk_legal_gender,
            "spk_legal_telp" => $request->spk_legal_telp,
            "spk_legal_no_phone" => $request->spk_legal_no_phone
        ]);
    }

    function createSpkDocument($createSPK, $request)
    {
        if ($request->hasFile('spk_additional_document_ktp')) {
            $imagePathKtp = $request->file('spk_additional_document_ktp')->store('spk', 'public');
        } else {
            $imagePathKtp = null; // or any default value you prefer
        }
        if ($request->hasFile('spk_additional_document_kk')) {
            $imagePathKK = $request->file('spk_additional_document_kk')->store('spk', 'public');
        } else {
            $imagePathKK = null; // or any default value you prefer
        }
        return SpkAdditionalDocument::create([
            "spk_id" => $createSPK->spk_id,
            "spk_additional_document_ktp" => $imagePathKtp,
            "spk_additional_document_kk" => $imagePathKK,
        ]);
    }

    function createSpkDocumentAnother($createSPK, $request)
    {
        $createSpkDocument = [];
        if ($request->spk_additional_document_another) {
            foreach ($request->file("spk_additional_document_another") as $item) {
                $imagePath = $item->store('spk', 'public');

                $createSpkDocument[] = SpkAdditionalDocumentAnother::create([
                    "spk_id" => $createSPK->spk_id,
                    "spk_additional_document_another_name" => $imagePath
                ]);
            }
        }

        return $createSpkDocument;
    }

    function createSpkPricing($createSpk, $request)
    {
        return SpkPricing::create([
            "spk_id" => $createSpk->spk_id,
            "spk_pricing_off_the_road" => $request->spk_pricing_off_the_road,
            "spk_pricing_bbn" => $request->spk_pricing_bbn,
            "spk_pricing_on_the_road" => $request->spk_pricing_on_the_road,
            "spk_pricing_indent_nominal" => $request->spk_pricing_indent_nominal,
            "spk_pricing_discount" => $request->spk_pricing_discount,
            "spk_pricing_subsidi" => $request->spk_pricing_subsidi,
            "spk_pricing_booster" => $request->spk_pricing_booster,
            "spk_pricing_commission" => $request->spk_pricing_commission,
            "spk_pricing_commission_surveyor" => $request->spk_pricing_commission_surveyor,
            "broker_id" => $request->broker_id,
            "spk_pricing_broker_name" => $request->spk_pricing_broker_name,
            "spk_pricing_broker_commission" => $request->spk_pricing_broker_commission,
            "spk_pricing_cashback" => $request->spk_pricing_cashback,
            "spk_pricing_delivery_cost" => $request->spk_pricing_delivery_cost, "spk_pricing_on_the_road_note" => $request->spk_pricing_on_the_road_note,
            "spk_pricing_indent_note" => $request->spk_pricing_indent_note,
            "spk_pricing_discount_note" => $request->spk_pricing_discount_note,
            "spk_pricing_subsidi_note" => $request->spk_pricing_subsidi_note,
            "spk_pricing_booster_note" => $request->spk_pricing_booster_note,
            "spk_pricing_commission_note" => $request->spk_pricing_commission_note,
            "spk_pricing_surveyor_commission_note" => $request->spk_pricing_surveyor_commission_note,
            "spk_pricing_broker_note" => $request->spk_pricing_broker_note,
            "spk_pricing_broker_commission_note" => $request->spk_pricing_broker_commission_note,
            "spk_pricing_cashback_note" => $request->spk_pricing_cashback_note,
            "spk_pricing_delivery_cost_note" => $request->spk_pricing_delivery_cost_note,
            "spk_pricing_over_discount" => $request->spk_pricing_over_discount,
            "spk_pricing_over_discount_note" => $request->spk_pricing_over_discount_note,
        ]);
    }

    function createSpkPricingAccecories($createSPK, $request)
    {
        $create = [];

        if (is_array($request->spk_pricing_accecories_price) && count($request->spk_pricing_accecories_price) > 0) {
            foreach ($request->spk_pricing_accecories_price as $item) {
                $create[] = SpkPricingAccecories::create([
                    "spk_id" => $createSPK->spk_id,
                    "spk_pricing_accecories_price" => $item["price"],
                    "spk_pricing_accecories_note" => isset($item["note"]) ? $item["note"] : null
                ]);
            }
        }

        return $create;
    }
    function createSpkPricingAdditional($createSPK, $request)
    {
        $create = [];

        if (is_array($request->spk_pricing_additional_price) && count($request->spk_pricing_additional_price) > 0) {
            foreach ($request->spk_pricing_additional_price as $item) {
                $create[] = SpkPricingAdditional::create([
                    "spk_id" => $createSPK->spk_id,
                    "spk_pricing_additional_price" => $item["price"],
                    "spk_pricing_additional_note" => isset($item["note"]) ? $item["note"] : null
                ]);
            }
        }

        return $create;
    }

    function createSpkDeliveryKtp($createSpk, $request)
    {
        if ($request->spk_delivery_type === "ktp") {
            return SpkDeliveryKtp::create([
                "spk_id" => $createSpk->spk_id,
                "spk_delivery_ktp_customer_name" => $request->spk_delivery_ktp_customer_name,
                "spk_delivery_ktp_customer_address" => $request->spk_delivery_ktp_customer_address,
                "spk_delivery_ktp_city" => $request->spk_delivery_ktp_city,
                "spk_delivery_ktp_no_phone" => $request->spk_delivery_ktp_no_phone,
                "spk_delivery_ktp_no_telp" => $request->spk_delivery_ktp_no_telp
            ]);
        }
    }

    function createSpkDeliveryNeq($createSPK, $request)
    {
        return SpkDeliveryNeq::create([
            "spk_id" => $createSPK->spk_id,
            "dealer_neq_id" => $request->dealer_delivery_neq_id,
            "dealer_delivery_neq_customer_name" => $request->dealer_delivery_neq_customer_name,
            "dealer_delivery_neq_customer_no_phone" => $request->dealer_delivery_neq_customer_no_phone,
        ]);
    }

    function createSpkDeliveryDomicile($createSPK, $request)
    {
        return SpkDeliveryDomicile::create([
            "spk_id" => $createSPK->spk_id,
            "spk_delivery_domicile_customer_name" => $request->spk_delivery_domicile_customer_name,
            "spk_delivery_domicile_address" => $request->spk_delivery_domicile_address,
            "spk_delivery_domicile_city" => $request->spk_delivery_domicile_city,
            "spk_delivery_file_sk" => "null"
        ]);
    }

    function createFileSK($createSpkDelivery, $request)
    {


        $createSpkDocument = [];
        if ($request->spk_delivery_file_sk) {
            foreach ($request->file("spk_delivery_file_sk") as $item) {
                $imagePath = $item->store('spk', 'public');

                $createSpkDocument[] = SpkDeliveryFileSk::create([
                    "spk_delivery_domicile_id" => $createSpkDelivery->spk_delivery_domicile_id,
                    "file" => $imagePath
                ]);
            }
        }

        return $createSpkDocument;
    }

    function creaeteSpkDeliveryDealer($createSPK, $request)
    {
        return SpkDeliveryDealer::create([
            "spk_id" => $createSPK->spk_id,
            "spk_delivery_dealer_customer_name" => $request->spk_delivery_dealer_customer_name,
            "spk_delivery_dealer_no_phone" => $request->spk_delivery_dealer_no_phone
        ]);
    }

    public function createSPK(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), self::validator);

            self::isDealerRequired($validator);
            self::isDealerNeqRequired($validator);
            self::spk_transaction_method_payment_credit($validator);
            self::spk_transaction_method_payment_cash($validator);
            self::isSelectedSpkDeliveryKtp($validator);
            self::isSelectedSpkDeliveryNeq($validator);
            self::isSelectedSpkDeliveryDomicile($validator);
            self::isSelectedSpkDeliveryDealer($validator);
            //custome body untuk adira ==>null

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            DB::beginTransaction();

            // buat spk
            $createSPK = self::createSPKMaster($getDealerSelected, $request);

            //buat spk general
            $createSPKGeneral = self::createSPKGeneral($createSPK, $request);

            //buat spk unit
            $createSPKUnit = self::createSPKUnit($createSPK, $request);

            //buat spk transaction
            $createSPKTransaction = self::createSpkTransaction($createSPK, $request);


            //buat spk customer
            $createSPKCustomer = self::createSpkCustomer($createSPK, $request);

            //buat spk legal
            $createSPKLegal = self::createSpkLegal($createSPK, $request);

            //buat spk document
            $createSPKDocument = self::createSpkDocument($createSPK, $request);


            //buat spk document another
            $createSpkAnotherFile = self::createSpkDocumentAnother($createSPK, $request);

            //buat spk pricing
            $createSPKPricing = self::createSpkPricing($createSPK, $request);

            //buat spk pricing accecories
            $createSPKPricingAccecroies = self::createSpkPricingAccecories($createSPK, $request);

            //buat spk pricing additional
            $createSPKPricingAdditional = self::createSpkPricingAdditional($createSPK, $request);


            //buat spk delivery berdasarkan type
            if ($request->spk_delivery_type === "ktp") {
                $createSPKDelivery = self::createSpkDeliveryKtp($createSPK, $request);
            }
            if ($request->spk_delivery_type === "neq") {
                $createSPKDelivery = self::createSpkDeliveryNeq($createSPK, $request);
            }
            if ($request->spk_delivery_type === "dealer") {
                $createSPKDelivery = self::creaeteSpkDeliveryDealer($createSPK, $request);
            }
            if ($request->spk_delivery_type === "domicile") {
                $createSPKDelivery = self::createSpkDeliveryDomicile($createSPK, $request);
                $createFileSK = null;
                if (isset($createSPKDelivery->spk_delivery_domicile_id)) {
                    $createFileSK = self::createFileSK($createSPKDelivery, $request);
                }
            }

            //buat spk log
            $createSPKLog = self::createSpkLog($createSPK, $user, "Create Spk");

            $data = [
                "spk" => $createSPK,
                "spk_general" => $createSPKGeneral,
                "spk_unit" => $createSPKUnit,
                "spk_log" => $createSPKLog,
                "spk_transaction" => $createSPKTransaction,
                "spk_customer" => $createSPKCustomer,
                "spk_legal" => $createSPKLegal,
                "spk_document" => $createSPKDocument,
                "spk_document_another" => $createSpkAnotherFile,
                "spk_pricing" => $createSPKPricing,
                "spk_pricing_acceccories" => $createSPKPricingAccecroies,
                "spk_pricing_additional" => $createSPKPricingAdditional,
                "spk_delivery" => $createSPKDelivery,
            ];

            if ($request->spk_delivery_type === "domicile") {
                $data["file_sk"] = $createFileSK;
            }
            DB::commit();


            return ResponseFormatter::success($data, "Successfully created SPK !");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
