<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\{User, Device_info, User_details, User_subscription_details, User_wallet, Wallet};
use App\Models\admin\{Content_categories, Comics_series, Comic_ratings, Comics_episodes, Comics_episode_page_mapping, Comics_categories_mapping, User_comic_notify, User_comics, Notification, Setting};
use DB, Helper, Log;

class HomeController extends Controller
{
    public $isUserLoggedIn = false;
    public $currentUserId = false;
    public $userType = false;

    function __construct()
    {
        if(Auth::guard('api')->check())
        {
            $this->isUserLoggedIn = true;
            $this->currentUserId = Auth::guard('api')->user()->id;
            $this->userInfo = [
                'name'=> Auth::guard('api')->user()->name,
                'email'=> Auth::guard('api')->user()->email,
                'login_type'=> Auth::guard('api')->user()->login_type
            ];

            $value = User_details::where(['user_id'=>Auth::guard('api')->user()->id])->get()->first();

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

                if($value->user_type == 1)
                {
                    $subscription['plan'] = "Free Subscriptions";
                    $subscription['price'] = Helper::makeCurrency(0);
                }
                else
                {
                    $usd = User_subscription_details::where("user_id", Auth::guard('api')->user()->id)->latest('id')->first();

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

                $totalCoins = Helper::getcoinsTotal(Auth::guard('api')->user()->id);
                $this->userInfo['coins'] = $totalCoins;
            }
        }
    }

    public function index(Request $request)
    {
        $homeServiceResponse = [];

        $homeServiceResponse['image_path'] = [
            'thumbnail'=>env("IMAGE_PATH_thumbnail"),
            'medium'=>env("IMAGE_PATH_medium"),
            'large'=>env("IMAGE_PATH_large"),
            'orignal'=>env("IMAGE_PATH_orignal")
        ];


        $homeServiceResponse['profile'] = (object)[];
        if($this->isUserLoggedIn)
        $homeServiceResponse['profile'] = $this->userInfo;

        $homeServiceResponse['genres'] = (object)[];
        $genres = Content_categories::select("id", "category_name")->get();

        if($genres->isNotEmpty())
        $homeServiceResponse['genres'] = $genres;

        $homeServiceResponse['recently_added'] = (object)[];

        $recently_added = DB::select("select `cs`.`id`, `cs`.`name`, `cs`.`featured_image`, `cs`.`access_type`, CAST(avg(IFNULL( `cr`.`ratings` , 0 )) AS DECIMAL(10,2)) as ratings ,COUNT(`ce`.id) as total_comics from `comics_series` as `cs` left join `comic_ratings` as `cr` on `cr`.`comic_id` = `cs`.`id` left join `users` as `u` on `u`.`id` = `cs`.`created_by` left join `comics_episodes` as `ce` on `ce`.`comics_series_id` = `cs`.`id` where `cs`.`status` = 1 AND `u`.`status` = 1 AND `ce`.`status` = 1 group by `cs`.`id` HAVING total_comics > 0 order by `cs`.`created_at` desc limit 10");

        if(!empty($recently_added))
        $homeServiceResponse['recently_added'] = $recently_added;

        $homeServiceResponse['featured'] = (object)[];

        $featured = DB::select("select `cs`.`id`, `cs`.`name`, `cs`.`featured_image`, `cs`.`access_type`, CAST(avg(IFNULL( `cr`.`ratings` , 0 )) AS DECIMAL(10,2)) as ratings ,COUNT(`ce`.id) as total_comics from `comics_series` as `cs` left join `comic_ratings` as `cr` on `cr`.`comic_id` = `cs`.`id` left join `comics_episodes` as `ce` on `ce`.`comics_series_id` = `cs`.`id` where `cs`.`is_featured` = 2 AND `cs`.`status` = 1 AND `ce`.`status` = 1 group by `cs`.`id` HAVING total_comics > 0 order by `cs`.`created_at` desc limit 10");

        if(!empty($featured))
        $homeServiceResponse['featured'] = $featured;

        $homeServiceResponse['popular'] = (object)[];
        $popular = DB::select("select `cs`.`id`, `cs`.`name`, `cs`.`featured_image`, `cs`.`access_type`, CAST(avg(IFNULL( `cr`.`ratings` , 0 )) AS DECIMAL(10,2)) as ratings ,COUNT(`ce`.id) as total_comics from `comics_series` as `cs` left join `comic_ratings` as `cr` on `cr`.`comic_id` = `cs`.`id` left join `users` as `u` on `u`.`id` = `cs`.`created_by` left join `comics_episodes` as `ce` on `ce`.`comics_series_id` = `cs`.`id` where `cs`.`status` = 1 AND `u`.`status` = 1 AND `ce`.`status` = 1 group by `cs`.`id` HAVING total_comics > 0 order by `ce`.`view` desc limit 10");

        if(!empty($popular))
        $homeServiceResponse['popular'] = $popular;


        $homeServiceResponse['publisher'] = (object)[];
        $publisher = User::select("users.id", "users.name", "users.image")
        ->where("users.status", 1)
        ->where("users.role", 3)
        ->where("cs.status", 1)
        ->where("ce.status", 1)
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

        if(!empty($publisher))
        $homeServiceResponse['publisher'] = $publisher;


        $homeServiceResponse['notification'] = 0;
        if($this->currentUserId)
        {
            $homeServiceResponse['notification'] = Notification::where('user_id', $this->currentUserId)->where('is_read', 0)->count();
        }

        return response()->json(['status'=> true, 'data'=>$homeServiceResponse], 200);
    }

