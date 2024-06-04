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
            // DB::commit();

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
                return ResponseFormatter::error("The old password is incorrect", "Unauthorized", 401);
            }
            $getDetail->update([
                "password" => Hash::make($request->password),
                "password_reset_at" => null,
            ]);

            $getUser = User::findOrFail($user_id);
            return ResponseFormatter::success($getUser);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function getUserDetail(Request $request, $user_id)
    {
        try {

            $getDetail = User::with(['dealer_by_user_many.dealer'])->where("user_id", $user_id)
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

    public function updateStatus($user_id)
    {
        try {

            DB::beginTransaction();

            $getDetail = User::where("user_id", $user_id)->first();
            $status = $getDetail->status === 'unactive' ? "active" : "unactive";

            $getDetail->update([
                "status" => $status
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
            $allPermission = Permission::latest()->get();
            $groupedPermissions = [];

            // Function to convert permission name to a more human-readable format
            function convertToHumanReadable($string)
            {
                // Define mapping for word replacements
                $wordReplacements = [
                    'put' => 'Edit',
                    'post' => 'Create',
                    // Add more replacements as needed
                ];

                // Replace words based on mapping
                $string = str_replace(array_keys($wordReplacements), array_values($wordReplacements), $string);

                return ucwords(str_replace('_', ' ', $string));
            }

            // Function to check if a permission name contains a certain string
            function contain($string, $permissions)
            {
                foreach ($permissions as $permission) {
                    if (strpos($permission->name, $string) !== false) {
                        return convertToHumanReadable($permission->alias_name ?? $permission->name); // Return alias name if available, otherwise return permission name
                    }
                }
                return false;
            }

            foreach ($allPermission as $permission) {
                // Check if the permission name contains 'user'
                if ($aliasName = contain('user', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "User"
                    ];
                }
                // Check if the permission name contains 'pricelist'
                elseif ($aliasName = contain('pricelist', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Price List"
                    ];
                } elseif ($aliasName = contain('shipping_order', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Shipping Order"
                    ];
                } elseif ($aliasName = contain('unit', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Unit"
                    ];
                } elseif ($aliasName = contain('po_inst', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "PO Instansi"
                    ];
                } elseif ($aliasName = contain('spk_inst', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "SPK Instansi"
                    ];
                } elseif ($aliasName = contain('payment_po_inst', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Payment PO Instansi"
                    ];
                } elseif ($aliasName = contain('payment_spk', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Payment SPK"
                    ];
                } elseif ($aliasName = contain('excess_payment', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Overpayment SPK"
                    ];
                } elseif ($aliasName = contain('spk', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "SPK"
                    ];
                } elseif ($aliasName = contain('purchase_order_spk', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Purchase Order SPK"
                    ];
                } elseif ($aliasName = contain('delete_purchase_order', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Purchase Order SPK"
                    ];
                } elseif ($aliasName = contain('delete_dcmt_another', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "SPK"
                    ];
                } elseif ($aliasName = contain('delete_dcmt_file_sk', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "SPK"
                    ];
                } elseif ($aliasName = contain('delete_price_accessories', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "SPK"
                    ];
                } elseif ($aliasName = contain('retur_unit', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Return Unit"
                    ];
                } elseif ($aliasName = contain('delete_unit_list', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Return Unit"
                    ];
                } elseif ($aliasName = contain('master', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Master"
                    ];
                } elseif ($aliasName = contain('indent_inst', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Indent Instansi"
                    ];
                } elseif ($aliasName = contain('indent', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Indent Regular"
                    ];
                } elseif ($aliasName = contain('neq_return', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Return NEQ"
                    ];
                } elseif ($aliasName = contain('neq', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Transfer NEQ"
                    ];
                } elseif ($aliasName = contain('return_event', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Return Event"
                    ];
                } elseif ($aliasName = contain('event', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Transfer Event Event"
                    ];
                } elseif ($aliasName = contain('delivery', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Delivery"
                    ];
                } elseif ($aliasName = contain('repair_return', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Return Repair"
                    ];
                } elseif ($aliasName = contain('repair', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Repair"
                    ];
                } elseif ($aliasName = contain('reapair', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Repair"
                    ];
                } elseif ($aliasName = contain('post_sync_data', [$permission])) {
                    $groupedPermissions[] = [
                        'name' => $permission->name,
                        'alias_name' => $aliasName, // Use alias name if available
                        'group_name' => "Shipping Order"
                    ];
                }
                // Add other conditions for different groupings if needed
            }

            return ResponseFormatter::success($groupedPermissions);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }



    public function getPermissionUser(Request $request, $user_id)
    {
        try {

            $getPermissionUser = User::findOrFail($user_id)->with("permissions")->first();


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
                "permission_name" => "required",
                "user_id" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }


            $user_id = $request->user_id;

            $getUser = User::where("user_id", $user_id)->first();

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
