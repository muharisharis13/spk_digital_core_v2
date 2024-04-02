<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Indent;
use App\Models\IndentPayment;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ExportPDFController extends Controller
{
    //

    public function printPaymentIndent(Request $request, $indent_payment_id)
    {
        try {
            $getDetailPaymentIndent = IndentPayment::with(["bank", "indent"])->where("indent_payment_id", $indent_payment_id)->first();
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $html = view("pdf.faktur.faktur_payment_indent", ["indent_payment" => $getDetailPaymentIndent, "dealer" => $getDealerSelected])->render();

            // Logika pembuatan PDF
            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            // Simpan PDF sebagai file sementara
            $pdfFilePath = public_path("$getDetailPaymentIndent->indent_payment_id.pdf");
            file_put_contents($pdfFilePath, $pdf->output());

            // Kembalikan PDF langsung sebagai respons
            return Response::download($pdfFilePath, "$getDetailPaymentIndent->indent_payment_id.pdf")->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function printPdfIndent2(Request $request, $indent_id)
    {
        try {

            $getDetailIndent = Indent::where("indent_id", $indent_id)
                ->with(["sales", "motor", "color",  "indent_payment.bank", "dealer", "indent_payment" => function ($query) {
                    $query->where("indent_payment_type", "payment");
                }])
                ->first();
            // $user = Auth::user();
            // $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);
            // Load HTML dari template Blade
            $html = view('pdf.faktur.faktur_non_bootstrap', ["indent" => $getDetailIndent, "dealer" => $getDetailIndent])->render();
            // $html = view('pdf.faktur.faktur', ["indent" => $getDetailIndent, "dealer" => $getDealerSelected])->render();

            // Logika pembuatan PDF
            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            // Simpan PDF sebagai file sementara
            $pdfFilePath = public_path("$getDetailIndent->indent_people_name.pdf");
            file_put_contents($pdfFilePath, $pdf->output());

            // Kembalikan PDF langsung sebagai respons
            return Response::download($pdfFilePath, "$getDetailIndent->indent_people_name.pdf")->deleteFileAfterSend(true);
            // return $pdf->stream("$getDetailIndent->indent_people_name.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
}
