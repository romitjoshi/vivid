<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Helper,Image,Storage, DB;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping, Comic_ratings};
use App\Models\{User_details, User,User_subscription_details, Payout_requests, Wallet};
use  Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{
    function myDashboard()
    {
        $userId = Auth::user()->id;
        $comicCount = DB::table('comics_series as cs')
        ->where("created_by", $userId)->count();

        $viewsCount = DB::table('comics_series as cs')
        ->where("created_by", $userId)
        ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')->sum('ce.view');


        $wallet = user_details::where("user_id", $userId)->value("wallet");

        $referralCount = Wallet::where("user_id", $userId)->count();

        $topComics = Comics_episodes::select("comics_episodes.name as episode_name", "cs.name as comic_name", "comics_episodes.view")
        ->where("created_by", $userId)
        ->join("comics_series as cs", "cs.id", "comics_episodes.comics_series_id")
        ->limit(3)
        ->groupBy("cs.id")
        ->orderBy("comics_episodes.view", "DESC")
        ->get();

        $payoutRequest = Payout_requests::where("user_id", $userId)->get();

        $user = User::with("getUserDetails")->where("id", $userId)->first();

        return view('/content/front/publisher/dashboard', ['comicCount'=>$comicCount,'viewsCount'=>$viewsCount,'wallet'=>$wallet,'referralCount'=>$referralCount,'topComics'=>$topComics, 'payoutRequest'=>$payoutRequest, 'user'=>$user]);
    }

    function myWallet()
    {
        $userId = Auth::user()->id;

        $UserDetails = User::with("getUserDetails")->where("id", $userId)->first();

        $payoutRequest = Payout_requests::where("user_id", $userId)->get();

        $referralCount = Wallet::where("user_id", $userId)->count();

        $wl = $UserDetails->getUserDetails->wallet ?? 0;
        $withdrawal = $UserDetails->getUserDetails->withdrawal ?? 0;
        $restAmount = $wl - $withdrawal;
        // echo "<pre>";
        // print_r($UserDetails->toArray());
        // die('aa');


        return view('/content/front/publisher/wallet', ['payoutRequest'=>$payoutRequest, 'UserDetails'=>$UserDetails, 'referralCount'=>$referralCount, 'restAmount'=>$restAmount]);
    }

    function payoutRequest(Request $request)
    {
        $rules = array(
            'amount' => 'required',
        );

        $messages = array(
            'required' => ':attribute field is required.'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }
        $userId = Auth::user()->id;
        $amount = $request->input("amount");

        $u = User::where("id", $userId)->where("role", 3)->first();

        if(empty($u))
        {
            return response()->json(['status' => false, 'message' => 'Publisher not found'], 200);
        }

        $ud = User_details::where("user_id", $userId)->value('wallet');

        if(0 >= $amount)
        {
            return response()->json(['status' => false, 'message' => 'You can\'t request for the payout if the wallet amount is less than amount entered for payout'], 200);
        }

        if($ud < $amount)
        {
            return response()->json(['status' => false, 'message' => 'You can\'t request for the payout if the wallet amount is less than amount entered for payout'], 200);
        }

        $restAmount = $ud - $amount;

        $pr = new Payout_requests;
        $pr->user_id = $userId;
        $pr->amount = $amount;
        $pr->status = 0;
        $pr->save();

        $ud = User_details::where("user_id", $userId)->update(['wallet'=>$restAmount]);

        return response()->json(['status' => true, 'message' => 'Amount Request Created Succesfully'], 200);

    }

    public function uploadDocument(Request $request)
    {
        $data = [];
        if($request->hasFile('lic_doc')){
            $fileName = $request->file('lic_doc');
            $lic_doc = Helper::pdfupload($fileName);
            $data['lic_doc'] = $lic_doc;
        }
        if($request->hasFile('tax_doc')){
            $fileName = $request->file('tax_doc');
            $tax_doc = Helper::pdfupload($fileName);
            $data['tax_doc'] = $tax_doc;
        }


        $userId = Auth::user()->id;
        User_details::where("user_id", $userId)->update($data);

        return response()->json(['status'=>true, 'message'=>'Document Uploaded Successfully'], 200);
    }


    public function myComic()
    {
        $userId = Auth::user()->id;
        $user = User::with('getUserDetails')->where("id", $userId)->first();
        return view('/content/front/publisher/comic', ['user'=>$user]);
    }
    public function getComics(Request $request)
    {

        $columns = array(
            0 => 'id',
            1 => 'featured_image',
            2 => 'name',
            3 => 'avg',
            4 => 'approve',
            5 => 'status',
            6 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $pagename = $request->input('pagename');

        $userId = Auth::user()->id;
        $getData = Comics_series::select('*')->where('created_by', $userId);

        $recordsFiltered =$getData->count();


        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('comics_series.name', 'LIKE', "%{$search}%")
                    ->orwhere('comics_series.description', 'LIKE', "%{$search}%");
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
            $avg = Comic_ratings::where("comic_id", $singleRow->id)->avg("ratings");
            $avgSum = "0.00";
            if(!empty($avg))
            $avgSum = Helper::makeCurrencyWithoutSymbol($avg);

            $comicCatid = Comics_categories_mapping::where('comics_id', $singleRow->id)->pluck('category_id');
            $getCatName = [];
            foreach($comicCatid as $cid)
            {
                $getCatData = content_categories::where('id', $cid)->first();
                $getCatName[] = $getCatData->category_name;
            }

            $comicCatid = json_encode($comicCatid);
            $getCatName = json_encode($getCatName);

            $action = '<div class=action-btn-ct><a href="'.url('/publisher/view-comics').'/'.$singleRow->id.'" class="item-edit"><i class="fa fa-eye" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';

            $name = "<a href='#' style='text-decoration:none;color:#fff;'><span class='dataField item-edit' data-bs-toggle='modal' data-bs-target='#modals-slide-in-update' id='".$singleRow->id."' name='".$singleRow->name."' description='".$singleRow->description."' access_type='".$singleRow->access_type."' is_featured='".$singleRow->is_featured."' comicCatid='".$comicCatid."' getCatName='".$getCatName."' imagePath='".env("IMAGE_PATH_medium").$singleRow->featured_image."'>$singleRow->name</span></a>";

            $image='<a href="#" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><img src="'.env("IMAGE_PATH_medium").$singleRow->featured_image.'" width="60" height="60"></a>';



            $approve=Helper::approveStatusFront($singleRow->status);

            $status=Helper::statusgetFront($singleRow->status);

            $data[] = [
                "id" => $singleRow->id,
                "featured_image"=>$image,
                "name" => $name,
                "avg" => $avgSum,
                'approve'=>$approve,
                'status'=>$status,
                'description'=>$singleRow->description,
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

    public function getEpisode(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'image',
            2 => 'name',
            3 => 'description',
            4 => 'impression',
            5 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData =Comics_episodes::select('*')->where("comics_series_id", $request->id);
        $recordsFiltered =$getData->count();

        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('comics_episodes.name', 'LIKE', "%{$search}%")
                    ->orwhere('comics_episodes.description', 'LIKE', "%{$search}%");
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
            $action = '<a href="'.url('/customer/episode-detail').'/'.$singleRow->id.'/'.$singleRow->comics_series_id.'/viv-v2w-episode" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="'.url('/publisher/edit-episode').'/'.$singleRow->id.'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';

            $image='<a href="'.url('/publisher/edit-episode').'/'.$singleRow->id.'" style="text-decoration:none;color:#fff;"><img src="'.env("IMAGE_PATH_medium").$singleRow->image.'" width="60" height="60"></a>';


            $name = '<a href="'.url('/publisher/edit-episode').'/'.$singleRow->id.'" style="text-decoration:none;color:#fff;"><span class="dataField" id="'.$singleRow->id.'">'.$singleRow->name.'</span></a>';

            $description=Helper::get_words($singleRow->description, 6);

            $data[] = [
                "id" => $singleRow->id,
                "image"=>$image,
                "name" => $name,
                "impression" => $singleRow->view ?? 0,
                "description" =>$description,
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

    public function editEpisode($id)
    {
        $getData = Comics_episodes::with('episode_page_mapping')->where('id', $id)->first();
        return view('/content/front/publisher/edit-episode', ['getData'=>$getData , 'id'=>$id]);
    }

    function myAccount()
    {
        $userId = Auth::user()->id;
        $userData = User::with('getUserDetails', 'getUserSubscriptionDetails')
        ->where('id', $userId)
        ->first();
        return view('/content/front/publisher/account',['userData'=>$userData]);
    }

    function myReferral()
    {

        $userId = Auth::user()->id;

        $UserDetails = User::with("getUserDetails")->where("id", $userId)->first();

        $payoutRequest = Payout_requests::where("user_id", $userId)->get();

        $wallet = wallet::with("getUser")->where("user_id", $userId)->get();

        // echo "<pre>";
        // print_r($wallet->toArray());
        // die('aa');


        return view('/content/front/publisher/my-referral', ['payoutRequest'=>$payoutRequest, 'wallet'=>$wallet,'UserDetails'=>$UserDetails]);

    }

    public function viewComics($id)
    {
        $comicInfo = Comics_series::where('id', $id)->first();

        if(empty($comicInfo))
        {
            exit("Comic Not Found");
        }

        $cr = Comic_ratings::where("comic_id", $id);
        $ratings = $cr->avg("ratings");
        $countavg = $cr->count("ratings");

        if(!empty($cr))
        $avgSum = Helper::makeCurrencyWithoutSymbol($ratings);

        $comicCatid = Comics_categories_mapping::where('comics_id', $comicInfo->id)->pluck('category_id');

        $getCatName = [];
        foreach($comicCatid as $cid)
        {

            $getCatData = content_categories::where('id', $cid)->first();
            $getCatName[] = $getCatData->category_name;

        }
        return view('/content/front/publisher/view-comics', ['comicInfo' => $comicInfo, 'id'=>$id, 'comicCatid'=>$comicCatid, 'getCatName'=>$getCatName,'avgSum'=>$avgSum,'countavg'=>$countavg]);
    }

    public function index($id)
    {
        return view('/content/front/publisher/add-episode', ['id'=>$id]);
    }



    public function updateProfile(Request $request)
    {
        //print_r(($request->All()));exit;
        $userId = Auth::user()->id;
        $login_type = Auth::user()->login_type;
        $rules = array(
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255'],
            'Phone_number'=> 'required','max:10'
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
        ];

        if($request->hasFile('image')){
            $image=$request->file('image');
            $fileName=Helper::imageupload($image);
            $User['image'] = $fileName;
        }
        //name   email   Phone_number  EIN  business_address  business_type pass  image
        if($login_type == 1)
        {
            $User['email'] = $request->input('email');
        }

        User::where('id', $userId)->update($User);

        $User_details = [
            'Phone_number' => $request->input('Phone_number'),
            // 'EIN' => $request->input('EIN'),
            'business_address' => $request->input('business_address'),
            'business_type' => $request->input('business_type'),
            'dob' => $request->input('dob'),
            'about' =>$request->input('about'),
        ];

        $User_details['slug'] = Helper::makeSlug("user_details", $request->input('name'));


        User_details::where('user_id', $userId)->update($User_details);
        return response()->json(['status' => true, 'message' => "Profile updated Successfully"], 200);
    }

   // get-publisher-profile
}
