<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\{User, Device_info, User_details, User_subscription_details, Wallet, User_wallet};
use App\Models\admin\{User_address, Notification, Plan, Setting, Coin_slab};
use Illuminate\Support\Facades\Password;
use App\Mail\ForgotEmailSend;
use App\Mail\PublisherReferBySubscription;
use Carbon\Carbon;
use Hash, Helper, Str, DB, Stripe;
use Google_Client;

class AuthController extends Controller
{
    public $userType = false;
    public $userInfo = [];

    public function getPrfileData($userId)
    {
        if(!empty($userId))
        {
            $user = User::where("id", $userId)->first();
            $this->userInfo = [
                'name'=> $user->name,
                'email'=> $user->email,
                'login_type'=> $user->login_type
            ];

            $value = User_details::where(['user_id'=>$user->id])->get()->first();

            if(!empty($value))
            {
                $this->userInfo['dob'] = "";
                if(!is_null($value->dob))
                $this->userInfo['dob'] = $value->dob;

                $this->userInfo['stripe_user_id'] = "";
                if(!is_null($value->stripe_user_id))
                $this->userInfo['stripe_user_id'] = $value->stripe_user_id;

                if(!is_null($value->user_type))
                $this->userType = $value->user_type;

                $subscription = [];

                $subscription['user_type'] = $value->user_type;
                $subscription['subscription_type'] = 0;
                $subscription['cancel'] = 1;

                if($value->user_type == 1)
                {
                    $subscription['plan'] = "Free Subscriptions";
                    $subscription['price'] = Helper::makeCurrency(0);
                }
                else
                {
                    $usd = User_subscription_details::where("user_id", $userId)->latest('id')->first();

                    if(!empty($usd))
                    {
                        $subscription['subscription_type'] = $usd->subscription_type;
                        $subscription['cancel'] = $usd->cancel;

                        if($usd->plan_id == 1)
                        {
                            $subscription['plan'] = "1 Month plan Subscriptions";
                            $subscription['price'] = Helper::makeCurrency($usd->price);
                        }
                        else
                        {
                            $subscription['plan'] = "1 Year plan Subscriptions";
                            $subscription['price'] = Helper::makeCurrency($usd->price);
                        }
                    }
                    else
                    {
                        $subscription['plan'] = "Free Subscriptions";
                        $subscription['price'] = Helper::makeCurrency(0);
                    }

                }

                $this->userInfo['subscription'] = $subscription;
                $totalCoins = Helper::getcoinsTotal($userId);
                $this->userInfo['coins'] = $totalCoins;
            }
        }
        return $this->userInfo;
    }

    public function signup(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required',
            'dob' => 'required',
            'user_type' => 'required',
            'login_type' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        );

        $messages = array(
            'required' => ':attribute field is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $data = [];
        $data['name'] = "";
        $data['token'] = "";
        $data['profile'] = (object)[];

        if ($validator->fails()) {
            return response()->json(['status' => false, 'data' => $data, 'message' => $validator->messages()->first()], 200);
        } else {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->login_type = $request->input('login_type');
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
                    if(!empty($ud))
                    $userDetail->refer_by = $ud->user_id;
                }
                
            }

            $userDetail->dob = date("Y-m-d", strtotime($request->input('dob')));
            $userDetail->user_type = $request->input('user_type');
            $userDetail->account_create_device_type = $request->input('device_type');
            $userDetail->save();

            if ($request->input('device_type')) {
                $deviceArray = new Device_info;
                $deviceArray->user_id = $user->id;
                $deviceArray->push_token = $request->input('device_token');
                $deviceArray->save();
            }

            $data['name'] = $request->name;
            $data['token'] = $user->createToken('Vivid')->accessToken;
            $data['profile'] = $this->getPrfileData($user->id);

            $user_wallet = new User_wallet;
            $user_wallet->user_id = $user->id;
            $user_wallet->coins = Helper::freeCoinsForFreeUser($user->id);
            $user_wallet->type = 2;
            $user_wallet->comic = "Free Coins";
            $user_wallet->transaction_type = 1;
            $user_wallet->save();

