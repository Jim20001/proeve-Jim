<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Auth extends Controller
{
    public function login()
    {
    	return view('admin.login');
    }

    public function authenticate(Request $request)
    {
    	$credentials = $request->only('email', 'password');
    	if (auth('admin')->attempt($credentials, false)) {
    		return redirect()->intended(route('admin.dashboard'));
    	} else {
            return redirect()->back()->withErrors([trans('common.login_notfound')]);
        }
    }

    public function logout()
    {
    	auth('admin')->logout();

        return redirect()->route('admin.login');
    }
}