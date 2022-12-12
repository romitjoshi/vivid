<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,User_comic_notify,Comics_episodes,Comics_episode_page_mapping,User_comics,Comic_ratings,Banner,Setting};
use App\Models\{User, User_details, User_wallet, Wallet};
use Helper,Image,Storage;
class HomeController extends Controller
{
    public $isUserLoggedIn = false;
    public $currentUserId = false;
    public $userType = false;

    function __construct()
    {
        if((isset(Auth::user()->id)) && (!empty(Auth::user()->id)))
        {
            $this->isUserLoggedIn = true;
            $this->currentUserId = Auth::user()->id;
            $value = User_details::where(['user_id'=>Auth::user()->id])->get()->first();
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
            }
        }
    }

    public function home()
    {
        $userLoginCheck = false;
        $ud = [];
        if(!empty(Auth::user()->id)){
            $userLoginCheck = true;
            $ud = User_details::where('user_id',Auth::user()->id)->first();
        }

        $featured = DB::select("select `cs`.`id`, `cs`.`name`, `cs`.`featured_image`, `cs`.`access_type`, `cs`.`description`, `cs`.`slug`, CAST(avg(IFNULL( `cr`.`ratings` , 0 )) AS DECIMAL(10,2)) as ratings ,COUNT(`ce`.id) as total_comics from `comics_series` as `cs` left join `comic_ratings` as `cr` on `cr`.`comic_id` = `cs`.`id` left join `users` as `u` on `u`.`id` = `cs`.`created_by` left join `comics_episodes` as `ce` on `ce`.`comics_series_id` = `cs`.`id` where `cs`.`is_featured` = 2 AND `cs`.`status` = 1 AND `u`.`status` = 1 AND `ce`.`status` = 1 group by `cs`.`id` HAVING total_comics > 0 order by `cs`.`created_at` desc limit 20");

        // echo "<pre>";
        // print_r($featured);
        // exit;

        $populars = DB::select("select `cs`.`id`, `cs`.`name`, `cs`.`featured_image`, `cs`.`access_type`, `cs`.`description`, `cs`.`slug`, CAST(avg(IFNULL( `cr`.`ratings` , 0 )) AS DECIMAL(10,2)) as ratings ,COUNT(`ce`.id) as total_comics from `comics_series` as `cs` left join `comic_ratings` as `cr` on `cr`.`comic_id` = `cs`.`id` left join `users` as `u` on `u`.`id` = `cs`.`created_by` left join `comics_episodes` as `ce` on `ce`.`comics_series_id` = `cs`.`id` where `cs`.`status` = 1 AND `u`.`status` = 1 AND `ce`.`status` = 1 group by `cs`.`id` HAVING total_comics > 0 order by `ce`.`view` desc limit 10");


        $recentlyadded = DB::select("select `cs`.`id`, `cs`.`name`, `cs`.`featured_image`, `cs`.`access_type`,`cs`.`description`, `cs`.`slug`, CAST(avg(IFNULL( `cr`.`ratings` , 0 )) AS DECIMAL(10,2)) as ratings ,COUNT(`ce`.id) as total_comics from `comics_series` as `cs` left join `comic_ratings` as `cr` on `cr`.`comic_id` = `cs`.`id` left join `users` as `u` on `u`.`id` = `cs`.`created_by` left join `comics_episodes` as `ce` on `ce`.`comics_series_id` = `cs`.`id` where `cs`.`status` = 1 AND `u`.`status` = 1 AND `ce`.`status` = 1 group by `cs`.`id` HAVING total_comics > 0 order by `cs`.`created_at` desc limit 10");

        $bannerimage = Banner::select('image','name','link', 'mobile_banner')->where("status", 1)->orderBy('order', 'ASC')->get();

        $publisher = User::select("users.id", "users.name", "users.image", "ud.slug")
        ->where("users.status", 1)
        ->where("users.role", 3)
        ->where("cs.status", 1)
        ->where("ce.status", 1)
        ->join("user_details as ud", "ud.user_id", "users.id")
        ->join("comics_series as cs", "cs.created_by", "users.id")
        ->join("comics_episodes as ce", "ce.comics_series_id", "cs.id")
        ->groupBy("users.id")
        ->get()->map(function($data) {
            if ( ! $data->image) {
                $data->image = env("APP_URL_SERVE").'/images/avatars/userdummy.png';
            }else
            {
                $data->image = env("IMAGE_PATH_medium").$data->image;
            }
            return $data;
        });


        return view('/content/front/home',['featured' => $featured,'populars'=>$populars,'bannerimage' => $bannerimage,'userLoginCheck'=>$userLoginCheck,'ud'=>$ud,'recentlyadded'=>$recentlyadded,'publisher'=>$publisher]);
    }

    public function comicdetail($slug)
    {
        $id = Helper::getComicIdByslug($slug);
        if(empty($id))
        return redirect()->back()->with('success', 'Slug not found!');

        $userLoginCheck = false;
        $ud = [];
        $response = [];
        $comicDetails = Comics_series::with('getCategory')
        ->where("id", $id)->get();

        if($comicDetails->isEmpty())
        {
            return response()->json(['status' => false, 'data'=>"{}", 'message' => "Comic not Found"], 200);
        }

        $cr = Comic_ratings::where("comic_id", $id);
        foreach($comicDetails as $data)
        {
            $getPub = Helper::getPublisherInfo($data->created_by);
            $response['publisher'] = $getPub;

            $response['details'] = [
                'id'=>$data->id,
                'name'=>$data->name,
                'description'=>$data->description,
                'is_featured'=>$data->is_featured,
                'access_type'=>$data->access_type,
                'deep_url'=>$data->deep_url,
                'featured_image'=>$data->featured_image,
            ];

            $response['category'] = "";
            $catData = [];
            foreach($data->getCategory as $cData)
            {
                $catName = Content_categories::where("id", $cData->category_id)->first();
                $catData[] = [
                    'id'=>$cData->category_id,
                    'name'=>$catName->category_name ?? "",
                ];
            }

            $response['details']['notify'] = false;
            $response['details']['episode_readed'] = 0;
            $response['details']['episode_readed_id'] = 0;
            $response['details']['user_type'] = 0;
            $response['details']['is_user_rating'] = "0.0";
            if(!empty(Auth::user()->id))
            {
                $userId = Auth::user()->id;
                $getNotify = User_comic_notify::where('comic_id', $data->id)->where('user_id', $userId)->count();
                if($getNotify > 0)
                $response['details']['notify'] = true;

                $getUserComic = User_comics::where('comic_id', $id)->where('user_id', $userId)->latest('id')->first();

                if(!empty($getUserComic->comic_id))
                {
                    $cpi = $getUserComic->comic_episode_id;
                    $ce = Comics_episodes::where('id', $cpi)->first();

                    $response['details']['episode_readed'] = $ce->episode_no ?? 0;
                    $response['details']['episode_readed_id'] = $cpi ?? 0;
                }

                $ud = User_details::where("user_id", $userId)->first();
                $response['details']['user_type'] = $ud->user_type;
                $cr = Comic_ratings::where("comic_id", $id)->where("user_id", $userId)->first();
                if(!empty($cr))
                $response['details']['is_user_rating'] = $cr->ratings ?? "0.0";
            }

            if(!empty($catData))
            {
                $response['category'] = $catData;
            }
        }

        $comicRatings = Comic_ratings::where("comic_id", $id)->avg('ratings');

        $response['details']['ratings'] = "0.0";
        if(!empty($comicRatings))
        $response['details']['ratings'] = number_format($comicRatings, 1);

        $ComicsEpisodes = Comics_episodes::where("comics_series_id", $id)
        ->where("status", 1)
        ->get();

        $response['episode'] = [];

        $pr = 0;
        $response['details']['preview_pages'] = 0;
        foreach($ComicsEpisodes as $epiData)
        {
            if($pr == 0)
            {
                $response['details']['preview_pages'] = $epiData->preview_pages;
            }
            $preview_pages = 0;

            if(!empty($epiData->preview_pages))
            {
                $preview_pages = $epiData->preview_pages;
            }

            $is_paid_coins = 2;
            if(!empty(Auth::user()->id))
            {
                $checkFirst = User_wallet::where("user_id", Auth::user()->id)->where("episode_id", $epiData->id)->first();
                
                //echo "<pre>"; print_r($checkFirst);exit;
                if(!empty($checkFirst))
                {
                    $is_paid_coins = 1;
                    $preview_pages = 0;
                }

            }

            $response['episode'][$pr] = [
                'id'=>$epiData->id,
                'name'=>$epiData->name,
                'image'=>$epiData->image,
                'access_type'=>$epiData->access_type,
                'preview_pages'=>$preview_pages,
                'is_paid_coins'=>$is_paid_coins, //1 yes 2 no
            ];

            $response['episode'][$pr]['charge_coin_free_user'] = $epiData->charge_coin_free_user;
            $response['episode'][$pr]['charge_coin_paid_user'] = $epiData->charge_coin_paid_user;

            $response['episode'][$pr]['coins'] = $epiData->charge_coin_free_user;
            if(!empty(Auth::user()->id))
            {
                if($ud->user_type == 1)
                $response['episode'][$pr]['coins'] = $epiData->charge_coin_free_user;
                else
                $response['episode'][$pr]['coins'] = $epiData->charge_coin_paid_user;
            }

            $pr++;
        }

        $shareButtons = \Share::page(
            env("APP_URL_SERVE"),
            'Vivid',
        )
        ->facebook()
        ->twitter()
        ->linkedin()
        ->telegram()
        ->whatsapp()
        ->reddit();

        // echo "<pre>";
        // print_r($response['episode']);
        // exit;

        return view('content/front/comic-details',['response'=>$response, 'shareButtons'=>$shareButtons,'ud'=>$ud]);
    }

    public function backComicdetail($comic_id, $comic_episode_id, $page_number)
    {
        $cs = Comics_series::where("id", $comic_id)->get();
        if($cs->isEmpty())
        {
            return response()->json(['status' => false, 'data'=>[], 'message' => "Comic Not Found"], 200);
        }

        $ce = Comics_episodes::where("id", $comic_episode_id)->where("comics_series_id", $comic_id)->get();
        if($ce->isEmpty())
        {
            return response()->json(['status' => false, 'data'=>[], 'message' => "Comic Episode Not Found"], 200);
        }

        $cepm = Comics_episode_page_mapping::where("id", $page_number)->where("comics_series_id", $comic_id)->where("episode_id", $comic_episode_id)->get();
        if($cepm->isEmpty())
        {
            return response()->json(['status' => false, 'data'=>[], 'message' => "Comic Episode Page Not Found"], 200);
        }

        $check = User_comics::where("user_id", Auth::user()->id)->where("comic_id", $comic_id)->where("comic_episode_id",$comic_episode_id)->get();

        //if($check->isEmpty())
        //{
            $User_comics = new User_comics;
            $User_comics->user_id = Auth::user()->id;
            $User_comics->comic_id = $comic_id;
            $User_comics->comic_episode_id = $comic_episode_id;
            $User_comics->page_number = $page_number;
            $User_comics->save();
       // }

       $findEpisode = Comics_episodes::find($comic_episode_id);
       $findEpisode->view++;
       $findEpisode->save();

        $response = [];

        $response['episode_readed'] = 0;
        $response['episode_readed_id'] = 0;

        $getUserComic = User_comics::where('comic_id', $comic_id)->where('user_id', Auth::user()->id)->where('comic_episode_id', $comic_episode_id)->latest('id')->first();


        if(!empty($getUserComic->comic_id))
        {
            $cmId = $getUserComic->comic_id;
            $cpi = $getUserComic->comic_episode_id;
            $ce = Comics_episodes::where('id', $cpi)->first();

            $response['episode_readed'] = $ce->episode_no ?? 0;
            $response['episode_readed_id'] = $cpi ?? 0;
        }

        $slug = $cs[0]->slug ?? 0;
        return redirect('/comic-detail/'.$slug);
    }

    public function episodedetail($id, $comic_id, $view)
    {
        $response = [];

        $getCustomerType = Helper::getCustomerType(Auth::user()->id);

        //echo $getCustomerType;exit;

        $ComicsEpisodes = Comics_episodes::with('episode_page_mapping')->where("id", $id)->get();

        if($ComicsEpisodes->isEmpty())
        {
            return response()->json(['status' => false, 'data'=>"{}", 'message' => "Episode not Found"], 200);
        }
        $response['episode'] = "";

        foreach($ComicsEpisodes as $epiData)
        {
            $preview_pages = 0;
            if(!empty($epiData->preview_pages))
            $preview_pages = $epiData->preview_pages;

            if(!empty(Auth::user()->id))
            {
                $checkFirst = User_wallet::where("user_id", Auth::user()->id)->where("episode_id", $epiData->id)->first();
                if(!empty($checkFirst) || $getCustomerType == 2)
                {
                    $preview_pages = 0;
                }

            }

            $response['episode'] = [
                'id'=>$epiData->id,
                'name'=>$epiData->name,
                'description'=>$epiData->description,
                'image'=>$epiData->image,
                'total_page_count'=>$epiData->total_page_count,
                'audio_file'=>env("AUDIO_PATH").$epiData->audio_file,
                'access_type'=>$epiData->access_type,
                'preview_pages'=>$preview_pages,
                'charge_coin_free_user'=>$epiData->charge_coin_free_user,
                'charge_coin_paid_user'=>$epiData->charge_coin_paid_user,
            ];

            $response['episode']['pages'] = "";
            $pageData = [];
            $p = 0;
            foreach($epiData->episode_page_mapping as $pData)
            {
                $pageData[] = [
                    'id'=>$pData->id,
                    'page_number'=>$pData->page_number,
                    'image_url'=>env('PDF_IMAGE_PATH').$pData->image_url,
                    'audio_start'=>$pData->audio_start,
                    'audio_end'=>$pData->audio_end
                ];
                $p++;
                // if(!empty($epiData->preview_pages))
                // {
                //     if($epiData->preview_pages == $p)
                //     {
                //         if(!empty(Auth::user()->id) && Auth::user()->id == 1 && !empty($view) && $view != 'viv-v2w-episode')
                //         {
                //             break;
                //         }
                //     }
                // }
            }
            $response['episode']['pages'] = $pageData;

             //coins detect
             if(!empty(Auth::user()->id))
             {
 
                 $checkFirst = User_wallet::where("user_id", Auth::user()->id)->where("episode_id", $id)->first();
 
                 if(empty($checkFirst))
                 {
                     if((!empty($view) && ($view == 'viv-v2w-episode') && $epiData->access_type != 1) || (!empty($view) && ($view == 'viv-v1w-episode') && $epiData->access_type != 1 && $getCustomerType == 2))
                     {
                        $userCoins = Helper::getcoinsTotal(Auth::user()->id);
 
                        if($getCustomerType == 1)
                        $charge = $epiData->charge_coin_free_user;
                        else
                        $charge = $epiData->charge_coin_paid_user;
 
 
                         if($charge > $userCoins)
                         {
                             return response()->json(['status' => false, 'data'=>"{}", 'message' => "insufficient coins"], 200);
                         }
 
                         $getCoimcData = Comics_series::where('id', $epiData->comics_series_id)->first();
 
                         $user_wallet = new User_wallet;
                         $user_wallet->user_id = Auth::user()->id;
                         $user_wallet->coins = $charge;
                         $user_wallet->type = 1;
                         $user_wallet->transaction_type = 2;
                         $user_wallet->comic = $getCoimcData->name;
                         $user_wallet->episode_name = $epiData->name;
                         $user_wallet->episode_id = $epiData->id;
                         $user_wallet->save();
 
                        $setting = Setting::where("id", 1)->first();
 
                        $prc = $setting->price_v_coin * $charge;

                        $prTot = ($setting->reseller_payout_percentage / 100) * $prc;
 
                        DB::table('user_details')
                         ->where('user_id', $getCoimcData->created_by )
                         ->increment('wallet', $prTot);

                        $wallet = new Wallet;
                        $wallet->user_id = $getCoimcData->created_by;
                        $wallet->customer_id = Auth::user()->id;
                        $wallet->total = $prc;
                        $wallet->percentage = $setting->reseller_payout_percentage;
                        $wallet->amount = $prTot;
                        $wallet->comic = $getCoimcData->name;
                        $wallet->episode_name = $epiData->name;
                        $wallet->save();


                     }
                 }
                 $response['episode']['user_pending_coins'] = Helper::getcoinsTotal(Auth::user()->id);
             }
        }

        $response['episode']['page_readed_id'] = 0;
        $response['episode']['user_type'] = 0;
        if(!empty(Auth::user()->id))
        {
            $getUserComic = User_comics::where('comic_id', $comic_id)->where('comic_episode_id', $id)->where('user_id', Auth::user()->id)->latest('id')->first();

            if(!empty($getUserComic))
            {
                $response['episode']['page_readed_id'] = $getUserComic->page_number;
            }

            $ud = User_details::where("user_id", Auth::user()->id)->first();
            $response['episode']['user_type'] = $ud->user_type;
        }

        $response['play']['next_episode'] = 0;
        $response['play']['next_episode_preview'] = 0;
        $response['play']['next_episode_access_type'] = 0;
        $response['play']['next_charge_coin_free_user'] = 0;
        $response['play']['next_charge_coin_paid_user'] = 0;
        $response['play']['next_is_paid_coins'] = 2;
        $response['play']['next_coins'] = 0;

        $response['play']['previous_episode'] = 0;
        $response['play']['previous_episode_preview'] = 0;
        $response['play']['previous_episode_access_type'] = 0;
        $response['play']['previous_charge_coin_free_user'] = 0;
        $response['play']['previous_charge_coin_paid_user'] = 0;
        $response['play']['previous_is_paid_coins'] = 2;
        $response['play']['previous_coins'] = 0;

        // $checkAsociate = Comics_episodes::select("id")->where("id", $id)->where("comics_series_id", $comic_id)->get();

        // if($checkAsociate->isEmpty())
        // {
        //     return response()->json(['status' => false, 'data'=>[], 'message' => "Episode id not associated with Comic Id"], 200);
        // }

        $getEpisodeComic = Comics_episodes::select("id", "comics_series_id")->where("comics_series_id", $comic_id)->get();

        $episodeIdArray = [];
        foreach($getEpisodeComic as $gEc)
        {
            $episodeIdArray[] = $gEc->id;
        }

        $findId = array_search($id, $episodeIdArray);

        $nextId = $findId + 1;
        $previousId = $findId - 1;


        if(!empty($episodeIdArray))
        {
            if(!empty($episodeIdArray[$nextId]))
            {
                $response['play']['next_episode'] = $episodeIdArray[$nextId];
                $pn = Comics_episodes::select("preview_pages", "access_type", "charge_coin_free_user", "charge_coin_paid_user")->where("id", $episodeIdArray[$nextId])->first();
                $response['play']['next_episode_preview'] = (!empty($pn->preview_pages)) ? $pn->preview_pages : 0;
                $response['play']['next_episode_access_type'] = (!empty($pn->access_type)) ? $pn->access_type : 0;

                $response['play']['next_charge_coin_free_user'] = (!empty($pn->charge_coin_free_user)) ? $pn->charge_coin_free_user : 0;
                $response['play']['next_charge_coin_paid_user'] = (!empty($pn->charge_coin_paid_user)) ? $pn->charge_coin_paid_user : 0;

                $chf = User_wallet::where("user_id", Auth::user()->id)->where("episode_id", $episodeIdArray[$nextId])->first();

                if(!empty($chf))
                {
                    $response['play']['next_is_paid_coins'] = 1;
                    $response['play']['next_episode_preview'] = 0;
                }

                if($response['episode']['user_type'] == 2)
                $response['play']['next_coins'] = $pn->charge_coin_paid_user;
                else
                $response['play']['next_coins'] = $pn->charge_coin_free_user;
            }


            if((!empty($episodeIdArray[$previousId])) && ($findId != 0))
            {
                $response['play']['previous_episode'] = $episodeIdArray[$previousId];
                $pp = Comics_episodes::select("preview_pages", "access_type", "charge_coin_free_user", "charge_coin_paid_user")->where("id", $episodeIdArray[$previousId])->first();
                $response['play']['previous_episode_preview'] = (!empty($pp->preview_pages)) ? $pp->preview_pages : 0;
                $response['play']['previous_episode_access_type'] = (!empty($pp->access_type)) ? $pp->access_type : 0;

                $response['play']['previous_charge_coin_free_user'] = (!empty($pp->charge_coin_free_user)) ? $pp->charge_coin_free_user : 0;
                $response['play']['previous_charge_coin_paid_user'] = (!empty($pp->charge_coin_paid_user)) ? $pp->charge_coin_paid_user : 0;

                $chp = User_wallet::where("user_id", Auth::user()->id)->where("episode_id", $episodeIdArray[$previousId])->first();

                if(!empty($chp))
                {
                    $response['play']['previous_is_paid_coins'] = 1;
                    $response['play']['previous_episode_preview'] = 0;
                }

                if($response['episode']['user_type'] == 2)
                $response['play']['previous_coins'] = $pp->charge_coin_paid_user;
                else
                $response['play']['previous_coins'] = $pp->charge_coin_free_user;
            }

        }

        $response['play']['comic_id'] = (int)$comic_id;
        $response['episode']['view'] = $view;

        // echo "<pre>";
        // print_r($response);
        // exit; 
        return view('/content/front/episode-details', ['response'=>$response]);

    }

    public function refer($refer)
    {
        $cookie_name = "virefvier";
        $cookie_value = $refer;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 300), "/");

        // $pubid = User_details::where("refer_code", $refer)
        // ->join("comics_series as cs", "cs.cretaed_by")
        // ->value("slug");

        $pubid = User_details::where("refer_code", $refer)
        ->join("comics_series as cs", "cs.created_by", "user_details.user_id")
        ->join("comics_episodes as ce", "ce.comics_series_id", "cs.id")
        ->where("cs.status", 1)
        ->value("user_details.slug");

        // $url = Helper::redirectPage();
        if(!empty($pubid))
        $url = env("APP_URL_SERVE").'/publisher-profile/'.$pubid;
        else
        $url = env("APP_URL_SERVE").'/publisher';

        return redirect($url);

    }
}
