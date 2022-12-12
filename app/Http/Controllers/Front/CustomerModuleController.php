<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\{User, User_details, Customer_stripe_session, User_subscription_details,Cart, Payout_requests, Wallet, User_wallet};
use App\Models\admin\{Content_categories, User_comics, Plan, Setting, Coin_slab, Comics_series, Store_products};
use Log, DB,Hash,Str, Stripe;
use App\Mail\PublisherReferBySubscription;
use Helper;

class CustomerModuleController extends Controller
{
    public function test()
    {
        $pubid = User_details::where("refer_code", 'Zc6TLq')
        ->join("comics_series as cs", "cs.created_by", "user_details.user_id")
        ->join("comics_episodes as ce", "ce.comics_series_id", "cs.id")
        ->where("cs.status", 1)
        ->get();

        echo "<pre>";
        print_r($pubid->toArray());

        exit("hh");

    }
    public function myAccount()
    {
        $userId = Auth::user()->id;
        $userData = User::with('getUserDetails', 'getUserSubscriptionDetails')
        ->where('id', $userId)
        ->first();

        // echo "<pre>";
        // print_r($userData->toArray());
        // exit("hh");
        return view('content/front/my-account', ['userData'=>$userData]);
    }

    public function myOrder(Request $request)
    {
        $limit  = 20;
        $page = 1;
        $offset = ($page - 1) * $limit;

        $getOrder = DB::table("orders as o")
        ->select("o.id", "o.order_request_id", "o.created_at", "o.order_status", "o.amount")
        ->join("order_product_details as opd", "opd.order_id", "o.id")
        ->where("o.user_id", Auth::user()->id)
        ->groupBy("o.id")
        ->latest("id")
        ->take($offset)
        ->paginate($limit);
        return view('content/front/myorder',['getOrder'=>$getOrder]);
    }


    public function subscriptionPage()
    {
        $userId = Auth::user()->id;
        $userData = User::with('getUserDetails', 'getUserSubscriptionDetails')
        ->where('id', $userId)
        ->first();

        $planData = Plan::get();
        // echo "<pre>";
        // print_r($planData->toArray());
        // exit;
        return view('content/front/subscription', ['userData'=>$userData, 'planData'=>$planData]);
    }

    public function subscriptionYear(Request $request)
    {
        $userId = Auth::user()->id;
        $userDetails = user_details::where('user_id', $userId)->first();
        $params = [
            'user_id'=> $userId,
            'product'=> env("STRIPE_PRODUCT_KEYS_YEAR")
        ];
        if(!empty($userDetails->stripe_user_id))
        $params['customer'] = $userDetails->stripe_user_id;
        $getSessionCreate = $this->getSessionCreate($params);
        $data = [];
        if(!empty($getSessionCreate))
        {
            $css = new Customer_stripe_session;
            $css->user_id = $userId;
            $css->session_id = $getSessionCreate['id'];
            $css->save();
            $data = [
                'url'=>$getSessionCreate['url']
            ];
           return response()->json(['status'=>true, 'data'=>$data, 'message'=>'Success'], 200);
        }
        return response()->json(['status'=>false, 'data'=>$data, 'message'=>$getSessionCreate['error']], 200);
    }

    public function subscriptionMonth(Request $request)
    {
        $userId = Auth::user()->id;
        $userDetails = user_details::where('user_id', $userId)->first();
        $params = [
            'user_id'=> $userId,
            'product'=> env("STRIPE_PRODUCT_KEYS_MONTH")
        ];
        if(!empty($userDetails->stripe_user_id))
        $params['customer'] = $userDetails->stripe_user_id;
        $getSessionCreate = $this->getSessionCreate($params);
        $data = [];
        if(!empty($getSessionCreate))
        {
            $css = new Customer_stripe_session;
            $css->user_id = $userId;
            $css->session_id = $getSessionCreate['id'];
            $css->save();
            $data = [
                'url'=>$getSessionCreate['url']
            ];
           return response()->json(['status'=>true, 'data'=>$data, 'message'=>'Success'], 200);
        }
        return response()->json(['status'=>false, 'data'=>$data, 'message'=>$getSessionCreate['error']], 200);
    }

