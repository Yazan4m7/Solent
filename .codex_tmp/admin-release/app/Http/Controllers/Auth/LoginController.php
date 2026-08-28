<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
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
