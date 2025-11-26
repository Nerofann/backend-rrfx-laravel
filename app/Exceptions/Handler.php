<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use mysqli_sql_exception;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle JWT Exceptions
        $this->renderable(function (TokenExpiredException $e, $request) {
            return ApiResponse::error('Token has expired', [], 401);
        });

        $this->renderable(function (TokenInvalidException $e, $request) {
            return ApiResponse::error('Token is invalid', [], 401);
        });

        $this->renderable(function (JWTException $e, $request) {
            return ApiResponse::error('Token is required', [], 401);
        });

        // Handle Authentication Exception
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('v1/*') || $request->expectsJson()) {
                return ApiResponse::error('Unauthenticated', [], 401);
            }
        });
    }

}
