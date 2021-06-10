<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;


class MainController extends Controller
{
    public function login(){
        return view('auth.login');
    }

    public function authenticate(Request $request){
        $credentials = $request->only('email', 'password');
        if (auth('admin')->attempt($credentials, false)) {
            return redirect()->intended(route('admin.dashboard'));
        } else {
            return redirect()->back()->with('error', 'Administrator credentials not valid!');
        }
    }

    function dashboard(){
        return view('admin.dashboard');
    }
}
