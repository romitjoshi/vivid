<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Helper,Str;
use App\Models\admin\{User_comics,Comic_ratings,Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping};
use App\Models\{User_details, User,User_wallet};
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function index(){
        $breadcrumbs = [
            ['link' => "/admin/user", 'name' => "Home"],['link'=>"admin/user/ ",'name'=>"User"]
        ];
        return view('/content/Admin/user/usershow', ['breadcrumbs' => $breadcrumbs]);
    }

    public function get(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'user_type',
            4 => 'status',
            5 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = User::with("getUserDetails")->where("role", 2);
        // print_r($getData->toArray());
        // die('a');
        $recordsFiltered =$getData->count();


        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('users.name', 'LIKE', "%{$search}%")
                    ->orwhere('users.email', 'LIKE', "%{$search}%");
                }
            );

            $recordsFiltered = $getData->count();
        }

        $getArrayData = $getData->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
        $data = [];
        foreach($getArrayData as $singleRow)
        {
            $action = '<a href="'.url('/admin/view-user').'/'.base64_encode($singleRow->id).'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;';

            $status= Helper::lightbadge($singleRow->status);

            if(!empty($singleRow->getUserDetails->user_type))
            $typeuser= Helper::planUser($singleRow->getUserDetails->user_type);
            else
            $typeuser= Helper::planUser(1);

            $status .='&nbsp;<i data-feather="edit" updateid="'.$singleRow->id.'" class="confirm-text  statuschange"></i>';

            $data[] = [
                "id" => $singleRow->id,
                "name" =>$singleRow->name,
                "email"=>$singleRow->email,
                "user_type" =>$typeuser,
                "status" =>$status,
                "action" => $action,
            ];
        }

        $response = [];
        $response['recordsTotal'] = intval($recordsFiltered);
        $response['recordsFiltered'] = intval($recordsFiltered);
        $response['draw'] = intval($request->input('draw'));
        $response['status'] = true;
        $response['data'] = $data;
        echo json_encode($response);
    }

    public function viewuser($id)
    {

        $id=base64_decode($id);

        $getData = User::with('getUserDetails','getUserSubscriptionDetails')->where('id', $id)->first();
        $breadcrumbs = [
            ['link' => "/admin/user", 'name' => "Home"],['link'=>"admin/user/ ",'name'=>"User"]
        ];

         return view('/content/Admin/user/view-user', ['breadcrumbs' => $breadcrumbs,'id'=>$id,'getData'=>$getData]);
    }

    public function statuschange(Request $request)
    {
        $statusId = $request->statusId;
        $getUser = User::where("id", $statusId)->first();
        if(!empty($getUser))
        {
            if($getUser->status == 2)
            $status = 1;
            else
            $status = 2;

            $data = [
                'status' => $status
            ];
            User::where("id", $statusId)->update($data);
        }

        $response['status'] = true;

        echo json_encode($response);
    }

    public function getReview(Request $request)
    {

        // $userid=$request->userid;
        // print_r($userid);
        // die();

        $columns = array(
            0 => 'id',
            1 =>'name',
            2 => 'ratings',
            3 => 'created_at',


        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Comic_ratings::with('comicRating')->select('*')->where('user_id','=',$request->userid);
        // print_r($getData->toArray());
        // die();
        //$getData = Comic_ratings::with('getRating')->where('id', $id)->first();
        // $getid =$getData[0]['comic_id'];

       // $user=DB::table('Comic_ratings')->join('Comic_series','Comic_series.id','=','Comic_ratings.comic_id')->select('Comic_series.name')->whereIn('Comic_ratings.comic_id');

        $recordsFiltered =$getData->count();


        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('comic_ratings.ratings', 'LIKE', "%{$search}%")
                    ->orwhere('comic_ratings.user_id', 'LIKE', "%{$search}%");
                }
            );

            $recordsFiltered = $getData->count();
        }

        $getArrayData = $getData->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
        $data = [];
        foreach($getArrayData as $singleRow)
        {
             $action = '<a href="'.url('/admin/view-user').'/'.base64_encode($singleRow->id).'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;';

             $rating = '';
             if($singleRow->ratings == 1)
             {
                 $rating = '<i data-feather="star"></i>';
             }
             else if($singleRow->ratings == 2)
             {
                 $rating = '<i data-feather="star" fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i>';
             }
             else if($singleRow->ratings == 3)
             {
                 $rating = '<i data-feather="star" fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i>';
             }
             else if($singleRow->ratings == 4)
             {
                 $rating = '<i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i>';
             }
             else if($singleRow->ratings == 5)
             {
                 $rating = '<i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i> <i data-feather="star"fill="#f39c12"></i>';
             }

            // $status .='&nbsp;<i data-feather="edit" updateid="'.$singleRow->id.'" class="confirm-text  statuschange"></i>';



            $data[] = [
                "id" => $singleRow->id,
                "name" => $singleRow->comicRating->name,
                "ratings" =>$rating,
                "created_at"=> $singleRow-> created_at->format('d-m-Y'),
                "action" => $action,

            ];
        }

        $response = [];
        $response['recordsTotal'] = intval($recordsFiltered);
        $response['recordsFiltered'] = intval($recordsFiltered);
        $response['draw'] = intval($request->input('draw'));
        $response['status'] = true;
        $response['data'] = $data;
        echo json_encode($response);
    }

    public function getLibrary(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 =>'order_request_id',
            2 => 'amount',
            3=> 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $getData =DB::table("orders as o")
        ->select("o.id", "o.user_id",  "o.order_request_id", "o.created_at", "o.order_status", "o.amount")
        ->join("order_product_details as opd", "opd.order_id", "o.id")
        ->where("o.user_id", $request->userid);

        $recordsFiltered =$getData->count();

        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('order.id', 'LIKE', "%{$search}%")
                    ->orwhere('order.amount', 'LIKE', "%{$search}%");
                }
            );
            $recordsFiltered = $getData->count();
        }
        $getArrayData = $getData->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->groupBy('o.order_request_id')
        ->get();
        $data = [];
        // print_r($getArrayData->toArray());
        // die();
        foreach($getArrayData as $singleRow)
        {


             $action = '<a href="'.url('/admin/view-orders').'/'.$singleRow->id.'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;';
             $data[] = [
                "id" => $singleRow->id,
                "order_request_id" => "#".$singleRow->order_request_id,
                "amount" =>$singleRow->amount,
                "action" => $action,
            ];
        }
        $response = [];
        $response['recordsTotal'] = intval($recordsFiltered);
        $response['recordsFiltered'] = intval($recordsFiltered);
        $response['draw'] = intval($request->input('draw'));
        $response['status'] = true;
        $response['data'] = $data;
        echo json_encode($response);
    }

    public function getComic(Request $request)
    {
        // $userid=$request->userid;
        // print_r($userid);
        // die();
        $columns = array(
            0 => 'id',
            1 =>'comic_name',
            2 =>'episode_name',
            2 =>'page_number',
            3=> 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = User_comics::with('getComicName','getEpisodeName');

        $recordsFiltered =$getData->groupBy("comic_id")->count();
        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('comic_ratings.ratings', 'LIKE', "%{$search}%")
                    ->orwhere('comic_ratings.user_id', 'LIKE', "%{$search}%");
                }
            );
            $recordsFiltered = $getData->count();
        }
        $getArrayData = $getData->groupBy("comic_id")->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->groupBy("comic_id")
        ->get();

        $data = [];

        foreach($getArrayData as $singleRow)
        {
            //$singleRow = Str::limit($singleRow->name,7);
          $action = '<a href="'.url('/admin/view-user').'/'.base64_encode($singleRow->id).'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;';
            $data[] = [
                "id" => $singleRow->id,
                "comic_name" =>$singleRow->getComicName[0]->name ?? '',
                "episode_name" => $singleRow->getEpisodeName[0]->name ?? '',
                "page_number" => $singleRow->page_number,
                "action" => $action,
            ];
        }
        $response = [];
        $response['recordsTotal'] = intval($recordsFiltered);
        $response['recordsFiltered'] = intval($recordsFiltered);
        $response['draw'] = intval($request->input('draw'));
        $response['status'] = true;
        $response['data'] = $data;
        echo json_encode($response);
    }

    public function getCoin(Request $request)
    {
        $userid=$request->userid;
        $columns = array(
            0 => 'id',
            1 =>'created_at',
            2 =>'coins',
            3 =>'description',
            4 => 'transaction_type',

        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = User_wallet::where("user_id", $userid);


        $recordsFiltered =$getData->count();
        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('user_wallet.coin', 'LIKE', "%{$search}%")
                    ->orwhere('user_wallet.id', 'LIKE', "%{$search}%");
                }
            );
            $recordsFiltered = $getData->count();
        }
        $getArrayData = $getData->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

        $data = [];

        foreach($getArrayData as $singleRow)
        {
            $transaction = Helper::transactionType($singleRow->transaction_type);
            $description =$singleRow->comic;
                if(!empty($singleRow->episode_name))
                {
                    $description =$singleRow->comic.' [ '.$singleRow->episode_name. ' ]';
                }

            //$singleRow->comic(if($request->episode_name) [ {{ $request->episode_name }} ] endif

                $data[] = [
                    "id" => $singleRow->id,
                    "created_at"=> $singleRow->created_at->format('d-m-Y'),
                    //"created_at" => $singleRow->created_at,
                    "coins" => $singleRow->coins,
                    "description" =>$description,
                    "transation_id" => $transaction ,
            ];
        }

        $response = [];
        $response['recordsTotal'] = intval($recordsFiltered);
        $response['recordsFiltered'] = intval($recordsFiltered);
        $response['draw'] = intval($request->input('draw'));
        $response['status'] = true;
        $response['data'] = $data;
        echo json_encode($response);

    }

    public function addCoins(Request $request)
    {

        $user_wallet = new User_wallet;
        $user_wallet->user_id = $request->userCoinId;
        $user_wallet->coins = $request->coins;
        $user_wallet->type = 2;
        $user_wallet->transaction_type = 1;
        $user_wallet->comic = "Free Coins";
        $user_wallet->save();

        return response(['status'=>true, 'message'=>'coins added successfully']);
    }
}