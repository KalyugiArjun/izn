<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     * Intercept 419 Session Expired and Auth Exceptions gracefully without showing error screens.
     */
    public function render($request, Throwable $e)
    {
        // 1. Session Expired / Token Mismatch (CSRF or Session Timeout)
        if ($e instanceof \Illuminate\Session\TokenMismatchException || 
            ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 419)) {
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'session_expired' => true,
                    'message' => __('Your session has expired. Please login again.'),
                    'redirect' => route('login'),
                ], 419);
            }

            return redirect()->route('login')->with('info', __('Your session has expired. Please login again.'));
        }

        // 2. Unauthenticated (Authentication timeout on auth routes)
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'session_expired' => true,
                    'message' => __('Your session has ended. Please login again.'),
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->guest(route('login'))->with('info', __('Your session has ended. Please login again.'));
        }

        return parent::render($request, $e);
    }
}
