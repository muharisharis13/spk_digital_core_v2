<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        // Periksa apakah pengguna terotentikasi dan memiliki salah satu permission yang diperlukan
        if (!$request->user() || !$request->user()->hasAnyPermission($permissions)) {
            // abort(403, 'Unauthorized');
            return ResponseFormatter::error("Kamu tidak memiliki hak akses api ini", "Unauthorized", 403);
        }

        return $next($request);
    }
}
