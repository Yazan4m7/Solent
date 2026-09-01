<?php

namespace App\Http\Middleware;

use App\Support\DemoMode;
use Closure;
use Illuminate\Http\Request;

class EnforceDemoReadOnly
{
    private const MUTATING_GET_ROUTES = [
        'reset-to-waiting',
        'complete-by-admin',
        'assign-to-delivery-person',
        'delivery-accept',
        'createDummyCase',
        'receive-payment',
        'lock-case',
        'unlock-case',
        'send-case-to-delivery',
        'update-sys-config',
        'restore-case',
        'soft-delete-user',
        'soft-delete-client',
        'toggle-client-active',
        'switch-env',
        'switch_env',
        'delete-payment',
        'material-delete',
        'delete-case',
        'assign-to-me',
        'finish-case',
        'assign-and-finish',
        'delivered-in-box',
        'new-notification',
        'test-notificaion',
        'test-notification-by-type',
        'finish-case-completely',
    ];

    private const MUTATING_GET_ACTIONS = [
        'resetCaseToWaiting',
        'completeByAdmin',
        'assignToDelivery',
        'acceptCaseByDelivery',
        'createDummyCase',
        'receivePayment',
        'lockCase',
        'unlockCase',
        'sendCaseToDelivery',
        'updateSystemConfig',
        'restoreDeletedCase',
        'softDelete',
        'toggleActive',
        'switchEnvironment',
        'deletePayment',
        'delete',
        'deleteCase',
        'assignToMe',
        'finishCaseStage',
        'assignAndFinish',
        'deliveredInBox',
        'sendNotification',
        'testNotification',
        'finishCaseCompletely',
    ];

    public function handle(Request $request, Closure $next)
    {
        $tenantDemo = app()->bound('app.tenant_demo_active') && app('app.tenant_demo_active');
        $key = $request->attributes->get('tenant_demo_session_key');
        $mutates = ! $request->isMethodSafe() || $this->isMutatingGetRoute($request);
        if ($key && $mutates && ! $this->isAllowedWrite($request)
            && $request->session()->get($key . '.switched')
            && ! hash_equals((string) $request->session()->get($key . '.token'),
                (string) ($request->header('X-Data-Context') ?: $request->input('_data_context', '')))) {
            abort(409, 'The data mode changed. Refresh this page before making changes.');
        }
        // Internal transport metadata must never reach legacy mass-assignment handlers.
        $request->query->remove('_data_context');
        $request->request->remove('_data_context');
        if (! $tenantDemo && (! DemoMode::isDemoRequest($request)
            || ! (bool) config('domain_context.demo.read_only', false))) {
            return $next($request);
        }

        if ($this->isAllowedWrite($request)) {
            return $next($request);
        }

        if ($request->isMethodSafe() && ! $this->isMutatingGetRoute($request)) {
            return $next($request);
        }

        $message = 'Demo mode is read-only. Changes are not saved on ' . $request->getHost() . '.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], 423);
        }

        return back()->with('error', $message);
    }

    private function isAllowedWrite(Request $request): bool
    {
        if (in_array($request->route()?->getName(), ['login', 'logout', 'tenant-demo.update'], true)) {
            return true;
        }

        return $request->isMethod('POST') && $request->is('login');
    }

    private function isMutatingGetRoute(Request $request): bool
    {
        $route = $request->route();
        if (! $route) {
            return false;
        }

        return in_array($route->getName(), self::MUTATING_GET_ROUTES, true)
            || in_array($route->getActionMethod(), self::MUTATING_GET_ACTIONS, true);
    }
}
