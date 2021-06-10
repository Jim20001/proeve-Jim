<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Users\User;
use App\Services\Email;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Mail;
use DB;
use Carbon\Carbon;


class Auth extends Controller
{
    public function login()
    {
    	return view('app.auth.login');
    }

    public function authenticate(Request $request)
    {
    	$credentials = $request->only('email', 'password');
    	if (auth('user')->attempt($credentials, false)) {

            if(auth('user')->user()->is_blocked == 1){
                auth('user')->logout();
                return redirect()->back()->withErrors(['Ви заблоковані!']);
            }

            // if(auth('user')->user()->login_token !== NULL){
            //     //dd("a");
            //     auth('user')->logout();
            //     return redirect()->back()->withErrors(['Ви заблоковані!']);
            // }else {
            //     User::where('id', auth('user')->user()->id)->update(['login_token'=>Str::uuid(), 'login_token_ts'=>Date('Y-m-d H:i:s')]);
            //     return redirect()->intended(route('app.dashboard'));
            // }

    		return redirect()->intended(route('app.dashboard'));
    	} else {
            return redirect()->back()->withErrors([trans('common.login_notfound')]);
        }
    }

    public function mobAuth(Request $request) {

        $request->validate([
            'email' => 'required',
            'password' => 'required',
            // 'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ['error' => 'The provided credentials are incorrect.'];
        }

        return ['token' => $user->createToken("token")->plainTextToken];
    }

    public function mobRegister(Request $request) {
        $request->validate([
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        $user = User::create(['email' => $request->email, 'password' => Hash::make($request->password)]);
 
        return $user;
    }

    public function logout()
    {
        User::where('id', auth('user')->user()->id)->update(['login_token'=>NULL, 'login_token_ts'=>Date('Y-m-d H:i:s')]);
        auth('user')->logout();
        return redirect()->route('app.login');
    }

    public function register()
    {
        $seo = [
            'title' => "Зареєструватися | ".app('config')->get('options')['name'],
            'description' => ''
        ];

        app('config')->set('seo', $seo);
        
        return view('app.auth.register');
    }

    public function isLoggedIn() {
        // op log in voeg info toe aan de login token
        // check if login info is empty
        // if emtpy assign value
        // else block user from loggin in
    }

    public function adduser(Request $request)
    {
        $this->validate(request(), [
            'g-recaptcha-response' => 'required|captcha',
            'surname' => 'required',
            'firstname' => 'required',
            'patronymic' => 'required',
            'email' => 'unique:users|required|email',
            'password' => 'min:6|required_with:password2|same:password2',
            'password2' => 'min:6',
        ]);

        $user = new User;
        $user->name = $request->input('surname');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->firstname = $request->input('firstname');
        $user->patronymic = $request->input('patronymic');
        $user->save();

        (new Email)->confirmEmail($user);

        $credentials = $request->only('email', 'password');
        if (auth('user')->attempt($credentials, false)) {
            return redirect()->intended(route('app.dashboard'));
        } else {
            return redirect()->back()->withErrors([trans('common.login_notfound')]);
        }
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $suser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/app/login');
        }

        $existingUser = User::where('email', $suser->email)->first();

        if($existingUser){
            auth('user')->login($existingUser, true);
            return redirect()->intended(route('app.dashboard'));
        } else {
            $user = new User;
            $user->name = $suser->name;
            $user->email = $suser->email;
            $user->gid = $suser->id;
            $user->save();

            (new Email)->confirmEmail($user);
            auth('user')->login($user, true);
            return redirect()->intended(route('app.dashboard'));
        }
        // $user->token;
    }

    public function forgot() {
        return view('app.auth.passwords.email');
    }

    public function forgotValidation(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $token = Str::random(60);

        if(User::where('email', $request->email)->first()) {
            DB::table('password_resets')->insert(
                ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
        );

            $name = app('config')->get('options')['name'];

            Mail::send('app.auth.passwords.verify',['token' => $token, 'name' => $name], function($message) use ($request) {
                $message->from( 'noreply@testvprokuraturu.com.ua' );
                $message->to($request->email);
                $message->subject('Перевірте свою адресу електронної пошти');
            });  
                return back()->with('message', 'Перевірте свою адресу електронної пошти!');
        } else {
            return back();        }
    }

    public function reset($token) {
        return view('app.auth.passwords.reset', compact('token'));
    }

    public function resetValidation(Request $request) {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);
        
        $updatePassword = DB::table('password_resets')
                            ->where(['email' => $request->email, 'token' => $request->token])
                            ->first();
                            

        // dd($updatePassword);
        if(!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        } else {
            $user = User::where('email', $request->email)
              ->update(['password' => Hash::make($request->password)]);

            DB::table('password_resets')->where(['email'=> $request->email])->delete();

            return redirect('/app/login')->with('message', 'Підтвердити пароль!');
        }
    }
}