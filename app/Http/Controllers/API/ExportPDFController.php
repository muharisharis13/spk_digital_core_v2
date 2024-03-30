<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Indent;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportPDFController extends Controller
{
    //

    public function printPdfIndent2(Request $request, $indent_id)
    {
        try {

            $getDetailIndent = Indent::where("indent_id", $indent_id)
                ->with(["sales", "motor", "color",  "indent_payment.bank", "indent_payment" => function ($query) {
                    $query->where("indent_payment_type", "payment");
                }])
                ->first();
            // Load HTML dari template Blade
            $html = view('pdf.faktur.faktur', ["indent" => $getDetailIndent])->render();

            // Logika pembuatan PDF
            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();

            // Simpan PDF sebagai file sementara
            $pdfFilePath = public_path('generated-pdf.pdf');
            file_put_contents($pdfFilePath, $pdf->output());

            // Kembalikan PDF langsung sebagai respons
            return Response::download($pdfFilePath, 'generated-pdf.pdf')->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function printPdfIndent(Request $request, $indent_id)
    {
        try {

            $getDetailIndent = Indent::where("indent_id", $indent_id)
                ->with(["sales", "motor", "color",  "indent_payment.bank", "indent_payment" => function ($query) {
                    $query->where("indent_payment_type", "payment");
                }])
                ->first();
            // Load HTML dari template Blade
            $html = view('pdf.faktur.faktur', ["indent" => $getDetailIndent])->render();

            // Logika pembuatan PDF
            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();

            // Simpan PDF sebagai file sementara
            $pdfFilePath = public_path('generated-pdf.pdf');
            file_put_contents($pdfFilePath, $pdf->output());

            // Konversi PDF ke JSON
            $pdfContent = file_get_contents($pdfFilePath);
            $base64PdfContent = base64_encode($pdfContent);

            // Hapus file PDF setelah dikonversi
            unlink($pdfFilePath);

            // Kembalikan JSON
            return ResponseFormatter::success($base64PdfContent, "Successfully generated file");
            // return response()->json(['pdf_content' => $base64PdfContent]);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
}
