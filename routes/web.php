<?php

use App\Http\Controllers\API\ExportPDFController;
use App\Http\Controllers\WEB\DetailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/faktur', function () {
    return view('pdf.faktur.faktur_payment_indent2');
});
Route::get('/surat_jalan', function () {
    return view('pdf.surat_jalan.surat_jalan');
});

Route::get("/detail/indent/{id}", [DetailController::class, "indent"]);
Route::get("/detail/indent_instansi/{id}", [DetailController::class, "indentInstansi"]);
Route::get("/detail/instansi", [DetailController::class, "instansi"]);
Route::get("/detail/instansi_payment/{id}", [DetailController::class, "instansiPayment"]);
Route::get("/detail/payment/{id}", [DetailController::class, "payment"]);
