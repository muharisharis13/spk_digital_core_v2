<?php

namespace App\Http\Controllers\API;

use App\Enums\UsersStatusEnum;
use App\Helpers\ResponseFormatter;
use App\Helpers\ValidatorFailed;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', "register"]]);
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "username" => "required|regex:/^(?!_)(?!.*?_$)[a-zA-Z0-9_]{3,20}$/",
                "password" => "required"
            ]);

            ValidatorFailed::validatorFailed($validator);
            DB::beginTransaction();

            $createUser = User::create([
                "username" => $request->username,
                "password" => Hash::make($request->password),
                "user_status" => UsersStatusEnum::ACTIVE
            ]);


            $data = [
                "user" => $createUser
            ];
            DB::commit();

            return ResponseFormatter::success($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "internal server", 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "username" => "required",
                "password" => "required"
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "authentication failed", 401);
            }



            $user = User::where("username", $request->get("username"))
                ->with("dealer_by_user.dealer")
                ->first();

            if (!Hash::check($request->get("password"), $user->password, [])) {
                return ResponseFormatter::error("Invalid Password", "Authentication Failed", 401);
            }
            // if (!Hash::check($request->get("password"), $user->password, [])) {
            //     throw new \Exception("Invalid Password");
            // }


            $tokenResult = $user->createToken("authToken")->plainTextToken;

            $data = [
                "token" => $tokenResult,
                "token_type" => "Bearer",
                "user" => $user
            ];

            return  ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), "internal server", 500);
        }
    }
}
