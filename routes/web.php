<?php

use App\Http\Controllers\API\ExportPDFController;
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

Route::get("/detail/indent", function () {
    return view("detail.indent");
});
