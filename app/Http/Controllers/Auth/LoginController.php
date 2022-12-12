<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\{User};
 


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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Login
    public function showLoginForm()
    {
        $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
        ];

        return view('/auth/login', [
            'pageConfigs' => $pageConfigs
        ]);
    }

    public function adminLogin(Request $request){
        
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );

        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'email' => 'Email',
            'password' => 'Password',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->with('errors', $validator->errors())->withInput();
        }
        else
        {
            $getDetails = User::select('status')->where('email', $request->email)->where('role', 1)->first();
            if(!empty($getDetails['status']))
            {
                if($getDetails['status'] == 1)
                {
                    if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 1, 'status' => 1])) {
                        $user = Auth::user();
                        return redirect('/');
                    } else {
                        Session::flash('error', 'Invalid email or password.');
                        return redirect()->back();
                    }
                }
                else
                {
                    Session::flash('error', 'Inactive account');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('error', 'Invalid email or password.');
                return redirect()->back();
            }
        }
    }
    
    public function loginview(){
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/comic/comics",'name'=>"Comics"]
        ];
        return view('/content/userlogin', ['breadcrumbs' => $breadcrumbs]);
    }

    public function loginuser(Request $request){
      
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );

        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'email' => 'Email',
            'password' => 'Password',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->with('errors', $validator->errors())->withInput();
        }
        else
        {
            $getDetails = User::select('status')->where('email', $request->email)->where('role', 2)->first();
            if(!empty($getDetails['status']))
            {
                if($getDetails['status'] == 1)
                {
                    if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2, 'status' => 1])) {
                        $user = Auth::user();
                     
                         return redirect('home');
                    } else {
                        Session::flash('error', 'Invalid email or password.');
                        return redirect()->back();
                    }
                }
                else
                {
                    Session::flash('error', 'Inactice account');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('error', 'Invalid email or password.');
                return redirect()->back();
            }
        }
    }
 
    
}
