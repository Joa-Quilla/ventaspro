<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Manejar errores de timeout de manera más amigable
        if ($exception instanceof \ErrorException && str_contains($exception->getMessage(), 'Maximum execution time')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'La operación está tardando más de lo esperado. Por favor, intenta nuevamente o contacta al administrador.',
                ], 504);
            }

            return response()->view('errors.timeout', [
                'message' => 'La operación está tardando más de lo esperado. Por favor, recarga la página e intenta nuevamente.',
            ], 504);
        }

        return parent::render($request, $exception);
    }
}
