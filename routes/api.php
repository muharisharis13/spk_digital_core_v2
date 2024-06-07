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
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\ShippingOrderController;
use App\Http\Controllers\API\ShippingOrderController2;
use App\Http\Controllers\API\SPKController;
use App\Http\Controllers\API\SpkInstansiController;
use App\Http\Controllers\API\SyncController;
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

    Route::prefix("sync-data")->group(function () {
        Route::post("/sync", [SyncController::class, "syncData"]);
        Route::get("/check-dealer", [SyncController::class, "checkDealer"]);
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
            Route::post("/remove-permission", [UserController::class, "removePermission"]);
            Route::get("/user-permission", [UserController::class, "getPermissionUser"])->middleware('permission:get_permission_user');
            Route::get("/current-dealer", [UserController::class, "getCurrentDealer"])->middleware('permission:get_current_dealer_user');
            Route::post("/logout", [UserController::class, "logout"]);
            Route::put("/select-dealer/{dealer_by_user_id}", [UserController::class, "selectDealerByUser"]);
            Route::get("/role", [UserController::class, "getRoles"])->middleware("permission:get_role_user");
            Route::put("/status/{id}", [UserController::class, "updateStatus"])->middleware("permission:put_status_user");
            Route::get("/list", [UserController::class, "getUserList"])->middleware("permission:get_user");
            Route::get("/detail/{id}", [UserController::class, "getUserDetail"])->middleware("permission:get_detail_user");
            Route::post("/create", [UserController::class, "createuser"])->middleware("permission:post_user");
            Route::put("/update/{id}", [UserController::class, "updateUser"])->middleware("permission:put_user");
            Route::put("/update-password/{id}", [UserController::class, "updatePassword"]);
            Route::put("/reset-password/{id}", [UserController::class, "resetPassword"]);
        });

        Route::prefix("shipping-order")->group(function () {
            // Route::post("/sync-data/{city}", [ShippingOrderController::class, "sycnShippingOrder"]);
            Route::post("/sync-data2", [ShippingOrderController2::class, "syncShippingOrderMD"])
                ->middleware('permission:post_sync_data');
            Route::get("/list", [ShippingOrderController::class, "getListShippingOrder"])
                ->middleware('permission:read_shipping_order');
            Route::get("/detail/{shipping_order_id}", [ShippingOrderController::class, "getDetailShippingOrder"])->middleware('permission:read_shipping_order_detail');
        });

        Route::prefix("unit")->group(function () {
            Route::put("/status/{unit_id}", [ShippingOrderController::class, "updateTerimaUnitShippingOrder"])
                ->middleware('permission:read_unit_detail');
            Route::get("/list", [UnitContoller::class, "getListPaginateUnit"])
                ->middleware('permission:read_unit');
            Route::get("/detail/{unit_id}", [UnitContoller::class, "getDetailUnit"])->middleware('permission:read_unit_detail');

            Route::prefix("pricelist")->group(function () {
                Route::post("/create", [UnitContoller::class, "addPrice"]);
                Route::get("/list", [UnitContoller::class, "getListPriceList"])->middleware('permission:get_pricelist');
                Route::get("/detail/{id}", [UnitContoller::class, "getDetailPriceList"])->middleware('permission:get_detail_pricelist');
                Route::post("/clone", [UnitContoller::class, "clonePriceList"]);
                Route::put("/update/{id}", [UnitContoller::class, "updatePrice"]);
            });
        });

        Route::prefix("master")->group(function () {
            Route::get("/motor", [Master::class, "getListPaginateMotor"])->middleware('permission:read_motor_master');
            Route::get("/dealer-neq", [Master::class, "getListDealerNeq"])->middleware('permission:read_dealer_neq_master');
            Route::get("/mds", [Master::class, "getListDealerMDS"])->middleware('permission:read_mds_master');
            Route::get("/dealer-selected", [Master::class, "getListDealerSelected"]);
            Route::get("/location-current", [Master::class, "getListLocationByUserLogin"])
                ->middleware('permission:read_location_current_master');
            Route::get("/main-dealer", [Master::class, "GelistPaginateMainDealer"])
                ->middleware('permission:read_main_dealer_master');

            Route::prefix("event")->group(function () {
                Route::post("/create", [Master::class, "createEvent"])
                    ->middleware('permission:post_event_create_master');
                Route::get("/detail/{master_event_id}", [Master::class, "getDetailMasterEvent"])
                    ->middleware('permission:read_event_detail_master');
                Route::put("/update/{master_event_id}", [Master::class, "updateEvent"])
                    ->middleware('permission:update_event_master');
                Route::put("/status/{master_event_id}", [Master::class, "updateStatusEvent"])
                    ->middleware('permission:update_event_status_master');
                Route::get("/list", [Master::class, "getEventPaginate"])
                    ->middleware('permission:read_event_master');
            });

            Route::prefix("sales")->group(function () {
                Route::get("/list", [Master::class, "getSales"])->middleware('permission:get_sales_master');
            });
            Route::prefix("microfinance")->group(function () {
                Route::get("/list", [Master::class, "getMicrofinance"])->middleware('permission:get_miscrofinance_master');
            });
            Route::prefix("leasing")->group(function () {
                Route::get("/list", [Master::class, "getLeasing"])->middleware('permission:get_leasing_master');
            });
            Route::prefix("color")->group(function () {
                Route::get("/list", [Master::class, "getColor"])->middleware('permission:get_color_master');
            });
            Route::prefix("bank")->group(function () {
                Route::get("/list", [Master::class, "getBank"])->middleware('permission:get_bank_master');
                Route::post("/create", [Master::class, "createBank"])->middleware('permission:post_bank_master');
                Route::delete("/delete/{id}", [Master::class, "deleteBank"])->middleware('permission:delete_bank_master');
            });
            Route::prefix("marital")->group(function () {
                Route::get("/list", [Master::class, "getListMaritalStatus"])->middleware('permission:get_marital_master');
            });
            Route::prefix("hobby")->group(function () {
                Route::get("/list", [Master::class, "getListHobby"])->middleware('permission:get_hobby_master');
            });
            Route::prefix("tenor")->group(function () {
                Route::get("/list", [Master::class, "getListTenor"])->middleware('permission:get_tenor_master');
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
                Route::get("/list", [Master::class, "getListMotorBrand"])->middleware('permission:get_motor_brand_master');
            });
            Route::prefix("motor")->group(function () {
                Route::get("/detail/{id}", [Master::class, "getDetailMotor"])->middleware('permission:get_motor_detail_master');
            });
        });

        Route::prefix("repair")->group(function () {
            Route::post("/create", [RepairController::class, "createRepair"])
                ->middleware('permission:post_repair_create');
            Route::put("/update/{repair_id}", [RepairController::class, "updateRepair"])->middleware('permission:update_repair');
            Route::put("/update/status/{repair_id}", [RepairController::class, "updateStatusRepair"])->middleware('permission:update_repair_status');
            Route::delete("/delete/{repair_id}", [RepairController::class, "deleteRepair"])->middleware('permission:delete_reapair');
            Route::delete("/unit/delete/{repair_unit_id}", [RepairController::class, "deleteRepairUnit"])->middleware('permission:delete_repair_unit');
            Route::get("/detail/{repair_id}", [RepairController::class, "getDetailRepair"])->middleware('permission:read_repair_detail');
            Route::get("/list", [RepairController::class, "getPaginateRepairUnit"])->middleware('permission:read_repair');

            Route::prefix("return")->group(function () {
                Route::get("/list", [RepairReturnController::class, "getPaginateRepairReturn"])->middleware('permission:read_repair');
                Route::post("/create", [RepairReturnController::class, "createRepairReturn"])->middleware('permission:post_repair_return_create');
                Route::put("/update/{repair_return_id}", [RepairReturnController::class, "updateRepairReturn"])->middleware('permission:update_repair_return');
                Route::put("/status/{repair_return_id}", [RepairReturnController::class, "updateStatusRepairReturn"])->middleware('permission:update_repair_return_status');
                Route::get("/detail/{repair_return_id}", [RepairReturnController::class, "getDetailRepairReturn"])->middleware('permission:read_repair_return_detail');
                Route::delete("/delete/unit/{repair_return_unit_id}", [RepairReturnController::class, "deleteRepairReturnUnit"])
                    ->middleware("permission:delete_repair_return_unit");
                Route::delete("/delete/{repair_return_id}", [RepairReturnController::class, "deleteRepairReturn"])->middleware('permission:delete_repair_return');
                Route::prefix("repair-unit")->group(function () {
                    Route::get("/list", [RepairReturnController::class, "getRepairUnit"])->middleware('permission:read_retur_unit');
                });
            });
        });

        Route::prefix("delivery")->group(function () {
            Route::post("/create", [DeliveryController::class, "CreateDelivery"])->middleware('permission:post_delivery');
            Route::get("/list", [DeliveryController::class, "GetListPagianteDelivery"])->middleware('permission:get_delivery');
            Route::get("/detail/{delivery_id}", [DeliveryController::class, "DetailDelivery"])->middleware('permission:get_detail_delivery');
            Route::put("/status/{delivery_id}", [DeliveryController::class, "changeStatusDelivery"])->middleware('permission:put_status_delivery');
            Route::put("/update/{delivery_id}", [DeliveryController::class, "updateDelivery"])->middleware('permission:put_delivery');
            Route::delete("/delete/{delivery_id}", [DeliveryController::class, "deleteDelivery"])->middleware('permission:delete_delivery');
        });

        Route::prefix("event")->group(function () {
            Route::post("/create", [EventController::class, "createEvent"])->middleware('permission:post_event');
            Route::put("/update/{event_id}", [EventController::class, "updateEvent"])->middleware('permission:put_event');
            Route::get("/list", [EventController::class, "getPaginateEvent"])->middleware('permission:read_event');
            Route::get("/detail/{event_id}", [EventController::class, "getDetailEvent"])->middleware('permission:read_event_detail');
            Route::put("/status/{event_id}", [EventController::class, "updateStatusEvent"])->middleware('permission:put_status_event');
            Route::delete("/unit/delete/{event_list_unit_id}", [EventController::class, "deleteUnitEvent"])->middleware('permission:delete_unit_event');
            Route::delete("/delete/{event_id}", [EventController::class, "deleteEvent"])->middleware('permission:delete_event');


            Route::prefix("return")->group(function () {
                Route::post("/create", [EventReturnController::class, "createEventReturn"])->middleware('permission:post_return_event');
                Route::get("/list", [EventReturnController::class, "getPaginateEventReturn"])->middleware('permission:get_return_event');
                Route::get("/detail/{event_return_id}", [EventReturnController::class, "getDetailEventReturn"])->middleware('permission:get_detail_return_event');
                Route::put("/status/{event_return_id}", [EventReturnController::class, "updateStatusEventReturn"])->middleware('permission:put_status_return_event');
                Route::put("/update/{event_return_id}", [EventReturnController::class, "updateEventReturn"])->middleware('permission:put_return_event');
                Route::delete("/delete/{event_return_id}", [EventReturnController::class, "deleteEventReturn"])->middleware('permission:delete_return_event');
                Route::delete("/unit/delete/{event_return_list_unit_id}", [EventReturnController::class, "deleteEventReturnUnit"])->middleware('permission:delete_unit_return_event');
                Route::prefix("event-unit")->group(function () {
                    Route::get("/list/{master_event_id}", [EventReturnController::class, "getAllUnitEvent"])->middleware('permission:get_unit_return_event');
                });
            });
        });

        Route::prefix("neq")->group(function () {
            Route::post("/create", [NeqController::class, "createNeq"])->middleware('permission:post_new');
            Route::get("/list", [NeqController::class, "getPaginateNeq"])->middleware('permission:read_neq');
            Route::get("/detail/{neq_id}", [NeqController::class, "getDetailNeq"])->middleware('permission:read_neq_detail');
            Route::put("/status/{neq_id}", [NeqController::class, "updateStatusNeq"])->middleware('permission:put_status_neq');
            Route::put("/update/{neq_id}", [NeqController::class, "updateNeq"])->middleware('permission:put_neq');
            Route::delete("/delete/{neq_id}", [NeqController::class, "deleteNeq"])->middleware('permission:delete_neq');
            Route::prefix("unit")->group(function () {
                Route::delete("/delete/{neq_unit_id}", [NeqController::class, "deleteUnitNeq"])->middleware('permission:delete_unit_neq');
            });

            Route::prefix("return")->group(function () {
                Route::post("/create", [NeqReturnController::class, "createNeqReturn"])->middleware('permission:post_neq_return');
                Route::put("/update/{neq_return_id}", [NeqReturnController::class, "updateNeqReturn"])->middleware('permission:put_neq_return');
                Route::get("/list", [NeqReturnController::class, "getPaginateNeqReturn"])->middleware('permission:get_neq_return');
                Route::get("/detail/{neq_return_id}", [NeqReturnController::class, "getDetailNeqReturn"])->middleware('permission:get_neq_detail_return');
                Route::delete("/delete/{neq_return_id}", [NeqReturnController::class, "deleteNeqReturn"])->middleware('permission:delete_neq_return');
                Route::put("/status/{neq_return_id}", [NeqReturnController::class, "updateStatusNeqReturn"])->middleware('permission:put_status_neq_return');
                Route::prefix("neq-unit")->group(function () {
                    Route::delete("/delete/{neq_return_unit_id}", [NeqReturnController::class, "deleteNeqReturnUnit"])->middleware('permission:delete_unit_neq_return');
                    Route::get("/list/{neq_id}", [NeqReturnController::class, "getAllUnitNeq"])->middleware('permission:get_unit_neq_return');
                });
            });
        });

        Route::prefix("indent")->group(function () {
            Route::post("/create", [IndentController::class, "createIndent"])->middleware('permission:post_indent');
            Route::get("/list", [IndentController::class, "getPaginate"])->middleware('permission:read_indent');
            Route::put("/update/{indent_id}", [IndentController::class, "updateIndent"])->middleware('permission:put_indent');
            Route::get("/detail/{indent_id}", [IndentController::class, "getDetailInden"])->middleware('permission:read_indent_detail');
            Route::post("/payment/{indent_id}", [IndentController::class, "addPayment"])->middleware('permission:post_payment_indent');
            Route::put("/status/{indent_id}", [IndentController::class, "updateStatusIndent"])->middleware('permission:pit_status_indent');
            Route::prefix("payment")->group(function () {
                Route::put("/delete/{indent_payment_id}", [IndentController::class, "refundPayment"])->middleware('permission:delete_payment_indent');
                Route::put("/refund-all/{indent_id}", [IndentController::class, "refundAllPayment"])->middleware('permission:put_refund_all_payment_indent');
            });
            Route::put("/cancel/{indent_id}", [IndentController::class, "cancelIndent"])->middleware('permission:put_cancel_payment_indent');
        });

        Route::prefix("indent-instansi")->group(function () {
            Route::get("/list", [IndentInstansiController::class, "getPaginate"])->middleware('permission:get_indent_inst');
            Route::post("/create", [IndentInstansiController::class, "createIndentInstansi"])->middleware('permission:post_indent_inst');
            Route::put("/update/{id}", [IndentInstansiController::class, "updateIndentInstansi"]);
            Route::put("/status/{id}", [IndentInstansiController::class, "updateStatus"]);
            Route::get("/detail/{id}", [IndentInstansiController::class, "getDetail"])->middleware('permission:get_detail_indent_inst');

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
            Route::get("/list", [SPKController::class, "getPaginateSpk"])->middleware('permission:read_spk');
            Route::get("/detail/{spk_id}", [SPKController::class, "getDetailSpk"])->middleware('permission:read_spk_detail');
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
                Route::get("/list", [SPKController::class, "getpaginateExcessPayment"])->middleware('permission:get_excess_payment');
                Route::get("/detail/{id}", [SPKController::class, "getDetailExcessPayment"])->middleware('permission:get_detail_excess_paymnent');
            });

            Route::prefix("payment")->group(function () {
                Route::get("/list", [SPKController::class, "getPaginateSpkPayment"])->middleware('permission:get_payment_spk');
                Route::get("/detail/{spk_payment_id}", [SPKController::class, "getDetailSpkPayment"])->middleware('permission:get_detail_payment_spk');
                Route::post("/create/{spk_payment_id}", [SPKController::class, "addSpkPayment"]);
                Route::delete("/delete/{spk_payment_list_id}", [SPKController::class, "deletePayment"]);
                Route::put("/refund/{spk_payment_id}", [SPKController::class, "refundAllPaymentt"]);
                Route::put("/status/{spk_payment_id}", [SPKController::class, "updateStatusPaymentList"]);
            });
        });

        Route::prefix("retur-unit")->group(function () {
            Route::post("/create", [ReturUnitController::class, "createReturUnit"]);
            Route::get("/list", [ReturUnitController::class, "getPaginateReturUnit"])->middleware('permission:read_retur_unit');
            Route::get("/detail/{retur_unit_id}", [ReturUnitController::class, "getDetailReturUnit"])->middleware('permission:read_retur_unit_detail');
            Route::put("/update/{retur_unit_id}", [ReturUnitController::class, "editReturUNit"]);
            Route::delete("/delete/{retur_unit_id}", [ReturUnitController::class, "deleteRetur"]);
            Route::put("/confirm/{retur_unit_id}", [ReturUnitController::class, "confirmStatusReturUnit"]);

            Route::prefix("unit-list")->group(function () {
                Route::delete("/delete/{retur_unit_list_id}", [ReturUnitController::class, "deleteReturUnitList"]);
            });
        });

        Route::prefix("po-instansi")->group(function () {
            Route::get("/list", [SpkInstansiController::class, "getPaginate"])->middleware('permission:get_po_inst');
            Route::get("/detail/{id}", [SpkInstansiController::class, "getDetail"])->middleware('permission:get_detail_po_inst');
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
                Route::get("/detail/{id}", [SpkInstansiController::class, "detailPayment"])->middleware('permission:get_detail_payment_po_inst');
                Route::post("/refund/{id}", [SpkInstansiController::class, "refundAllPayment"]);
                Route::get("/list", [SpkInstansiController::class, "getPaginatePayment"])->middleware('permission:get_payment_po_inst');
            });

            Route::prefix("unit")->group(function () {
                Route::post("/add-legal/{id}", [SpkInstansiController::class, "addUnitLegal"]);
                Route::post("/delivery", [SpkInstansiController::class, "addUnitDelivery"]);
            });
        });


        Route::prefix("spk-instansi")->group(function () {
            Route::get("/list", [SpkInstansiController::class, "getPaginateSpkInstansiUnit"])->middleware('permission:get_spk_inst');
            Route::get("/detail/{id}", [SpkInstansiController::class, "getDetailSpkInstansiUnit"])->middleware('permission:get_detail_spk_inst');
        });

        Route::prefix("role")->group(function () {
            Route::post("/create", [RoleController::class, "createRole"]);
            Route::get("/list", [RoleController::class, "getPaginateRole"]);
        });
    });
});
