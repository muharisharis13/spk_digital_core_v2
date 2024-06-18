<?php

namespace App\Http\Controllers\API;

use App\Exports\UnitExport;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DealerLogo;
use App\Models\delivery;
use App\Models\DeliveryRepair;
use App\Models\District;
use App\Models\Indent;
use App\Models\IndentInstansi;
use App\Models\IndentInstansiPayment;
use App\Models\IndentPayment;
use App\Models\Province;
use App\Models\Spk;
use App\Models\SpkExcessFunds;
use App\Models\SpkInstansi;
use App\Models\SpkInstansiMotor;
use App\Models\SpkInstansiPayment;
use App\Models\SpkInstansiPaymentList;
use App\Models\SpkInstansiUnit;
use App\Models\SpkPayment;
use App\Models\SpkPaymentList;
use App\Models\SubDistrict;
use App\Models\Unit;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ExportPDFController extends Controller
{
    //

    public function printPDFPoInstansi(Request $request, $spk_instansi_id)
    {
        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);

            $getDetail = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)
                ->with(["spk_instansi_delivery.dealer_neq", "delivery_spk_instansi.spk_instansi", "delivery_spk_instansi.spk_instansi_unit_delivery", "spk_instansi_unit.spk_instansi_unit_delivery"])
                ->first();

            // return ResponseFormatter::success($getDetail);


            $html = view('pdf.faktur.faktur_po_instansi', ["data" => $getDetail, "logo" => $base64])->render();

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;
            return $pdf->stream("faktur_po_instansi_$spk_instansi_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function printPDFSPKInstansi(Request $request, $spk_instansi_unit_id)
    {
        try {
            $getDetail = SpkInstansiUnit::where("spk_instansi_unit_id", $spk_instansi_unit_id)
                ->with(["motor", "unit", "spk_instansi.spk_instansi_delivery.dealer_neq", "spk_instansi_unit_legal", "spk_instansi_unit_delivery"])
                ->first();


            $getDetailMotor = SpkInstansiMotor::where("spk_instansi_id", $getDetail->spk_instansi_id)->where("motor_id", $getDetail->motor_id)->first();

            // return ResponseFormatter::success([$getDetail, $getDetailMotor]);


            $html = view('pdf.faktur.faktur_spk_instansi', ["data" => $getDetail, "motor" => $getDetailMotor])->render();

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;
            return $pdf->stream("faktur_spk_$spk_instansi_unit_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function exportExcelMotor(Request $request)
    {
        try {
            return Excel::download(new UnitExport, 'unit.xlsx');
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

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

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            $html = view("pdf.faktur.faktur_payment_indent", ["indent_payment" => $getDetailPaymentIndent, "dealer" => $getDealerSelected, "logo" => $DataLogo->logo])->render();

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



    public function printPdfIndentInstansi(Request $request, $indent_instansi_id)
    {
        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);


            $getDetail = IndentInstansi::where("indent_instansi_id", $indent_instansi_id)
                ->with(["dealer", "motor"])
                ->first();


            // return ResponseFormatter::success($getDetail);


            $html = view('pdf.faktur.faktur_indent_instansi', ["indent" => $getDetail, "dealer" => $getDetail, "logo" => $base64])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');

            $currentTime = Carbon::now()->timestamp;
            return $pdf->stream("indent-instansi-$getDetail->indent_instansi_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function printPdfIndent2(Request $request, $indent_id)
    {
        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);

            $getDetailIndent = Indent::where("indent_id", $indent_id)
                ->with(["sales", "motor", "color",  "indent_payment.bank", "dealer", "indent_payment" => function ($query) {
                    $query->where("indent_payment_type", "payment");
                }])
                ->first();

            if (!isset($getDetailIndent->indent_id)) {
                return ResponseFormatter::error("data not found", "bad request", 400);
            }


            $html = view('pdf.faktur.faktur_non_bootstrap', ["indent" => $getDetailIndent, "dealer" => $getDetailIndent, "logo" => $base64])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');

            $currentTime = Carbon::now()->timestamp;
            return $pdf->stream("indent-$getDetailIndent->indent_number-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function prinSpk(Request $request, $spk_id)
    {
        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);

            $getDetail = Spk::where("spk_id", $spk_id)
                ->with(["dealer"])
                ->first();

            // return ResponseFormatter::success($getDetail);

            $html = view('pdf.faktur.faktur_spk', ["data" => $getDetail, "logo" => $base64])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');

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
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);


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
                $getDetailSpkLeasing = SpkPayment::where("spk_id", $getDetail->spk_id)->where("spk_payment_type", "leasing")->first();
                $getDetailSpkDP = SpkPayment::where("spk_id", $getDetail->spk_id)->where("spk_payment_type", "dp")->with(["spk"])->first();
                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalLeasing($getDetail);
                $getDetail["data_leasing"] = $getDetailSpkLeasing;
                $getDetail["data_dp"] = $getDetailSpkDP;
                $getDetail["spk_payment_amount_total_dp"] = self::sumAmountTotalDp($getDetailSpkDP);
            }
            if ($getDetail->spk_payment_type === "cash") {

                $getDetail["spk_payment_amount_total"] = self::sumAmountTotalCash($getDetail);
            }


            // return ResponseFormatter::success($getDetail);
            if ($getDetail->spk_payment_type === "leasing") {
                $description = $request->input('description');
                $total = $request->input('total');
                $descriptionLeasing = $request->input('descriptionLeasing');
                $totalLeasing = $request->input('totalLeasing');
                $pdf = Pdf::loadView('pdf.faktur.faktur_payment_spk_leasing', ["data" => $getDetail, "description" => $description, "total" => $total, "descriptionLeasing" => $descriptionLeasing, "totalLeasing" => $totalLeasing, "logo" => $base64]);
                $pdf->setPaper('a4', 'landscape');
            } else {
                $description = $request->input('description');
                $total = $request->input('total');
                $pdf = Pdf::loadView('pdf.faktur.faktur_payment_spk', ["data" => $getDetail, "description" => $description, "total" => $total, "logo" => $base64]);
                $pdf->setPaper('a4', 'landscape');
            }

            $currentTime = Carbon::now()->timestamp;

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_payment_$getDetail->spk_payment_type-$spk_payment_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }



    public function printSpkPaymentDetail(Request $request, $spk_payment_list_id)
    {
        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);

            $getDetailPaymentList = SpkPaymentList::where("spk_payment_list_id", $spk_payment_list_id)
                ->with(["spk_payment.spk"])
                ->first();

            // return ResponseFormatter::success($getDetailPaymentList);
            $pdf = Pdf::loadView('pdf.faktur.faktur_payment_spk_detail', ["data" => $getDetailPaymentList, "logo" => $base64]);
            $pdf->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_payment_detail_$spk_payment_list_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function printPDFPaymentSPKInstansiDetail(Request $request, $spk_instansi_payment_list_id)
    {
        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);
            $getDetailPaymentList = SpkInstansiPaymentList::where("spk_instansi_payment_list_id", $spk_instansi_payment_list_id)
                ->with(["spk_instansi_payment.spk_instansi", "bank"])
                ->first();


            // return ResponseFormatter::success($getDetailPaymentList);
            $pdf = Pdf::loadView('pdf.faktur.faktur_payment_spk_instansi_detail', ["data" => $getDetailPaymentList, "logo" => $base64]);
            $pdf->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_payment_instansi_detail_$spk_instansi_payment_list_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function printPDFPaymentSPKInstansi(Request $request, $spk_instansi_payment_id)
    {
        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);

            $getDetail = SpkInstansiPayment::with(["spk_instansi_payment_refund", "spk_instansi", "spk_instansi_payment_list.bank", "spk_instansi_payment_list.spk_instansi_payment_list_file"])
                ->where("spk_instansi_payment_id", $spk_instansi_payment_id)->first();



            // return ResponseFormatter::success($getDetail);
            $description = $request->input("description");
            $total = $request->input("total");
            $pdf = Pdf::loadView('pdf.faktur.faktur_payment_spk_instansi', ["data" => $getDetail, "description" => $description, "total" => $total, "logo" => $base64]);
            $pdf->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_payment_$spk_instansi_payment_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function printPDFOverPayment(Request $request, $spk_excess_fund_id)
    {
        try {
            $getDetail = SpkExcessFunds::where("spk_excess_fund_id", $spk_excess_fund_id)->with(["spk"])->first();


            // return ResponseFormatter::success($getDetail);
            $pdf = Pdf::loadView('pdf.faktur.faktur_over_payment', ["data" => $getDetail]);
            $pdf->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_over_payment_$spk_excess_fund_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function printPdfIndentInstansiPayment(Request $request, $indent_instansi_payment_id)
    {

        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);
            $getDetailIndentInstansiPayment = IndentInstansiPayment::where("indent_instansi_payment_id", $indent_instansi_payment_id)
                ->with(["indent_instansi.dealer", "indent_instansi.motor"])
                ->first();


            // return ResponseFormatter::success($getDetailIndentInstansiPayment);
            $pdf = Pdf::loadView('pdf.faktur.faktur_payment_indent_instansi', ["data" => $getDetailIndentInstansiPayment, "logo" => $base64]);
            $pdf->setPaper('a4', 'landscape');

            $currentTime = Carbon::now()->timestamp;

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_indent_instansi_payment_$indent_instansi_payment_id-$currentTime.pdf");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function printPDFPayment2(Request $request, $indent_payment_id)
    {

        try {
            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $DataLogo = DealerLogo::where("dealer_id", $getDealerSelected->dealer_id)->first();

            // Path gambar
            $path = public_path("storage/$DataLogo->logo");

            // Konversi gambar ke base64
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);

            $getDetailPaymentIndent = IndentPayment::with(["bank", "indent.dealer", "indent.motor", "indent.color"])->where("indent_payment_id", $indent_payment_id)->first();



            $pdf = Pdf::loadView('pdf.faktur.faktur_payment_indent2', ["data" => $getDetailPaymentIndent, "logo" => $base64]);
            $pdf->setPaper('a4', 'portrait');

            // Kembalikan PDF langsung sebagai respons
            return $pdf->stream("faktur_payment_$indent_payment_id.pdf");
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
