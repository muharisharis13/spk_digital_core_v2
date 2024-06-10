<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\delivery;
use App\Models\DeliveryRepair;
use App\Models\District;
use App\Models\Indent;
use App\Models\IndentPayment;
use App\Models\Province;
use App\Models\Spk;
use App\Models\SpkPayment;
use App\Models\SubDistrict;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ExportPDFController extends Controller
{
    //

    public function getProvince(Request $request)
    {
        try {
            DB::beginTransaction();
            $getProvince = Http::get('https://api.binderbyte.com/wilayah/provinsi?api_key=7a2b1fd83f501ba6e776b74499f448a155c9eee5268aa82584baea671db240ea');

            $getProvince = $getProvince->json();
            if (isset($getProvince["value"])) {
                foreach ($getProvince["value"] as $item) {
                    $createProvince = Province::create([
                        "province_name" => $item["name"],
                    ]);

                    $getCity = Http::get('https://api.binderbyte.com/wilayah/kabupaten?api_key=7a2b1fd83f501ba6e776b74499f448a155c9eee5268aa82584baea671db240ea&id_provinsi=' . $item["id"]);

                    $getCity = $getCity->json();
                    if (isset($getCity["value"])) {

                        foreach ($getCity["value"] as $itemCity) {
                            $createCity = City::create([
                                "city_name" => $itemCity["name"],
                                "province_id" => $createProvince->province_id
                            ]);

                            $getDistrict = Http::get('https://api.binderbyte.com/wilayah/kecamatan?api_key=7a2b1fd83f501ba6e776b74499f448a155c9eee5268aa82584baea671db240ea&id_kabupaten=' . $itemCity["id"]);

                            $getDistrict = $getDistrict->json();
                            if (isset($getDistrict["value"])) {
                                foreach ($getDistrict["value"] as $itemDistrict) {
                                    $createDistrict =  District::create([
                                        "district_name" => $itemDistrict["name"],
                                        "city_id" => $createCity->city_id
                                    ]);

                                    $getSubDistrict = Http::get('https://api.binderbyte.com/wilayah/kelurahan?api_key=8e49f28e0f2f2cf56393c352613eec358e85fb7077ce6f7f453ebb826a7b1f6d&id_kecamatan=' . $itemDistrict["id"]);

                                    $getSubDistrict = $getSubDistrict->json();

                                    foreach ($getSubDistrict["value"]  as $itemSubDistrict) {
                                        SubDistrict::create([
                                            "sub_district_name" => $itemSubDistrict["name"],
                                            "district_id" => $createDistrict->district_id
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            return ResponseFormatter::success("Berhasil");
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

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


            $html = view('pdf.faktur.faktur_non_bootstrap', ["indent" => $getDetailIndent, "dealer" => $getDetailIndent])->render();

            $pdf = Pdf::loadHTML($html);

            $currentTime = Carbon::now()->timestamp;
            return $pdf->stream("indent-$getDetailIndent->indent_number-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function prinSpk(Request $request, $spk_id)
    {
        try {

            $getDetail = Spk::where("spk_id", $spk_id)
                ->with(["dealer"])
                ->first();

            // return ResponseFormatter::success($getDetail);

            $html = view('pdf.faktur.faktur_spk', ["data" => $getDetail])->render();

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;
            return $pdf->stream("faktur_spk_$spk_id-$currentTime.pdf");
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

    public function printPDFPaymentSPK(Request $request, $spk_payment_id)
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


            // return ResponseFormatter::success($getDetail);
            $pdf = Pdf::loadView('pdf.faktur.faktur_payment_spk', ["data" => $getDetail]);
            $pdf->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_payment_$spk_payment_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function printPDFPayment2(Request $request, $indent_payment_id)
    {

        try {
            $getDetailPaymentIndent = IndentPayment::with(["bank", "indent.dealer", "indent.motor", "indent.color"])->where("indent_payment_id", $indent_payment_id)->first();



            $pdf = Pdf::loadView('pdf.faktur.faktur_payment_indent2', ["data" => $getDetailPaymentIndent]);
            $pdf->setPaper('a4', 'portrait');

            // Kembalikan PDF langsung sebagai respons
            return $pdf->download("faktur_payment_$indent_payment_id.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function printSuratJalan(Request $request)
    {
        try {

            $uuid = $request->input("uuid");
            $typeDelivery = $request->input("typeDelivery");

            $delivery = $this->getDetailDelivery($uuid, $typeDelivery);

            // return ResponseFormatter::success($repair);

            // return ResponseFormatter::success($delivery);
            switch ($typeDelivery) {
                case 'spk':
                    return self::suratJalanSpk($delivery);
                    break;
                case 'repair':
                    return self::suratJalanRepair($delivery);
                    break;
                case 'repair_return':
                    return self::suratJalanRepairReturn($delivery);
                    break;
                case 'event':
                    return self::suratJalanEvent($delivery);
                    break;
                case 'event_return':
                    return self::suratJalanEventReturn($delivery);
                    break;
                case 'neq':
                    return self::suratJalanNeq($delivery);
                    break;
                case 'neq_return':
                    return self::suratJalanNeq($delivery);
                    break;

                default:
                    # code...
                    break;
            }
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }


    private function suratJalanNeq($delivery)
    {
        // return ResponseFormatter::success($delivery);
        $html = view('pdf.faktur.faktur_surat_jalan_neq', ["data" => $delivery])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        $currentTime = Carbon::now()->timestamp;
        return $pdf->stream("Surat-jalan-repair-return-$delivery->delivery_number-$currentTime.pdf");
    }
    private function suratJalanEventReturn($delivery)
    {
        // return ResponseFormatter::success($delivery);
        $html = view('pdf.faktur.faktur_surat_jalan_event_return', ["data" => $delivery])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        $currentTime = Carbon::now()->timestamp;
        return $pdf->stream("Surat-jalan-repair-return-$delivery->delivery_number-$currentTime.pdf");
    }
    private function suratJalanEvent($delivery)
    {
        // return ResponseFormatter::success($delivery);
        $html = view('pdf.faktur.faktur_surat_jalan_event', ["data" => $delivery])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        $currentTime = Carbon::now()->timestamp;
        return $pdf->stream("Surat-jalan-repair-$delivery->delivery_number-$currentTime.pdf");
    }
    private function suratJalanRepairReturn($delivery)
    {
        // return ResponseFormatter::success($delivery);
        $html = view('pdf.faktur.faktur_surat_jalan_repair_return', ["data" => $delivery])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        $currentTime = Carbon::now()->timestamp;
        return $pdf->stream("Surat-jalan-repair-$delivery->delivery_number-$currentTime.pdf");
    }
    private function suratJalanRepair($delivery)
    {
        // return ResponseFormatter::success($delivery);
        $html = view('pdf.faktur.faktur_surat_jalan_repair', ["data" => $delivery])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        $currentTime = Carbon::now()->timestamp;
        return $pdf->stream("Surat-jalan-repair-$delivery->delivery_number-$currentTime.pdf");
    }
    private function suratJalanSpk($delivery)
    {
        // return ResponseFormatter::success($delivery);
        $html = view('pdf.faktur.faktur_surat_jalan', ["data" => $delivery])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        $currentTime = Carbon::now()->timestamp;
        return $pdf->stream("Surat-jalan-spk-$delivery->delivery_number-$currentTime.pdf");
    }



    private function getDetailDelivery($delivery_id, $typeDelivery)
    {

        $response = delivery::where("delivery_id", $delivery_id)
            // ->with(["delivery_repair.repair.repair_unit.unit.motor", "dealer"])
            ->with(["dealer"])
            ->first();

        switch ($typeDelivery) {
            case 'spk':
                $response->load(["delivery_spk.spk.spk_unit.unit.motor"]);
                break;
            case 'repair':
                $response->load([
                    "delivery_repair.repair.repair_unit",
                    "delivery_repair.repair.repair_unit.unit.motor",
                ]);
                break;
            case 'repair_return':
                $response->load([
                    "delivery_repair_return.repair_return"
                ]);
                break;
            case 'event':
                $response->load([
                    "delivery_event.event.master_event",
                    "delivery_event.event.event_unit.unit.motor",
                    "delivery_event.event.master_event.dealer",
                    "delivery_log" => function ($query) {
                        $query->latest();
                    },
                ]);
                break;
            case 'event_return':
                $response->load([
                    "delivery_event_return.event_return.master_event.event",
                    "delivery_event_return.event_return.event_return_unit",
                    "delivery_event_return.event_return.dealer"
                ]);
                break;
            case 'neq':
                $response->load([
                    "delivery_neq.neq.neq_unit.unit.motor", "delivery_neq.neq.dealer_neq"
                ]);
                break;
            case 'neq_return':
                $response->load([
                    "delivery_neq_return.neq_return.neq_return_unit.neq_unit.unit.motor", "delivery_neq_return.neq_return.dealer_neq"
                ]);
                break;

            default:

                break;
        }

        return $response;
    }
}
