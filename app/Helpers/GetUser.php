<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class GetUser
{

  public static function GetUser()
  {
    return Auth::user();
  }
}