            return response()->json(['status' => true, 'data' => $data, 'message' => "Successfully Registered"], 200);
        }
    }


    public function socialSignup(Request $request)
    {

        $rules = array(
            //'name' => 'required',
            //'email' => ['required', 'string', 'email', 'max:255'],
            'social_token' => 'required',
            'user_type' => 'required',
            'login_type' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        );

        $messages = array(
            'required' => ':attribute field is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);

        $data = [];
        $data['name'] = "";
        $data['token'] = "";
        $data['profile'] = (object)[];

        if ($validator->fails()) {
            return response()->json(['status' => false, 'data' => $data, 'message' => $validator->messages()->first()], 200);
        } else {
            $getExsistUser = User::where('social_token', '=', $request->input('social_token'))->first();

            $social_token = $request->get('social_token') ?? "";
            $login_type = $request->get('login_type');

            $name = $request->input('name') ?? "";
            $email = $request->input('email') ?? "";

            if ($login_type == 4) {
                $tokenParts = explode(".", $social_token);
                $tokenHeader = base64_decode($tokenParts[0]);
                $tokenPayload = base64_decode($tokenParts[1]);
                $jwtHeader = json_decode($tokenHeader);
                $jwtPayload = json_decode($tokenPayload);
                $email = $jwtPayload->email ?? "user".time()."@gmail.com";
                $name = "User";
                $getExsistUser = User::where('email', '=', $email)->first();
            }
            if (!empty($getExsistUser)) {
                if (Auth::loginUsingId($getExsistUser->id)) {
                    $user = Auth::user();

                    // if ($request->input('device_type')) {
                    //     $checkTokenExsist = Device_info::select('id')->where('push_token', $request->input('device_token'))->count();

                    //     if ($checkTokenExsist > 0) {
                    //         $updateArray = [
                    //             'push_token' => $request->input('device_token'),
                    //         ];
                    //         Device_info::where('push_token', $request->input('device_token'))->update($updateArray);
                    //     } else {
                    //         $deviceArray = new Device_info;
                    //         $deviceArray->user_id = $user->id;
                    //         $deviceArray->push_token = $request->input('device_token');
                    //         $deviceArray->save();
                    //     }
                    // }
                    if ($request->input('device_type')) {
                        $deviceArray = new Device_info;
                        $deviceArray->user_id = $user->id;
                        $deviceArray->push_token = $request->input('device_token');
                        $deviceArray->save();
                    }
                    $data['token'] = $user->createToken('Vivid')->accessToken;
                    $data['profile'] = $this->getPrfileData($user->id);
                    return response()->json(['status' => true, 'data' => $data, 'message' => "Successfully Login"], 200);
                } else {
                    return response()->json(['status' => false, 'data' => $data, 'message' => "Something Went Wrong"], 200);
                }
            } else {
                //user table Entry

                try {

                    $user = new User;
                    $user->email = isset($email) ? $email : "";
                    $user->name = isset($name) ? $name : "";
                    $user->social_token = $request->social_token;
                    $user->login_type = $request->login_type;
                    $user->role = 2;
                    $user->status = 1;
                    $user->save();

                    //User Detils Table
                    $userDetail = new User_details;
                    $userDetail->user_id = $user->id;
                    $userDetail->user_type = $request->input('user_type');
                    $userDetail->account_create_device_type = $request->input('device_type');
                    $userDetail->save();

                    // if ($request->input('device_type')) {
                    //     $checkTokenExsist = Device_info::select('id')->where('push_token', $request->input('device_token'))->count();
                    //     if ($checkTokenExsist > 0) {
                    //         $updateArray = [
                    //             'push_token' => $request->input('device_token'),
                    //         ];
                    //         Device_info::where('push_token', $request->input('device_token'))->update($updateArray);
                    //     } else {
                    //         $deviceArray = new Device_info;
                    //         $deviceArray->user_id = $user->id;
                    //         $deviceArray->push_token = $request->input('device_token');
                    //         $deviceArray->save();
                    //     }
                    // }
                    if ($request->input('device_type')) {
                        $deviceArray = new Device_info;
                        $deviceArray->user_id = $user->id;
                        $deviceArray->push_token = $request->input('device_token');
                        $deviceArray->save();
                    }

                    if (!empty($user) && !empty($userDetail)) {
                        $data['token'] = $user->createToken('Vivid')->accessToken;

                        return response()->json(['status' => true, 'data' => $data, 'message' => 'Successfully Login'], 200);
                    } else {
                        return response()->json(['status' => false, 'data' => $data, 'message' => 'Something Went Wrong'], 200);
                    }

                  } catch (\Exception $e) {
                      return response()->json(['status' => false, 'data' => $data, 'message' => "Your account already exist with $email email, so you can't use the same account in case of social login."], 200);
                  }

            }
        }
    }


    public function login(Request $request)
    {
        $rules = array(
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        );

        $messages = array(
            "email.required" => "Email is required",
            "email.email" => "Email is not valid",
            "email.exists" => "Email doesn't exists",
            'password.required' => 'Password is required',
            'device_type.required' => 'Device Type is required',
            'device_token.required' => 'Device Token is required',
        );
        $fieldNames = array(
            'email' => 'Email',
            'password' => 'Password',
            'device_type' => 'Device type',
            'device_token' => 'Device token'
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);
        $data = [];
        $data['name'] = "";
        $data['token'] = "";
        $data['profile'] = (object)[];
        if ($validator->fails()) {
            return response()->json(['status' => false, 'email_link'=>'', 'data' => $data, 'message' => $validator->messages()->first()]);
        } else {
            $checkUser = User::where("email", $request->input('email'))->first();

            if(!empty($checkUser->login_type) && $checkUser->login_type != 1)
            {
                return response()->json(['status' => false, 'email_link'=>'', 'data' => $data,'message' => "Seems like this email account is associated with social login. Please use either Facebook or Google login option."], 200);
            }

            if(!empty($checkUser->status) && $checkUser->status == 2)
            {
                return response()->json(['status' => false, 'email_link'=> $request->input('email'), 'data' => $data,'message' => "Your account has been deactivated please contact management@vivpanel.com for further assistance"], 200);
            }
            if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'role' => 2, 'login_type'=>1, 'status'=>1])) {
                $user = Auth::user();
                $data['name'] = $user->name;
                $data['token'] = $user->createToken('Vivid')->accessToken;


                $data['profile'] = $this->getPrfileData($user->id);



                // if ($request->input('device_type')) {
                //     $checkTokenExsist = Device_info::select('id')->where('push_token', $request->input('device_token'))->count();
                //     if ($checkTokenExsist > 0) {
                //         $updateArray = [
                //             'push_token' => $request->input('device_token'),
                //         ];
                //         Device_info::where('push_token', $request->input('device_token'))->update($updateArray);
                //     } else {
                //         $deviceArray = new Device_info;
                //         $deviceArray->user_id = $user->id;
                //         $deviceArray->push_token = $request->input('device_token');
                //         $deviceArray->save();
                //     }
                // }

                if ($request->input('device_type')) {
                    $deviceArray = new Device_info;
                    $deviceArray->user_id = $user->id;
                    $deviceArray->push_token = $request->input('device_token');
                    $deviceArray->save();
                }


                return response()->json(['status' => true, 'email_link'=>'', 'data' => $data, 'message' => "Successfully Login"], 200);
            } else {
                return response()->json(['status' => false, 'email_link'=>'', 'data' => $data, 'message' => "Incorrect username or password."], 200);
            }
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            //Auth::user()->AauthAcessToken()->delete();
            $accessToken = auth()->user()->token();
            $token = $request->user()->tokens->find($accessToken);
            $token->revoke();
        }
        return response()->json(['status' => true, 'message' => "Logout Successfully"], 200);
    }

    public function deleteAccount(Request $request)
    {
        if (Auth::check()) {
            $accessToken = auth()->user()->token();
            $userId = auth()->user()->id;
            $token = $request->user()->tokens->find($accessToken);
            $token->revoke();

            User::where("id", $userId)->delete();
            User_details::where("user_id", $userId)->delete();
        } else {
            return response()->json(['status' => false, 'message' => "User Not Found"], 200);
        }
        return response()->json(['status' => true, 'message' => "Account Delete Successfully"], 200);
    }


    public function changePassword(Request $request)
    {
        $userId = Auth::guard('api')->user()->id;
        $rules = array(
            'old_password' => 'required',
            'password' => 'required',
            'cpassword' => 'required|same:password',
        );

        $messages = array(
            'required' => ':attribute field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        } else {
            $user = User::find($userId);
            if (!Hash::check($request->input('old_password'), $user->password)) {
                return response()->json(['status' => false, 'message' => "Old Password didn't match.Please check the password."], 200);
            } else {
                $user->update([
                    'password' => bcrypt($request->input('password'))
                ]);
                return response()->json(['status' => true, 'message' => "Password Updated Successfully"], 200);
            }
        }
    }

    public function changeProfile(Request $request)
    {
        $userId = Auth::guard('api')->user()->id;
        $rules = array(
            'name' => 'required',
            'email' => 'required',
            'dob' => 'required',
        );

        $messages = array(
            'required' => ':attribute field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        $User = [
            'name' => $request->input('name'),
            'email' => $request->input('email')
        ];
        User::where('id', $userId)->update($User);

        $User_details = [
            'dob' => date("Y-m-d", strtotime($request->input('dob')))
        ];
        User_details::where('user_id', $userId)->update($User_details);
        return response()->json(['status' => true, 'message' => "Profile updated Successfully"], 200);
    }

    public function addAdress(Request $request)
    {
        $userId = Auth::guard('api')->user()->id;
        $rules = array(
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
        );

        $messages = array(
            'required' => ':attribute field field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        if(!empty($request->input('id')))
        {
            $getData = User_address::where("id", $request->input('id'))->get();
            if($getData->isEmpty())
            {
                return response()->json(['status' => false, 'message' => "Address Not Found!"], 200);
            }
            $data = [
                'address'=>$request->input('address'),
                'city'=>$request->input('city'),
                'state'=>$request->input('state'),
                'zip_code'=>$request->input('zip_code'),
            ];
            User_address::where("id", $request->input('id'))->update($data);
            return response()->json(['status' => true, 'message' => "Address Updated Successfully",'id'=>$request->input('id')], 200);
        }
        else
        {
            $User_address = new User_address;
            $User_address->user_id = $userId;
            $User_address->address = $request->input('address');
            $User_address->city = $request->input('city');
            $User_address->state = $request->input('state');
            $User_address->zip_code = $request->input('zip_code');
            $User_address->save();
            return response()->json(['status' => true, 'message' => "Address Added Successfully", 'id'=>(string)$User_address->id], 200);
        }

    }

    public function getAdress(Request $request)
    {
        $userId = Auth::guard('api')->user()->id;

        $User_address = User_address::select("id", "address", "city", "state", "zip_code")->where("user_id", $userId)->get();

        if($User_address->isNotEmpty())
        {
            return response()->json(['status' => true, 'data' => $User_address], 200);
        }
        else
        {
            return response()->json(['status' => false, 'data' =>(object)[]], 200);
        }

    }

    public function getNotification()
    {
        $userId = Auth::guard('api')->user()->id;
        $reposne = (object)[];

        $responseData = Notification::select('id','title','comic_id', 'description', 'is_read', 'created_at as date')->where('user_id', $userId)->orderby('id', 'DESC')->get();


        Notification::where('user_id', $userId)->where('is_read', 0)->update(array('is_read'=>1));

        if(!empty($responseData))
        $reposne = $responseData;


        if(!empty($reposne)){
            return response()->json(['status' => true, 'data' => $reposne], 200);
        } else {
            return response()->json(['status' => false, 'data' => $reposne], 200);
        }
    }


    public function deleteNotification(Request $request)
    {
        $userId = Auth::guard('api')->user()->id;

        $rules = array(
            'id' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }
        $id = $request->input('id');
        $deleteResponse = Notification::where('id', $id)->where("user_id", $userId)->delete();

        if($deleteResponse)
        {
            return response()->json(['status' => true, 'message'=>"Notification Deleted Successfully"], 200);
        }
        return response()->json(['status' => false, 'message'=>"Notification Not Found"], 200);
    }

    public function deleteAllNotification(Request $request)
    {
        $userId = Auth::guard('api')->user()->id;
        $deleteResponse = Notification::where('user_id', $userId)->delete();
        if($deleteResponse)
        {
            return response()->json(['status' => true, 'message'=>"Notification Deleted Successfully"], 200);
        }
        return response()->json(['status' => false, 'message'=>"Notification Not Found"], 200);
    }

    public function cancelSubscription(Request $request)
    {
        $rules = array(
            'sub_type' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        $userId = Auth::guard('api')->user()->id;
        $sub_type = $request->input('sub_type');

        $usd = user_subscription_details::where('user_id', $userId)->where("status", 1)->where("subscription_type", $sub_type)->where("cancel", 1)->first();

        if(empty($usd->user_id))
        {
            return response()->json(['status'=>false, 'message'=>'Subscription Not Found'], 200);
        }

        if($usd->subscription_type == 1)
        {
            $userData = [
                'subscription_id'=>$usd->subscription_id
            ];

            $css = $this->cancelSubscriptionStripe($userData);
            if(!empty($css['error']))
            {
                return response()->json(['status'=>false, 'message'=>$css['error']], 200);
            }
        }

        user_subscription_details::where('user_id', $userId)->where("status", 1)->where("subscription_type", $sub_type)->where("cancel", 1)->update(['cancel'=>2]);

        return response()->json(['status' => true, 'message'=>"Cancel Subscription Successfully"], 200);
    }
    public function cancelSubscriptionStripe($userData)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $customer = \Stripe\Subscription::update(
                $userData['subscription_id'],
                [
                  'cancel_at_period_end' => true,
                ]
              );
            $stripeResponse = $customer->jsonSerialize();
        }
        catch (\Stripe\Exception\CardException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\RateLimitException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\InvalidRequestException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\AuthenticationException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\ApiConnectionException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\ApiErrorException $e)
        {
            $error = $e->getMessage();
        }
        catch (Exception $e)
        {
            $error = $e->getMessage();
        }
        if(!empty($error))
        {
            $error=array('error' => $error);
            $stripeResponse=$error;
        }

        return $stripeResponse;
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

    public function testPush(Request $request)
    {
        $sendpush = Helper::sendPush($request->token, $request->msg,$request->title);
        print_r($sendpush);
        exit("hh");
    }

    public function iosReciptCurl($sub_data)
    {
        $postdata = json_encode(['receipt-data' => $sub_data,'password' => '274982e0399e44169b7d2bdbf835a70e']);
        $curl = curl_init();
        //CURLOPT_URL => 'https://buy.itunes.apple.com/verifyReceipt',
        curl_setopt_array($curl, array
        (
                CURLOPT_URL => 'https://sandbox.itunes.apple.com/verifyReceipt',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$postdata,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $iosReciptData = json_decode($response,true);
        return $iosReciptData;
    }

    public function subscription(Request $request)
    {
        try
        {
            $user_id = Auth::user()->id;
            $sub_data = $request['sub_data'];

            if($request->input('sub_type') == 'ios')
            {
                $iosReciptData = $this->iosReciptCurl($sub_data);
                //echo "<pre>";print_r($iosReciptData);die;

                if(!empty($iosReciptData) && $iosReciptData['status'] == 0)
                {
                    if(isset($iosReciptData['latest_receipt_info'][0]) && !empty($iosReciptData['latest_receipt_info'][0]))
                    {
                        $getSubscriptionRetrieve = $iosReciptData['latest_receipt_info'][0];

                        $cps = $this->getOnlyDate($getSubscriptionRetrieve['purchase_date']);
                        $cpe = $this->getOnlyDate($getSubscriptionRetrieve['expires_date']);
                        $interval = $getSubscriptionRetrieve['product_id'];

                        if($interval == 'com.app.vividpanel.monthly')
                        $plan_id = 1;
                        else
                        $plan_id = 2;

                        $plan = Plan::select("price")->where("id", $plan_id)->first();
                        $amount_total =  $plan->price;

                        $ud = [
                            'user_type'=>2
                        ];
                        User_details::where("user_id", $user_id)->update($ud);

                        $usd = new User_subscription_details;
                        $usd->user_id = $user_id;
                        $usd->price = $amount_total;
                        $usd->subscription_type = 2;
                        //$usd->subscription_id = $subscription;
                        $usd->current_period_start = $cps;
                        $usd->current_period_end = $cpe;
                        $usd->renewal_date = $cpe;
                        $usd->interval = $interval;
                        $usd->status = 1;
                        //$usd->receipt = $sub_data;
                        $usd->plan_id = $plan_id;

                        $usd->save();

                        // $us = User_details::where("user_id", $user_id)->whereNotNull("refer_by")->first();
                        // if(!empty($us))
                        // {
                        //     $referUserId = $us->refer_by;
                        //     $st = Setting::where("id", 1)->first();
                        //     $prTot = ($st->reseller_payout_percentage / 100) * $amount_total;
                        //     //$rest = $amount_total - $prTot;
                        //     $rest = $prTot;

                        //     DB::table('user_details')
                        //     ->where('user_id', $referUserId )
                        //     ->increment('wallet', $rest);

                        //     $wallet = new Wallet;
                        //     $wallet->user_id = $referUserId;
                        //     $wallet->customer_id = $user_id;
                        //     $wallet->total = $amount_total;
                        //     $wallet->percentage = $st->reseller_payout_percentage;
                        //     $wallet->amount = $rest;
                        //     $wallet->save();

                        //     //Mail Code
                        //     $publisher_name = Helper::getCustomerName($referUserId);
                        //     $customer_name = Helper::getCustomerName($user_id);
                        //     $pubEmail = Helper::getCustomerEmail($referUserId);
                        //     $mailData = [
                        //         'publisher_name' => $publisher_name,
                        //         'customer_name' => $customer_name,
                        //         'link'=> env('APP_URL_SERVE').'/login'
                        //     ];
                        //     Mail::to($pubEmail)->send(new PublisherReferBySubscription($mailData));
                        // }

                        Helper::freeCoinsForNewSubscription($user_id);

                        return response()->json(['status' => true, 'message' => 'Subscription Created Successfully'], 200);
                    }
                }
            }
            elseif($request->input('sub_type') == 'android')
            {
                $req = json_decode($sub_data,true);

                $applicationName = 'vivid in-app purchase';
                $scope = ['https://www.googleapis.com/auth/androidpublisher'];
                $configLocation = str_replace('public', '', env("APP_FILE_URL"))."/android-in-app.json";
                $packageName = $req['packageName'];
                $purchaseToken = $req['purchaseToken'];
                $productId = $req['productId'];

                $client = new \Google_Client();
                $client->setApplicationName($applicationName);
                $client->setAuthConfig($configLocation);
                $client->setScopes($scope);
                $validator =  new \Google\Service\AndroidPublisher($client);

                try
                {
                    $subscription = $validator->purchases_subscriptions->get($packageName,$productId,$purchaseToken);

                    $getSubscriptionRetrieve = $subscription;
                    $cps = $this->changeDateFormat($getSubscriptionRetrieve->startTimeMillis);
                    $cpe = $this->changeDateFormat($getSubscriptionRetrieve->expiryTimeMillis);
                    $amount_total = $this->getSubscriptionPrice($subscription->priceAmountMicros);

                    $interval = $productId;

                    if($interval == 'com.app.vividpanel.monthly')
                    $plan_id = 1;
                    else
                    $plan_id = 2;

                    $ud = [
                        'user_type'=>2
                    ];
                    User_details::where("user_id", $user_id)->update($ud);

                    $usd = new User_subscription_details;
                    $usd->user_id = $user_id;
                    $usd->price = $amount_total;
                    $usd->subscription_type = 3;
                    //$usd->subscription_id = $subscription;
                    $usd->current_period_start = $cps;
                    $usd->current_period_end = $cpe;
                    $usd->renewal_date = $cpe;
                    $usd->interval = $interval;
                    $usd->status = 1;
                    //$usd->receipt = $sub_data;
                    $usd->plan_id = $plan_id;

                    $usd->save();

                    // $us = User_details::where("user_id", $user_id)->whereNotNull("refer_by")->first();
                    // if(!empty($us))
                    // {
                    //     $referUserId = $us->refer_by;
                    //     $st = Setting::where("id", 1)->first();
                    //     $prTot = ($st->reseller_payout_percentage / 100) * $amount_total;
                    //    // $rest = $amount_total - $prTot;
                    //     $rest = $prTot;

                    //     DB::table('user_details')
                    //     ->where('user_id', $referUserId )
                    //     ->increment('wallet', $rest);

                    //     $wallet = new Wallet;
                    //     $wallet->user_id = $referUserId;
                    //     $wallet->customer_id = $user_id;
                    //     $wallet->total = $amount_total;
                    //     $wallet->percentage = $st->reseller_payout_percentage;
                    //     $wallet->amount = $rest;
                    //     $wallet->save();

                    //     //Mail Code
                    //     $publisher_name = Helper::getCustomerName($referUserId);
                    //     $customer_name = Helper::getCustomerName($user_id);
                    //     $pubEmail = Helper::getCustomerEmail($referUserId);
                    //     $mailData = [
                    //         'publisher_name' => $publisher_name,
                    //         'customer_name' => $customer_name,
                    //         'link'=> env('APP_URL_SERVE').'/login'
                    //     ];
                    //     Mail::to($pubEmail)->send(new PublisherReferBySubscription($mailData));
                    // }
                    Helper::freeCoinsForNewSubscription($user_id);

                    return response()->json(['status' => true, 'message' => 'Subscription Created Successfully'], 200);
                }
                catch (\Exception $e)
                {
                    return response()->json(['status' => false, 'message' =>$e->getMessage()], 200);
                }
            }
            else
            {
                return response()->json(['status' => false, 'message' =>"Subscription type not found"], 200);
            }
        }
        catch(Exception $e)
        {
            return response()->json(['status' => false, 'message' =>
            $e->getMessage()], 200);
        }
    }

    public function getOnlyDate($t)
    {
       return date('Y-m-d H:i:s',strtotime(str_replace(' Etc/GMT', '', $t)));
    }
    public function changeDateFormat($timeInMilliseconds)
    {
        return date('Y-m-d h:i:s',$timeInMilliseconds/1000);
    }
    public function getSubscriptionPrice($price)
    {
        return number_format((float)$price/1000000, 2, '.', '');
    }

    public function userSubscriptionGet()
    {
        $userId = Auth::user()->id;

        $User_subscription_details = User_subscription_details::where("user_id", $userId)
        ->orderBy("id", "DESC")
        ->first();

        $sub_data = $User_subscription_details->receipt;
        $iosReciptData = $this->iosReciptCurl($sub_data);

        if(empty($iosReciptData))
        {
            return response()->json(['status' => false,  'message' =>'Recipt not found', 'cancel' =>0], 200);
        }


        if(empty($iosReciptData['pending_renewal_info'][0]['auto_renew_status']) && $iosReciptData['pending_renewal_info'][0]['auto_renew_status'] == 0)
        {
            User_subscription_details::where("id", $User_subscription_details->id)->update(['cancel'=>2]);
        }


        $cancel = User_subscription_details::where("user_id", $userId)
        ->orderBy("id", "DESC")
        ->value('cancel');

        $cancel = $User_subscription_details->cancel;


        if(!empty($cancel))
        {
            return response()->json(['status' => true, 'message' =>'subscription found', 'cancel' =>$cancel], 200);
        }
        else
        {
            return response()->json(['status' => false,  'message' =>'subscription not found', 'cancel' =>0], 200);
        }
    }

    public function getWalletHistory(Request $request)
    {
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;
        $userId = Auth::user()->id;
        $user_wallet = DB::table('user_wallet as us')
        ->where("user_id", $userId)
        ->select("us.transaction_type","us.coins", "us.type", "us.comic as coins_title", DB::Raw('IFNULL(us.episode_name, "") as episode_name'), "us.created_at");

        $user_wallet = $user_wallet->take($offset);
        $user_wallet = $user_wallet->orderBy("id", 'DESC')
        ->paginate($limit);

        $totalCoins = Helper::getcoinsTotal($userId);

        $Coin_slab = Coin_slab::select("slabs", "coins", "android_id")->get();

        return response()->json(['status'=> true, 'data'=>$user_wallet, 'total'=>$totalCoins, 'Coin_slab'=>$Coin_slab], 200);
    }
    public function purchaseCoins(Request $request)
    {
        $rules = array(
            'type' => 'required',
            'id' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        // if($request->input('type') == 1)
        // {
        //     $t = 'android_id';
        // }
        // else
        // {
        //     $t = 'ios_id';
        // }

        $t = 'android_id';

        $userId = Auth::user()->id;
        $getCoins = Coin_slab::where($t, $request->input('id'))->value("coins");

        if (empty($getCoins)) {
            return response()->json(['status' => false, 'message' => 'coins not found']);
        }

        $user_wallet = new User_wallet;
        $user_wallet->user_id = $userId;
        $user_wallet->coins = $getCoins;
        $user_wallet->type = 1;
        $user_wallet->transaction_type = 1;
        $user_wallet->save();

        return response()->json(['status'=> true, 'message'=>'Coins Added Successfully'], 200);

    }
}
