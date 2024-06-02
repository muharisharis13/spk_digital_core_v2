<?php

namespace App\Http\Controllers\API;

use App\Enums\UsersStatusEnum;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Helpers\ValidatorFailed;
use App\Http\Controllers\Controller;
use App\Models\DealerByUser;
use App\Models\ModelHasPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //

    public function removePermission(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "permission_name" => "required",
            ]);

            $user = Auth::user();

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }
            $getDetail = User::where("user_id", $user->user_id)
                ->first();

            // $getDetail->removePermission($request->permission_name);
            $getDetail->revokePermissionTo($request->permission_name);

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function updateUser(Request $request, $user_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roles" => "required"
            ]);

            ValidatorFailed::validatorFailed($validator);
            DB::beginTransaction();

            $updateUser = User::where("user_id", $user_id)->first();

            $updateUser->update([
                "roles" => $request->roles
            ]);


            $data = [
                "user" => $updateUser
            ];
            DB::commit();

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function createuser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "username" => "required|regex:/^(?!_)(?!.*?_$)[a-zA-Z0-9_]{3,20}$/",
                "password" => "required",
                "roles" => "required",
                "permission" => "array",
                "permission.*" => "required"
            ]);

            ValidatorFailed::validatorFailed($validator);
            DB::beginTransaction();

            $createUser = User::create([
                "username" => $request->username,
                "password" => Hash::make($request->password),
                "user_status" => UsersStatusEnum::ACTIVE,
                "roles" => $request->roles
            ]);

            foreach ($request->permission as $item) {
                $createUser->givePermissionTo($item);
            }


            $data = [
                "user" => $createUser
            ];
            DB::commit();

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getUserDetail(Request $request, $user_id)
    {
        try {

            $getDetail = User::where("user_id", $user_id)
                ->first();

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
    public function getUserList(Request $request)
    {
        try {

            $limit = $request->input("limit", 5);
            $getPaginate = User::latest()
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function updateStatus(Request $request, $user_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "status" => "in:active,unactive",
            ]);


            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $getDetail = User::where("user_id", $user_id)->first();

            $getDetail->update([
                "status" => $request->status
            ]);

            DB::commit();

            return ResponseFormatter::success($getDetail);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getRoles(Request $request)
    {
        try {

            $getRoles = Role::all();

            return ResponseFormatter::success($getRoles);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getCurrentDealer(Request $request)
    {
        try {
            $user = Auth::user();

            $getDealerByUserSelected = GetDealerByUserSelected::GetUser($user->user_id);

            return ResponseFormatter::success($getDealerByUserSelected);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

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
