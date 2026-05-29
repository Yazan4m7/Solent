<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Something went wrong. Please try again.',
                'status' => $statusCode,
            ], $statusCode);
        }

        try {
            return response()->view('errors.generic', [
                'statusCode' => $statusCode,
            ], $statusCode);
        } catch (Throwable $viewException) {
            return response('Something went wrong. Please try again.', $statusCode);
        }
    }
}
