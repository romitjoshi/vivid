<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\{Setting,Coin_slab};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class SettingController extends Controller
{
    //
    public function index()
    {
        $getData = Setting::select('*')->first();
        $getsetting= Coin_slab::get();
        // echo "<pre>";
        // print_r($getsetting);
        // die();
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link' => "admin/settings", 'name' => "Settings"]
        ];
        return view('/content/Admin/setting', ['breadcrumbs' => $breadcrumbs,'getData'=>$getData,'getsetting'=>$getsetting]);
    }



   public function update(Request $request)
   {

        // echo "<pre>";
        // print_r($request->all());
        // die();
       $rules = array(
           'reseller_payout_percentage' => 'required',
           'free_coin_for_new_user' => 'required',
           'email' => 'required',
           'price_v_coin' => 'required',
           'new_subscription_free_coin' => 'required',
           'renewal_subscription_free_coin' => 'required',
           'slabs' =>'required',
           'coins'=> 'required',
       );
       $messages = array(
           'required' => ':attribute is required.'
       );
       $fieldNames = array(
           'reseller_payout_percentage' => 'Reseller Payout Percentage',
           'free_coin_for_new_user' => 'free_coin_for_new_user',
           'price_v_coin' => 'Price V Coin',
           'new_subscription_free_coin' => 'New Subscription Free Coin',
           'renewal_subscription_free_coin' => 'Subscription Renewal',
           'slabs' => 'Slabs',
           'coins' => 'Coins'
       );

       $validator = Validator::make($request->all(), $rules, $messages);
       $validator->setAttributeNames($fieldNames);

       if ($validator->fails())
       {
           $response['status'] = false;
           $response['message'] = ERROR;
       }
       else
       {
           $seting=Setting::find('1');
           $seting->reseller_payout_percentage = $request->reseller_payout_percentage;
           $seting->free_coin_for_new_user = $request->free_coin_for_new_user;
           $seting->email_notification = $request->email;
           $seting->price_v_coin = $request->price_v_coin;
           $seting->new_subscription_free_coin = $request->new_subscription_free_coin;
           $seting->renewal_subscription_free_coin = $request->renewal_subscription_free_coin;
           $seting->save();



           for($i = 0; $i < count($request->slabs); $i++ )
           {
                Coin_slab::where('slabs', $request->slabs[$i])->update(['coins'=>$request->coins[$i]]);
           }

           $response['status'] = true;
           $response['message'] = SETTINGUPDATE;
       }
       echo json_encode($response);
   }

}
