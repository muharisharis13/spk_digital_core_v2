<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    //

    public function indentInstansi()
    {
        return view("detail.indent_instansi");
    }

    public function instansi()
    {
        return view("detail.instansi");
    }

    public function instansiPayment()
    {
        return view("detail.instansi_payment");
    }
    public function payment()
    {
        return view("detail.payment");
    }
}
