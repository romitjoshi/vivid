<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\{User, User_details, User_wallet};
use Laravel\Socialite\Facades\Socialite;
use DB,Helper, Log, Str;
use App\Mail\ForgotEmailSend;
use App\Mail\PublisherSignup;
use App\Mail\PublisherReferBySubscription;
use Carbon\Carbon;
class AuthfrontController extends Controller
{
    public function login()
    {
       // echo Auth::user()->id; exit("login");
        if(!empty(Auth::user()->id))
        {
            return redirect('/home');
        }
        return view('/content/front/auth/login');
    }

    public function loginPublisher()
    {
        return view('/content/front/auth/login-publisher');
    }

    public function loginFront(Request $request)
    {

        $rules = array(
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        );
        $messages = array(
            "email.required" => "Email is required",
            "email.email" => "Email is not valid",
            "email.exists" => "Email doesn't exists",
            'password.required' => 'Password is required',
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
            $getDetails = User::where('email', $request->email)->where('role', '!=', 1)->first();

            if(!empty($getDetails['role']) && $getDetails['role'] == 1)
            {
                Session::flash('error', 'Only customer or publisher login');
                return redirect()->back();
            }

            if(!empty($getDetails['login_type']) && $getDetails['login_type'] != 1)
            {
                Session::flash('error', 'Seems like this email account is associated with social login. Please use either Facebook or Google login option.');
                return redirect()->back();
            }

            if(!empty($getDetails['status']))
            {
                if($getDetails['status'] == 1)
                {
                    if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1])) {
                        //$user = Auth::user();
                        if(!empty($_COOKIE['askLogin']))
                        {
                            return redirect($_COOKIE['askLogin']);
                        }
                        if($getDetails['role'] == 2)
                        return redirect('/');
                        else
                        return redirect('/publisher/my-dashboard');

                    } else {
                        Session::flash('error', 'Incorrect username or password.');
                        return redirect()->back();
                    }
                }
                else
                {
                    $erMsg = "Your account has been deactivated please contact <a href='mailto: management@vivpanel.com'> management@vivpanel.com</a>  for further assistance";
                    Session::flash('error', $erMsg);
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

    public function loginFrontPublisher(Request $request)
    {

        $rules = array(
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        );
        $messages = array(
            "email.required" => "Email is required",
            "email.email" => "Email is not valid",
            "email.exists" => "Email doesn't exists",
            'password.required' => 'Password is required',
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
            $getDetails = User::where('email', $request->email)->where('role', 3)->first();

            if(!empty($getDetails['login_type']) && $getDetails['login_type'] != 1)
            {
                Session::flash('error', 'Seems like this email account is associated with social login. Please use either Facebook or Google login option.');
                return redirect()->back();
            }

            if(!empty($getDetails['status']))
            {
                if(Auth::attempt(['email' => 'management@vivpanel.com', 'password' => $request->password, 'role' => 3]))
                {
                    if($getDetails['status'] == 2 && $getDetails['role'] == 3)
                    {
                        Auth::logout();
                        Session::flash('error', 'Your account is unproved. please contact to admin');
                        return redirect()->back();
                    }

                    if($getDetails['status'] == 2)
                    {
                        Auth::logout();
                        $erMsg = "Your account has been deactivated please contact <a href='mailto: management@vivpanel.com'> management@vivpanel.com</a>  for further assistance";
                        Session::flash('error', $erMsg);
                        return redirect()->back();
                    }

                    //$user = Auth::user();
                    if(!empty($_COOKIE['askLogin']))
                    {
                        return redirect($_COOKIE['askLogin']);
                    }
                    return redirect('/');

                }
                else
                {
                    Session::flash('error','Incorrect username or password.');
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

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }

    public function signup()
    {
        return view('/content/front/auth/signup');
    }

    public function signupPublisher()
    {
        return view('/content/front/auth/signup-publisher');
    }

    public function signupFront(Request $request)
    {

        $rules = array(
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:6',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'dob' => 'required',
        );

        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'name' => 'Name',
            'email' => 'Email',
            'dob' => 'Date of birth',
            'password' => 'Password',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->with('errors', $validator->errors())->withInput();
        }
        else
        {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->login_type = 1;
            $user->role = 2;
            $user->status = 1;
            $user->password = bcrypt($request->input('password'));
            $user->save();


            $userDetail = new User_details;
            $userDetail->user_id = $user->id;


            if(!empty($request->input('refer_code')))
            {
                $ud = User_details::where("refer_code", $request->input('refer_code'))->first();

                if(!empty($ud))
                {
                    $pubEmail = User::where("id", $ud->user_id)->value("email");
                    if(!empty($ud))
                    $userDetail->refer_by = $ud->user_id;
                }
               

                // $mailData = [
                //     'publisher_name' => $ud->business_name,
                //     'customer_name' => $request->input('name'),
                //     'link'=> env('APP_URL_SERVE').'/login'
                // ];
                // Mail::to($pubEmail)->send(new PublisherReferBySubscription($mailData));

            }

            $userDetail->dob = date("Y-m-d", strtotime($request->input('dob')));
            $userDetail->user_type = 1;
            $userDetail->account_create_device_type = 1;
            $userDetail->save();

            if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'role' => 2, 'status' => 1])) {

                $user_wallet = new User_wallet;
                $user_wallet->user_id = $user->id;
                $user_wallet->coins = Helper::freeCoinsForFreeUser($user->id);
                $user_wallet->type = 2;
                $user_wallet->comic = "Free Coins";
                $user_wallet->transaction_type = 1;
                $user_wallet->save();

                $user = Auth::user();
                if(!empty($_COOKIE['askLogin']))
                {
                    return redirect($_COOKIE['askLogin']);
                }
                return redirect('/');
            }
        }
    }

