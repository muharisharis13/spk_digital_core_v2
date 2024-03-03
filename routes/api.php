<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\ShippingOrderController;
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

Route::middleware(["auth:api"])->prefix("v1")->group(function () {
    Route::prefix("authentication")->group(function () {
        Route::post("/login", [AuthenticationController::class, "login"]);
        Route::post("/register", [AuthenticationController::class, "register"]);
    });

    Route::prefix("shipping-order")->group(function(){
        Route::post("/sync-data/{city}", [ShippingOrderController::class, "sycnShippingOrder"]);
    });
});
