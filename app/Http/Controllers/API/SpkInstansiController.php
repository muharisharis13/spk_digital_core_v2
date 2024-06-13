<?php

namespace App\Http\Controllers\API;

use App\Helpers\GenerateAlias;
use App\Helpers\GenerateNumber;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\delivery;
use App\Models\deliveryLog;
use App\Models\DeliverySpkInstansi;
use App\Models\IndentInstansi;
use App\Models\IndentInstansiLog;
use App\Models\SpkInstansi;
use App\Models\SpkInstansiAdditional;
use App\Models\SpkInstansiAdditionalFile;
use App\Models\SpkInstansiDelivery;
use App\Models\SpkInstansiDeliveryFile;
use App\Models\SpkInstansiGeneral;
use App\Models\SpkInstansiLegal;
use App\Models\SpkInstansiLog;
use App\Models\SpkInstansiMotor;
use App\Models\SpkInstansiPayment;
use App\Models\SpkInstansiPaymentList;
use App\Models\SpkInstansiPaymentListFile;
use App\Models\SpkInstansiPaymentLog;
use App\Models\SpkInstansiRefundPayment;
use App\Models\SpkInstansiUnit;
use App\Models\SpkInstansiUnitDelivery;
use App\Models\SpkInstansiUnitDeliveryFile;
use App\Models\SpkInstansiUnitLegal;
use App\Models\Unit;
use App\Models\UnitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpkInstansiController extends Controller
{
    //

    public function addCRO(Request $request, $spk_instansi_id)
    {
        try {

            $validator = Validator::make($request->all(), [
                "is_cro_check" => "nullable|in:1,0",
                "spk_instansi_cro_check_note" => "nullable"
            ]);



            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();


            $getDetail = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)->first();

            $getDetail->update([
                "is_cro_check" => $request->is_cro_check,
                "spk_instansi_cro_check_note" => $request->spk_instansi_cro_check_note
            ]);

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateStatusPayment(Request $request, $spk_instansi_payment_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "spk_instansi_payment_status" => "required|in:cashier_check,finance_check"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetail = SpkInstansiPayment::where("spk_instansi_payment_id", $spk_instansi_payment_id)->first();


            if (!isset($getDetail->spk_instansi_payment_id)) {
                return ResponseFormatter::error("spk instansi payment not found");
            }

            $getDetail->update([
                "spk_instansi_payment_status" => $request->spk_instansi_payment_status
            ]);

            $user = Auth::user();

            // buat log spk payment
            $createLog = SpkInstansiPaymentLog::create([
                "user_id"
                => $user->user_id,
                "spk_instansi_payment_log_note" => "Update Status SPK Payment to $request->spk_instansi_payment_status",
                "spk_instansi_payment_id" => $spk_instansi_payment_id
            ]);

            DB::commit();

            $data = [
                "spk_instansi_payment" => $getDetail,
                "spk_instansi_payment_log" => $createLog
            ];
            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function detailPayment(Request $request, $spk_instansi_payment_id)
    {
        try {
            $getDetail = SpkInstansiPayment::with(["spk_instansi_payment_refund", "spk_instansi", "spk_instansi_payment_list.bank", "spk_instansi_payment_list.spk_instansi_payment_list_file"])
                ->where("spk_instansi_payment_id", $spk_instansi_payment_id)->first();


            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getPaginatePayment(Request $request)
    {
        try {

            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);



            $limit = $request->input("limit", 5);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $q = $request->input('q');

            $getPaginate = SpkInstansiPayment::latest()
                ->with(["spk_instansi"])
                ->whereHas("spk_instansi", function ($query) use ($getDealerByUserSelected) {
                    return $query->where("dealer_id", $getDealerByUserSelected->dealer_id);
                })
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->when($q, function ($query) use ($q) {
                    return $query->where("spk_instansi_payment_number", "LIKE", "%$q%")
                        ->whereHas("spk_instansi", function ($query) use ($q) {
                            return $query->where("spk_instansi_number", "LIKE", "%$q%");
                        });
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function refundAllPayment(Request $request, $spk_instansi_payment_id)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "note" => "required"
            ]);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            // mendapatkan total payment yang sudah terjadi
            $totalSpkPaymentList = SpkInstansiPaymentList::where("spk_instansi_payment_id", $spk_instansi_payment_id)->sum('payment_list_amount');

            DB::beginTransaction();


            $createtPaymentListRefund = SpkInstansiRefundPayment::create([
                "spk_instansi_payment_id" => $spk_instansi_payment_id,
                "amount_total" => intval($totalSpkPaymentList),
                "number" => GenerateNumber::generate("REFUND-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "spk_instansi_refund_payments", "number"),
                "note" => $request->note
            ]);

            //melakukan penghapusan spk payment list secara keseluruhan
            SpkInstansiPaymentList::where("spk_instansi_payment_id", $spk_instansi_payment_id)->delete();

            //melakukan update spk payment status menjadi unpaid
            SpkInstansiPayment::where("spk_instansi_payment_id", $spk_instansi_payment_id)->update([
                "spk_instansi_payment_status" => "unpaid"
            ]);

            $user = Auth::user();

            //create log
            SpkInstansiPaymentLog::create([
                "user_id" => $user->user_id,
                "spk_instansi_payment_id" => $spk_instansi_payment_id,
                "spk_instansi_payment_log_note" => "delete payment list"
            ]);

            DB::commit();

            return ResponseFormatter::success("Successfully refund payment");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deletePayment(Request $request, $spk_instansi_payment_list_id)
    {
        try {
            $getDetailPaymentList = SpkInstansiPaymentList::where("spk_instansi_payment_list_id", $spk_instansi_payment_list_id)->first();

            DB::beginTransaction();

            $user = Auth::user();

            //create log
            SpkInstansiPaymentLog::create([
                "user_id" => $user->user_id,
                "spk_instansi_payment_id" => $getDetailPaymentList->spk_instansi_payment_id,
                "spk_instansi_payment_log_note" => "delete payment list"
            ]);

            //delete image file
            SpkInstansiPaymentListFile::where("payment_list_id", $getDetailPaymentList->spk_instansi_payment_id)->delete();

            $getDetailPaymentList->delete();

            DB::commit();


            return ResponseFormatter::success("Successfully deleted payment");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    function sumAmountTotalCash($getDetail)
    {
        $result = null;
        $spk = $getDetail->spk_instansi;

        $result =
            ($spk->spk_instansi_general->po_values ?? 0) -
            ($spk->indent_instansi->indent_instansi_nominal ?? 0);

        return $result;
    }

    public function addSpkInstansiPayment(Request $request, $spk_instansi_payment_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "payment_list_file" => "nullable|array",
                "payment_list_file.*" => "required|mimes:png,jpg,pdf|max:5120",
                "payment_list_method" => "required|in:cash,bank_transfer,giro",
                "payment_list_amount" => "integer|required|min:1",
                "payment_list_note" => "nullable",
                "payment_list_date" => "required"
            ]);

            $validator->sometimes(["bank_id",], 'required', function ($input) {
                return $input->payment_list_method == 'bank_transfer';
            });
            $validator->sometimes(["bank_id",], 'required', function ($input) {
                return $input->payment_list_method == 'giro';
            });


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $totalSpkInstansiPayment = SpkInstansiPaymentList::where("spk_instansi_payment_id", $spk_instansi_payment_id)->sum("payment_list_amount");


            $getDetail = SpkInstansiPayment::latest()
                ->with(["spk_instansi"])
                ->where("spk_instansi_payment_id", $spk_instansi_payment_id)
                ->first();



            // melakukan penjumlahan data lama dengan data baru
            $totalSpkInstansiPayment = intval($totalSpkInstansiPayment) + intval($request->payment_list_amount);
            $spk_payment_amount_total = self::sumAmountTotalCash($getDetail);


            // if (intval($totalSpkInstansiPayment) >=  intval($spk_payment_amount_total)) {
            //     DB::rollBack();
            //     return ResponseFormatter::error("Payment Harus sama besar dengan total amount", "Bad Request", 400);
            // }



            $payment_list_method = strtoupper($request->payment_list_method);
            $alias = GenerateAlias::generate($getDealerSelected->dealer->dealer_name);
            $number = "SPK-INST/$payment_list_method";

            $data = [
                "spk_instansi_payment_id" => $spk_instansi_payment_id,
                "payment_list_method" => $request->payment_list_method,
                "payment_list_amount" => $request->payment_list_amount,
                "payment_list_date" => $request->payment_list_date,
                "payment_list_note" => $request->payment_list_note,
                "payment_list_number" => GenerateNumber::generate($number, $alias, "spk_instansi_payment_lists", "payment_list_number")
            ];

            if ($request->payment_list_method === "bank_transfer") {
                $data["bank_id"] = $request->bank_id;
            }

            $createPayment = SpkInstansiPaymentList::create($data);

            $createPaymentImg = [];

            if ($request->payment_list_file) {
                foreach ($request->file("payment_list_file") as $item) {
                    $imagePath = $item->store("spk_instansi", "public");

                    $createPaymentImg[] = SpkInstansiPaymentListFile::create([
                        "payment_list_id" => $createPayment->spk_instansi_payment_list_id,
                        "file" => $imagePath
                    ]);
                }
            }

            //create log
            $log = SpkInstansiPaymentLog::create([
                "user_id" => $user->user_id,
                "spk_instansi_payment_id" => $spk_instansi_payment_id,
                "spk_instansi_payment_log_note" => "add payment list"
            ]);

            $data = [
                "spk_instansi_payment_list" => $createPayment,
                "spk_instansi_payment_file" => $createPaymentImg,
                "spk_instansi_payment_log" => $log
            ];



            DB::commit();

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function deleteDeliveryUnitFile(Request $request, $spk_instansi_unit_deliv_file_id)
    {
        try {
            DB::beginTransaction();
            $getDetail = SpkInstansiUnitDeliveryFile::where("spk_instansi_unit_deliv_file_id", $spk_instansi_unit_deliv_file_id)->first();
            $getDetail->delete();

            DB::commit();

            return ResponseFormatter::success("Successfully deleted");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }
    public function deleteDeliveryFile(Request $request, $spk_instansi_delivery_file_id)
    {
        try {
            DB::beginTransaction();
            $getDetail = SpkInstansiDeliveryFile::where("spk_instansi_delivery_file_id", $spk_instansi_delivery_file_id)->first();
            $getDetail->delete();

            DB::commit();

            return ResponseFormatter::success("Successfully deleted");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function deleteAdditionalFile(Request $request, $spk_instansi_additional_file_id)
    {
        try {
            DB::beginTransaction();
            $getDetail = SpkInstansiAdditionalFile::where("spk_instansi_additional_file_id", $spk_instansi_additional_file_id)->first();
            $getDetail->delete();

            DB::commit();

            return ResponseFormatter::success("Successfully deleted");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function addUnitDelivery(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "delivery_driver_name" => "required",
                "delivery_vehicle" => "required",
                "delivery_note" => "nullable",
                "delivery_completeness" => "nullable",
                "delivery_type" => "required|in:ktp,dealer,neq,domicile",
                "instansi_unit" => "array|required",
            ]);

            self::isSelectedDeliveryTypeKTP($validator);
            self::isSelectedDeliveryTypeDealer($validator);
            self::isSelectedDeliveryTypeNeq($validator);
            self::isSelectedDeliveryTypeDomicile($validator);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $getDealer = GetDealerByUserSelected::GetUser($user->user_id);

            $createSpkUnitDelivery = [];
            $createSpkDeliveryFile = [];
            $createDeliveryUnit = [];

            $createDelivery = delivery::create([
                "delivery_driver_name" => $request->delivery_driver_name,
                "delivery_vehicle" => $request->delivery_vehicle,
                "delivery_note" => $request->delivery_note,
                "delivery_completeness" => $request->delivery_completeness,
                "delivery_number" => GenerateNumber::generate("TEMP-DELIVERY", GenerateAlias::generate($getDealer->dealer->dealer_name), "deliveries", "delivery_number"),
                "dealer_id" => $getDealer->dealer_id,
                "delivery_status" => "create",
                "delivery_type" => "spk_instansi"
            ]);

            $createDeliveryLog = deliveryLog::create([
                "user_id" => $user->user_id,
                "delivery_log_action" => "create",
                "delivery_note" => "Create Delivery",
                "delivery_id" => $createDelivery->delivery_id
            ]);

            foreach ($request->instansi_unit as $item) {
                $dataDelivery = [
                    "spk_instansi_unit_id" => $item,
                    "delivery_type" => $request->delivery_type,
                ];

                if ($request->delivery_type === "ktp") {
                    $dataDelivery["name"] = $request->delivery_name;
                    $dataDelivery["address"] = $request->delivery_address;
                    $dataDelivery["city"] = $request->city;
                    $dataDelivery["no_telp"] = $request->delivery_no_telp;
                    $dataDelivery["no_hp"] = $request->delivery_no_hp;
                }
                if ($request->delivery_type === "dealer") {
                    $dataDelivery["name"] = $request->delivery_name;
                    $dataDelivery["no_hp"] = $request->delivery_no_hp;
                }
                if ($request->delivery_type === "neq") {
                    $dataDelivery["name"] = $request->delivery_name;
                    $dataDelivery["no_hp"] = $request->delivery_no_hp;
                    $dataDelivery["dealer_neq_id"] = $request->dealer_neq_id;
                }

                if ($request->delivery_type === "domicile") {
                    $dataDelivery["name"] = $request->delivery_name;
                    $dataDelivery["address"] = $request->delivery_address;
                    $dataDelivery["city"] = $request->city;
                    $dataDelivery["is_domicile"] = true;
                }

                $createSpkUnitDelivery2 = SpkInstansiUnitDelivery::create($dataDelivery);
                $createSpkUnitDelivery[] = $createSpkUnitDelivery2;

                //update spk unit  is_delivery_partial true
                SpkInstansiUnit::where("spk_instansi_unit_id", $item)
                    ->first()
                    ->update([
                        "is_delivery_partial" => true
                    ]);



                if ($request->delivery_type === "domicile") {
                    $validator = Validator::make($request->all(), [
                        'file_sk' => 'nullable|array',
                        'file_sk.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
                    ]);
                    if ($validator->fails()) {
                        DB::rollBack();
                        return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
                    }
                    if (isset($request->file_sk)) {
                        foreach ($request->file("file_sk") as $itemFile) {
                            $imagePath = $itemFile->store("spk_instansi", "public");

                            $createSpkDeliveryFile[] = SpkInstansiUnitDeliveryFile::create([
                                "spk_instansi_unit_deliv_id" => $createSpkUnitDelivery2->spk_instansi_unit_delivery_id,
                                "file" => $imagePath
                            ]);
                        }
                    }
                }


                $createDeliveryUnit[] = DeliverySpkInstansi::create([
                    "spk_instansi_unit_delivery_id" => $createSpkUnitDelivery2->spk_instansi_unit_delivery_id,
                    "type" => "partial",
                    "delivery_id" => $createDelivery->delivery_id
                ]);
            }


            $data = [
                "spk_instansi_unit_delivery" => $createSpkUnitDelivery,
                "spk_instansi_spk_delivery_file" => $createSpkDeliveryFile,
                "delivery" => $createDelivery,
                "delivery_log" => $createDeliveryLog,
                "delivery_spk_instansi" => $createDeliveryUnit
            ];


            DB::commit();

            return ResponseFormatter::success($data, "Successfully add delivery unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function addUnitLegal(Request $request, $spk_instansi_unit_id)
    {
        try {

            $validator = Validator::make($request->all(), [
                "instansi_name" => "required",
                "instansi_address" => "required",
                "province" => "required",
                "city" => "required",
                "district" => "required",
                "sub_district" => "required",
                "postal_code" => "required",
                "no_telp" => "nullable",
                "no_hp" => "required",
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetail = SpkInstansiUnit::where("spk_instansi_unit_id", $spk_instansi_unit_id)->first();
            $getDetail->update([
                "is_have_legal" => true
            ]);

            //buat legal unit
            $createUnitLegal = SpkInstansiUnitLegal::create([
                "spk_instansi_unit_id" => $spk_instansi_unit_id,
                "instansi_name" => $request->instansi_name,
                "instansi_address" => $request->instansi_address,
                "province" => $request->province,
                // "province_id" => $request->province_id,
                "city" => $request->city,
                // "city_id" => $request->city_id,
                "district" => $request->district,
                // "district_id" => $request->district_id,
                "sub_district" => $request->sub_district,
                // "sub_district_id" => $request->sub_district_id,
                "postal_code" => $request->postal_code,
                "no_telp" => $request->no_telp,
                "no_hp" => $request->no_hp,
            ]);

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $getDetail->spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "add legal unit"
            ];

            DB::commit();

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_unit_legal" => $createUnitLegal,
                "spk_instansi_log" => $createLog
            ];

            return ResponseFormatter::success($data, "Successfully add new legal unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function deleteSpkInstansi(Request $request, $spk_instansi_id)
    {
        try {
            DB::beginTransaction();

            $getDetailSpkInstansi = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)->first();

            //update unit menjadi on_hand
            $getUnitSpk = SpkInstansiUnit::where("spk_instansi_id", $spk_instansi_id)->get();

            if ($getUnitSpk->count() > 0) {
                foreach ($getUnitSpk as $item) {
                    Unit::where("unit_id", $item->unit_id)->update([
                        "unit_status" => "on_hand"
                    ]);
                }
            }

            $user = Auth::user();



            //update indent status jadi hold

            if (isset($getDetailSpkInstansi->indent_instansi_id)) {
                IndentInstansi::where("indent_instansi_id", $getDetailSpkInstansi->indent_instansi_id)->update([
                    "indent_instansi_status" => "finance_check"
                ]);


                IndentInstansiLog::create([
                    "indent_instansi_id" => $request->indent_instansi_id,
                    "user_id" => $user->user_id,
                    "indent_instansi_log_action" => "update status to finance check"
                ]);
            }



            $getDetailSpkInstansi->delete();




            $data = [
                "spk_instansi" => $getDetailSpkInstansi,
            ];
            DB::commit();

            return ResponseFormatter::success($data, "Successfully deleted status");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function updateStatusToCancel(Request $request, $spk_instansi_id)
    {
        try {
            DB::beginTransaction();

            $getDetailSpkInstansi = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)->first();
            $getDetailSpkInstansi->update([
                "spk_instansi_status" => "cancel"
            ]);

            //update unit menjadi on_hand
            $getUnitSpk = SpkInstansiUnit::where("spk_instansi_id", $spk_instansi_id)->get();

            if ($getUnitSpk->count() > 0) {
                foreach ($getUnitSpk as $item) {
                    Unit::where("unit_id", $item->unit_id)->update([
                        "unit_status" => "on_hand"
                    ]);
                }
            }

            $user = Auth::user();



            //update indent status jadi hold

            if (isset($getDetailSpkInstansi->indent_instansi_id)) {
                IndentInstansi::where("indent_instansi_id", $getDetailSpkInstansi->indent_instansi_id)->update([
                    "indent_instansi_status" => "finance_check"
                ]);


                IndentInstansiLog::create([
                    "indent_instansi_id" => $request->indent_instansi_id,
                    "user_id" => $user->user_id,
                    "indent_instansi_log_action" => "update status to finance check"
                ]);
            }

            $dataRequestLog = [
                "spk_instansi_id" => $spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "update status to " . "cancel"
            ];

            DB::commit();

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi" => $getDetailSpkInstansi,
                "spk_instansi_log" => $createLog
            ];

            return ResponseFormatter::success($data, "Successfully update status");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }



    public function getDetailSpkInstansiUnit(Request $request, $spk_instansi_unit_id)
    {
        try {
            $getDetail = SpkInstansiUnit::where("spk_instansi_unit_id", $spk_instansi_unit_id)
                ->with(["motor", "unit", "spk_instansi.spk_instansi_delivery.dealer_neq", "spk_instansi_unit_legal", "spk_instansi_unit_delivery"])
                ->first();

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getPaginateSpkInstansiUnit(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);

            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $q = $request->input('q');
            $status = $request->input("spk_instansi_status");


            $getPaginate = SpkInstansiUnit::latest()
                ->with(["motor", "unit", "spk_instansi"])
                ->whereHas("spk_instansi", function ($query) use ($getDealerByUserSelected) {
                    return $query->where("dealer_id", $getDealerByUserSelected->dealer_id)
                        ->where("spk_instansi_status", "publish")
                        ->orWhere("spk_instansi_status", "cancel");
                })
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->when($q, function ($query) use ($q) {
                    return $query->whereHas("spk_instansi", function ($query) use ($q) {
                        return $query->where("spk_instansi_number", "LIKE", "%$q%")
                            ->orWhere("spk_instansi_number", "LIKE", "%$q%");
                    })
                        ->orWhereHas("motor", function ($query) use ($q) {
                            return $query->where("motor_name", "LIKE", "%$q%");
                        });
                })->when($status, function ($query) use ($status) {
                    return $query->whereHas("spk_instansi", function ($query) use ($status) {
                        return $query->where("spk_instansi_status", $status);
                    });
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }


    public function terbitSpk(Request $request, $spk_instansi_id)
    {
        try {

            DB::beginTransaction();

            $getDetailSpkInstansi = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)->first();
            $getDetailSpkInstansi->update([
                "spk_instansi_status" => "publish"
            ]);

            foreach ($getDetailSpkInstansi->spk_instansi_unit as $item) {
                Unit::where("unit_id", $item->unit_id)->update([
                    "unit_status" => "spk"
                ]);
            }



            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $getDetailIndentInstansi = IndentInstansi::where("indent_instansi_id", $getDetailSpkInstansi->indent_instansi_id)->update([
                "indent_instansi_status" => "spk"
            ]);

            IndentInstansiLog::create([
                "indent_instansi_id" => $getDetailIndentInstansi->indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "update status to spk"
            ]);

            //generate payment
            $createPayment = SpkInstansiPayment::create([
                "spk_instansi_id" => $spk_instansi_id,
                "spk_instansi_payment_number" =>
                GenerateNumber::generate("SPK-INSTANSI-PAYMENT", GenerateAlias::generate($getDealerSelected->dealer->dealer_name), "spk_instansi_payments", "spk_instansi_payment_number"),
                "spk_instansi_payment_for" => "company",
                "spk_instansi_payment_type" => "cash",
                "spk_instansi_payment_status" => "unpaid",
            ]);

            SpkInstansiPaymentLog::create([
                "user_id" => $user->user_id,
                "spk_instansi_payment_id" =>  $createPayment->spk_instansi_payment_id,
                "spk_instansi_payment_log_note" => "create payment"
            ]);



            $dataRequestLog = [
                "spk_instansi_id" => $spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "update status to " . "publish"
            ];

            DB::commit();

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi" => $getDetailSpkInstansi,
                "spk_instansi_payment" => $createPayment,
                "spk_instansi_log" => $createLog
            ];

            return ResponseFormatter::success($data, "Successfully update status");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function updateStatus(Request $request, $spk_instansi_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "spk_instansi_status" => "required|in:finance_check,shipment"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailSpkInstansi = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)->first();
            $getDetailSpkInstansi->update([
                "spk_instansi_status" => $request->spk_instansi_status
            ]);

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "update status to " . $request->spk_instansi_status
            ];

            DB::commit();

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi" => $getDetailSpkInstansi,
                "spk_instansi_log" => $createLog
            ];

            return ResponseFormatter::success($data, "Successfully update status");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function deleteUnit(Request $request, $spk_instansi_unit_id)
    {
        try {
            DB::beginTransaction();

            $getDetail = SpkInstansiUnit::where("spk_instansi_unit_id", $spk_instansi_unit_id)->first();

            if (!isset($getDetail->spk_instansi_unit_id)) {
                return ResponseFormatter::error("unit not found", "Bad Request", 400);
            }

            //update unit_status by doni
            $getUnit = Unit::where('unit_id', $getDetail->unit_id)->first();

            $getUnit->update([
                'unit_status' => 'on_hand'
            ]);
            // end updated

            $getDetail->delete();

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $getDetail->spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "delete unit"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_unit" => $getDetail,
                "spk_instansi_log" => $createLog
            ];
            DB::commit();

            return ResponseFormatter::success($data, "Successfully deleted po instansi unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function updateUnit(Request $request, $spk_instansi_unit_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "unit_id" => "required",
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetail = SpkInstansiUnit::where("spk_instansi_unit_id", $spk_instansi_unit_id)->first();

            if (!isset($getDetail->spk_instansi_unit_id)) {
                return ResponseFormatter::error("unit not found", "Bad Request", 400);
            }
            //update unit_status by doni
            $getUnit = Unit::where('unit_id', $getDetail->unit_id)->first();

            $getUnit->update([
                'unit_status' => 'on_hand'
            ]);
            // end updated

            $getDetail->update([
                "unit_id" => $request->unit_id,
            ]);

            $updateUnitStatus = Unit::where('unit_id', $request->unit_id);
            $updateUnitStatus->update([
                'unit_status' => 'hold'
            ]);

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $getDetail->spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "update unit"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_unit" => $getDetail,
                "spk_instansi_log" => $createLog
            ];
            DB::commit();

            return ResponseFormatter::success($data, "Successfully updated po instansi unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function addUnit(Request $request, $spk_instansi_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "unit_id" => "required",
                "motor_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            //melakukan pengechekan apakah sudah shipment
            $getDetailShipment = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)->first();

            if ($getDetailShipment->spk_instansi_status !== "shipment") {
                return ResponseFormatter::error("Spk Harus Shipment untuk bisa nambah unit", "Bad Request", 400);
            }

            $getDetailUnit = Unit::where("unit_id", $request->unit_id)->first();

            if ($getDetailUnit->unit_status === "hold") {
                return ResponseFormatter::error("Unit status sudah hold", "bad request", 400);
            }

            $createUnitInstansi  = SpkInstansiUnit::firstOrCreate([
                "unit_id" => $request->unit_id,
                "spk_instansi_id" => $spk_instansi_id
            ], [
                "motor_id" => $request->motor_id,
                "unit_id" => $request->unit_id,
                "spk_instansi_id" => $spk_instansi_id
            ]);

            $user = Auth::user();

            //melakukan update unit


            $getDetailUnit->update([
                "unit_status" => "hold"
            ]);
            UnitLog::create([
                "unit_id" => $getDetailUnit->unit_id,
                "user_id" => $user->user_id,
                "unit_log_number" => "NULL",
                "unit_log_action" => "hold",
                "unit_log_status" => "hold"
            ]);


            $dataRequestLog = [
                "spk_instansi_id" => $spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "Add unit "
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            DB::commit();

            $data = [
                "spk_instansi_unit" => $createUnitInstansi,
                "spk_instansi_log" => $createLog
            ];

            return ResponseFormatter::success($data, "Successfully add unit");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function deleteAdditional(Request $request, $spk_instansi_additional_id)
    {
        try {
            // $validator = Validator::make($request->all(), [
            //     "additional_cost" => "required",
            //     "additional_note" => "nullable"
            // ]);
            // if ($validator->fails()) {
            //     return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            // }

            DB::beginTransaction();

            $getDetail = SpkInstansiAdditional::where("spk_instansi_additional_id", $spk_instansi_additional_id)->first();

            if (!isset($getDetail->spk_instansi_additional_id)) {
                return ResponseFormatter::error("additional po not found", "Bad Request", 400);
            }

            $getDetail->delete();

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $getDetail->spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "delete additional cost"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_additional" => $getDetail,
                "spk_instansi_log" => $createLog
            ];
            DB::commit();

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }
    public function updateAdditional(Request $request, $spk_instansi_additional_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "additional_cost" => "required",
                "additional_note" => "nullable"
            ]);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetail = SpkInstansiAdditional::where("spk_instansi_additional_id", $spk_instansi_additional_id)->first();

            if (!isset($getDetail->spk_instansi_additional_id)) {
                return ResponseFormatter::error("additional po not found", "Bad Request", 400);
            }

            $getDetail->update([
                "additional_cost" => $request->additional_cost,
                "additional_note" => $request->additional_note,
            ]);

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $getDetail->spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "update additional cost"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);
            DB::commit();

            $data = [
                "spk_instansi_additional" => $getDetail,
                "spk_instansi_log" => $createLog
            ];


            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function addAdditionalNote(Request $request, $spk_instansi_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "additional_cost" => "required",
                "additional_note" => "nullable"
            ]);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $createAdditional = SpkInstansiAdditional::create([
                "additional_cost" => $request->additional_cost,
                "additional_note" => $request->additional_note,
                "spk_instansi_id" => $spk_instansi_id
            ]);

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "add new additional cost"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_additional" => $createAdditional,
                "spk_instansi_log" => $createLog
            ];

            DB::commit();

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::success($e->getMessage(), "Internal Server", 500);
        }
    }

    public function deleteMotor(Request $request, $spk_instansi_motor_id)
    {
        try {
            $getDetail = SpkInstansiMotor::where("spk_instansi_motor_id", $spk_instansi_motor_id)
                ->first();

            if (!isset($getDetail->spk_instansi_motor_id)) {
                return ResponseFormatter::error("motor not found", "Bad Request", 400);
            }

            DB::beginTransaction();


            $getDetail->delete();
            $previousMotors = SpkInstansiMotor::where('spk_instansi_id', $getDetail->spk_instansi_id)->get();


            $total = 0;
            foreach ($previousMotors as $previousMotor) {
                $prev_qty = intval($previousMotor->qty);
                $prev_off_the_road = intval($previousMotor->off_the_road);
                $prev_bbn = intval($previousMotor->bbn);
                $prev_discount = intval($previousMotor->discount);
                $prev_discount_over = intval($previousMotor->discount_over);
                $prev_additional_cost = intval($previousMotor->additional_cost);

                $total = ((($prev_off_the_road + $prev_bbn)) - $prev_discount - $prev_discount_over + $prev_additional_cost) * $prev_qty;
            }
            $getSpkInstansiIndent = SpkInstansi::where('spk_instansi_id', $getDetail->spk_instansi_id)->first();

            if ($getSpkInstansiIndent) {
                $indentNominal = $getSpkInstansiIndent->indent_instansi->indent_instansi_nominal ?? 0;
                $poValues = $total - $indentNominal;
                $getDetailGeneral = SpkInstansiGeneral::where('spk_instansi_id', $getDetail->spk_instansi_id)->first();
                $getDetailGeneral->update(['po_values' => $poValues]);
            }

            $user = Auth::user();

            $dataRequestLog = [
                "spk_instansi_id" => $getDetail->spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "delete motor"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_motor" => $getDetail,
                "spk_instansi_log" => $createLog
            ];

            DB::commit();

            return ResponseFormatter::success($data, "Successfully delete po instansi motor");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    // public function updateMotor(Request $request, $spk_instansi_motor_id)
    // {
    //     try {
    //         $getDetail = SpkInstansiMotor::where("spk_instansi_motor_id", $spk_instansi_motor_id)
    //             ->first();

    //         if (!isset($getDetail->spk_instansi_motor_id)) {
    //             return ResponseFormatter::error("motor not found", "Bad Request", 400);
    //         }

    //         $validator = Validator::make($request->all(), [
    //             "motor_id" => "required",
    //             "qty" => "required"
    //         ]);
    //         if ($validator->fails()) {
    //             return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
    //         }

    //         DB::beginTransaction();

    //         $getDetail->update([
    //             "motor_id" => $request->motor_id,
    //             "qty" => $request->qty
    //         ]);

    //         $user = Auth::user();


    //         //menghitung nilai kontrak untuk di update ke database
    //         $total = ((intval($getDetail->off_the_road) + intval($getDetail->bbn)) * intval($request->qty) + (intval($request->qty) * intval($getDetail->additional_cost))) - intval($getDetail->discount) - intval($getDetail->discount_over);

    //         $getDetailGeneral = SpkInstansiGeneral::where("spk_instansi_id", $getDetail->spk_instansi_id)->first();

    //         $totalBaru = intval($getDetailGeneral->po_values) + $total;
    //         $getDetailGeneral->update([
    //             "po_values" => $totalBaru
    //         ]);

    //         $dataRequestLog = [
    //             "spk_instansi_id" => $getDetail->spk_instansi_id,
    //             "user_id" => $user->user_id,
    //             "spk_instansi_log_action" => "update motor"
    //         ];

    //         $createLog = SpkInstansiLog::create($dataRequestLog);

    //         $data = [
    //             "spk_instansi_motor" => $getDetail,
    //             "spk_instansi_log" => $createLog
    //         ];
    //         DB::commit();

    //         return ResponseFormatter::success($data, "Successfully updated po instansi motor");
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
    //     }
    // }



    public function updateMotor(Request $request, $spk_instansi_motor_id)
    {
        try {
            $getDetail = SpkInstansiMotor::where("spk_instansi_motor_id", $spk_instansi_motor_id)->first();

            if (!isset($getDetail->spk_instansi_motor_id)) {
                return ResponseFormatter::error("Motor not found", "Bad Request", 400);
            }

            $validator = Validator::make($request->all(), [
                "motor_id" => "required",
                "qty" => "required" // Ensure qty is a positive integer
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();



            // Fetch previous instances of SpkInstansiMotor for the same spk_instansi_id
            $previousMotors = SpkInstansiMotor::where("spk_instansi_id", $getDetail->spk_instansi_id)
                ->where("spk_instansi_motor_id", "!=", $spk_instansi_motor_id)
                ->get();

            $getDetail->update([
                "motor_id" => $request->motor_id,
                "qty" => $request->qty,
                "color_id" => $request->color_id,
                "off_the_road" => $request->off_the_road,
                "bbn" => $request->bbn,
                "on_the_road" => $request->on_the_road,
                "discount" => $request->discount,
                "discount_over" => $request->discount_over,
                "commission" => $request->commission,
                "booster" => $request->booster,
                "additional_cost" => $request->additional_cost,
                "additional_cost_note" => $request->additional_cost_note,
            ]);

            $user = Auth::user();

            // Calculate the new total considering qty anomalies
            $qty = intval($request->qty);
            $off_the_road = intval($getDetail->off_the_road);
            $bbn = intval($getDetail->bbn);
            $additional_cost = intval($getDetail->additional_cost);
            $discount = intval($getDetail->discount);
            $discount_over = intval($getDetail->discount_over);

            $total = ((($off_the_road + $bbn))  - $discount - $discount_over + $additional_cost) * $qty;


            foreach ($previousMotors as $previousMotor) {
                $prev_qty = intval($previousMotor->qty);
                $prev_off_the_road = intval($previousMotor->off_the_road);
                $prev_bbn = intval($previousMotor->bbn);
                $prev_discount = intval($previousMotor->discount);
                $prev_discount_over = intval($previousMotor->discount_over);
                $prev_additional_cost = intval($previousMotor->additional_cost);

                $total += ((($prev_off_the_road + $prev_bbn)) - $prev_discount - $prev_discount_over + $prev_additional_cost) * $prev_qty;
            }
            $getDetailGeneral = SpkInstansiGeneral::where("spk_instansi_id", $getDetail->spk_instansi_id)->first();
            // $totalBaru = intval($getDetailGeneral->po_values) + $total;
            $getSpkInstansiIndent = SpkInstansi::where('spk_instansi_id', $getDetail->spk_instansi_id)->first();

            if ($getSpkInstansiIndent) {
                $indentNominal = $getSpkInstansiIndent->indent_instansi->indent_instansi_nominal ?? 0;
                $poValues = $total - $indentNominal;
                $getDetailGeneral->update([
                    "po_values" => $poValues
                ]);
            }


            $dataRequestLog = [
                "spk_instansi_id" => $getDetail->spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "update motor"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_motor" => $getDetail,
                "spk_instansi_log" => $createLog
            ];

            DB::commit();

            return ResponseFormatter::success($data, "Successfully updated po instansi motor");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }



    public function addMotor(Request $request, $spk_instansi_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "motor_id" => "required",
                "color_id" => "required",
                "qty" => "required",
                "off_the_road" => "required",
                "bbn" => "required",
                "on_the_road" => "required",
                "discount" => "nullable",
                "discount_over" => "nullable",
                "commission" => "nullable",
                "booster" => "nullable",
                "additional_cost" => "nullable",
                "additional_cost_note" => "nullable"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $dataRequest = [
                "motor_id" => $request->motor_id,
                "color_id" => $request->color_id,
                "qty" => $request->qty,
                "off_the_road" => $request->off_the_road,
                "bbn" => $request->bbn,
                "on_the_road" => $request->on_the_road,
                "discount" => $request->discount,
                "discount_over" => $request->discount_over,
                "commission" => $request->commission,
                "booster" => $request->booster,
                "additional_cost" => $request->additional_cost,
                "additional_cost_note" => $request->additional_cost_note,
                "spk_instansi_id" => $spk_instansi_id
            ];
            $user = Auth::user();

            $createSpkInstansiMotor = SpkInstansiMotor::create($dataRequest);


            //menghitung nilai kontrak untuk di update ke database
            // $total = ((intval($request->off_the_road) + intval($request->bbn)) * intval($request->qty) + (intval($request->qty) * intval($request->additional_cost))) - intval($request->discount) - intval($request->discount_over);
            $off_the_road = intVal($request->off_the_road);
            $bbn = intVal($request->bbn);
            $qty = intVal($request->qty);
            $additional_cost = intVal($request->additional_cost);
            $discount = intVal($request->discount);
            $discount_over = intVal($request->discount_over);
            $total = (($off_the_road + $bbn) - $discount - $discount_over + $additional_cost) * $qty;

            $getDetailGeneral = SpkInstansiGeneral::where("spk_instansi_id", $spk_instansi_id)->first();
            $previousMotors = SpkInstansiMotor::where("spk_instansi_id", $spk_instansi_id)
                ->where("spk_instansi_motor_id", "!=", $createSpkInstansiMotor->spk_instansi_motor_id)
                ->get();

            foreach ($previousMotors as $previous_motor) {
                $prev_qty = intval($previous_motor->qty);
                $prev_off_the_road = intval($previous_motor->off_the_road);
                $prev_bbn = intval($previous_motor->bbn);
                $prev_discount = intval($previous_motor->discount);
                $prev_discount_over = intval($previous_motor->discount_over);
                $prev_additional_cost = intval($previous_motor->additional_cost);

                $total += ((($prev_off_the_road + $prev_bbn)) - $prev_discount - $prev_discount_over + $prev_additional_cost) * $prev_qty;
            }
            $getSpkInstansiIndent = SpkInstansi::where('spk_instansi_id', $spk_instansi_id)->first();
            $getDetailGeneral->update([
                "po_values" => 0
            ]);
            if ($getSpkInstansiIndent) {
                $indentNominal = $getSpkInstansiIndent->indent_instansi->indent_instansi_nominal ?? 0;
                $poValues = $total - $indentNominal;
                $getDetailGeneral->update([
                    "po_values" => $poValues
                ]);
            }
            // $totalNew = $total - $getSpkInstansiIndent->indent_instansi->indent_instansi_nominal ?? 0;


            $dataRequestLog = [
                "spk_instansi_id" => $spk_instansi_id,
                "user_id" => $user->user_id,
                "spk_instansi_log_action" => "add new motor"
            ];

            $createLog = SpkInstansiLog::create($dataRequestLog);

            $data = [
                "spk_instansi_motor" => $createSpkInstansiMotor,
                "spk_instansi_log" => $createLog
            ];
            DB::commit();

            return ResponseFormatter::success($data, "Successfully add new motor into spk instansi");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getDetail(Request $request, $spk_instansi_id)
    {
        try {
            $getDetail = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)
                ->with(["spk_instansi_delivery.dealer_neq", "delivery_spk_instansi.spk_instansi", "delivery_spk_instansi.spk_instansi_unit_delivery", "spk_instansi_unit.spk_instansi_unit_delivery", "spk_instansi_log"])
                ->first();

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function getPaginate(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $q = $request->input('q');
            $limit = $request->input("limit", 5);
            $status = $request->input("spk_instansi_status");

            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $getPaginate = SpkInstansi::latest()
                ->where("dealer_id", $getDealerByUserSelected->dealer_id)
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->when($q, function ($query) use ($q) {
                    return $query->where("spk_instansi_number", "LIKE", "%$q%")
                        ->orWhere("spk_instansi_status", "LIKe", "%$q%");
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where("spk_instansi_status", $status);
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function update(Request $request, $spk_instansi_id)
    {
        try {
            $validator = Validator::make($request->all(), self::validator);

            self::isSelectedDeliveryTypeKTP($validator);
            self::isSelectedDeliveryTypeDealer($validator);
            self::isSelectedDeliveryTypeNeq($validator);
            self::isSelectedDeliveryTypeDomicile($validator);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }
            DB::beginTransaction();

            $updateSpk = SpkInstansi::where("spk_instansi_id", $spk_instansi_id)->first();
            //update indent menjadi ke fincane_check
            IndentInstansi::where("indent_instansi_id", $updateSpk->indent_instansi_id)->update([
                "indent_instansi_status" => "finance_check"
            ]);
            $updateSpk->update([
                "indent_instansi_id" => $request->indent_instansi_id
            ]);
            //update indent baru menjadi hold
            IndentInstansi::where("indent_instansi_id", $request->indent_instansi_id)->update([
                "indent_instansi_status" => "hold"
            ]);

            //general
            $updateSpkGeneral = SpkInstansiGeneral::where("spk_instansi_id", $spk_instansi_id)->first();
            $updateSpkGeneral->update([
                "sales_name" => $request->sales_name,
                "sales_id" => $request->sales_id,
                "po_number" => $request->po_number,
                "po_date" => $request->po_date,
                "instansi_name" => $request->instansi_name,
                "instansi_address" => $request->instansi_address,
                "province" => $request->province,
                "province_id" => $request->province_id,
                "city" => $request->city,
                "city_id" => $request->city_id,
                "district" => $request->district,
                "district_id" => $request->district_id,
                "sub_district" => $request->sub_district,
                "sub_district_id" => $request->sub_district_id,
                "postal_code" => $request->postal_code,
                "no_telp" => $request->no_telp,
                "no_hp" => $request->no_hp,
                "email" => $request->email,
            ]);
            $updateSpkLegal = SpkInstansiLegal::where("spk_instansi_id", $spk_instansi_id)->first();
            $updateSpkLegal->update([
                "instansi_name" => $request->instansi_name_legal,
                "instansi_address" => $request->instansi_address_legal,
                "province" => $request->province_legal,
                "province_id" => $request->province_id_legal,
                "city" => $request->city_legal,
                "city_id" => $request->city_id_legal,
                "district" => $request->district_legal,
                "district_id" => $request->district_id_legal,
                "sub_district" => $request->sub_district_legal,
                "sub_district_id" => $request->sub_district_id_legal,
                "postal_code" => $request->postal_code_legal,
                "no_telp" => $request->no_telp_legal,
                "no_hp" => $request->no_hp_legal,
            ]);
            $updateSpkDelivery = SpkInstansiDelivery::where("spk_instansi_id", $spk_instansi_id)->first();
            $dataDelivery = [
                "spk_instansi_id" => $spk_instansi_id,
                "delivery_type" => $request->delivery_type,
            ];

            if ($request->delivery_type === "ktp") {
                $dataDelivery["name"] = $request->delivery_name;
                $dataDelivery["address"] = $request->delivery_address;
                $dataDelivery["city"] = $request->delivery_city;
                $dataDelivery["no_telp"] = $request->delivery_no_telp;
                $dataDelivery["no_hp"] = $request->delivery_no_hp;
            }
            if ($request->delivery_type === "dealer") {
                $dataDelivery["name"] = $request->delivery_name;
                $dataDelivery["no_hp"] = $request->delivery_no_hp;
            }
            if ($request->delivery_type === "neq") {
                $dataDelivery["name"] = $request->delivery_name;
                $dataDelivery["no_hp"] = $request->delivery_no_hp;
                $dataDelivery["dealer_neq_id"] = $request->dealer_neq_id;
            }

            $createSpkDeliveryFile = [];

            if ($request->delivery_type === "domicile") {
                $dataDelivery["name"] = $request->delivery_name;
                $dataDelivery["address"] = $request->delivery_address;
                $dataDelivery["city"] = $request->delivery_city;
                $dataDelivery["is_domicile"] = true;


                // $validator = Validator::make($request->file("file_sk"), [
                //     'file_sk' => 'nullable',
                //     'file_sk.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
                // ]);
                // if ($validator->fails()) {
                //     return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
                // }
                if ($request->file_sk) {
                    foreach ($request->file("file_sk") as $item) {
                        $imagePath = $item->store("spk_instansi", "public");

                        $createSpkDeliveryFile[] = SpkInstansiDeliveryFile::create([
                            "spk_instansi_delivery_id" => $updateSpkDelivery->spk_instansi_delivery_id,
                            "files" => $imagePath
                        ]);
                    }
                }
            }
            $updateSpkDelivery->update($dataDelivery);

            $createSpkDeliveryFile = [];
            // $validator = Validator::make($request->file("file_sk"), [
            //     'file_additional' => 'nullable',
            //     'file_additional.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
            // ]);
            // if ($validator->fails()) {
            //     return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            // }
            if ($request->file_additional) {
                foreach ($request->file("file_additional") as $item) {
                    $imagePath = $item->store("spk_instansi", "public");

                    $createSpkDeliveryFile[] = SpkInstansiAdditionalFile::create([
                        "spk_instansi_id" => $spk_instansi_id,
                        "files" => $imagePath
                    ]);
                }
            }

            DB::commit();

            return ResponseFormatter::success("Successfully updated");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    const validator = [
        "sales_id" => "required",
        "sales_name" => "required",
        "indent_instansi_id" => "nullable",
        "po_number" => "required",
        "po_date" => "required",
        "instansi_name" => "required",
        "instansi_address" => "required",
        "province" => "required",
        "city" => "required",
        "district" => "required",
        "sub_district" => "required",
        "postal_code" => "nullable",
        "no_telp" => "nullable",
        "no_hp" => "required",
        "email" => "nullable",
        "instansi_name_legal" => "required",
        "instansi_address_legal" => "required",
        "province_legal" => "required",
        "city_legal" => "required",
        "district_legal" => "required",
        "sub_district_legal" => "required",
        "postal_code_legal" => "nullable",
        "no_telp_legal" => "nullable",
        "no_hp_legal" => "required",
        "delivery_type" => "required|in:ktp,dealer,neq,domicile",
        "file_additional.*" => "nullable|mimes:pdf,jpg,png,pdf|max:5120"

    ];


    function isSelectedDeliveryTypeKTP($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_address", "delivery_city", "delivery_no_hp",
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "ktp";
            }
        )->sometimes(["delivery_no_telp"], "nullable", function ($input) {
            return $input->delivery_type === "ktp";
        });
    }
    function isSelectedDeliveryTypeDealer($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_no_hp",
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "dealer";
            }
        );
    }
    function isSelectedDeliveryTypeNeq($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_no_hp", "dealer_neq_id"
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "neq";
            }
        );
    }
    function isSelectedDeliveryTypeDomicile($validator)
    {
        return $validator->sometimes(
            [
                "delivery_name", "delivery_address", "city",
            ],
            "required",
            function ($input) {
                return $input->delivery_type === "domicile";
            }
        )->sometimes(
            ["file_sk.*"],
            "nullable|mimes:pdf,jpg,png,pdf|max:5120",
            function ($input) {
                return $input->delivery_type === 'domicile';
            }
        );
    }


    protected function createSpkMaster($dealerSelected, $request)
    {


        $result = SpkInstansi::create([
            "spk_instansi_number" => GenerateNumber::generate("SPK-INSTANSI", GenerateAlias::generate($dealerSelected->dealer->dealer_name), "spk_instansis", "spk_instansi_number"),
            "dealer_id" => $dealerSelected->dealer_id,
            "spk_instansi_status" => "create",
            "indent_instansi_id" => $request->indent_instansi_id
        ]);

        //update indent status jadi hold

        if (isset($request->indent_instansi_id)) {
            IndentInstansi::where("indent_instansi_id", $request->indent_instansi_id)->update([
                "indent_instansi_status" => "hold"
            ]);

            $user = Auth::user();

            IndentInstansiLog::create([
                "indent_instansi_id" => $request->indent_instansi_id,
                "user_id" => $user->user_id,
                "indent_instansi_log_action" => "update status to hold"
            ]);
        }

        return $result;
    }

    protected function createSpkInstansiLog($createSpk, $user, $action)
    {
        $data = [
            "spk_instansi_id" => $createSpk->spk_instansi_id,
            "user_id" => $user->user_id,
            "spk_instansi_log_action" => $action
        ];

        $result = SpkInstansiLog::create($data);


        return $result;
    }

    protected function createSpkGeneral($createSpk, $request, $dealerSelected)
    {


        $result = SpkInstansiGeneral::create([
            "spk_instansi_id" => $createSpk->spk_instansi_id,
            "sales_name" => $request->sales_name,
            "sales_id" => $request->sales_id,
            "po_no" => GenerateNumber::generate("PO-INST", GenerateAlias::generate($dealerSelected->dealer->dealer_name), "spk_instansi_generals", "po_no"),
            "po_number" => $request->po_number,
            "po_date" => $request->po_date,
            "instansi_name" => $request->instansi_name,
            "instansi_address" => $request->instansi_address,
            "province" => $request->province,
            "province_id" => $request->province_id,
            "city" => $request->city,
            "city_id" => $request->city_id,
            "district" => $request->district,
            "district_id" => $request->district_id,
            "sub_district" => $request->sub_district,
            "sub_district_id" => $request->sub_district_id,
            "postal_code" => $request->postal_code,
            "no_telp" => $request->no_telp,
            "no_hp" => $request->no_hp,
            "email" => $request->email,
        ]);

        return $result;
    }

    protected function createSpkLegal($createSpk, $request)
    {

        return SpkInstansiLegal::create([
            "spk_instansi_id" => $createSpk->spk_instansi_id,
            "instansi_name" => $request->instansi_name_legal,
            "instansi_address" => $request->instansi_address_legal,
            "province" => $request->province_legal,
            "province_id" => $request->province_id_legal,
            "city" => $request->city_legal,
            "city_id" => $request->city_id_legal,
            "district" => $request->district_legal,
            "district_id" => $request->district_id_legal,
            "sub_district" => $request->sub_district_legal,
            "sub_district_id" => $request->sub_district_id_legal,
            "postal_code" => $request->postal_code_legal,
            "no_telp" => $request->no_telp_legal,
            "no_hp" => $request->no_hp_legal,
        ]);
    }

    protected function createSpkDelivery($createSpk, $request)
    {
        $dataDelivery = [
            "spk_instansi_id" => $createSpk->spk_instansi_id,
            "delivery_type" => $request->delivery_type,
        ];

        if ($request->delivery_type === "ktp") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["address"] = $request->delivery_address;
            $dataDelivery["city"] = $request->delivery_city;
            $dataDelivery["no_telp"] = $request->delivery_no_telp;
            $dataDelivery["no_hp"] = $request->delivery_no_hp;
        }
        if ($request->delivery_type === "dealer") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["no_hp"] = $request->delivery_no_hp;
        }
        if ($request->delivery_type === "neq") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["no_hp"] = $request->delivery_no_hp;
            $dataDelivery["dealer_neq_id"] = $request->dealer_neq_id;
        }

        if ($request->delivery_type === "domicile") {
            $dataDelivery["name"] = $request->delivery_name;
            $dataDelivery["address"] = $request->delivery_address;
            $dataDelivery["city"] = $request->delivery_city;
            $dataDelivery["is_domicile"] = true;
        }

        $result = SpkInstansiDelivery::create($dataDelivery);

        return $result;
    }
    protected function createSpkDeliveryFile($createSpkDelivery, $request)
    {
        $createSpkDeliveryFile = [];
        // $validator = Validator::make($request->file("file_sk"), [
        //     'file_sk' => 'nullable',
        //     'file_sk.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
        // ]);
        // if ($validator->fails()) {
        //     return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
        // }
        if ($request->file_sk) {
            foreach ($request->file("file_sk") as $item) {
                $imagePath = $item->store("spk_instansi", "public");

                $createSpkDeliveryFile[] = SpkInstansiDeliveryFile::create([
                    "spk_instansi_delivery_id" => $createSpkDelivery->spk_instansi_delivery_id,
                    "files" => $imagePath
                ]);
            }
        }


        return $createSpkDeliveryFile;
    }

    protected function createSpkAdditionalFIle($createSpk, $request)
    {
        $createSpkDeliveryFile = [];
        // $validator = Validator::make($request->file("file_sk"), [
        //     'file_additional' => 'nullable',
        //     'file_additional.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
        // ]);
        // if ($validator->fails()) {
        //     return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
        // }
        if ($request->file_additional) {
            foreach ($request->file("file_additional") as $item) {
                $imagePath = $item->store("spk_instansi", "public");

                $createSpkDeliveryFile[] = SpkInstansiAdditionalFile::create([
                    "spk_instansi_id" => $createSpk->spk_instansi_id,
                    "files" => $imagePath
                ]);
            }
        }

        return $createSpkDeliveryFile;
    }


    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), self::validator);

            self::isSelectedDeliveryTypeKTP($validator);
            self::isSelectedDeliveryTypeDealer($validator);
            self::isSelectedDeliveryTypeNeq($validator);
            self::isSelectedDeliveryTypeDomicile($validator);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);

            DB::beginTransaction();

            $createSpk = self::createSpkMaster($getDealerSelected, $request);
            $createSpkGeneral = self::createSpkGeneral($createSpk, $request, $getDealerSelected);
            $createSpkLegal = self::createSpkLegal($createSpk, $request);
            $createSpkDelivery = self::createSpkDelivery($createSpk, $request);
            $createSpkDeliveryFile = null;
            if ($request->delivery_type === "domicile") {
                $createSpkDeliveryFile = self::createSpkDeliveryFile($createSpkDelivery, $request);
            }
            $createSpkAdditionalFile = null;
            $createSpkAdditionalFile = self::createSpkAdditionalFIle($createSpk, $request);

            $createSpkInstansiLog =  self::createSpkInstansiLog($createSpk, $user, "Create Spk Instansi");

            $data = [
                "spk_instansi" => $createSpk,
                "spk_instansi_general" => $createSpkGeneral,
                "spk_instansi_legal" => $createSpkLegal,
                "spk_instansi_delivery" => $createSpkDelivery,
                "spk_instansi_delivery_file" => $createSpkDeliveryFile,
                "spk_instansi_additional_file" => $createSpkAdditionalFile,
                "spk_instansi_log" => $createSpkInstansiLog
            ];

            DB::commit();

            return ResponseFormatter::success($data, "Successfully created SPK !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
}
