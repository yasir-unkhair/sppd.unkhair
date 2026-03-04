<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ambil api key dari header
        $apiKey = $request->header('X-API-KEY');
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. API Key tidak valid!'
            ], 401);
        }

        $validKey = env('API_KEY');
        if ($apiKey !== $validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. API Key tidak valid!'
            ], 401);
        }

        return $next($request);
    }
}
