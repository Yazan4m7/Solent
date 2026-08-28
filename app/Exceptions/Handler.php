<?php

namespace App\Exceptions;

use App\Support\DemoMode;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
        $this->reportable(function (Throwable $e): void {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return parent::render($request, $e);
        }

        $statusCode = 500;
        if ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
        }
        $isDemoRequest = DemoMode::isDemoRequest($request);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => $isDemoRequest
                    ? 'Demo version does not allow this operation.'
                    : 'Something went wrong. Please try again.',
                'status' => $statusCode,
            ], $statusCode);
        }

        if ($statusCode === 503 && app()->isDownForMaintenance()) {
            try {
                return response()->view('errors.maintenance', [
                    'retryAfter' => $e instanceof HttpExceptionInterface
                        ? $e->getHeaders()['Retry-After'] ?? null
                        : null,
                ], 503, $e instanceof HttpExceptionInterface ? $e->getHeaders() : []);
            } catch (Throwable $viewException) {
                return response(config('branding.defaults.name', 'Solent') . ' is temporarily down for maintenance. Please try again soon.', 503);
            }
        }

        try {
            return response()->view('errors.generic', [
                'statusCode' => $statusCode,
                'isDemoRequest' => $isDemoRequest,
                'developerMessage' => Str::limit(trim($e->getMessage()) ?: class_basename($e), 180)
                    ,
            ], $statusCode);
        } catch (Throwable $viewException) {
            return response('Something went wrong. Please try again later.' . $e->getMessage(), $statusCode);
        }
    }
}
