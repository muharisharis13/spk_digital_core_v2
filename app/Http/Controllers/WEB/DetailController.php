<?php

namespace App\Http\Controllers\WEB;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Indent;
use App\Models\IndentInstansi;
use App\Models\SpkInstansiPayment;
use App\Models\SpkPayment;
use Error;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    //

    public function indent(Request $request, $indent_id)
    {
        try {

            $getDetailIndent = Indent::where("indent_id", $indent_id)
                ->with(["motor", "color", "indent_log.user", "indent_payment.bank", "indent_payment_refund", "spk_general.spk"])
                ->first();

            if (!$getDetailIndent) {
                return ResponseFormatter::error("Indent not found", "Bad Request", 400);
            }
            if ($getDetailIndent->indent_type == 'credit') {
                $getDetailIndent->load(["leasing"]);
            }

            if ($getDetailIndent->indent_type == 'cash') {
                $getDetailIndent->load(["micro_finance"]);
            }



            return view("detail.indent", ["data" => $getDetailIndent]);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function indentInstansi(Request $request, $indent_instansi_id)
    {
        try {

            $getDetail = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)
                ->with(["dealer", "motor"])
                ->first();

            return view("detail.indent_instansi", ["data" => $getDetail]);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function instansi()
    {
        return view("detail.instansi");
    }

    public function instansiPayment(Request $request, $spk_instansi_payment_id)
    {

        try {
            $getDetail = SpkInstansiPayment::with(["spk_instansi_payment_refund", "spk_instansi", "spk_instansi_payment_list.bank", "spk_instansi_payment_list.spk_instansi_payment_list_file"])
                ->where("spk_instansi_payment_id", $spk_instansi_payment_id)->first();


            return view("detail.instansi_payment", ["data" => $getDetail]);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
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
    public function payment(Request $request, $spk_payment_id)
    {

        try {

            $getDetail = SpkPayment::latest()
                ->with(["spk"])
                ->where("spk_payment_id", $spk_payment_id)
                ->first();

            if (!isset($getDetail->spk_payment_type)) {
                return ResponseFormatter::error("payment not found", "Bad Request", 400);
            }

            if ($getDetail->spk_payment_type === "dp") {

                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalDp($getDetail);
            }
            if ($getDetail->spk_payment_type === "leasing") {

                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalLeasing($getDetail);
            }
            if ($getDetail->spk_payment_type === "cash") {

                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalCash($getDetail);
            }



            return view("detail.payment", ["data" => $getDetail]);
            // return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
