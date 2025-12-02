<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        // EXAMPLE #1: A specific check for QueryException

        // EXAMPLE #2: Another custom check, e.g., for your own custom exceptions
//        if ($e instanceof NotFoundHttpException) {
//            // Pass a message or any data you need
//            return response()->view('generic.oops', [
//                'errorMessage' => "Not found bro!",
//                'exception'   =>  $e,
//            ], 400);
//        }

        // EXAMPLE #3: A fallback for all "other" exceptions
        // Let Laravel handle the rest it
        return parent::render($request, $e);

    }
}