    public function getComic(Request $request)
    {
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;
        $comicList = DB::table('comics_series as cs')
        ->select("cs.id", "cs.name", "cs.featured_image", "cs.access_type", "cs.deep_url",  DB::Raw('IFNULL( AVG( `cr`.`ratings`) , 0.0 ) as ratings'))
        ->where('cs.status', 1)
        ->where('ce.status', 1)
        ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
        ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')
        ->leftjoin('comic_ratings as cr', 'cr.comic_id', '=', 'cs.id');

        if(!empty($request->input('category')))
        {
            $comicList = $comicList->whereIn("ccm.category_id", $request->input('category'));
        }

        if(!empty($request->input('serach_text')))
        {
            $searchTerm = $request->input('serach_text');
            $comicList = $comicList->where('cs.name', 'LIKE', "%{$searchTerm}%");
        }

        if(!empty($request->input('sort_by')))
        {
            $comicList = $comicList->orderBy('cs.name', $request->input('sort_by'));
        }

        $comicList = $comicList->groupby('cs.id');
        $comicList = $comicList->take($offset)
        ->paginate($limit);

        if(!empty($comicList))
        {
            return response()->json(['status'=> true, 'data'=>$comicList], 200);
        }

        return response()->json(['status'=> false, 'data'=>(object)[]], 200);

    }

