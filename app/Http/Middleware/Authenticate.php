<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated user for API & Web
     */
    protected function unauthenticated($request, array $guards)
    {
        // ✅ Always return JSON for API routes
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'status'  => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401));
        }

        // 🌐 Web fallback redirect
        redirect()->route('login')->throwResponse();
    }

    /**
     * Redirect path (fallback)
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        return route('login');
    }
}
