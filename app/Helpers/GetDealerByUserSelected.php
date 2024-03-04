<?php

namespace App\Helpers;

use App\Models\DealerByUser;
use Illuminate\Support\Facades\Auth;

class GetDealerByUserSelected
{

    public static function GetUser($user_id)
    {
        return DealerByUser::where("user_id", $user_id)->where("isSelected", true)->with(["dealer"])->first();
    }
}