    public function frontSignupPublisher(Request $request)
    {

        $rules = array(
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:6',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'Phonenumber' => ['required','digits:10'],
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'name' => 'Name',
            'email' => 'Email',
            'Phonenumber' =>'Phone Number',
            'password' => 'Password',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->with('errors', $validator->errors())->withInput();
        }
        else
        {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->login_type = 1;
            $user->role = 3;
            $user->status = 2;
            $user->is_approve = 0;
            $user->password = Hash::make($request->input('password'));
            $user->save();
            $userDetail = new User_details;
            $userDetail->user_id = $user->id;
            //$userDetail->dob = date("Y-m-d", strtotime($request->input('dob')));
            do {
                $refer_code = Helper::generateRandomString(6);
            } while ( DB::table('user_details')->where( 'refer_code', $refer_code )->exists() );

            try{

                $api_key = 'AIzaSyBVAFz2csMMjWShDuFBHEIgCReqEc-6BAc';
                $headers = array(
                    "Content-Type: application/json;charset=utf-8"
                );
                $data = [
                    'longDynamicLink'=>'https://publishers.vivpanel.com/?link=https://www.vivpanel.com/r/'.$refer_code.'&apn=com.app.vividpanel&isi=1639112308&ibi=com.app.vividpanel',
                    'suffix'=>[
                        'option'=>'SHORT',
                    ]
                ];
                $apiurl = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key='.$api_key;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiurl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $curl_output = curl_exec($ch);
                if(!curl_errno($ch)){
                    $output = json_decode($curl_output);
                    //return response()->json(['status'=>true,'redirect_link'=>$output->shortLink],200);
                }else{
                    $curl_output = new \stdClass();
                    $curl_output->success = false;
                    $curl_output->message = curl_error($ch);
                    Session::flash('message',$curl_output);
                    //return response()->json(['status'=>false,'error'=>$curl_output],200);
                }
            }catch(\Exception $e){
                Session::flash('message',$curl_output);
            }


            $userDetail->user_type = 1;
            $userDetail->account_create_device_type = 1;
            $userDetail->Phone_number = $request->input('Phonenumber');
            $userDetail->business_name = $request->input('name');
            $userDetail->business_type = $request->input('businesstype');
            $userDetail->business_address = $request->input('address');
            $userDetail->EIN = $request->input('ein');
            $userDetail->refer_link = $output->shortLink ?? '';
            $userDetail->refer_code = $refer_code;
            $userDetail->slug = Helper::makeSlug("user_details", $request->input('name'));
            $userDetail->save();

            Session::flash('message','Thank you for your interest in Vivid Panel! Someone will reach out to you about setting up your account shortly.<span style="color:red">*Please check your spam folder and add us as a contact to receive important account updates*</span>');

            $adminemail =Helper::getAdminEmail();
            $mailData = [
                'publisher_name' => $request->input('name'),
                'link'=> env('APP_URL_SERVE').'/admin/login'
            ];
           Mail::to($adminemail)->send(new PublisherSignup($mailData));

            return redirect()->back();
        }
    }

