<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInactiveToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {

            // Token expire ho gaya?
            if ($token->expires_at && now()->greaterThan($token->expires_at)) {

                $token->delete();

                return response()->json([
                    'status' => false,
                    'message' => 'Session expired. Please login again.'
                ], 401);
            }

            // Har valid request par expiry ko 2 hours aage badha do
            $token->update([
                'expires_at' => now()->addHours(2)
            ]);
        }

        return $next($request);
    }
}