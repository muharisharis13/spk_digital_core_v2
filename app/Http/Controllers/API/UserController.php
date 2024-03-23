<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //

    public function assignPermission(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "permission_name" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            $user = Auth::user();

            $getUser = User::where("user_id", $user->user_id)->first();

            if ($getUser) {
                $getUser->givePermissionTo($request->permission_name);
            }

            return ResponseFormatter::success("Permission assigned successfully");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
}
