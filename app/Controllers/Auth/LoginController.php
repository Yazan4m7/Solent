<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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

    protected function sendFailedLoginResponse(Request $request)
    {
        $username = (string) $request->input($this->username(), '');
        $userExists = $username !== '' && User::where($this->username(), $username)->exists();

        throw ValidationException::withMessages([
            $userExists ? 'password' : $this->username() => [
                $userExists
                    ? 'The password entered for this account is incorrect.'
                    : 'We could not find an account with that username.',
            ],
        ]);
    }

}
