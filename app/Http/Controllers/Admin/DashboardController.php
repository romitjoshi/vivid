<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\Validator;
use  Illuminate\Support\Facades\Auth;
use App\Models\{User_details, User,User_subscription_details};
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping,Store_products,Orders};
use Helper,Image,Storage, DB;
class DashboardController extends Controller
{
    public function home()
    {
        $loopIosData = [];
        $loopAndroidData = [];
        $loopWebData = [];

        //Statistics
        $allComic = Comics_series::count();
        $allEpisode = Comics_episodes::count();
        $allUser = User::count();
        //$allProduct = store_products::count();
        $ud = User_details::where("user_type", 2);
        $allPaidUser = $ud->count();
        $storeRevenue = Orders::sum('amount');
        $comicRevenue = User_subscription_details::sum("price");

        //User Chart
        $graphDays = strtotime('-13 day', strtotime(date("Y-m-d H:i:s")));
        for($i=1; $i<=13; $i++)
        {
            $plusDays = '+'.$i.' day';
            $getLastDays = date("Y-m-d", strtotime($plusDays, $graphDays));
            $webUser = User_details::select([
                DB::raw('DATE(created_at) AS date'),
                DB::raw('COUNT(id) AS count'),
            ])
            ->where('account_create_device_type', 1)
            ->whereDate('created_at', $getLastDays)
            ->orderBy('date', 'DESC')
            ->first();

            $iosUser = User_details::select([
                DB::raw('DATE(created_at) AS date'),
                DB::raw('COUNT(id) AS count'),
            ])
            ->where('account_create_device_type', 1)
            ->whereDate('created_at', $getLastDays)
            ->orderBy('date', 'DESC')
            ->first();

            $androidUser = User_details::select([
                DB::raw('DATE(created_at) AS date'),
                DB::raw('COUNT(id) AS count'),
            ])
            ->where('account_create_device_type', 1)
            ->whereDate('created_at', $getLastDays)
            ->orderBy('date', 'DESC')
            ->first();

            $keyData = date("d/m", strtotime($getLastDays));
            $loopData['web'][$keyData] = !empty($webUser) ? $webUser->count : 0;
            $loopData['ios'][$keyData] = !empty($iosUserUser) ? $iosUserUser->count : 0;
            $loopData['android'][$keyData] = !empty($androidUser) ? $androidUser->count : 0;

        }
        foreach ($loopData['web'] as $key => $value)
        {
            $categoriesArray[] = (string)$key;
            $webusr[] = $value;
        }
        foreach ($loopData['ios'] as $key => $value)
        {
            $iosusr[] = $value;
        }
        foreach ($loopData['android'] as $key => $value)
        {
            $androidusr[] = $value;
        }

        //Revenue Ratio
        $webRevenue = User_subscription_details::where("subscription_type", 1)->sum("price");
        $iosRevenue = User_subscription_details::where("subscription_type", 2)->sum("price");
        $androidRevenue = User_subscription_details::where("subscription_type", 3)->sum("price");


        $webRev = $webRevenue ?? 0 / $comicRevenue * 100;
        $iosRev = $iosRevenue ?? 0 / $comicRevenue * 100;
        $androidRev = $androidRevenue ?? 0 / $comicRevenue * 100;


        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Index"]
        ];

        //Top Comic
        $cs = DB::select("SELECT `comics_series`.`id`, `comics_series`.`name`, count(`user_comics`.comic_id) as view FROM `comics_series` left join `user_comics` ON user_comics.comic_id = comics_series.id GROUP by `comics_series`.`id` order by view desc limit 5");



        return view('/content/Admin/dashboard', ['breadcrumbs' => $breadcrumbs, 'allComic'=>$allComic,'allEpisode'=>$allEpisode,'allUser'=>$allUser,'allPaidUser'=>$allPaidUser,'storeRevenue'=>$storeRevenue,'comicRevenue'=>$comicRevenue,'categoriesArray'=>$categoriesArray,'webusr'=>$webusr,'iosusr'=>$iosusr,'androidusr'=>$androidusr, 'webRev'=>$webRev, 'iosRev'=>$iosRev, 'androidRev'=>$androidRev, 'cs'=>$cs]);
    }
}
