<?php

namespace App\Http\Controllers\API;

use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Broker;
use App\Models\City;
use App\Models\Color;
use App\Models\Dealer;
use App\Models\DealerByUser;
use App\Models\DealerNeq;
use App\Models\District;
use App\Models\Education;
use App\Models\Event;
use App\Models\Hobby;
use App\Models\Leasing;
use App\Models\MainDealer;
use App\Models\Martial;
use App\Models\MasterEvent;
use App\Models\MicroFinance;
use App\Models\Motor;
use App\Models\MotorBrand;
use App\Models\Province;
use App\Models\Residence;
use App\Models\Sales;
use App\Models\SubDistrict;
use App\Models\Tenor;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class Master extends Controller
{
    //

    public function getBank(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $searchQuery = $request->input("q");
            $getPaginate = Bank::latest()
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where("bank_name", "LIKE", "%$searchQuery%");
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function getColor(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $searchQuery = $request->input("q");
            $getPaginate = Color::latest()
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where("color_name", "LIKE", "%$searchQuery%");
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function getLeasing(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $searchQuery = $request->input("q");
            $getPaginate = Leasing::latest()
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where("leasing_name", "LIKE", "%$searchQuery%");
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function getMicrofinance(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $searchQuery = $request->input("q");
            $getPaginate = MicroFinance::latest()
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where("micro_finance_name", "LIKE", "%$searchQuery%");
                })
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getSales(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $searchQuery = $request->input("q");
            $getSalesPaginate = Sales::latest()
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where("sales_name", "LIKE", "%$searchQuery%")
                        ->orWhere("sales_nip", "LIKE", "%$searchQuery%");
                })
                ->paginate($limit);

            return ResponseFormatter::success($getSalesPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getDetailMasterEvent(Request $request, $master_event_id)
    {
        try {
            $getDetailMasterEvent = MasterEvent::where("master_event_id", $master_event_id)
                ->with(["event" => function ($query) {
                    $query->where("event_status", "approve")
                        ->with(["event_unit" => function ($query) {
                            $query->with(["unit" => function ($query) {
                                $query->with("motor"); // Mengambil detail motor untuk setiap unit
                            }])
                                ->where("is_return", false);
                        }])
                        ->withCount(['event_unit as event_unit_total' => function ($query) {
                            $query
                                ->where("is_return", false)
                                ->selectRaw('count(*)');
                        }]); // Menghitung total event unit
                }])
                ->first();

            // Return response
            return ResponseFormatter::success($getDetailMasterEvent);;
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getEventPaginate(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListEvent = MasterEvent::with([
                    "event" => function ($query) {
                        $query->where("event_status", "approve");
                        $query->withCount('event_unit as event_unit_total');
                        $query->whereHas("event_unit", function ($query) {
                            $query->where("is_return", false);
                        });
                    },
                    "event.event_unit"
                ])->where(function ($query) use ($searchQuery) {
                    $query->where("master_event_name", "LIKE", "%$searchQuery%")
                        ->orWhere("master_event_location", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListEvent = MasterEvent::with(["event.event_unit"])->where(function ($query) use ($searchQuery) {
                    $query->where("master_event_name", "LIKE", "%$searchQuery%")
                        ->orWhere("master_event_location", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListEvent);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateStatusEvent(Request $request, $master_event_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "master_event_status" => "required|boolean",
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailMasterEvent = MasterEvent::where("master_event_id", $master_event_id)->first();

            $getDetailMasterEvent->update([
                "master_event_status" => $request->master_event_status,
            ]);



            DB::commit();

            return ResponseFormatter::success($getDetailMasterEvent, "Successfully updated event status !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function updateEvent(Request $request, $master_event_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "master_event_date" => "required|date",
                "master_event_name" => "required",
                "master_event_location" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetailMasterEvent = MasterEvent::where("master_event_id", $master_event_id)->first();

            $getDetailMasterEvent->update([
                "master_event_date" => $request->master_event_date,
                "master_event_name" => $request->master_event_name,
                "master_event_location" => $request->master_event_location,
                "master_event_note" => $request->master_event_note
            ]);



            DB::commit();

            return ResponseFormatter::success($getDetailMasterEvent, "Successfully updated event !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function createEvent(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "master_event_date" => "required|date",
                "master_event_name" => "required",
                "master_event_location" => "required"
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);

            $createEvent = MasterEvent::create([
                "master_event_date" => $request->master_event_date,
                "master_event_name" => $request->master_event_name,
                "master_event_location" => $request->master_event_location,
                "dealer_id" => $getDealerByUserSelected->dealer_id,
                "master_event_note" => $request->master_event_note
            ]);

            DB::commit();

            return ResponseFormatter::success($createEvent, "Successfully created event !");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function GelistPaginateMainDealer(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListMainDealer = MainDealer::where(function ($query) use ($searchQuery) {
                    $query->where("main_dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("main_dealer_identifier", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListMainDealer = MainDealer::where(function ($query) use ($searchQuery) {
                    $query->where("main_dealer_name", "LIKE", "%$searchQuery%")
                        ->orWhere("main_dealer_identifier", "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListMainDealer);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
    public function getListPaginateMotor(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");
            $sortBy = $request->input('sort_by', 'motor_name');
            $sortOrder = $request->input('sort_order', 'asc');

            $user = Auth::user();
            $getDealerSelected = GetDealerByUserSelected::GetUser($user->user_id);



            if ($paginate === "true") {
                $getListMotor = Motor::where(function ($query) use ($searchQuery) {
                    $query->where("motor_name", "LIKE", "%$searchQuery%")
                        ->orWhere("motor_code", "LIKE", "%$searchQuery%");
                })
                    ->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListMotor = Motor::with(["motor_pricelist" => function ($query) use ($getDealerSelected) {
                    return $query->where("dealer_id", $getDealerSelected->dealer_id);
                }])
                    ->where(function ($query) use ($searchQuery) {
                        $query->where("motor_name", "LIKE", "%$searchQuery%")
                            ->orWhere("motor_code", "LIKE", "%$searchQuery%");
                    })
                    ->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListMotor);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListLocationByUserLogin(Request $request)
    {
        try {
            $user = Auth::user();
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');
            $spk_location = $request->input('spk_location');


            $getDealerByUser = DealerByUser::where("user_id", $user->user_id)
                ->with(["dealer"])
                ->where("isSelected", 1)
                ->orderBy($sortBy, $sortOrder)
                ->first();

            if (!$getDealerByUser) {
                return ResponseFormatter::error("Dealer not found for the user", 404);
            }

            $dealerNeqList = DealerNeq::where("dealer_id", $getDealerByUser->dealer_id)->get();
            $data = [
                "dealer" => $getDealerByUser,
                "dealer_neq" => $dealerNeqList,
            ];

            if ($spk_location !== "true") {
                $eventList = MasterEvent::where('dealer_id', $getDealerByUser->dealer_id)->get();
                $data["event_list"] = $eventList;
            }

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListDealerSelected(Request $request)
    {
        try {
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            $user = Auth::user();

            $getListAllDealerSelected = DealerByUser::where("user_id", $user->user_id)->with(["dealer"])->orderBy($sortBy, $sortOrder)->get();

            return ResponseFormatter::success($getListAllDealerSelected);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListDealerMDS(Request $request)
    {
        try {
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');



            // $getListAllDealer = DealerByUser::with(["dealer"])
            $getListAllDealer = Dealer::orderBy($sortBy, $sortOrder)->get();

            return ResponseFormatter::success($getListAllDealer);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListDealerNeq(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $paginate = $request->input("paginate");
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListAllMDSMD = DealerNeq::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_neq_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_neq_code", "LIKE", "%$searchQuery%");
                })
                    ->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListAllMDSMD = DealerNeq::where(function ($query) use ($searchQuery) {
                    $query->where("dealer_neq_name", "LIKE", "%$searchQuery%")
                        ->orWhere("dealer_neq_code", "LIKE", "%$searchQuery%");
                })
                    ->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListAllMDSMD);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListMaritalStatus(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListMaritalStatus = Martial::where(function ($query) use ($searchQuery) {
                    $query->where('martial_name', "LIKE", "%$searchQuery%");
                })->where('martial_status', "active")->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListMaritalStatus = Martial::where(function ($query) use ($searchQuery) {
                    $query->where('martial_name', "LIKE", "%$searchQuery%");
                })->where('martial_status', "active")->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListMaritalStatus);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListHobby(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'asc');

            if ($paginate === "true") {
                $getListHobby = Hobby::where(function ($query) use ($searchQuery) {
                    $query->where('hobbies_name', "LIKE", "%$searchQuery%");
                })->where('hobbies_status', "active")->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getListHobby = Hobby::where(function ($query) use ($searchQuery) {
                    $query->where('hobbies_name', "LIKE", "%$searchQuery%");
                })->where('hobbies_status', "active")->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getListHobby);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListTenor(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit');
            ($limit) ? $limit : $limit = 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('tenor_amount_total', 'asc');

            if ($paginate === "true") {
                $getTenorList = Tenor::where(function ($query) use ($searchQuery) {
                    $query->where('tenor_amount_total', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getTenorList = Tenor::where(function ($query) use ($searchQuery) {
                    $query->where('tenor_amount_total', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getTenorList);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListProvince(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $sortBy = $request->input('sort_by', 'created_at');
            $limit = $request->input('limit') ?? 5;
            $sortOrder = $request->input('province_name', 'asc');

            if ($paginate === "true") {
                $getProvinceList = Province::where(function ($query) use ($searchQuery) {
                    $query->where('province_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getProvinceList = Province::where(function ($query) use ($searchQuery) {
                    $query->where('province_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }



            return ResponseFormatter::success($getProvinceList);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListCity(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $province_id = $request->input('province_id');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('city_name', 'asc');


            if ($paginate === "true") {
                $getCityList = City::where('province_id', $province_id)->where(function ($query) use ($searchQuery) {
                    $query->where('city_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getCityList =  City::where('province_id', $province_id)->where(function ($query) use ($searchQuery) {
                    $query->where('city_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }

            return ResponseFormatter::success($getCityList);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function getListDistrict(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $city_id = $request->input('city_id');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('district_name', 'asc');
            if ($paginate === "true") {
                $getDistrictList = District::where('city_id', $city_id)->where(function ($query) use ($searchQuery) {
                    $query->where('district_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getDistrictList = District::where('city_id', $city_id)->where(function ($query) use ($searchQuery) {
                    $query->where('district_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }
            return ResponseFormatter::success($getDistrictList);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListSubdistrict(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $district_id = $request->input('district_id');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sub_district_name', 'asc');
            if ($paginate === "true") {
                $getDistrictList = SubDistrict::where('district_id', $district_id)->where(function ($query) use ($searchQuery) {
                    $query->where('sub_district_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->paginate($limit);
            } else {
                $getDistrictList = SubDistrict::where('district_id', $district_id)->where(function ($query) use ($searchQuery) {
                    $query->where('sub_district_name', "LIKE", "%$searchQuery%");
                })->orderBy($sortBy, $sortOrder)->get();
            }
            return ResponseFormatter::success($getDistrictList);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListResidence(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('residence_name', 'asc');

            $getResidenceList = Residence::where((function ($query) use ($searchQuery) {
                $query->where('residence_name', 'LIKE', "%$searchQuery%");
            }))->where('residence_status', 'active')->orderBy($sortBy, $sortOrder);

            if ($paginate === 'true') {
                $getResidenceList = $getResidenceList->paginate($limit);
            } else {
                $getResidenceList = $getResidenceList->get();
            }
            return ResponseFormatter::success($getResidenceList);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListEducation(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('education_name', 'asc');

            $getEducationList = Education::where((function ($query) use ($searchQuery) {
                $query->where('education_name', 'LIKE', "%$searchQuery%");
            }))->where('education_status', 'active')->orderBy($sortBy, $sortOrder);

            if ($paginate === 'true') {
                $getEducationList = $getEducationList->paginate($limit);
            } else {
                $getEducationList = $getEducationList->get();
            }
            return ResponseFormatter::success($getEducationList);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListWork(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('work_name', 'asc');

            $getListWork = Work::where((function ($query) use ($searchQuery) {
                $query->where('work_name', 'LIKE', "%$searchQuery%");
            }))->where('work_status', 'active')->orderBy($sortBy, $sortOrder);

            if ($paginate === 'true') {
                $getListWork = $getListWork->paginate($limit);
            } else {
                $getListWork = $getListWork->get();
            }
            return ResponseFormatter::success($getListWork);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListIncome(Request $request)
    {
        try {

            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('created_at', 'asc');
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListExpenditure(Request $request)
    {
        try {

            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('created_at', 'asc');
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListBroker(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('broker_name', 'asc');
            $getListBroker = Broker::where((function ($query) use ($searchQuery) {
                $query->where('broker_name', 'LIKE', "%$searchQuery%");
            }))->where('broker_status', 'active')->orderBy($sortBy, $sortOrder);

            if ($paginate === 'true') {
                $getListBroker = $getListBroker->paginate($limit);
            } else {
                $getListBroker = $getListBroker->get();
            }
            return ResponseFormatter::success($getListBroker);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

    public function getListMotorBrand(Request $request)
    {
        try {
            $paginate = $request->input('paginate');
            $searchQuery = $request->input('q');
            $limit = $request->input('limit') ?? 5;
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('motor_brand_name', 'asc');
            $getListMotorBrand = MotorBrand::where((function ($query) use ($searchQuery) {
                $query->where('motor_brand_name', 'LIKE', "%$searchQuery%");
            }))->where('motor_brand_status', 'active')->orderBy($sortBy, $sortOrder);

            if ($paginate === 'true') {
                $getListMotorBrand = $getListMotorBrand->paginate($limit);
            } else {
                $getListMotorBrand = $getListMotorBrand->get();
            }
            return ResponseFormatter::success($getListMotorBrand);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }
}
