<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
    public function render($request, Throwable $e)
    {
        // Return JSON responses for API requests
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions and return consistent JSON responses.
     */
    private function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Resource not found.',
            ], 404);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return response()->json([
                'message' => 'Access denied.',
            ], 403);
        }

        // For other exceptions in production, don't expose details
        if (config('app.debug')) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }

        return response()->json([
            'message' => 'Internal server error.',
        ], 500);
    }
}