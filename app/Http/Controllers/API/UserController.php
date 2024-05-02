<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\DealerByUser;
use App\Models\ModelHasPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    //

    public function getPermissionAttribute()
    {
        try {
            $allPermission =  Permission::latest()->get();

            return ResponseFormatter::success($allPermission);
        } catch (\Throwable $e) {

            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }


    public function getPermissionUser(Request $request)
    {
        try {
            $getPermissionUser = User::with("permissions")->latest()->get();


            return ResponseFormatter::success($getPermissionUser);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user(); // Mengambil user yang sedang login
            $user->tokens()->delete(); // Menghapus semua token yang terkait dengan user yang sedang login

            return ResponseFormatter::success($user, "Successfully logout");
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }

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

    public function selectDealerByUser(Request $request, $dealer_by_user_id)
    {
        try {

            $validator = Validator::make($request->all(), [
                "dealer_id" => "required",
                "user_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }
            $user = Auth::user();

            DB::beginTransaction();

            $getPreviousDealerByUser = DealerByUser::where("dealer_by_user_id", $dealer_by_user_id)->first();
            $getPreviousDealerByUser->update([
                'isSelected' => false
            ]);

            $getDealer = DealerByUser::with(['dealer'])->where('dealer_id', $request->dealer_id)->where("user_id", $user->user_id)->first();
            if (isset($getDealer)) {
                $getDealer->update(([
                    'isSelected' => true
                ]));
            } else {
                return ResponseFormatter::error('Dealer by User not found', "Bad Request", 400);
            }
            DB::commit();
            return ResponseFormatter::success($getDealer);
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
}
