<?php

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\API\AdiraController;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\EventReturnController;
use App\Http\Controllers\API\ExportPDFController;
use App\Http\Controllers\API\IndentController;
use App\Http\Controllers\API\IndentInstansiController;
use App\Http\Controllers\API\Master;
use App\Http\Controllers\API\NeqController;
use App\Http\Controllers\API\NeqReturnController;
use App\Http\Controllers\API\RepairController;
use App\Http\Controllers\API\RepairReturnController;
use App\Http\Controllers\API\ReturUnitController;
use App\Http\Controllers\API\ShippingOrderController;
use App\Http\Controllers\API\ShippingOrderController2;
use App\Http\Controllers\API\SPKController;
use App\Http\Controllers\API\SpkInstansiController;
use App\Http\Controllers\API\UnitContoller;
use App\Http\Controllers\API\UserController;
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
        // Route::post("/logout", [AuthenticationController::class, "logout"]);
    });

    Route::prefix("export")->group(function () {
        Route::prefix("faktur")->group(function () {
            Route::get("/indent/{indent_id}", [ExportPDFController::class, "printPdfIndent2"]);
            Route::get("/indent-payment/{indent_payment_id}", [ExportPDFController::class, "printPDFPayment2"]);
        });
        Route::get("/surat-jalan", [ExportPDFController::class, "printSuratJalan"]);
        Route::get("/province", [ExportPDFController::class, "getProvince"]);
    });


    Route::prefix("adira-spk")->group(function () {
        Route::post("/create", [AdiraController::class, "createSPK"]);
        Route::get("/list", [AdiraController::class, "getPaginateSpk"]);
    });


    Route::prefix("secret")->group(function () {
        Route::prefix("retur-unit")->group(function () {
            Route::post("/update-unit/{id}", [ReturUnitController::class, "receivedApprovedReject"])->middleware(["checkApiKeyMD"]);
        });
    });


    Route::middleware("auth:sanctum")->group(function () {

        Route::prefix("permissions")->group(function () {
            Route::get("/list", [UserController::class, "getPermissionAttribute"]);
        });

        Route::prefix("user")->group(function () {
            Route::post("/assign-permission", [UserController::class, "assignPermission"]);
            Route::get("/user-permission", [UserController::class, "getPermissionUser"]);
            Route::get("/current-dealer", [UserController::class, "getCurrentDealer"]);
            Route::post("/logout", [UserController::class, "logout"]);
            Route::put("/select-dealer/{dealer_by_user_id}", [UserController::class, "selectDealerByUser"]);
        });

        Route::prefix("shipping-order")->group(function () {
            // Route::post("/sync-data/{city}", [ShippingOrderController::class, "sycnShippingOrder"]);
            Route::post("/sync-data2", [ShippingOrderController2::class, "syncShippingOrderMD"]);
            Route::get("/list", [ShippingOrderController::class, "getListShippingOrder"]);
            Route::get("/detail/{shipping_order_id}", [ShippingOrderController::class, "getDetailShippingOrder"]);
        });

        Route::prefix("unit")->group(function () {
            Route::put("/status/{unit_id}", [ShippingOrderController::class, "updateTerimaUnitShippingOrder"]);
            Route::get("/list", [UnitContoller::class, "getListPaginateUnit"]);
            // ->middleware('permission:read_unit');
            Route::get("/detail/{unit_id}", [UnitContoller::class, "getDetailUnit"]);

            Route::prefix("pricelist")->group(function () {
                Route::post("/create", [UnitContoller::class, "addPrice"]);
                Route::get("/list", [UnitContoller::class, "getListPriceList"]);
                Route::get("/detail/{id}", [UnitContoller::class, "getDetailPriceList"]);
                Route::post("/clone", [UnitContoller::class, "clonePriceList"]);
                Route::put("/update/{id}", [UnitContoller::class, "updatePrice"]);
            });
        });

        Route::prefix("master")->group(function () {
            Route::get("/motor", [Master::class, "getListPaginateMotor"]);
            Route::get("/dealer-neq", [Master::class, "getListDealerNeq"]);
            Route::get("/mds", [Master::class, "getListDealerMDS"]);
            Route::get("/dealer-selected", [Master::class, "getListDealerSelected"]);
            Route::get("/location-current", [Master::class, "getListLocationByUserLogin"]);
            Route::get("/main-dealer", [Master::class, "GelistPaginateMainDealer"]);

            Route::prefix("event")->group(function () {
                Route::post("/create", [Master::class, "createEvent"]);
                Route::get("/detail/{master_event_id}", [Master::class, "getDetailMasterEvent"]);
                Route::put("/update/{master_event_id}", [Master::class, "updateEvent"]);
                Route::put("/status/{master_event_id}", [Master::class, "updateStatusEvent"]);
                Route::get("/list", [Master::class, "getEventPaginate"]);
            });

            Route::prefix("sales")->group(function () {
                Route::get("/list", [Master::class, "getSales"]);
            });
            Route::prefix("microfinance")->group(function () {
                Route::get("/list", [Master::class, "getMicrofinance"]);
            });
            Route::prefix("leasing")->group(function () {
                Route::get("/list", [Master::class, "getLeasing"]);
            });
            Route::prefix("color")->group(function () {
                Route::get("/list", [Master::class, "getColor"]);
            });
            Route::prefix("bank")->group(function () {
                Route::get("/list", [Master::class, "getBank"]);
            });
            Route::prefix("marital")->group(function () {
                Route::get("/list", [Master::class, "getListMaritalStatus"]);
            });
            Route::prefix("hobby")->group(function () {
                Route::get("/list", [Master::class, "getListHobby"]);
            });
            Route::prefix("tenor")->group(function () {
                Route::get("/list", [Master::class, "getListTenor"]);
            });

            Route::prefix("province")->group(function () {
                Route::get("/list", [Master::class, "getListProvince"]);
            });

            Route::prefix("cities")->group(function () {
                Route::get("/list", [Master::class, "getListCity"]);
            });
            Route::prefix("district")->group(function () {
                Route::get("/list", [Master::class, "getListDistrict"]);
            });
            Route::prefix("subdistrict")->group(function () {
                Route::get("/list", [Master::class, "getListSubdistrict"]);
            });
            Route::prefix("residence")->group(function () {
                Route::get("/list", [Master::class, "getListResidence"]);
            });
            Route::prefix("education")->group(function () {
                Route::get("/list", [Master::class, "getListEducation"]);
            });
            Route::prefix("work")->group(function () {
                Route::get("/list", [Master::class, "getListWork"]);
            });

            Route::prefix("broker")->group(function () {
                Route::get("/list", [Master::class, "getListBroker"]);
            });

            Route::prefix("motor-brand")->group(function () {
                Route::get("/list", [Master::class, "getListMotorBrand"]);
            });
            Route::prefix("motor")->group(function () {
                Route::get("/detail/{id}", [Master::class, "getDetailMotor"]);
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
                Route::delete("/delete/{event_return_id}", [EventReturnController::class, "deleteEventReturn"]);
                Route::delete("/unit/delete/{event_return_list_unit_id}", [EventReturnController::class, "deleteEventReturnUnit"]);
                Route::prefix("event-unit")->group(function () {
                    Route::get("/list/{master_event_id}", [EventReturnController::class, "getAllUnitEvent"]);
                });
            });
        });

        Route::prefix("neq")->group(function () {
            Route::post("/create", [NeqController::class, "createNeq"]);
            Route::get("/list", [NeqController::class, "getPaginateNeq"]);
            Route::get("/detail/{neq_id}", [NeqController::class, "getDetailNeq"]);
            Route::put("/status/{neq_id}", [NeqController::class, "updateStatusNeq"]);
            Route::put("/update/{neq_id}", [NeqController::class, "updateNeq"]);
            Route::delete("/delete/{neq_id}", [NeqController::class, "deleteNeq"]);
            Route::prefix("unit")->group(function () {
                Route::delete("/delete/{neq_unit_id}", [NeqController::class, "deleteUnitNeq"]);
            });

            Route::prefix("return")->group(function () {
                Route::post("/create", [NeqReturnController::class, "createNeqReturn"]);
                Route::put("/update/{neq_return_id}", [NeqReturnController::class, "updateNeqReturn"]);
                Route::get("/list", [NeqReturnController::class, "getPaginateNeqReturn"]);
                Route::get("/detail/{neq_return_id}", [NeqReturnController::class, "getDetailNeqReturn"]);
                Route::delete("/delete/{neq_return_id}", [NeqReturnController::class, "deleteNeqReturn"]);
                Route::put("/status/{neq_return_id}", [NeqReturnController::class, "updateStatusNeqReturn"]);
                Route::prefix("neq-unit")->group(function () {
                    Route::delete("/delete/{neq_return_unit_id}", [NeqReturnController::class, "deleteNeqReturnUnit"]);
                    Route::get("/list/{neq_id}", [NeqReturnController::class, "getAllUnitNeq"]);
                });
            });
        });

        Route::prefix("indent")->group(function () {
            Route::post("/create", [IndentController::class, "createIndent"]);
            Route::get("/list", [IndentController::class, "getPaginate"]);
            Route::put("/update/{indent_id}", [IndentController::class, "updateIndent"]);
            Route::get("/detail/{indent_id}", [IndentController::class, "getDetailInden"]);
            Route::post("/payment/{indent_id}", [IndentController::class, "addPayment"]);
            Route::put("/status/{indent_id}", [IndentController::class, "updateStatusIndent"]);
            Route::prefix("payment")->group(function () {
                Route::put("/delete/{indent_payment_id}", [IndentController::class, "refundPayment"]);
                Route::put("/refund-all/{indent_id}", [IndentController::class, "refundAllPayment"]);
            });
            Route::put("/cancel/{indent_id}", [IndentController::class, "cancelIndent"]);
        });

        Route::prefix("indent-instansi")->group(function () {
            Route::get("/list", [IndentInstansiController::class, "getPaginate"]);
            Route::post("/create", [IndentInstansiController::class, "createIndentInstansi"]);
            Route::put("/update/{id}", [IndentInstansiController::class, "updateIndentInstansi"]);
            Route::put("/status/{id}", [IndentInstansiController::class, "updateStatus"]);
            Route::get("/detail/{id}", [IndentInstansiController::class, "getDetail"]);

            Route::post("/payment/{indent_instansi_id}", [IndentInstansiController::class, "addPayment"]);
            Route::put("/cancel/{id}", [IndentInstansiController::class, "cancelIndentInstansi"]);


            Route::prefix("payment")->group(function () {
                Route::delete("/delete/{id}", [IndentInstansiController::class, "deletePayment"]);
                Route::put("/refund-all/{id}", [IndentInstansiController::class, "refundAllPayment"]);
            });
        });

        Route::prefix("spk")->group(function () {

            Route::post("/create", [SPKController::class, "createSPK"]);
            Route::post("/update/{spk_id}", [SPKController::class, "updateSpk"]);
            Route::delete("/delete/{spk_id}", [SPKController::class, "deleteSPK"]);
            Route::get("/list", [SPKController::class, "getPaginateSpk"]);
            Route::get("/detail/{spk_id}", [SPKController::class, "getDetailSpk"]);
            Route::put("/status/{spk_id}", [SPKController::class, "updateStatusSpk"]);
            Route::post("/shipment/{spk_id}", [SPKController::class, "addShipment"]);
            Route::post("/cro/{spk_id}", [SPKController::class, "addCRO"]);

            Route::prefix("purchase-order")->group(function () {
                Route::post("/create/{id}", [SPKController::class, "createPurchaseOrder"]);
                Route::put("/create-act-tac/{id}", [SPKController::class, "updateActualPurchase"]);
                Route::delete("/delete/{id}", [SPKController::class, "deletePurchaseOrder"]);
            });

            Route::prefix("delete")->group(function () {
                Route::delete("/dcmt-another/{id}", [SPKController::class, "deleteFileDocumentAnother"]);
                Route::delete("/dcmt-file-sk/{id}", [SPKController::class, "deleteFileDocumentSK"]);
                Route::delete("/price-accessories/{id}", [SPKController::class, "deletePriceAccessories"]);
                // Route::delete("/ktp/{spk_additional_document_id}", [SPKController::class, "deleteFileDocumentKtp"]);
                // Route::delete("/kk/{spk_additional_document_id}", [SPKController::class, "deleteFileDocumentKK"]);
            });

            Route::prefix("excess-payment")->group(function () {
                Route::get("/list", [SPKController::class, "getpaginateExcessPayment"]);
                Route::get("/detail/{id}", [SPKController::class, "getDetailExcessPayment"]);
            });

            Route::prefix("payment")->group(function () {
                Route::get("/list", [SPKController::class, "getPaginateSpkPayment"]);
                Route::get("/detail/{spk_payment_id}", [SPKController::class, "getDetailSpkPayment"]);
                Route::post("/create/{spk_payment_id}", [SPKController::class, "addSpkPayment"]);
                Route::delete("/delete/{spk_payment_list_id}", [SPKController::class, "deletePayment"]);
                Route::put("/refund/{spk_payment_id}", [SPKController::class, "refundAllPaymentt"]);
                Route::put("/status/{spk_payment_id}", [SPKController::class, "updateStatusPaymentList"]);
            });
        });

        Route::prefix("retur-unit")->group(function () {
            Route::post("/create", [ReturUnitController::class, "createReturUnit"]);
            Route::get("/list", [ReturUnitController::class, "getPaginateReturUnit"]);
            Route::get("/detail/{retur_unit_id}", [ReturUnitController::class, "getDetailReturUnit"]);
            Route::put("/update/{retur_unit_id}", [ReturUnitController::class, "editReturUNit"]);
            Route::delete("/delete/{retur_unit_id}", [ReturUnitController::class, "deleteRetur"]);
            Route::put("/confirm/{retur_unit_id}", [ReturUnitController::class, "confirmStatusReturUnit"]);

            Route::prefix("unit-list")->group(function () {
                Route::delete("/delete/{retur_unit_list_id}", [ReturUnitController::class, "deleteReturUnitList"]);
            });
        });

        Route::prefix("po-instansi")->group(function () {
            Route::get("/list", [SpkInstansiController::class, "getPaginate"]);
            Route::get("/detail/{id}", [SpkInstansiController::class, "getDetail"]);
            Route::post("/create", [SpkInstansiController::class, "create"]);
            Route::post("/update/{id}", [SpkInstansiController::class, "update"]);
            Route::post("/add-motor/{id}", [SpkInstansiController::class, "addMotor"]);
            Route::put("/update-motor/{id}", [SpkInstansiController::class, "updateMotor"]);
            Route::delete("/delete-motor/{id}", [SpkInstansiController::class, "deleteMotor"]);
            Route::post("/add-unit/{id}", [SpkInstansiController::class, "addUnit"]);
            Route::put("/update-unit/{id}", [SpkInstansiController::class, "updateUnit"]);
            Route::delete("/delete-unit/{id}", [SpkInstansiController::class, "deleteUnit"]);
            Route::post("/add-additional/{id}", [SpkInstansiController::class, "addAdditionalNote"]);
            Route::put("/update-additional/{id}", [SpkInstansiController::class, "updateAdditional"]);
            Route::delete("/delete-additional/{id}", [SpkInstansiController::class, "deleteAdditional"]);
            Route::delete("/delete-additional-file/{id}", [SpkInstansiController::class, "deleteAdditionalFile"]);
            Route::post("/status/{id}", [SpkInstansiController::class, "updateStatus"]);
            Route::post("/publish/{id}", [SpkInstansiController::class, "terbitSpk"]);
            Route::post("/cancel/{id}", [SpkInstansiController::class, "updateStatusToCancel"]);
            Route::delete("/delete/{id}", [SpkInstansiController::class, "deleteSpkInstansi"]);
            Route::delete("/delete-delivery-file/{id}", [SpkInstansiController::class, "deleteDeliveryFile"]);
            Route::delete("/delete-delivery-unit-file/{id}", [SpkInstansiController::class, "deleteDeliveryUnitFile"]);

            Route::prefix("payment")->group(function () {
                Route::post("/add/{id}", [SpkInstansiController::class, "addSpkInstansiPayment"]);
                Route::delete("/delete/{id}", [SpkInstansiController::class, "deletePayment"]);
                Route::post("/refund/{id}", [SpkInstansiController::class, "refundAllPayment"]);
                Route::get("/list", [SpkInstansiController::class, "getPaginatePayment"]);
            });

            Route::prefix("unit")->group(function () {
                Route::post("/add-legal/{id}", [SpkInstansiController::class, "addUnitLegal"]);
                Route::post("/delivery", [SpkInstansiController::class, "addUnitDelivery"]);
            });
        });

        Route::prefix("spk-instansi")->group(function () {
            Route::get("/list", [SpkInstansiController::class, "getPaginateSpkInstansiUnit"]);
            Route::get("/detail/{id}", [SpkInstansiController::class, "getDetailSpkInstansiUnit"]);
        });
    });
});
