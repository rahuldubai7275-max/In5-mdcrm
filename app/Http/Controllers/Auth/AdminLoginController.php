<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Route;
class AdminLoginController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/auth/login', [
            'pageConfigs' => $pageConfigs
        ]);
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
//        dd(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password]));
        // Attempt to log the user in
        if (Auth::guard('admin')->attempt(['status' => 1,'email' => $request->email, 'password' => $request->password], $request->remember)) {
            // if successful, then redirect to their intended location
            return redirect('admin/');
        }
        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');//redirect('/');
    }
}
