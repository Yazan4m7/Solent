<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Providers\RouteServiceProvider;
use App\Support\Tenancy\TenantContext;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo =  RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function username()
    {
        return 'username';
    }

    public function showLoginForm(Request $request)
    {
        return view($this->isPlatformAdminRequest($request)
            ? 'auth.platform-login'
            : 'auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($this->isPlatformAdminRequest($request)) {
            return redirect()->route('system.tenants.index');
        }

        $this->recordTenantLogin($request, $user);
    }

    private function recordTenantLogin(Request $request, User $user): void
    {
        if (!app()->bound('app.tenant_context')) {
            return;
        }

        $tenantContext = app('app.tenant_context');
        if (!$tenantContext instanceof TenantContext || !$tenantContext->tenantId) {
            return;
        }

        $connection = (string) config('tenancy.landlord_connection', 'landlord');
        if (
            !Schema::connection($connection)->hasColumn('tenants', 'last_login_at') ||
            !Schema::connection($connection)->hasColumn('tenants', 'last_login_host') ||
            !Schema::connection($connection)->hasColumn('tenants', 'last_login_username')
        ) {
            return;
        }

        Tenant::query()
            ->whereKey($tenantContext->tenantId)
            ->update([
                'last_login_at' => now(),
                'last_login_host' => $tenantContext->domain ?: $request->getHost(),
                'last_login_username' => $user->username,
            ]);
    }

    private function isPlatformAdminRequest(Request $request): bool
    {
        $requestHost = preg_replace('/^www\./', '', strtolower($request->getHost()));
        $adminHost = preg_replace('/^www\./', '', strtolower((string) config('tenancy.platform_admin_host')));

        return $requestHost === $adminHost;
    }

    protected function credentials(Request $request): array
    {
        $credentials = $request->only($this->username(), 'password');
        $table = (new User())->getTable();

        if (Schema::hasColumn($table, 'status')) {
            $credentials['status'] = 1;
        }
        if (Schema::hasColumn($table, 'active')) {
            $credentials['active'] = 1;
        }

        return $credentials;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $username = (string) $request->input($this->username(), '');
        $user = $username !== '' ? User::where($this->username(), $username)->first() : null;
        $userExists = $user !== null;
        $userAttributes = $user ? $user->getAttributes() : [];
        $userIsDisabled = $user && (
            (array_key_exists('status', $userAttributes) && ! (bool) $user->status) ||
            (array_key_exists('active', $userAttributes) && ! (bool) $user->active)
        );

        throw ValidationException::withMessages([
            $userIsDisabled || ! $userExists ? $this->username() : 'password' => [
                $userIsDisabled
                    ? 'This account is disabled.'
                    : ($userExists
                    ? 'The password entered for this account is incorrect.'
                    : 'We could not find an account with that username.'),
            ],
        ]);
    }

}