    public function redirect(){
       // return Socialite::driver('google')->redirect();
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function callbackGoogle(){
        try{
            //$google_user=Socialite::driver('google')->user();
            $google_user=Socialite::driver('google')->stateless()->user();

            //Log::debug('google_user');
            //Log::debug($google_user);

            $getUser =User::where('social_token',$google_user->getId())->first();
            
            if(empty($getUser))
            {
                try
                {
                    $em = $google_user->getEmail();
                    $user = new User;
                    $user->email = $em ?? "";
                    $user->name = $google_user->getName() ?? "";
                    $user->social_token = $google_user->getId();
                    $user->login_type = 3;
                    $user->role = 2;
                    $user->status = 1;
                    $user->save();

                    //User Detils Table
                    $userDetail = new User_details;
                    $userDetail->user_id = $user->id;
                    $userDetail->user_type = 1;
                    $userDetail->account_create_device_type = 1;
                    $userDetail->save();

                    Auth::login($user);
                    if(!empty($_COOKIE['askLogin']))
                    {
                        return redirect($_COOKIE['askLogin']);
                    }
                    return redirect('/');

                } catch (\Exception $e) {
                    return response()->json(['status' => false,  'message' => "Your account already exist with $em email, so you can't use the same account in case of social login."], 200);
                }

            }
            else
            {
                Auth::login($getUser);
                if(!empty($_COOKIE['askLogin']))
                {
                    return redirect($_COOKIE['askLogin']);
                }
                return redirect('/');
            }
        }
        catch(\Throwable $th){
            return redirect('/');
            //dd('Something went wrong !'.$th->getMessage());
        }
    }

    public function fbRedirect(){
        //return Socialite::driver('facebook')->redirect();
        return Socialite::driver('facebook')
        ->stateless()
        ->redirect();
    }

    public function callbackFromFacebook(){

        try{
            //$fb_user=Socialite::driver('facebook')->user();
            $fb_user=Socialite::driver('facebook')->stateless()->user();

            //Log::debug("Fb User");
            //Log::debug($fb_user);

            $getUser =User::where('social_token',$fb_user->getId())->first();
            if(empty($getUser))
            {
                $user = new User;
                $em = "user".time()."@gmail.com";
                $user->email = $fb_user->getEmail() ?? $em;
                $user->name = $fb_user->getName() ?? "User";
                $user->social_token = $fb_user->getId();
                $user->login_type = 3;
                $user->role = 2;
                $user->status = 1;
                $user->save();

                //User Detils Table
                $userDetail = new User_details;
                $userDetail->user_id = $user->id;
                $userDetail->user_type = 1;
                $userDetail->account_create_device_type = 1;
                $userDetail->save();

                Auth::login($user);
                if(!empty($_COOKIE['askLogin']))
                {
                    return redirect($_COOKIE['askLogin']);
                }
                return redirect('/');

            }
            else
            {
                Auth::login($getUser);
                if(!empty($_COOKIE['askLogin']))
                {
                    return redirect($_COOKIE['askLogin']);
                }
                return redirect('/');
            }
        }
        catch(\Throwable $th){
            return redirect('/');
            //dd('Something went wrong !'.$th->getMessage());
        }
    }


    public function resetPassword($token)
    {

        return view('/content/reset',['token' => $token]);
    }

    public function resetPasswords(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
                            ->where([
                              'token' => $request->token
                            ])
                            ->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = User::where('email', $updatePassword->email)
                    ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email'=> $updatePassword->email])->delete();

        return redirect('/reset-password/'.$request->token)->with('message', 'Your password has been changed!');

    }

    public function sendEmailForgotPassword(Request $request)
    {
        $rules = array(
            'email' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        $checkEmail = User::where("email", $request->input("email"))->first();
        if(empty($checkEmail))
        {
            return response()->json(['status' => false, 'message' => "Email id not found"], 200);
        }

        $checkEmail = User::where("email", $request->input("email"))->where("login_type", 1)->first();
        if(empty($checkEmail))
        {
            return response()->json(['status' => false, 'message' => "Seems like this email account is associated with social login. Please use either Facebook or Google login option."], 200);
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->input("email"),
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $link = env("APP_URL_SERVE").'/reset-password/'.$token;

        $mailData = [
            'link' => $link,
            'user' => $checkEmail->name
        ];
        $mailResponse = Mail::to($request->input("email"))->send(new ForgotEmailSend($mailData));
        return response()->json(['status' => true, 'message' => "Email Send Successfully"], 200);
    }

}