    public function cancelSubscription(Request $request)
    {
           $userId = Auth::user()->id;
           $usd = user_subscription_details::where('user_id', $userId)->where("status", 1)->where("subscription_type", 1)->first();

           if(empty($usd->subscription_id))
           {
            return response()->json(['status'=>false, 'message'=>'Subscription Not Found'], 200);
           }

           $userData = [
                'subscription_id'=>$usd->subscription_id
           ];

           $css = $this->cancelSubscriptionStripe($userData);
           if(!empty($css['error']))
           {
                return response()->json(['status'=>false, 'message'=>$css['error']], 200);
           }

           user_subscription_details::where('subscription_id', $usd->subscription_id)->where('user_id', $userId)->update(['cancel'=>2]);

           return redirect('customer/my-account');

    }

    public function reactiveCancelSubscription(Request $request)
    {
           $userId = Auth::user()->id;
           $usd = user_subscription_details::where('user_id', $userId)->where("cancel", 2)->first();

           if(empty($usd->subscription_id))
           {
            return response()->json(['status'=>false, 'message'=>'Subscription Not Found'], 200);
           }

           $userData = [
                'subscription_id'=>$usd->subscription_id
           ];

           $css = $this->reactiveCancelSubscriptionStripe($userData);
           if(!empty($css['error']))
           {
                return response()->json(['status'=>false, 'message'=>$css['error']], 200);
           }

           user_subscription_details::where('subscription_id', $usd->subscription_id)->where('user_id', $userId)->update(['cancel'=>1]);

           return redirect('customer/my-account');

    }

    public function reactiveCancelSubscriptionStripe($userData)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $customer = \Stripe\Subscription::retrieve($userData['subscription_id']);
            \Stripe\Subscription::update($userData['subscription_id'], [
              'cancel_at_period_end' => false,
              'proration_behavior' => 'create_prorations',
              'items' => [
                [
                  'id' => $customer->items->data[0]->id,
                ],
              ],
            ]);
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

    public function subscriptionSuccess($id)
    {
        $css = Customer_stripe_session::where('user_id', $id)->latest('id')->first();
        $user_id = $css->user_id;
        $session_id = $css->session_id;
        $params = [
            'session_id'=>$session_id
        ];
        $getSessionRetrieve = $this->getSessionRetrieve($params);
        $amount_total =  $getSessionRetrieve['amount_total'] / 100;
        $customer = $getSessionRetrieve['customer'];
        //$payment_status =  $getSessionRetrieve['payment_status'];
        $status = $getSessionRetrieve['status'];
        $subscription =  $getSessionRetrieve['subscription'];

        $params = [
            'subscription'=>$subscription
        ];
        $getSubscriptionRetrieve = $this->getSubscriptionRetrieve($params);

        $dpm = $getSubscriptionRetrieve['default_payment_method'];
        $cps = Date('Y-m-d H:i:s',$getSubscriptionRetrieve['current_period_start']);
        $cpe = Date('Y-m-d H:i:s',$getSubscriptionRetrieve['current_period_end']);
        $interval = $getSubscriptionRetrieve['items']['data']['0']['plan']['interval'];
        //$items_id = $subscriptions_detail['items']['data']['0']['id'];

        if($status == 'complete')
        {
            $ud = [
                'user_type'=>2,
                'stripe_user_id'=>$customer,
            ];
            User_details::where("user_id", $user_id)->update($ud);
            $usd = new User_subscription_details;
            $usd->user_id = $user_id;
            $usd->price = $amount_total;
            $usd->subscription_type = 1;
            $usd->subscription_id = $subscription;
            $usd->current_period_start = $cps;
            $usd->current_period_end = $cpe;
            $usd->renewal_date = $cpe;
            $usd->interval = $interval;
            $usd->status = 1;
            if($interval == 'month')
            $usd->plan_id = 1;
            else
            $usd->plan_id = 2;
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

            //     //Email Code
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
        }
        if(!empty($_COOKIE['askLogin']))
        {
            return redirect($_COOKIE['askLogin']);
        }
        return redirect('customer/my-account');

    }

