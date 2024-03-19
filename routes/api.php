<?php

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\EventReturnController;
use App\Http\Controllers\API\Master;
use App\Http\Controllers\API\RepairController;
use App\Http\Controllers\API\RepairReturnController;
use App\Http\Controllers\API\ShippingOrderController;
use App\Http\Controllers\API\UnitContoller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/hai', function (Request $request) {
    return ResponseFormatter::success("hello");
});

Route::prefix("v1")->group(function () {
    Route::prefix("authentication")->group(function () {
        Route::post("/login", [AuthenticationController::class, "login"]);
        Route::post("/register", [AuthenticationController::class, "register"]);
    });
    Route::middleware("auth:sanctum")->group(function () {



        Route::prefix("shipping-order")->group(function () {
            Route::post("/sync-data/{city}", [ShippingOrderController::class, "sycnShippingOrder"]);
            Route::get("/list", [ShippingOrderController::class, "getListShippingOrder"]);
            Route::get("/detail/{shipping_order_id}", [ShippingOrderController::class, "getDetailShippingOrder"]);
        });

        Route::prefix("unit")->group(function () {
            Route::put("/status/{unit_id}", [ShippingOrderController::class, "updateTerimaUnitShippingOrder"]);
            Route::get("/list", [UnitContoller::class, "getListPaginateUnit"]);
            Route::get("/detail/{unit_id}", [UnitContoller::class, "getDetailUnit"]);
        });

        Route::prefix("master")->group(function () {
            Route::get("/motor", [Master::class, "getListPaginateMotor"]);
            Route::get("/neq", [Master::class, "getListDealerNeq"]);
            Route::get("/mds", [Master::class, "getListDealerMDS"]);
            Route::get("/location-current", [Master::class, "getListLocationByUserLogin"]);
            Route::get("/main-dealer", [Master::class, "GelistPaginateMainDealer"]);

            Route::prefix("event")->group(function () {
                Route::post("/create", [Master::class, "createEvent"]);
                Route::get("/detail/{master_event_id}", [Master::class, "getDetailMasterEvent"]);
                Route::put("/update/{master_event_id}", [Master::class, "updateEvent"]);
                Route::put("/status/{master_event_id}", [Master::class, "updateStatusEvent"]);
                Route::get("/list", [Master::class, "getEventPaginate"]);
            });
        });

        Route::prefix("repair")->group(function () {
            Route::post("/create", [RepairController::class, "createRepair"]);
            Route::put("/update/{repair_id}", [RepairController::class, "updateRepair"]);
            Route::put("/update/status/{repair_id}", [RepairController::class, "updateStatusRepair"]);
            Route::delete("/delete/{repair_id}", [RepairController::class, "deleteRepair"]);
            Route::delete("/unit/delete/{repair_unit_id}", [RepairController::class, "deleteRepairUnit"]);
            Route::get("/detail/{repair_id}", [RepairController::class, "getDetailRepair"]);
            Route::get("/list", [RepairController::class, "getPaginateRepairUnit"]);

            Route::prefix("return")->group(function () {
                Route::get("/list", [RepairReturnController::class, "getPaginateRepairReturn"]);
                Route::post("/create", [RepairReturnController::class, "createRepairReturn"]);
                Route::put("/update/{repair_return_id}", [RepairReturnController::class, "updateRepairReturn"]);
                Route::put("/status/{repair_return_id}", [RepairReturnController::class, "updateStatusRepairReturn"]);
                Route::get("/detail/{repair_return_id}", [RepairReturnController::class, "getDetailRepairReturn"]);
                Route::delete("/delete/unit/{repair_return_unit_id}", [RepairReturnController::class, "deleteRepairReturnUnit"]);
                Route::delete("/delete/{repair_return_id}", [RepairReturnController::class, "deleteRepairReturn"]);
                Route::prefix("repair-unit")->group(function () {
                    Route::get("/list", [RepairReturnController::class, "getRepairUnit"]);
                });
            });
        });

        Route::prefix("delivery")->group(function () {
            Route::post("/create", [DeliveryController::class, "CreateDelivery"]);
            Route::get("/list", [DeliveryController::class, "GetListPagianteDelivery"]);
            Route::get("/detail/{delivery_id}", [DeliveryController::class, "DetailDelivery"]);
            Route::put("/status/{delivery_id}", [DeliveryController::class, "changeStatusDelivery"]);
            Route::put("/update/{delivery_id}", [DeliveryController::class, "updateDelivery"]);
            Route::delete("/delete/{delivery_id}", [DeliveryController::class, "deleteDelivery"]);
        });

        Route::prefix("event")->group(function () {
            Route::post("/create", [EventController::class, "createEvent"]);
            Route::put("/update/{event_id}", [EventController::class, "updateEvent"]);
            Route::get("/list", [EventController::class, "getPaginateEvent"]);
            Route::get("/detail/{event_id}", [EventController::class, "getDetailEvent"]);
            Route::put("/status/{event_id}", [EventController::class, "updateStatusEvent"]);
            Route::delete("/unit/delete/{event_list_unit_id}", [EventController::class, "deleteUnitEvent"]);
            Route::delete("/delete/{event_id}", [EventController::class, "deleteEvent"]);


            Route::prefix("return")->group(function () {
                Route::post("/create", [EventReturnController::class, "createEventReturn"]);
                Route::get("/list", [EventReturnController::class, "getPaginateEventReturn"]);
                Route::get("/detail/{event_return_id}", [EventReturnController::class, "getDetailEventReturn"]);
                Route::put("/status/{event_return_id}", [EventReturnController::class, "updateStatusEventReturn"]);
                Route::put("/update/{event_return_id}", [EventReturnController::class, "updateEventReturn"]);
                Route::prefix("event-unit")->group(function () {
                    Route::get("/list/{master_event_id}", [EventReturnController::class, "getAllUnitEvent"]);
                });
            });
        });
    });
});
