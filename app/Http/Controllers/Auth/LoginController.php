<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use RTippin\Messenger\Facades\Messenger;

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
    protected string $redirectTo = RouteServiceProvider::HOME;

    public function showLoginForm()
    {
        if(auth('employee')->check()){
            return redirect()->route('messenger.portal')->withSuccess(__('messages_employee.login_success'));
        }
        else if(auth('admin')->check()){
            return redirect()->route('messenger.portal')->withSuccess(__('messages_admin.login_success'));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if(auth('employee')->check()){
            return redirect()->route('home')->withSuccess(__('messages_employee.login_success'));
        }
        else if(auth('admin')->check()){
            return redirect()->route('home')->withSuccess(__('messages_admin.login_success'));
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:employee',
            'password' => 'required|min:8',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withError(implode("<br>", $validator->errors()->all()));
        }

        $remember_me = $request->has('remember_me') ? true : false;
        $credentials = $request->only('email', 'password');

        $guard = 'web';
        $type = $request->get('type');
        if ($type == 'admin') $guard = 'admin';
        else if ($type == 'employee') $guard = 'employee';

        if (auth($guard)->attempt($credentials, $remember_me)) {
            $user = auth($guard)->user();
            if (auth($guard)->check() && $user->verify_at && $user->status->value === 1) {
                $token = $user->createToken($request->email . '-' . now())->plainTextToken;
                Session::put('employee_access_token', $token);
                return redirect()->route('home')->withSuccess(__('messages_employee.login_email_success'));
            } else {
                return back()->withError(__('messages_employee.login_email_err'));
            }
        } else {
            return back()->withError(__('messages_employee.login_email_err'));
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return RedirectResponse|JsonResponse
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            Messenger::setProviderToOffline($request->user());
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
}
