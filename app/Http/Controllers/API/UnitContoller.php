<?php

namespace App\Http\Controllers\API;

use App\Enums\EventStatusEnum;
use App\Enums\NeqStatusEnum;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PricelistMotor;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitContoller extends Controller
{
    //

    public function clonePriceList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "location_before" => "required",
                // "location_type_before" => "required|in:dealer,neq",
                "location_after" => "required",
                "location_type_after" => "required|in:dealer,neq",
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $location_before = $request->location_before;

            //mendapatkan location awal pricelist
            $getPriceListBefore = PricelistMotor::latest()
                ->when($location_before, function ($query) use ($location_before) {
                    return $query->where("dealer_id", "LIKE", "%$location_before%")
                        ->orWhere("dealer_neq_id", "LIKE", "%$location_before%");
                })
                ->get();


            //membuat location baru untuk pricelist di dealer atau di neq

            $createPriceListAfter = [];

            foreach ($getPriceListBefore as $item) {
                $pricelistData = [
                    "motor_id" => $item->motor_id,
                    "off_the_road" => $item->off_the_road,
                    "bbn" => $item->bbn,
                    "pricelist_location_type" => $request->location_type_after,
                ];

                // Memasukkan dealer_id hanya jika pricelist_location_type adalah 'dealer'
                if ($request->location_type_after === 'dealer') {
                    $pricelistData['dealer_id'] = $request->location_after;
                }

                // Memasukkan dealer_neq_id hanya jika pricelist_location_type adalah 'neq'
                if ($request->location_type_after === 'neq') {
                    $pricelistData['dealer_neq_id'] = $request->location_after;
                }


                $createPriceListAfter[]  = PricelistMotor::create($pricelistData);
            }

            DB::commit();


            return ResponseFormatter::success($createPriceListAfter, "Successfully clone price list");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function getListPriceList(Request $request)
    {
        try {

            $location = $request->input("location");

            $getListPriceListt = PricelistMotor::latest()
                ->when($location, function ($query) use ($location) {
                    return $query->where("dealer_id", "LIKE", "%$location%")
                        ->orWhere("dealer_neq_id", "LIKE", "%$location%");
                })
                ->get();


            return ResponseFormatter::success($getListPriceListt);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function addPrice(Request $request,)
    {
        try {
            $validator = Validator::make($request->all(), [
                "motor_id" => "required",
                "off_the_road" => "required",
                "bbn" => "required",
                "pricelist_location_type" => "required|in:neq,dealer",
            ]);

            $validator->sometimes(["dealer_neq_id", "dealer_id"], "required", function ($input) {
                return $input->pricelist_location_type === 'neq';
            });
            $validator->sometimes(["dealer_id"], "required", function ($input) {
                return $input->pricelist_location_type === 'dealer';
            });

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();
            // Membuat array dengan kolom-kolom yang akan dimasukkan ke dalam database
            $pricelistData = [
                "motor_id" => $request->motor_id,
                "off_the_road" => $request->off_the_road,
                "bbn" => $request->bbn,
                "pricelist_location_type" => $request->pricelist_location_type,
            ];

            // Memasukkan dealer_id hanya jika pricelist_location_type adalah 'dealer'
            if ($request->pricelist_location_type === 'dealer') {
                $pricelistData['dealer_id'] = $request->dealer_id;
            }

            // Memasukkan dealer_neq_id hanya jika pricelist_location_type adalah 'neq'
            if ($request->pricelist_location_type === 'neq') {
                $pricelistData['dealer_neq_id'] = $request->dealer_neq_id;
                $pricelistData['dealer_id'] = $request->dealer_id;
            }

            // Membuat daftar harga dengan data yang telah disiapkan
            $createPriceList = PricelistMotor::create($pricelistData);

            DB::commit();

            return ResponseFormatter::success($createPriceList, "Successfully createtd pricelist");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailUnit(Request $request, $unit_id)
    {
        try {
            $getDetailUnit = Unit::with(["motor", "shipping_order.dealer", "unit_log.user"])
                ->where("unit_id", $unit_id)->first();

            return ResponseFormatter::success($getDetailUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function getListPaginateUnit(Request $request)
    {
        try {

            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);


            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $date = $request->input('date');
            $motor = $request->input("motor");
            $location = $request->input("location");
            $unit_status = $request->input("unit_status");
            $unit_frame = $request->input("unit_frame");
            $motor_id = $request->input("motor_id");
            $searchQuery = $request->input('q');
            $sortBy = $request->input('sort_by', 'unit_id');
            $sortOrder = $request->input('sort_order', 'asc');
            $has_event = $request->input("has_event", "true");


            $getListPaginateUnit = Unit::with([
                "motor", "shipping_order.dealer", "event_list_unit" => function ($query) {
                    $query->whereHas("event", function ($query) {
                        $query->where("event_status", EventStatusEnum::approve);
                    })->where("is_return", false);
                }, "event_list_unit.event.master_event", "neq_unit.neq", "neq_unit" => function ($query) {
                    $query->whereHas("neq", function ($query) {
                        $query->where("neq_status", NeqStatusEnum::approve);
                    })->where("is_return", false);
                },
                "repair_unit" => function ($query) {
                    $query->whereHas("repair", function ($query) {
                        $query->where("repair_status", "approve");
                    })
                        ->where("is_return", false);
                },
            ])
                ->whereNotNull("unit_status")
                ->where(function ($query) use ($searchQuery) {
                    $query->where('unit_color', 'LIKE', "%$searchQuery%")
                        ->orWhere('unit_engine', 'LIKE', "%$searchQuery%")
                        ->orWhere('unit_status', 'LIKE', "%$searchQuery%")
                        ->orWhere('unit_frame', 'LIKE', "%$searchQuery%");
                })
                ->where(function ($query) use ($location) {
                    $query->where('dealer_id', "LIKE", "%$location%")
                        ->orWhere('dealer_neq_id', "LIKE", "%$location%")
                        ->orWhereNull('dealer_id')
                        ->orWhereNull('dealer_neq_id');
                })
                ->whereHas("shipping_order", function ($query) use ($getDealerByUserSelected) {
                    $query->where("dealer_id", $getDealerByUserSelected->dealer_id);
                })
                ->whereHas("motor", function ($query) use ($motor) {
                    $query->where("motor_name", "LIKE", "%$motor%");
                })
                ->where("unit_status", "LIKE", "%$unit_status%")
                ->where("motor_id", "LIKE", "%$motor_id%")
                ->where("unit_frame", "LIKE", "%$unit_frame%")
                ->when($date, function ($query) use ($date) {
                    return $query->whereDate('unit_received_date', 'LIKE', "%$date%");
                })
                ->orderBy($sortBy, $sortOrder);



            $getListPaginateUnit = $getListPaginateUnit->paginate($limit);


            return ResponseFormatter::success($getListPaginateUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
