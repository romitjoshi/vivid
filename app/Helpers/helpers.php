<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\admin\{Comics_series,Content_categories,Store_products,Notification,Comics_episodes,Setting,Comics_categories_mapping};
use App\Models\{User,Cart,User_details,User_wallet};
use Hash, Log,Image,Storage,Imagick,DB;
use Carbon\Carbon;
class Helper
{
    public static function applClasses()
    {
        // default data array
        $DefaultData = [
            'mainLayoutType' => 'vertical',
            'theme' => 'light',
            'sidebarCollapsed' => false,
            'navbarColor' => '',
            'horizontalMenuType' => 'floating',
            'verticalMenuNavbarType' => 'floating',
            'footerType' => 'static', //footer
            'layoutWidth' => 'boxed',
            'showMenu' => true,
            'bodyClass' => '',
            'pageClass' => '',
            'pageHeader' => true,
            'contentLayout' => 'default',
            'blankPage' => false,
            'defaultLanguage' => 'en',
            'direction' => env('MIX_CONTENT_DIRECTION', 'ltr'),
        ];

        // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
        $data = array_merge($DefaultData, config('custom.custom'));

        // All options available in the template
        $allOptions = [
            'mainLayoutType' => array('vertical', 'horizontal'),
            'theme' => array('light' => 'light', 'dark' => 'dark-layout', 'bordered' => 'bordered-layout', 'semi-dark' => 'semi-dark-layout'),
            'sidebarCollapsed' => array(true, false),
            'showMenu' => array(true, false),
            'layoutWidth' => array('full', 'boxed'),
            'navbarColor' => array('bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'),
            'horizontalMenuType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky'),
            'horizontalMenuClass' => array('static' => '', 'sticky' => 'fixed-top', 'floating' => 'floating-nav'),
            'verticalMenuNavbarType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky', 'hidden' => 'navbar-hidden'),
            'navbarClass' => array('floating' => 'floating-nav', 'static' => 'navbar-static-top', 'sticky' => 'fixed-top', 'hidden' => 'd-none'),
            'footerType' => array('static' => 'footer-static', 'sticky' => 'footer-fixed', 'hidden' => 'footer-hidden'),
            'pageHeader' => array(true, false),
            'contentLayout' => array('default', 'content-left-sidebar', 'content-right-sidebar', 'content-detached-left-sidebar', 'content-detached-right-sidebar'),
            'blankPage' => array(false, true),
            'sidebarPositionClass' => array('content-left-sidebar' => 'sidebar-left', 'content-right-sidebar' => 'sidebar-right', 'content-detached-left-sidebar' => 'sidebar-detached sidebar-left', 'content-detached-right-sidebar' => 'sidebar-detached sidebar-right', 'default' => 'default-sidebar-position'),
            'contentsidebarClass' => array('content-left-sidebar' => 'content-right', 'content-right-sidebar' => 'content-left', 'content-detached-left-sidebar' => 'content-detached content-right', 'content-detached-right-sidebar' => 'content-detached content-left', 'default' => 'default-sidebar'),
            'defaultLanguage' => array('en' => 'en', 'fr' => 'fr', 'de' => 'de', 'pt' => 'pt'),
            'direction' => array('ltr', 'rtl'),
        ];

        //if mainLayoutType value empty or not match with default options in custom.php config file then set a default value
        foreach ($allOptions as $key => $value) {
            if (array_key_exists($key, $DefaultData)) {
                if (gettype($DefaultData[$key]) === gettype($data[$key])) {
                    // data key should be string
                    if (is_string($data[$key])) {
                        // data key should not be empty
                        if (isset($data[$key]) && $data[$key] !== null) {
                            // data key should not be exist inside allOptions array's sub array
                            if (!array_key_exists($data[$key], $value)) {
                                // ensure that passed value should be match with any of allOptions array value
                                $result = array_search($data[$key], $value, 'strict');
                                if (empty($result) && $result !== 0) {
                                    $data[$key] = $DefaultData[$key];
                                }
                            }
                        } else {
                            // if data key not set or
                            $data[$key] = $DefaultData[$key];
                        }
                    }
                } else {
                    $data[$key] = $DefaultData[$key];
                }
            }
        }

        //layout classes
        $layoutClasses = [
            'theme' => $data['theme'],
            'layoutTheme' => $allOptions['theme'][$data['theme']],
            'sidebarCollapsed' => $data['sidebarCollapsed'],
            'showMenu' => $data['showMenu'],
            'layoutWidth' => $data['layoutWidth'],
            'verticalMenuNavbarType' => $allOptions['verticalMenuNavbarType'][$data['verticalMenuNavbarType']],
            'navbarClass' => $allOptions['navbarClass'][$data['verticalMenuNavbarType']],
            'navbarColor' => $data['navbarColor'],
            'horizontalMenuType' => $allOptions['horizontalMenuType'][$data['horizontalMenuType']],
            'horizontalMenuClass' => $allOptions['horizontalMenuClass'][$data['horizontalMenuType']],
            'footerType' => $allOptions['footerType'][$data['footerType']],
            'sidebarClass' => '',
            'bodyClass' => $data['bodyClass'],
            'pageClass' => $data['pageClass'],
            'pageHeader' => $data['pageHeader'],
            'blankPage' => $data['blankPage'],
            'blankPageClass' => '',
            'contentLayout' => $data['contentLayout'],
            'sidebarPositionClass' => $allOptions['sidebarPositionClass'][$data['contentLayout']],
            'contentsidebarClass' => $allOptions['contentsidebarClass'][$data['contentLayout']],
            'mainLayoutType' => $data['mainLayoutType'],
            'defaultLanguage' => $allOptions['defaultLanguage'][$data['defaultLanguage']],
            'direction' => $data['direction'],
        ];
        // set default language if session hasn't locale value the set default language
        if (!session()->has('locale')) {
            app()->setLocale($layoutClasses['defaultLanguage']);
        }

        // sidebar Collapsed
        if ($layoutClasses['sidebarCollapsed'] == 'true') {
            $layoutClasses['sidebarClass'] = "menu-collapsed";
        }

        // blank page class
        if ($layoutClasses['blankPage'] == 'true') {
            $layoutClasses['blankPageClass'] = "blank-page";
        }

        return $layoutClasses;
    }

    public static function getPendingComicAndEpisode()
    {
        $cs = Comics_series::where("approve", 0)->count();
        $ce = Comics_episodes::where("approve", 0)->count();
        return $cs + $ce;
    }
    public static function getPendingComic()
    {
        $count = Comics_series::where("approve", 0)->count();
        return $count;
    }
    public static function getPendingEpisode()
    {
        $count = Comics_episodes::where("approve", 0)->count();
        return $count;
    }
    public static function getPendingPublisher()
    {
        $count = User::where("is_approve", 0)->where("role", 3)->count();
        return $count;
    }
    public static function getCustomerName($id)
    {
        $name = User::where("id", $id)->value("name");
        return $name;
    }
    public static function getCustomerType($id)
    {
        $user_type = User_details::where("user_id", $id)->value("user_type");
        return $user_type;
    }
    public static function getCustomerEmail($id)
    {
        $email = User::where("id", $id)->value("email");
        return $email;
    }

    public static function redirectPage()
    {
        $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

        //do something with this information
        // if( $iPod || $iPhone ){

        //     $url = 'https://www.apple.com/in/app-store/';
        //     return redirect('');
        // }else if($iPad){

        //     $url = 'https://www.apple.com/in/app-store/';

        // }else if($Android){

        //     $url = 'https://play.google.com/store/apps?utm_source=apac_med&utm_medium=hasem&utm_content=Oct0121&utm_campaign=Evergreen&pcampaignid=MKT-EDR-apac-in-1003227-med-hasem-ap-Evergreen-Oct0121-Text_Search_BKWS-BKWS%7cONSEM_kwid_43700065162604516_creativeid_480912223170_device_c&gclid=CjwKCAjw7p6aBhBiEiwA83fGuuG1vs8qd_AsPEJF4kd1z1iYYJF7FspRTxhQpvKZ72U4LQrBynCAbhoCvX8QAvD_BwE&gclsrc=aw.ds';
        // }else{

        //     $url = env("APP_URL_SERVE").'/publisher';

        // }

        $url = env("APP_URL_SERVE").'/publisher';

        return $url;
    }

    public static function getComicIdByslug($slug)
    {
        $id = Comics_series::where("slug", $slug)->value("id");
        return $id;
    }

    public static function getGenersIdByslug($slug)
    {
        $id = Content_categories::where("slug", $slug)->value("id");
        return $id;
    }

    public static function getPublisherIdByslug($slug)
    {
        $id = User_details::where("slug", $slug)->value("user_id");
        return $id;
    }

    public static function getProductIdByslug($slug)
    {
        $id = Store_products::where("slug", $slug)->value("id");
        return $id;
    }

    public static function makeSlug($table, $title)
    {
        $businessName = $title;
        $businessNameURL = Str::slug($businessName, '-');
        $checkSlug = DB::table( $table )->whereSlug($businessNameURL)->exists();
        if($checkSlug){
        $numericalPrefix = 1;

        while(1){
            $newSlug = $businessNameURL."-".$numericalPrefix++;
            $newSlug = Str::slug($newSlug);
            $checkSlug = DB::table( $table )->whereSlug($newSlug)->exists();
            if(!$checkSlug){
                $slug = $newSlug;
                break;
            }
        }

        }else{
            $slug = $businessNameURL;
        }

        return $slug;
    }

    public static function generateRandomString($length = 24) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.' . $demo . '.' . $config, $val);
                }
            }
        }
    }

    //New Added Code
    public static function getPublisherInfo($userId)
    {
        $publisher = User::select("users.id", "users.name", "users.image", "ud.slug")
        ->join("user_details as ud", "ud.user_id", "users.id")
        ->where("users.id", $userId)->get()
        ->map(function($data) {
            if ( ! $data->image) {
                $data->image = env("APP_URL_SERVE").'/images/avatars/userdummy.png';
            }else
            {
                $data->image = env("IMAGE_PATH_medium").$data->image;
            }
            return $data;
        });

        return $publisher;
    }

    public static function accessType($status)
    {
        $badge = '';
        if($status == 3)
        {
            $badge = '<span class="badge badge-light-success">Original</span>';

        }
        else if($status == 2)
        {
            $badge = '<span class="badge badge-light-primary">Premium</span>';
        }
        else
        {
            $badge = '<span class="badge badge-light-danger">Free</span>';
        }
        return $badge;
    }

    public static function getcoinsTotal($userId)
    {
        $credit = User_wallet::where("user_id", $userId)->where("transaction_type", 1)->sum("coins");

        $debit = User_wallet::where("user_id", $userId)->where("transaction_type", 2)->sum("coins");

        $totalCoins = $credit - $debit;

        // if($totalCoins < 0)
        // {
        //     $totalCoins = 0;
        // }
        // else
        // {
        //     $totalCoins = $totalCoins;
        // }

        return $totalCoins;
    }
    public static function transactionType($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge bg-primary">Credit</span>';
        }
        else
        {
            $badge = '<span class="badge bg-success">Debit</span>';
        }
        return $badge;
    }
    public static function coinsType($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge bg-primary">Purchase</span>';
        }
        else
        {
            $badge = '<span class="badge bg-success">Free Coins</span>';
        }
        return $badge;
    }

    public static function statusget($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge bg-light-success">Active</span>';
        }
        else if($status == 0)
        {
            $badge = '<span class="badge bg-light-danger">Inactive</span>';
        }
        return $badge;
    }

    public static function statusgetFront($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge bg-success">Active</span>';
        }
        else if($status == 0)
        {
            $badge = '<span class="badge bg-danger">Inactive</span>';
        }
        return $badge;
    }


    public static function glowbadge($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge badge-glow bg-success">Active</span>';
        }
        else if($status == 2)
        {
            $badge = '<span class="badge badge-glow bg-danger">Inactive</span>';
        }
        return $badge;
    }
    public static function subscriptionType($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge badge-light-success">Web</span>';
        }
        else if($status == 2)
        {
            $badge = '<span class="badge badge-light-warning">Ios</span>';
        }
        else if($status == 3)
        {
            $badge = '<span class="badge badge-light-primary">Android</span>';
        }
        return $badge;
    }

    public static function orderStatus($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge badge-light-warning">Pending Payment</span>';
        }
        else if($status == 2)
        {
            $badge = '<span class="badge badge-light-warning">Refunded</span>';
        }
        else if($status == 3)
        {
            $badge = '<span class="badge badge-light-info">Processing</span>';
        }
        else if($status == 4)
        {
            $badge = '<span class="badge badge-light-warning">On Hold</span>';
        }
        else if($status == 5)
        {
            $badge = '<span class="badge badge-light-danger">Cancelled</span>';
        }
        else if($status == 6)
        {
            $badge = '<span class="badge badge-light-success">Completed</span>';
        }
        else if($status == 7)
        {
            $badge = '<span class="badge badge-light-primary">Failed</span>';
        }

        echo $badge;
    }

    public static function statusPayout($status)
    {
        $badge = '';
        if($status == 0)
            {
                $badge = '<span class="badge badge-light-warning">Pending</span>';
            }
        else if($status == 1)
            {
                $badge = '<span class="badge badge-light-success">Completed</span>';
            }
        else if($status == 2)
            {
                $badge = '<span class="badge badge-light-danger">Cancelled</span>';
            }
        return $badge;
    }
    public static function statusPayoutFront($status)
    {
        $badge = '';
        if($status == 0)
            {
                $badge = '<span class="badge badge-light-warning">Pending</span>';
            }
        else if($status == 1)
            {
                $badge = '<span class="badge badge-light-success">Completed</span>';
            }
        else if($status == 2)
            {
                $badge = '<span class="badge badge-light-danger">Cancelled</span>';
            }
        echo $badge;
    }
    public static function lightbadge($status)
    {
        $badge = '';
        if($status == 1)
        {
            $badge = '<span class="badge badge-light-success">Active</span>';
        }
        else if($status == 2)
        {
            $badge = '<span class="badge badge-light-danger">Inactive</span>';
        }
        return $badge;
    }

    public static function getAdminEmail()
    {
        $email_notification = Setting::where("id", 1)->value("email_notification");
        return $email_notification;
    }

    public static function isFeatured($status)
    {
        $isFeatured = '';
        if($status == 1)
        {
            $isFeatured = '<span class="badge badge-light-success">No</span>';
        }
        else if($status == 2)
        {
            $isFeatured = '<span class="badge badge-light-success">Yes</span>';
        }
        return $isFeatured;
    }
    public static function planType($status)
    {
        $planType = '';
        if($status == 1)
        {
            $planType = '<span class="badge badge-light-success">Monthly</span>';
        }
        else if($status == 2)
        {
            $planType = '<span class="badge badge-light-success">Yearly</span>';
        }
        return $planType;
    }

    public static function planUser($status)
    {
        $planType = '';
        if($status == 1)
        {
            $planType = '<span class="badge badge-light-danger">Free</span>';
        }
        else if($status == 2)
        {
            $planType = '<span class="badge badge-light-success">Paid</span>';
        }
        return $planType;
    }

    public static function approveStatus($status)
    {
        $planType = '';
        if($status == 0)
        {
            $planType = '<span class="badge badge-light-danger">Unapproved</span>';
        }
        else if($status == 1)
        {
            $planType = '<span class="badge badge-light-success">Approved</span>';
        }
        return $planType;
    }
    public static function approveStatusFront($status)
    {
        $planType = '';
        if($status == 0)
        {
            $planType = '<span class="badge bg-danger">Unapproved</span>';
        }
        else if($status == 1)
        {
            $planType = '<span class="badge bg-success">Approved</span>';
        }
        return $planType;
    }

    public static function actionBtn()
    {
        $action = '<a href="#" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><i data-feather="edit"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i data-feather="trash-2"></i></a>';

        return $action;
    }
    public static function get_words($sentence, $count = 10) {
        preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
        return $matches[0];
    }
    public static function get_name($sentence, $count = 1) {
        preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
        return $matches[0];
    }
    public static function makeCurrency($currency=0) {
        $a = $currency;
        $b = str_replace( ',', '', $a );

        if( is_numeric( $b ) ) {
            $a = $b;
        }
        return CURRENCYSYMBOL.number_format($a, 2);
    }

    public static function makeCurrencyWithoutSymbol($currency=0) {
        $a = $currency;
        $b = str_replace( ',', '', $a );

        if( is_numeric( $b ) ) {
            $a = $b;
        }
        return number_format($a, 2);
    }

    public static function freeCoinsForFreeUser($user_id)
    {
        $free_coin_for_new_user = Setting::where("id", 1)->value("free_coin_for_new_user");
        return $free_coin_for_new_user;
    }
    public static function freeCoinsForNewSubscription($user_id)
    {
        $new_subscription_free_coin = Setting::where("id", 1)->value("new_subscription_free_coin");

        $user_wallet = new User_wallet;
        $user_wallet->user_id = $user_id;
        $user_wallet->coins = $new_subscription_free_coin;
        $user_wallet->type = 2;
        $user_wallet->comic = "Free Coins";
        $user_wallet->transaction_type = 1;
        $user_wallet->save();

        return true;
    }
    public static function freeCoinsForRenewalSubscription($user_id)
    {
        $renewal_subscription_free_coin = Setting::where("id", 1)->value("renewal_subscription_free_coin");
        return $renewal_subscription_free_coin;
    }

    public static function imageupload($image){

        $fileName = Storage::disk('do_spaces')->put('public/images/orignal', $image, 'public');

        $fileName = str_replace('public/images/orignal/','',$fileName);

        $img = Image::make($image->getRealPath());
        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk('do_spaces')->put('public/images/thumbnail'.'/'.$fileName, $img, 'public');


        $img = Image::make($image->getRealPath());
        $img->resize(200, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk('do_spaces')->put('public/images/medium'.'/'.$fileName, $img, 'public');


        $img = Image::make($image->getRealPath());
        $img->resize(800, 1200, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk('do_spaces')->put('public/images/large'.'/'.$fileName, $img, 'public');

        return $fileName;
    }

    public static function audioupload($fileName){

        $audio_file = Storage::disk('do_spaces')->put('public/audio', $fileName, 'public');

        return str_replace('public/audio/','',$audio_file);
    }

    public static function pdfupload($fileName){

        $pdf_file = Storage::disk('do_spaces')->put('public/pdf', $fileName, 'public');

        return str_replace('public/pdf/','',$pdf_file);
    }

    public static function sendPush($token,$message, $title, $comic_id=1)
    {
        if(FIREBASE_KEY != "")
        {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $fields = array(
                'to'=> $token,
                'notification' =>  array('body'=> $message,'title'=>$title,'sound'=>'default','priority' => 10),
                'data'=>array('comic_id' => $comic_id)
            );

            $headers = array(
                'Authorization: key='.FIREBASE_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL,$url);
            curl_setopt( $ch,CURLOPT_POST,true);
            curl_setopt( $ch,CURLOPT_HTTPHEADER,$headers);
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt( $ch,CURLOPT_POSTFIELDS,json_encode($fields));
            $result = curl_exec($ch);

            //print_r($result);exit;
            curl_close($ch);

            return $result;
        }
    }
    public static function sendPushToAll($token,$message, $title)
    {
        if(FIREBASE_KEY != "")
        {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $fields = array
            (
                'to'=> '/topics/vivid_panel',
                'notification' =>  array('body'=> $message,'title'=>$title),
                'priority' => 10
            );
            $headers = array(
                'Authorization: key='.FIREBASE_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL,$url);
            curl_setopt( $ch,CURLOPT_POST,true);
            curl_setopt( $ch,CURLOPT_HTTPHEADER,$headers);
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt( $ch,CURLOPT_POSTFIELDS,json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
           //echo "<pre>";print_r($result);die;
        }

    }

    public static function seveNotification($user_id,$message,$title,$comic_id=0)
    {
        $notification = new Notification;
        $notification->user_id = $user_id;
        $notification->title = $title;
        $notification->description = $message;
        $notification->comic_id = $comic_id;
        $notification->save();
    }
    public static function countCart($userId)
    {
        $countCart=Cart::where('user_id', $userId)->count();
        return $countCart;
    }
    public static function getNotificationCount($userId)
    {
        $count=Notification::where('user_id', $userId)->where("is_read", 0)->count();
        return $count;
    }

    public static function getNotification()
    {
        $userId = Auth::user()->id;
        $response = (object)[];
        $responseData = Notification::select('notifications.id','notifications.title', 'notifications.description','notifications.comic_id',"cs.slug", 'notifications.is_read', 'notifications.created_at as date')
        ->leftjoin("comics_series as cs", "cs.id", "notifications.comic_id")
        ->where('notifications.user_id', $userId)
        ->orderby('notifications.id', 'DESC')
        ->get();
        // print_r($responseData);
        // die('a');

        if(!empty($responseData))
        $response = $responseData;


        return $response;
    }

    public static function getCategories(){
       $data = Content_categories::
       leftJoin('comics_categories_mapping','comics_categories_mapping.category_id','content_categories.id')
       ->leftJoin('comics_series',function($query){
           $query->on('comics_series.id','comics_categories_mapping.comics_id');
           $query->where('comics_series.status',1);
        })
        ->leftJoin('comics_episodes','comics_episodes.comics_series_id','comics_series.id')
       ->where('content_categories.status', 1)
       ->groupBy('content_categories.id')
       ->select('content_categories.*',DB::raw('COUNT(DISTINCT(`comics_episodes`.`comics_series_id`)) AS active_series'))
       ->get();
       return $data;
    }

    public static function getComicIdByEpisode($id){
        $Comics_series_id = Comics_episodes::where("id", $id)->value("comics_series_id");
        return $Comics_series_id;
    }
    public static function getCountComicByCatid($id){
        $count = comics_categories_mapping::where("category_id", $id)->count();
        return $count;
    }
    public static function getComicNameById($id){
        $name = Comics_series::where("id", $id)->value('name');
        return $name;
    }

}
