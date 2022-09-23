<?php

namespace App\Exceptions;

use App\Helpers\ResponseFormatter;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return ResponseFormatter::error(null, 'Record not found.', 404);
            }
        });

        //JWT
        $this->renderable(function (TokenInvalidException $e, $request) {
            return ResponseFormatter::error(null, 'Invalid token.', 401);
        });
        $this->renderable(function (TokenExpiredException $e, $request) {
            return ResponseFormatter::error(null, 'Token has Expired.', 401);
        });

        $this->renderable(function (JWTException $e, $request) {
            return ResponseFormatter::error(null, 'Token not parsed.', 401);
        });

        $this->renderable(function (Exception $e) {
            if ($e instanceof ValidationException) {
                return ResponseFormatter::error(null, $e->getMessage(), $e->status);
            }

            return ResponseFormatter::error(null, 'Maaf, kesalahan pada server.', 500);
        });
    }
}
