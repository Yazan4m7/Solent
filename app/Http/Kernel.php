<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\ApplyDomainContext::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
             \Illuminate\Session\Middleware\AuthenticateSession::class,
            \App\Http\Middleware\ApplyBranding::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            //'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'Designer' => \App\Http\Middleware\DesignerMiddleware::class,
        'Miller' => \App\Http\Middleware\MillerMiddleware::class,
        'Print3D' => \App\Http\Middleware\Print3DMiddleware::class,
        'SinterFurnace' => \App\Http\Middleware\SinterFurnaceMiddleware::class,
        'PressFurnace' => \App\Http\Middleware\PressFurnaceMiddleware::class,
        'Finishing' => \App\Http\Middleware\FinishingMiddleware::class,
        'QC' => \App\Http\Middleware\QCMiddleware::class,
        'Delivery' => \App\Http\Middleware\DeliveryMiddleware::class,
        'CreateCase' => \App\Http\Middleware\CreateCaseMiddleware::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'EditCases'=> \App\Http\Middleware\EditCasesMiddleware::class,
        'ViewCasesList' =>  \App\Http\Middleware\ViewCasesListMiddleware::class,
        'ViewInvoices'=>  \App\Http\Middleware\ViewInvoicesListMiddleware::class,
        'DeliveryMonitor' =>\App\Http\Middleware\DeliveryMonitorMiddleware::class,
        'LabWorkFlow'=>\App\Http\Middleware\LabWorkFlowMiddleware::class,
        'ViewDoctors' =>\App\Http\Middleware\ViewDoctorsMiddleware::class,
        'ReceivePayments'=>\App\Http\Middleware\ReceivePaymentsMiddleware::class,
        'ViewDeliverySchedule'=>\App\Http\Middleware\ViewDeliveryScheduleMiddleware::class,
        'TakePaymentsFromClients'=>\App\Http\Middleware\TakePaymentsFromClientsMiddleware::class,
        'ViewCasesMonitor'=>\App\Http\Middleware\ViewCasesMonitorMiddleware::class,
        'RejectCases'=>\App\Http\Middleware\RejectCaseMiddleware::class,
        'RepeatCases'=>\App\Http\Middleware\RepeatCaseMiddleware::class,
        'ModifyCases'=>\App\Http\Middleware\ModifyCaseMiddleware::class,
        'RedoCases'=>\App\Http\Middleware\RedoCaseMiddleware::class,
        'Reports' =>\App\Http\Middleware\ReportsMiddleware::class,
        'ViewPayments'=>\App\Http\Middleware\ViewPaymentsMiddleware::class,
        'MainDashboard'=>\App\Http\Middleware\MainDashboardMiddleware::class,
        'RejectedCases'=>\App\Http\Middleware\RejectedCasesMiddleware::class,
        'ViewAbutmentsDelivery'=>\App\Http\Middleware\ViewAbutmentsDeliveryMiddleware::class,
        'ReceiveAbutments'=>\App\Http\Middleware\ReceiveAbutmentsMiddleware::class,
        'OrderAbutments'=>\App\Http\Middleware\OrderAbutmentMiddleware::class,
        'ViewVouchers'=>\App\Http\Middleware\ViewVouchersMiddleware::class,
        'LockUnlockCases'=>\App\Http\Middleware\LockUnlockCasesMiddleware::class,
        'platform.admin'=>\App\Http\Middleware\PlatformAdminMiddleware::class,


    ];
}
