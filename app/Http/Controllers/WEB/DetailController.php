<?php

namespace App\Http\Controllers\WEB;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Indent;
use App\Models\IndentInstansi;
use App\Models\SpkInstansiPayment;
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
    public function payment()
    {
        return view("detail.payment");
    }
}