    public function addPaymentMethod(Request $request)
    {
        $cardname = $request->input('cardname');
        $stripeToken = $request->input('stripeToken');

        $params = [
            'cardname'=>$cardname,
            'stripeToken'=>$stripeToken
        ];

        // $createStripePaymentMethod = $this->createStripePaymentMethod($params);
        // if(!empty($createStripePaymentMethod['error']))
        // {
        //     return response()->json(['status'=> false, 'message'=>$createStripePaymentMethod['error']], 400);
        // }

        $userId = Auth::user()->id;
        $getUser = User::with('getUserDetails')->where("id", $userId)->first();

        $stripe_user_id = $getUser->getUserDetails->stripe_user_id ?? '';
        if(empty($getUser->getUserDetails->stripe_user_id))
        {
            $userData = ['email'=>$getUser->email];
            $stripeCustomer = $this->stripeCustomer($userData);
            if(!empty($stripeCustomer['error']))
            {
                return response()->json(['status'=> false, 'message'=>$stripeCustomer['error']], 200);
            }
            $stripe_user_id = $stripeCustomer['id'];
            User_details::where("user_id", $userId)->update(['stripe_user_id'=>$stripe_user_id]);
        }

        $param = [
            'stripe_user_id'=>$stripe_user_id,
            'stripeToken'=>$stripeToken
        ];

        $addCard = $this->addCard($param);
        if(!empty($addCard['error']))
        {
            return response()->json(['status'=> false, 'message'=>$addCard['error']], 200);
        }

        return response()->json(['status'=> true, 'message'=>"Add card successfully"], 200);

    }