    public function comicDetails(Request $request)
    {
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
        if(!empty($request->input('deep_url')))
        {
            $link = explode("/comic/", $request->input('deep_url'));
            if(!empty($link[1]))
            {
                $dp = Comics_series::select("id")->where("deep_id", $link[1])->first();
            }
            if(!empty($dp->id))
            $id = $dp->id;
        }

        $response = [];
        $comicDetails = Comics_series::with('getCategory')
        ->where("id", $id)->get();

        if($comicDetails->isEmpty())
        {
            return response()->json(['status' => false, 'data'=>"{}", 'message' => "Comic not Found"], 200);
        }



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
            if($this->isUserLoggedIn)
            {
                $userId = $this->currentUserId;
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

                $response['details']['user_type'] = $this->userType;
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
            if($this->isUserLoggedIn)
            {
                $checkFirst = User_wallet::where("user_id", $this->currentUserId)->where("episode_id", $epiData->id)->first();
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

            $response['episode'][$pr]['free_user_charge'] = $epiData->charge_coin_free_user;
            $response['episode'][$pr]['paid_user_charge'] = $epiData->charge_coin_paid_user;
            $response['episode'][$pr]['coins'] = $epiData->charge_coin_free_user;
            if($this->isUserLoggedIn)
            {
                if($this->userType == 1)
                $response['episode'][$pr]['coins'] = $epiData->charge_coin_free_user;
                else
                $response['episode'][$pr]['coins'] = $epiData->charge_coin_paid_user;
            }


            $pr++;
        }

        return response()->json(['status' => true, 'data'=>$response, 'message' => "Comic Details"], 200);
    }

    public function episodeDetails(Request $request)
    {
        $rules = array(
            'id' => 'required',
            'comic_id' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        //$userCoins = Helper::getcoinsTotal($this->currentUserId);


        $id  = $request->input('id');
        $comic_id  = $request->input('comic_id');

        $response = [];

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

            if($this->isUserLoggedIn)
            {
                $checkFirst = User_wallet::where("user_id", $this->currentUserId)->where("episode_id", $epiData->id)->first();
                if(!empty($checkFirst))
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
                if(!empty($epiData->preview_pages))
                {
                    if($epiData->preview_pages == $p)
                    {
                        if(!empty($this->userType) && $this->userType == 1 && !empty($request->is_user_preview) && $request->is_user_preview != 2)
                        {
                            break;
                        }
                    }
                }
            }
            $response['episode']['pages'] = $pageData;

            //coins detect
            if($this->isUserLoggedIn)
            {

                $checkFirst = User_wallet::where("user_id", $this->currentUserId)->where("episode_id", $id)->first();

                if(empty($checkFirst))
                {
                    if(!empty($request->is_user_preview) && ($request->is_user_preview == 2) && $epiData->access_type != 1)
                    {
                        $userCoins = Helper::getcoinsTotal($this->currentUserId);

                        if($this->userType == 1)
                        $charge = $epiData->charge_coin_free_user;
                        else
                        $charge = $epiData->charge_coin_paid_user;


                        if($charge > $userCoins)
                        {
                            return response()->json(['status' => false, 'data'=>"{}", 'message' => "insufficient coins"], 200);
                        }

                        $getCoimcData = Comics_series::where('id', $epiData->comics_series_id)->first();

                        $user_wallet = new User_wallet;
                        $user_wallet->user_id = $this->currentUserId;
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
                        $wallet->customer_id = $this->currentUserId;
                        $wallet->total = $prc;
                        $wallet->percentage = $setting->reseller_payout_percentage;
                        $wallet->amount = $prTot;
                        $wallet->comic = $getCoimcData->name;
                        $wallet->episode_name = $epiData->name;
                        $wallet->save();
                    }
                }
                $response['episode']['user_pending_coins'] = Helper::getcoinsTotal($this->currentUserId);
            }
        }

        $response['episode']['page_readed_id'] = 0;
        $response['episode']['user_type'] = 0;
        if($this->currentUserId)
        {
            $getUserComic = User_comics::where('comic_id', $comic_id)->where('comic_episode_id', $id)->where('user_id', $this->currentUserId)->latest('id')->first();

            if(!empty($getUserComic))
            {
                $response['episode']['page_readed_id'] = $getUserComic->page_number;
            }

            $response['episode']['user_type'] = $this->userType;

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

                $chf = User_wallet::where("user_id", $this->currentUserId)->where("episode_id", $episodeIdArray[$nextId])->first();

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

                $chp = User_wallet::where("user_id", $this->currentUserId)->where("episode_id", $episodeIdArray[$previousId])->first();

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

        return response()->json(['status' => true, 'data'=>$response, 'message' => "Episode Details"], 200);
    }

    public function Notify(Request $request)
    {
        $rules = array(
            'id' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $id = $request->input('id');

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        $getNotify = User_comic_notify::where("comic_id", $id)->where("user_id",  $this->currentUserId)->count();

        if($getNotify > 0)
        {
            User_comic_notify::where("comic_id", $id)->where("user_id",  $this->currentUserId)->delete();
            return response()->json(['status' => true, 'message' => "Notify Removed Successfully"], 200);
        }
        else
        {
            $User_comic_notify = new User_comic_notify;
            $User_comic_notify->comic_id = $id;
            $User_comic_notify->user_id = $this->currentUserId;
            $User_comic_notify->save();
        }
        return response()->json(['status' => true, 'message' => "Notify Added Successfully"], 200);
    }

    public function rating(Request $request)
    {
        $rules = array(
            'comic_id' => 'required',
            'ratings' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        $comic_id = $request->input('comic_id');
        $ratings = $request->input('ratings');
        $checkComic = Comics_series::where("id", $comic_id)->get();

        if($checkComic->isEmpty())
        {
            return response()->json(['status' => false, 'message' => "Comics not found"], 200);
        }

        $Comic_ratings = Comic_ratings::firstOrNew([
            'user_id'=>$this->currentUserId,
            'comic_id'=>$comic_id,
        ]);
        $Comic_ratings->ratings = $ratings;
        $Comic_ratings->save();

        return response()->json(['status' => true, 'message' => "Rating Added Successfully"], 200);
    }

    public function myLibrary(Request $request)
    {
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;

        $getLibrary = DB::table("user_comics as uc")
        ->select("cs.id","cs.name","cs.access_type", "cs.featured_image",DB::Raw('IFNULL( AVG( `cr`.`ratings`) , 0.0 ) as ratings'))
        ->join("comics_series as cs", "cs.id", "uc.comic_id")
        ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
        ->leftjoin("comic_ratings as cr", "cr.comic_id", "cs.id")
        ->where("uc.user_id", $this->currentUserId)->take($offset);

        if(!empty($request->input('category')))
        {
            $getLibrary = $getLibrary->whereIn("ccm.category_id", $request->input('category'));
        }

        $getLibrary = $getLibrary->groupby('cs.id');
        $getLibrary = $getLibrary->take($offset)
        ->paginate($limit);


        if(!empty($getLibrary))
        {
            return response()->json(['status'=> true, 'data'=>$getLibrary], 200);
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
        $userId = $this->currentUserId;


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

    public function userComic(Request $request)
    {
        $rules = array(
            'comic_id' => 'required',
            'comic_episode_id' => 'required',
            'page_number' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $comic_id = $request->input('comic_id');
        $comic_episode_id = $request->input('comic_episode_id');
        $page_number = $request->input('page_number');

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

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

        $ce = Comics_episode_page_mapping::where("id", $page_number)->where("comics_series_id", $comic_id)->where("episode_id", $comic_episode_id)->get();
        if($ce->isEmpty())
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
        //}

        $findEpisode = Comics_episodes::find($comic_episode_id);
        $findEpisode->view++;
        $findEpisode->save();

        $response = [];

        $response['episode_readed'] = 0;
        $response['episode_readed_id'] = 0;

        $getUserComic = User_comics::where('comic_id', $comic_id)->where('user_id', $this->currentUserId)->where('comic_episode_id', $comic_episode_id)->latest('id')->first();

       //print_r( $getUserComic->toArray());exit;

        if(!empty($getUserComic->comic_id))
        {
            $cmId = $getUserComic->comic_id;
            $cpi = $getUserComic->comic_episode_id;
            $ce = Comics_episodes::where('id', $cpi)->first();
            //print_r( $ce->toArray());exit;

            $response['episode_readed'] = $ce->episode_no ?? 0;
            $response['episode_readed_id'] = $cpi ?? 0;
        }

        return response()->json(['status' => true, 'data'=>$response, 'message' => "Pages Added Successfully"], 200);
    }

    public function getPublisher(Request $request)
    {
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;

        $publisher = User::select("users.id", "users.name", "users.image")
        ->where("users.status", 1)
        ->where("users.role", 3)
        ->where("cs.status", 1)
        ->where("ce.status", 1)
        ->join("comics_series as cs", "cs.created_by", "users.id")
        ->join("comics_episodes as ce", "ce.comics_series_id", "cs.id")
        ->groupBy("users.id")
        ->take($offset)
        ->paginate($limit);

        $publisher->getCollection()->transform(function ($value) {

            if ( ! $value->image) {
                $value->image = env("APP_URL_SERVE").'/images/avatars/userdummy.png';
            }else
            {
                $value->image = env("IMAGE_PATH_medium").$value->image;
            }
            return $value;
        });

        if(!empty($publisher))
        {
            return response()->json(['status'=> true, 'data'=>$publisher], 200);
        }

        return response()->json(['status'=> false, 'data'=>(object)[]], 200);

    }


    public function publisherDetails(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'limit' => 'required',
            'page' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        $userId = $request->input('user_id');
        $getUserData = User::with("getUserDetails")->where("id", $userId)->where("role", "!=", 2)->first();

        if(empty($getUserData))
        return response()->json(['status'=> false, 'message'=>"Publisher not found"], 200);

        $response['publisher'] = [
            'id'=>$getUserData->id,
            'name'=>$getUserData->name,
            'about'=>$getUserData->getUserDetails->about ?? '',
            'image'=>($getUserData->image) ? env("IMAGE_PATH_medium").$getUserData->image : env("APP_URL_SERVE").'/images/avatars/userdummy.png',
        ];

        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;
        $comicList = DB::table('comics_series as cs')
        ->where("created_by", $userId)
        ->select("cs.id", "cs.name", "cs.featured_image", "cs.access_type", "cs.deep_url",  DB::Raw('IFNULL( AVG( `cr`.`ratings`) , 0.0 ) as ratings'))
        ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
        ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')
        ->leftjoin('comic_ratings as cr', 'cr.comic_id', '=', 'cs.id');

        $comicList = $comicList->groupby('cs.id');
        $comicList = $comicList->take($offset)
        ->paginate($limit);

        $response['comic'] = (object)[];
        if(!empty($comicList))
        {
            $response['comic'] = $comicList;
        }

        if(!empty($response))
        {
            return response()->json(['status'=> true, 'data'=>$response], 200);
        }

        return response()->json(['status'=> false, 'data'=>(object)[]], 200);
    }


}
