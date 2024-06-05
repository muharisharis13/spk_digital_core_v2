<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    //

    public function getPaginateRole(Request $request)
    {
        try {
            $limit = $request->input("limit", 5);
            $getPaginate = Role::latest()
                ->paginate($limit);

            return ResponseFormatter::success($getPaginate);
        } catch (\Throwable $e) {
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }

    public function createRole(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_name' => 'required|string',
            ]);
            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
            }

            DB::beginTransaction();

            $create = Role::create([
                "name" => $request->role_name,
                "guard" => "api"
            ]);

            DB::commit();


            return ResponseFormatter::success($create);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), "Internal Server", 500);
        }
    }
}
