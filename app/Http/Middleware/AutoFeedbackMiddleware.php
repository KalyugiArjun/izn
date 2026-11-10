<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Events\AutoFeedbackEvent;

class AutoFeedbackMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Fire event — Listener handles the 12-hour lock internally
        AutoFeedbackEvent::dispatch();

        return $next($request);
    }
}
