<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use App\Models\ApiSecret;
use App\Models\Dealer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkApiKeyMD
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('DEALER-API-KEY');
        $dealerCode = $request->header('DEALER-CODE');

        $getApiKey = ApiSecret::where("api_secret_key", $apiKey)->first();
        $getDealer = Dealer::where("dealer_code", $dealerCode)->first();

        // Memeriksa apakah API key ada dan sesuai dengan yang diharapkan
        if (isset($getApiKey->api_key_secret) && isset($getDealer->dealer_id)) {
            // Jika API key valid, lanjutkan permintaan
            return $next($request);
        } else {
            return ResponseFormatter::error("invalid api key", "Unauthorized", 401);
        }
    }
}
