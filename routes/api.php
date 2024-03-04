<?php

use App\Http\Controllers\API\AuthenticationController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
    });
});
