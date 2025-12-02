<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Hash;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Client;

class LoginController extends Controller
{
    use IssueTokenTrait;
    private $client;

    public function __construct()
    {

        $this->client = Client::where('id', 2)->first();

    }
    protected function credentials(Request $request)
    {
        if (is_numeric($request->get('username'))) {
            return ['username' => $request->get('username'), 'password' => $request->get('password')];
        }
        return $request->only($this->username(), 'password');
    }

    public function login(Request $request)
    {
        $this->validate($request, [

            'password' => 'required'
        ]);
        // Get user data
        $user = User::where('username', $request->username)->first();

        if ( $user && ( Hash::check(request('password'), $user->password) ) ) {

            // User nor active
            $response = array(
                'status'  => false,
                'message' => 'Oops! Your account is not active. Please try again later.',
            );

            return $this->issueToken($request, 'password');

        } else {
            return response()->json("User isn't found", 404, []);
        }
       /* if($user){
            Cache::put('user-online-'.$user->id,1,Carbon::now()->addMinutes(1));
        } */

    }
    public function username()
    {
        return 'username';
    }
}