    public function stripeCustomer($userData)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $customer = \Stripe\Customer::create(
                [
                    "email" => $userData['email']
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

    public function addCard($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $resData = $stripe->customers->createSource(
                $params['stripe_user_id'],
                ['source' => $params['stripeToken']]
              );
            $stripeResponse = $resData->jsonSerialize();
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
    public function attechPaymentMethod($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $resData =  $stripe->paymentMethods->attach(
                    $params['pm'],
                    ['customer' => $params['stripe_user_id']]
                );
            $stripeResponse = $resData->jsonSerialize();
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

    public function createStripePaymentMethod($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $resData =  $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                  'token' => $params['stripeToken'],
                ],
            ]);
            $stripeResponse = $resData->jsonSerialize();
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

    public function getSubscriptionRetrieve($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $resData =  $stripe->subscriptions->retrieve($params['subscription'],[]);
            $stripeResponse = $resData->jsonSerialize();
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

    public function getSessionRetrieve($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $resData =  $stripe->checkout->sessions->retrieve($params['session_id'],[]);

            $stripeResponse = $resData->jsonSerialize();
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

    public function getSessionCreate($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            if(!empty($params['customer']))
            {
                //exit("yes");
                $resData =  $stripe->checkout->sessions->create([
                    'success_url' => url('subscription-success/'.$params['user_id']),
                    'cancel_url' => url('subscription-cancel'),
                    'customer' =>$params['customer'],
                    'line_items' => [
                      [
                        'price' => $params['product'],
                        'quantity' => 1,
                      ],
                    ],
                    'mode' => 'subscription',
                ]);
            }
            else
            {
                //exit("no");
                $resData =  $stripe->checkout->sessions->create([
                    'success_url' => url('subscription-success/'.$params['user_id']),
                    'cancel_url' => url('subscription-page'),
                    'line_items' => [
                      [
                        'price' => $params['product'],
                        'quantity' => 1,
                      ],
                    ],
                    'mode' => 'subscription',
                ]);
            }

            $stripeResponse = $resData->jsonSerialize();
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

    public function myLibrary(Request $request)
    {
        $cc = Content_categories::where('status', 1)->get();
        return view('content/front/my-library', ['cc'=>$cc]);
    }

    public function getLibrary(Request $request)
    {
        $userLoginCheck = false;
        $ud = [];
        if(!empty(Auth::user()->id)){
            $userLoginCheck = true;
            $ud = User_details::where('user_id',Auth::user()->id)->first();
        }
        $userId = Auth::user()->id;
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;

        $getLibrary = DB::table("user_comics as uc")
        ->select("cs.id","cs.name","cs.access_type", "cs.featured_image","cs.description","cs.slug",DB::raw('IFNULL(`cr`.`ratings`,"0.00") AS ratings'))
        ->join("comics_series as cs", "cs.id", "uc.comic_id")
        ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
        ->leftjoin("comic_ratings as cr", "cr.comic_id", "cs.id")
        ->where("cs.status", 1)
        ->where("uc.user_id", $userId)
        ->take($offset);

        if(!empty($request->input('category')) && $request->input('category') != 0)
        {
            $getLibrary = $getLibrary->whereIn("ccm.category_id", $request->input('category'));
        }

        $getLibrary = $getLibrary->groupby('cs.id');
        $getLibrary = $getLibrary
        ->take($limit)
        ->skip($offset)->get()->map(function ($getLibrary) {
            $getLibrary->name =$getLibrary->name;
            return $getLibrary;
        });
        if(!empty($getLibrary))
        {
            return response()->json(['status'=> true, 'data'=>$getLibrary,'userLoginCheck'=>$userLoginCheck, 'ud'=>$ud], 200);
        }

        return response()->json(['status'=> false, 'data'=>(object)[]], 200);
    }

    public function deleteLibrary(Request $request)
    {
        $rules = array(
            'comic_id' => 'required'
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $comic_id = $request->input('comic_id');
        $userId = Auth::user()->id;


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        $cs = User_comics::where("comic_id", $comic_id)->where("user_id", $userId)->get();
        if($cs->isEmpty())
        {
            return response()->json(['status' => false, 'message' => "Library Not Found"], 200);
        }

        User_comics::where("comic_id", $comic_id)->where("user_id", $userId)->delete();

        return response()->json(['status'=> true, 'message'=>"Library deleted successfully"], 200);
    }

    public function addToCart(Request $request)
    {
        //print_r($request->all());exit;
        $userId = Auth::user()->id;
        $productid = $request->id;
        $getCart = Cart::where("user_id", $userId)->where('product_id', $productid)->get();
        $url = env('APP_URL_SERVE').'/customer/buy-now';
        if($getCart->isNotEmpty())
        {
            return response()->json(['status'=> false, 'message'=>'Product already added to cart', 'url'=>$url], 200);
        }

        $cart = new Cart;
        $cart->user_id = $userId;
        $cart->product_id = $productid;
        $cart->qty = intval($cart->qty ?? 0) + 1;

        if(!empty($request->comic_arrange_series_id))
        {         
            $comic_series_id = [];
            $comic_series_id = json_encode($request->comic_arrange_series_id);
            $cart->comic_series_id = $comic_series_id;
            $cart->custom_title_name = $request->custom_title_name;

        }

        $cart->save();

        $countCart=Cart::where('user_id', $userId)->count();
        //Helper::countCart($userId,$productid);
        
        if(!empty($countCart))
        {
            return response()->json(['status'=> true, 'count'=>$countCart, 'message'=>'Product added to cart successfully', 'url'=>$url], 200);
        }
        return response()->json(['status'=> false, 'count'=>0, 'message'=>'Error', 'url'=>$url], 200);
    }

    public function updateProfile(Request $request)
    {
        $userId = Auth::user()->id;
        $login_type = Auth::user()->login_type;
        $rules = array(
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255'],
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
            'name' => $request->input('name')
        ];

        if($login_type == 1)
        {
            $User['email'] = $request->input('email');
        }

        User::where('id', $userId)->update($User);

        $User_details = [
            'dob' => date("Y-m-d", strtotime($request->input('dob')))
        ];
        User_details::where('user_id', $userId)->update($User_details);
        return response()->json(['status' => true, 'message' => "Profile updated Successfully"], 200);
    }


    public function changeProfilePassword(Request $request)
    {
       // die('aaa');
        $userId = Auth::user()->id;
        //echo $userId;die('a');
        $rules = array(
            'old_password' => 'required',
            'password' => 'required',
            'cpassword' => 'required|same:password',
        );

        $messages = array(
            'required' => ':attribute field is required.'
        );


        $fieldNames = array(
            'old_password' => 'Old password',
            'password' => 'Password',
            'cpassword' => 'Confirm password',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);
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


    public function purchaseCoins(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());exit;

        $price = $request->input('price');
        $cardname = $request->input('cardname');
        $card_id = $request->input('card_id');
        $stripeToken = $request->input('stripeToken');

        $userId = Auth::user()->id;

        $getUser = User::with('getUserDetails')->where("id", $userId)->first();

        $stripe_user_id = $getUser->getUserDetails->stripe_user_id ?? '';
        if(empty($getUser->getUserDetails->stripe_user_id))
        {
            $userData = ['email'=>$getUser->email];
            $stripeCustomer = $this->stripeCustomer($userData);
            if(!empty($stripeCustomer['error']))
            {
                return response()->json(['status'=> false, 'message'=>$stripeCustomer['error']], 200);
            }
            $stripe_user_id = $stripeCustomer['id'];
            User_details::where("user_id", $userId)->update(['stripe_user_id'=>$stripe_user_id]);
        }

        if(!empty($stripeToken))
        {
            $param = [
                'stripe_user_id'=>$stripe_user_id,
                'stripeToken'=>$stripeToken
            ];
    
            $addCard = $this->addCard($param);
            if(!empty($addCard['error']))
            {
                return response()->json(['status'=> false, 'message'=>$addCard['error']], 200);
            }
        }        

        $stripeAmount = $price * 100;
        $params = [
            'card_id'=>$card_id,
            'stripe_user_id'=>$stripe_user_id,
            'amount'=>$stripeAmount
        ];

        $responseCharge = $this->chargeCustomer($params);

        if(!empty($responseCharge['error']))
        {
            return response()->json(['status' => false, 'message' => $responseCharge['error']], 200);
        }

        $coins = Coin_slab::where("slabs", $price)->value("coins");

        $user_wallet = new User_wallet;
        $user_wallet->user_id = $userId;
        $user_wallet->coins = $coins;
        $user_wallet->type = 1;
        $user_wallet->transaction_type = 1;
        $user_wallet->save();

        $url = '';
        if(!empty($_COOKIE['askLogin']))
        {
            $url = $_COOKIE['askLogin'];
        }

        //echo $url;exit("g");

        return response()->json(['status'=> true, "url"=>$url, 'message'=>'Coins Added Successfully'], 200);
        
    }

    public function chargeCustomer($params)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $error="";
        $stripeResponse = "";
        try
        {
            $charge = \Stripe\Charge::create
            (
                array
                (
                    'card'=> $params['card_id'],
                    'customer' => $params['stripe_user_id'],
                    'amount' => $params['amount'],
                    'currency' => "gbp"
                )
            );
            $stripeResponse = $charge->jsonSerialize();
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

    public function selectedComicget(Request $request)
    {
        $st = Store_products::where("id", $request->product_id)->first();
        if(empty($request->comic_series_id) || count($request->comic_series_id) < $st->min_comics || count($request->comic_series_id) > $st->max_comics)
        {
            return response(['status'=>false, 'message'=>"Select minimum of $st->min_comics and maximum of $st->max_comics stories"], 200);
        }
        
        $decodeId = $request->comic_series_id;     
        $stringId = implode(",", $decodeId);   
        $getComic = Comics_series::select("id", "featured_image", "name")
        ->whereIn("id", $decodeId)
        ->orderBy(DB::raw("FIELD(ID, $stringId)"))
        ->get(); 
        return response(['status'=>true, 'data'=>$getComic], 200);
    }
}
