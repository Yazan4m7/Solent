<?php

namespace App\Modules\Portal\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\invoice;
use App\payment;

class PortalController extends Controller
{
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\AuthenticateClient::class)->only('dashboard');
    }

    public function showLoginForm()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username'   => 'required',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('clients')->attempt(['username' => $request->username, 'password' => $request->password], $request->get('remember'))) {

            return redirect()->intended('/portal/dashboard');
        }

        return back()->withInput($request->only('username', 'remember'));
    }

    public function logout()
    {
        Auth::guard('clients')->logout();
        return redirect('/portal/login');
    }

    public function dashboard()
    {
        $client = Auth::guard('clients')->user();
        $invoices = invoice::where('doctor_id', $client->id)->get();
        $payments = payment::where('doctor_id', $client->id)->get();
        $balance = $client->balanceAt(now());

        return view('portal.dashboard', compact('invoices', 'payments', 'balance', 'client'));
    }
}
