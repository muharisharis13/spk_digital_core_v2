<?php

namespace App\Http\Controllers\API;

use App\Enums\EventStatusEnum;
use App\Enums\NeqStatusEnum;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PricelistMotor;
use App\Models\PricelistMotorHistories;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitContoller extends Controller
{
    //

    public function getDetailPriceList(Request $request, $pricelist_motor_id)
    {
        try {




            $getPaginate = PricelistMotor::where("pricelist_motor_id", $pricelist_motor_id)->with(["motor",])->first();

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function clonePriceList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "location_before" => "required",
                // "location_type_before" => "required|in:dealer,neq",
                "location_after" => "required",
                "location_type_after" => "required|in:dealer,neq",
                "discount" => "nullable"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            $location_before = $request->location_before;

            //mendapatkan location awal pricelist
            $getPriceListBefore = PricelistMotor::latest()
                ->when($location_before, function ($query) use ($location_before) {
                    return $query->where("dealer_id", $location_before)
                        ->orWhere("dealer_neq_id",  $location_before);
                })
                ->get();


            //membuat location baru untuk pricelist di dealer atau di neq

            $createPriceListAfter = [];

            // return $getPriceListBefore

            $locationAfter = $request->location_after;

            foreach ($getPriceListBefore as $item) {
                $pricelistData = [
                    "motor_id" => $item->motor_id,
                    "off_the_road" => $item->off_the_road,
                    "bbn" => $item->bbn,
                    "pricelist_location_type" => $request->location_type_after,
                    "discount" => $request->discount
                ];

                // Memasukkan dealer_id hanya jika pricelist_location_type adalah 'dealer'
                if ($request->location_type_after === 'dealer') {
                    $pricelistData['dealer_id'] = $request->location_after;

                    $getDetailPriceListMotor = PricelistMotor::where([
                        "motor_id" => $item->motor_id,

                    ])
                        ->when($locationAfter, function ($query) use ($locationAfter) {
                            return $query->where("dealer_id", $locationAfter);
                        })
                        ->first();

                    if (!isset($getDetailPriceListMotor->pricelist_motor_id)) {
                        $createPriceListAfter[]  = PricelistMotor::create($pricelistData);
                    } else {
                        $getDetailPriceListMotor->update($pricelistData);
                    }
                }

                // Memasukkan dealer_neq_id hanya jika pricelist_location_type adalah 'neq'
                if ($request->location_type_after === 'neq') {
                    $pricelistData['dealer_neq_id'] = $request->location_after;

                    $getDetailPriceListMotor = PricelistMotor::where([
                        "motor_id" => $item->motor_id,

                    ])
                        ->when($locationAfter, function ($query) use ($locationAfter) {
                            return $query->where("dealer_neq_id", $locationAfter);
                        })
                        ->first();

                    if (!isset($getDetailPriceListMotor->pricelist_motor_id)) {
                        $createPriceListAfter[]  = PricelistMotor::create($pricelistData);
                    } else {
                        $getDetailPriceListMotor->update($pricelistData);
                    }
                }
            }

            DB::commit();

            $getListPriceList = PricelistMotor::when($locationAfter, function ($query) use ($locationAfter) {
                return $query->where("dealer_id", "LIKE", "%$locationAfter%")
                    ->orWhere("dealer_neq_id", "LIKE", "%$locationAfter%");
            })->get();


            return ResponseFormatter::success($getListPriceList, "Successfully clone price list");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function getListPriceList(Request $request)
    {
        try {

            $location = $request->input("location");
            $limit = $request->input("limit", 5);

            $getListPriceListt = PricelistMotor::latest()
                ->with(["motor",])
                ->when($location, function ($query) use ($location) {
                    return $query->where("dealer_id", "LIKE", "%$location%")
                        ->orWhere("dealer_neq_id", "LIKE", "%$location%");
                })
                ->paginate($limit);


            return ResponseFormatter::success($getListPriceListt);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updatePrice(Request $request, $pricelist_motor_id)
    {
        try {

            $validator = Validator::make($request->all(), [
                "off_the_road" => "required",
                "bbn" => "required",
                "discount" => "nullable",
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailPrice = PricelistMotor::where("pricelist_motor_id", $pricelist_motor_id)->first();

            $getDetailPrice->update([
                "off_the_road" => $request->off_the_road,
                "bbn" => $request->bbn,
                "discount" => $request->discount,
            ]);

            $user = Auth::user();

            PricelistMotorHistories::create([
                "pricelist_motor_id" => $getDetailPrice->pricelist_motor_id,
                "off_the_road" => $request->off_the_road,
                "bbn" => $request->bbn,
                "commission" => $getDetailPrice->commission,
                "user_id" => $user->user_id,
                "discount" => $request->discount
            ]);

            DB::commit();

            return ResponseFormatter::success($getDetailPrice, "Successfully update price");
        } catch (\Throwable $e) {
            DB::rollBack();
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
                "discount" => "nullable"
            ]);

            $validator->sometimes(["dealer_neq_id"], "required", function ($input) {
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
                "discount" => $request->discount
            ];

            // Memasukkan dealer_id hanya jika pricelist_location_type adalah 'dealer'
            if ($request->pricelist_location_type === 'dealer') {
                $pricelistData['dealer_id'] = $request->dealer_id;
            }

            // Memasukkan dealer_neq_id hanya jika pricelist_location_type adalah 'neq'
            if ($request->pricelist_location_type === 'neq') {
                $pricelistData['dealer_neq_id'] = $request->dealer_neq_id;
                // $pricelistData['dealer_id'] = $request->dealer_id;
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

            $limit = $request->input('limit') ?? 5;
            $date = $request->input('date');
            $motor = $request->input("motor");
            $location = $request->input("location");
            $unit_status = $request->input("unit_status");
            $unit_frame = $request->input("unit_frame");
            $motor_id = $request->input("motor_id");
            $searchQuery = $request->input('q');

            $getListPaginateUnit = Unit::with([
                "motor",
                "shipping_order.dealer",
                "event_list_unit" => function ($query) {
                    $query->whereHas("event", function ($query) {
                        $query->where("event_status", EventStatusEnum::approve);
                    })->where("is_return", false);
                },
                "event_list_unit.event.master_event",
                "neq_unit.neq",
                "neq_unit" => function ($query) {
                    $query->whereHas("neq", function ($query) {
                        $query->where("neq_status", NeqStatusEnum::approve);
                    })->where("is_return", false);
                },
                "repair_unit" => function ($query) {
                    $query->whereHas("repair", function ($query) {
                        $query->where("repair_status", "approve");
                    })->where("is_return", false);
                },
            ])
                ->whereNotNull("unit_status")
                ->when($searchQuery, function ($query, $searchQuery) {
                    $query->where(function ($query) use ($searchQuery) {
                        $query->where('unit_color', 'LIKE', "%$searchQuery%")
                            ->orWhere('unit_engine', 'LIKE', "%$searchQuery%")
                            ->orWhere('unit_status', 'LIKE', "%$searchQuery%")
                            ->orWhere('unit_frame', 'LIKE', "%$searchQuery%");
                    });
                })
                ->when($location, function ($query, $location) {
                    $query->where(function ($query) use ($location) {
                        $query->where('dealer_id', 'LIKE', "%$location%")
                            ->orWhere('dealer_neq_id', 'LIKE', "%$location%");
                    })
                        ->orWhereHas('event_list_unit.event.master_event', function ($query) use ($location) {
                            $query->where('master_event_id', 'LIKE', "%$location%");
                        });
                })
                ->whereHas("shipping_order", function ($query) use ($getDealerByUserSelected) {
                    $query->where("dealer_id", $getDealerByUserSelected->dealer_id);
                })
                ->when($motor, function ($query, $motor) {
                    $query->whereHas("motor", function ($query) use ($motor) {
                        $query->where("motor_name", "LIKE", "%$motor%");
                    });
                })
                ->when($unit_status, function ($query, $unit_status) {
                    return $query->where('unit_status', 'LIKE', "%$unit_status%");
                })
                ->when($motor_id, function ($query, $motor_id) {
                    return $query->where("motor_id", "=", "$motor_id");
                })
                ->when($unit_frame, function ($query, $unit_frame) {
                    return $query->where("unit_frame", "LIKE", "%$unit_frame%");
                })
                ->when($date, function ($query, $date) {
                    return $query->whereDate('unit_received_date', 'LIKE', "%$date%");
                })
                ->latest()
                ->paginate($limit);

            return ResponseFormatter::success($getListPaginateUnit);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
