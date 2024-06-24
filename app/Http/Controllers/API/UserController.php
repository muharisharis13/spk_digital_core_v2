<?php

namespace App\Http\Controllers\API;

use App\Enums\UsersStatusEnum;
use App\Helpers\GetDealerByUserSelected;
use App\Helpers\ResponseFormatter;
use App\Helpers\ValidatorFailed;
use App\Http\Controllers\Controller;
use App\Models\ApiSecret;
use App\Models\DealerByUser;
use App\Models\ModelHasPermission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

            $user_id = $request->user_id;

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }
            $getDetail = User::where("user_id", $user_id)
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
                "roles" => "required",
                "dealer_users" => "required|array",
                "dealer_users.*" => "required"
            ]);

            ValidatorFailed::validatorFailed($validator);
            DB::beginTransaction();

            $updateUser = User::where("user_id", $user_id)->first();

            $updateUser->update([
                "roles" => $request->roles
            ]);

            if ($request->has('dealer_users')) {
                $dealer_users = $request->input("dealer_users");
                $existingDealerUsers = DealerByUser::where("user_id", $user_id)->get();
                $existingDealerUserId = $existingDealerUsers->pluck('dealer_by_user_id')->toArray();
                $dealerByUserIdToKeep = [];

                foreach ($dealer_users as $key => $dealer) {
                    $isSelected = $key === 0 ? true : false;
                    if (!isset($dealer['dealer_by_user_id'])) {
                        $newDealerByUser =  DealerByUser::create([
                            'dealer_id' => $dealer['dealer_id'],
                            'user_id' => $user_id,
                            'isSelected' => $isSelected
                        ]);
                        $dealerByUserIdToKeep[] = $newDealerByUser->dealer_by_user_id;
                    } else {
                        $dealerByUser = DealerByUser::findOrFail($dealer['dealer_by_user_id']);
                        $dealerByUser->update([
                            'dealer_id' => $dealer['dealer_id'],
                            'isSelected' => $isSelected
                        ]);
                        $dealerByUserIdToKeep[] = $dealerByUser->dealer_by_user_id;
                    }
                }

                $deleteDealerByUserId = array_diff(
                    $existingDealerUserId,
                    $dealerByUserIdToKeep
                );
                DealerByUser::whereIn('dealer_by_user_id', $deleteDealerByUserId)->delete();
            }


            $user = User::with(['dealer_by_user_many', 'dealer_by_user_many.dealer'])->findOrFail($updateUser->user_id);

            $data = [
                "user" => $user
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
                // "password" => "required",
                "role" => "required",
                // "permission" => "array",
                // "permission.*" => "required",
                "dealer_users" => "required|array",
                "dealer_users.*" => "required"
            ]);

            // ValidatorFailed::validatorFailed($validator);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }
            DB::beginTransaction();

            $createUser = User::create([
                "username" => $request->username,
                "password" => Hash::make($request->password ?? 'password'),
                "user_status" => UsersStatusEnum::ACTIVE,
                "roles" => $request->role,
                "password_reset_at" => Carbon::now()
            ]);


            $permissions = Permission::all();

            foreach ($permissions as $item) {
                $createUser->givePermissionTo($item);
            }
            $dealer_users = $request->dealer_users;

            foreach ($dealer_users as $key => $dealer) {
                $isSelected = $key === 0 ? true : false;
                DealerByUser::create([
                    "dealer_id" => $dealer['dealer_id'],
                    "user_id" => $createUser->user_id,
                    "isSelected" => $isSelected,
                ]);
            }
            $user = User::with(['dealer_by_user', 'dealer_by_user.dealer'])->findOrFail($createUser->user_id);

            $data = [
                "user" => $user
            ];
            DB::commit();

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function resetPassword($user_id)
    {

        try {

            $getDetail = User::where("user_id", $user_id)
                ->first();
            $getDetail->update([
                "password_reset_at" => Carbon::now(),
                "password" => Hash::make('password')
            ]);
            $getUser = User::findOrFail($user_id);
            return ResponseFormatter::success($getUser);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function updatePassword(Request $request, $user_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed'
            ]);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }
            $getDetail = User::where("user_id", $user_id)
                ->first();
            if (!$getDetail) {
                return ResponseFormatter::error("User not found", "Not Found", 404);
            }
            if (!Hash::check($request->old_password, $getDetail->password)) {
                return ResponseFormatter::error("The old password is incorrect", "Unauthorized", 412);
            }
            $getDetail->update([
                "password" => Hash::make($request->password),
                "password_reset_at" => null,
            ]);

            $user = User::where("user_id", $user_id)
                ->with(["dealer_by_user.dealer", "dealer_by_user" => function ($query) {
                    $query->where("isSelected", true);
                }])
                ->first();
            $data = [
                "user" => $user
            ];
            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getUserDetail(Request $request, $user_id)
    {
        try {

            $getDetail = User::with(['dealer_by_user_many.dealer', 'permissions'])->where("user_id", $user_id)
                ->first();

            $apiSecret = ApiSecret::first();

            if ($getDetail) {
                $getDetail->api_secret = $apiSecret;
            }

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

    public function updateStatus($user_id)
    {
        try {

            DB::beginTransaction();

            $getDetail = User::where("user_id", $user_id)->first();
            $status = $getDetail->status === 'unactive' ? "active" : "unactive";

            $getDetail->update([
                "status" => $status
            ]);
            $getDetail->tokens()->delete();


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
            $allPermission = Permission::latest()->get();



            return ResponseFormatter::success($allPermission);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server Error", 500);
        }
    }







    public function getPermissionUser(Request $request)
    {
        try {
            $user = Auth::user();

            $getPermissionUser = User::findOrFail($user->user_id)->with("permissions")->first();


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

    // public function assignPermission(Request $request)
    // {
    //     try {
    //         $validator  = Validator::make($request->all(), [
    //             "permission_name" => "required",
    //             "user_id" => "required"
    //         ]);

    //         if ($validator->fails()) {
    //             return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
    //         }


    //         $user_id = $request->user_id;

    //         $getUser = User::where("user_id", $user_id)->first();

    //         if ($getUser) {
    //             $getUser->givePermissionTo($request->permission_name);
    //         }

    //         return ResponseFormatter::success("Permission assigned successfully");
    //     } catch (\Throwable $e) {
    //         return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
    //     }
    // }
    public function assignPermission(Request $request)
    {
        try {
            $validator  = Validator::make($request->all(), [
                "permission" => "required",
                "user_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            $user_id = $request->user_id;

            $getUser = User::where("user_id", $user_id)->first();

            if ($getUser) {
                if ($request->permission) {

                    $getUser->syncPermissions($request->permission);
                } else {
                    return
                        ResponseFormatter::error("Permission assigned something error", "Bad request", 400);
                }
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
    public function selectDealerByUser2(Request $request, $dealer_by_user_id)
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

            // Unselect any previously selected dealer for the user
            DealerByUser::where("user_id", $user->user_id)
                ->update(['isSelected' => false]);

            // Select the new dealer
            $getDealer = DealerByUser::with(['dealer.dealer_logo'])
                ->where('dealer_id', $request->dealer_id)
                ->where("user_id", $user->user_id)
                ->first();

            if ($getDealer) {
                $getDealer->update(['isSelected' => true]);
            } else {
                return ResponseFormatter::error('Dealer by User not found', "Bad Request", 400);
            }

            DB::commit();
            return ResponseFormatter::success($getDealer);
        } catch (\Throwable $e) {
            DB::rollback();
            return ResponseFormatter::error($e->getMessage(), "Internal Server Error", 500);
        }
    }
}
