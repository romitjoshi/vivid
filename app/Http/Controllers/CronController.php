<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Helper, Log;
use App\Models\{User, User_details, User_subscription_details};
use App\Models\admin\{PushNotification};

class CronController extends Controller
{
    public function renewSubscriptionAndroid(Request $request)
    {
        Log::debug('renewSubscriptionAndroid');
        Log::debug('request->all');
        Log::debug($request->all());

        $input = @file_get_contents('php://input');
        Log::debug('input');
        Log::debug($input);

        exit("Run");
    }
    public function renewSubscriptionIos(Request $request)
    { 
        Log::debug('renewSubscriptionIos');
        Log::debug('request->all');
        Log::debug($request->all());

        $input = @file_get_contents('php://input');
        Log::debug('input');
        Log::debug($input);

        exit("Run");
        // $subData = DB::table("user_subscription_details as usd")
        // ->select("usd.id as usd_id", "ud.id as ud_id", "usd.user_id as userId")
        // ->join("user_details as ud", "ud.user_id", "usd.user_id")
        // ->where("usd.subscription_type", 2)
        // ->get();

        // foreach($subData as $data)
        // {
        //     if(!empty($data->receipt))
        //     {
                
        //     }
        // }

        // exit("Run");  
    }
    public function renewSubscription(Request $request)
    { 
        $subscriptionArray=$request->all();

        if(!empty($subscriptionArray))
        {
            $getSubscriptionRetrieve = $subscriptionArray['data']['object'];       

            $cpe = date("Y-m-d H:i:s", $getSubscriptionRetrieve['current_period_end']);
            $cps = Date('Y-m-d H:i:s',$getSubscriptionRetrieve['current_period_start']);
            $interval = $getSubscriptionRetrieve['items']['data']['0']['plan']['interval'];
            $amount_total =  $getSubscriptionRetrieve['items']['data']['0']['plan']['amount'] / 100;

            $subscription = $getSubscriptionRetrieve['id'];
            $stripe_user_id = $getSubscriptionRetrieve['customer'];
            $status = $getSubscriptionRetrieve['status'];

            $subData = DB::table("user_subscription_details as usd")
            ->select("usd.id as usd_id", "ud._id as ud_id", "usd_user_id as userId")
            ->join("user_details as ud", "ud.user_id", "usd.user_id")
            ->where("usd.subscription_type", 1)
            ->where('ud.stripe_user_id', $stripe_user_id)
            ->get();

            foreach($subData as $data)
            {
                if($status != "active")
                {
                    $usdData = [
                        'status' => 2,
                    ];
                    User_subscription_details::where('id', $data['usd_id'])->update($usdData);

                    $udData = [
                        'user_type' => 1,
                    ];
                    User_details::where('id', $data['ud_id'])->update($udData);
                    
                }
                else
                {
                    $usdData = [
                        'status' => 2,
                    ];
                    User_subscription_details::where('id', $data['usd_id'])->update($usdData);

                    $udData = [
                        'user_type' => 2,
                    ];
                    User_details::where('id', $data['ud_id'])->update($udData);

                    $usd = new User_subscription_details;
                    $usd->user_id = $data['userId'];
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
                }
            }            
        }
        exit("Run");  
    }
    public function pushNotificationCron()
    {
        //Log::debug('Start Cron');
        $currentDateGet = date("Y-m-d H:i:s");
        $getData = PushNotification::select('push_notifications.*')
        ->where('push_notifications.status', 0)
        ->where('push_notifications.send_type', 2)
        ->where('send_datetime', '<', date('Y-m-d H:i:s', strtotime($currentDateGet)))
        ->get();

        //echo "<pre>";print_r($getData->toArray());exit;

        foreach ($getData as $value)
        {
            //Log::debug('Enter Cron');
            if($value->user == 1)
            {
                $title= $value->push_title;
                $final_msg= $value->push_description;
                $push_token = "";
                Helper::sendPushToAll($push_token, $final_msg, $title);
            }
            else
            {
                $push_conditions = json_decode($value->push_conditions);
                $makeLastData = date('Y-m-d 00:00:00', strtotime('-'.$push_conditions->inactiveDays.' days'));
                $getTokenData = User::select("id")->where("last_login_datetime", '<', $makeLastData)->get();
                foreach ($getTokenData as $cron)
                {
                    $checkTokenExsist = Device_info::where('user_id', $cron->id)->orderBy('id','desc')->first();
                    if(!empty($checkTokenExsist))
                    {
                        $title= $value->push_title;
                        $final_msg= $value->push_description;
                        $checkSend = Helper::sendPush($checkTokenExsist->push_token, $final_msg, $title);
                    } 
                }
            }
           PushNotification::where("id", $value->id)->update(["status"=>1]);
        }
       exit("Run");    
    }
}
