<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PublisherComicApprove;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping, Comic_ratings};
use App\Models\{User};

use App\Mail\PublisherComicAdd;
use Helper,Image,Storage, DB, Str;
class ComicController extends Controller
{
    //
    public function index()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/comic",'name'=>"Comics"]
        ];
        return view('/content/Admin/comic/comics', ['breadcrumbs' => $breadcrumbs]);
    }

    public function pendingComic()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/comic",'name'=>"Comics"]
        ];
        return view('/content/Admin/comic/pending-comics', ['breadcrumbs' => $breadcrumbs]);
    }
    public function pendingEpisodes()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/comic",'name'=>"Comics"]
        ];
        return view('/content/Admin/comic/pending-episode',['breadcrumbs' => $breadcrumbs]);
    }

    public function get(Request $request)
    {

        $columns = array(
            0 => 'id',
            1 => 'featured_image',
            2 => 'name',
            2 => 'avg',
            4 => 'is_featured',
            5 => 'is_featured',
            6 => 'access_type',
            7 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $pagename = $request->input('pagename');


        $getData = Comics_series::select('*')->where('approve',1);

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
            $is_featured=Helper::isFeatured($singleRow->is_featured);
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

            $action = '<a href="'.url('/admin/view-comics').'/'.$singleRow->id.'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><i data-feather="edit"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i data-feather="trash-2"></i></a>';

            $name = "<a href='".url('/admin/view-comics/'.$singleRow->id)."'><span class='dataField' id='".$singleRow->id."' name='".$singleRow->name."' status='".$singleRow->status."' description='".$singleRow->description."' access_type='".$singleRow->access_type."' is_featured='".$singleRow->is_featured."' comicCatid='".$comicCatid."' getCatName='".$getCatName."' imagePath='".env("IMAGE_PATH_medium").$singleRow->featured_image."'>$singleRow->name</span></a>";

            $image='<img src="'.env("IMAGE_PATH_medium").$singleRow->featured_image.'" width="60" height="60"></a>';


            $description=Helper::get_words($singleRow->description, 6);
            $accessType=Helper::accessType($singleRow->access_type);
            $status=Helper::statusget($singleRow->status);

            $data[] = [
                "id" => $singleRow->id,
                "featured_image"=>$image,
                "name" => $name,
                "avg" => $avgSum,
                "description" =>$description,
                "status" => $status,
                "access_type" => $accessType,
                "is_featured"=>($singleRow->is_featured == 2) ? '<span class="badge badge-light-success">Yes</span>' : '<span class="badge badge-light-success">No</span>',
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

    public function getpendingcomic(Request $request)
    {

        $columns = array(
            0 => 'id',
            1 => 'featured_image',
            2 => 'name',
            3 => 'pub_name',
            4 => 'description',
            5 => 'approve',
            6 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Comics_series::select('*')->with('getCreateBy')->where('approve',0);

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
            $is_featured=Helper::isFeatured($singleRow->is_featured);

            $comicCatid = Comics_categories_mapping::where('comics_id', $singleRow->id)->pluck('category_id');
            $getCatName = [];
            foreach($comicCatid as $cid)
            {
                $getCatData = content_categories::where('id', $cid)->first();
                $getCatName[] = $getCatData->category_name;
            }

            $comicCatid = json_encode($comicCatid);
            $getCatName = json_encode($getCatName);

            $action = '<a href="'.url('/admin/view-comics').'/'.$singleRow->id.'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i data-feather="trash-2"></i></a>';

            $name = "<span class='dataField' id='".$singleRow->id."' name='".$singleRow->name."' description='".$singleRow->description."' status='".$singleRow->status."' approve='".$singleRow->approve."' access_type='".$singleRow->access_type."' is_featured='".$singleRow->is_featured."' comicCatid='".$comicCatid."' getCatName='".$getCatName."' imagePath='".env("IMAGE_PATH_medium").$singleRow->featured_image."'>$singleRow->name</span>";

            //$status='<i data-feather="edit" updateid="'.$singleRow->id.'" class="confirm-text  statuschange"></i>';
            $status='<a href="#" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><i data-feather="edit"></i></a>';

            $image='<img src="'.env("IMAGE_PATH_medium").$singleRow->featured_image.'" width="60" height="60"></a>';



            $description=Helper::get_words($singleRow->description, 6);
            $accessType=Helper::accessType($singleRow->access_type);


            $data[] = [
                "id" => $singleRow->id,
                "featured_image"=>$image,
                "name" => $name,
                "pub_name" => $singleRow->getCreateBy->name,
                "description" =>$description,
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

    public function getpendingEpisodes(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'image',
            2 => 'name',
            3=> 'comic_name',
            3 => 'approve',
            4 =>  'action',

        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Comics_episodes::select('*')->where('approve',0);

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
            $action = '<a href="'.url('/admin/edit-episode').'/'.$singleRow->id.'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp; ';

            $name = "<span class='dataField' id='".$singleRow->id."' name='".$singleRow->name."'   status='".$singleRow->status."' approve='".$singleRow->approve."' access_type='".$singleRow->access_type."' is_featured='".$singleRow->is_featured."'   imagePath='".env("IMAGE_PATH_medium").$singleRow->image."'>$singleRow->name</span>";

            $status='<i data-feather="edit" updateid="'.$singleRow->id.'" side="episode" class="confirm-text  statuschange"></i>';

            $image='<img src="'.env("IMAGE_PATH_medium").$singleRow->image.'" width="60" height="60"></a>';



           // $description=Helper::get_words($singleRow->description, 6);
           // $accessType=Helper::accessType($singleRow->access_type);
            $comicName = Comics_series::where("id", $singleRow->comics_series_id)->value("name");

            $data[] = [
                "id" => $singleRow->id,
                "image"=>$image,
                "name" => $comicName,
                "comic_name" => $comicName,
                "approve"=> $status,
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




    public function comicStatusChange(Request $request)
    {
        $statusId = $request->statusId;
        $side = $request->side;
        //print_r($request->All());
        // die('a');
        if(!empty($request->side) && $request->side == 'episode')
        {
            $gatData = Comics_episodes::where("id", $statusId)->first();
        }
        else
        {
            $gatData = Comics_series::where("id", $statusId)->first();
        }

        if(!empty($gatData))
        {
            if($gatData->approve == 0)
            $approve = 1;
            else
            $approve = 0;

            $data = [
                'approve' => $approve
            ];
            if(!empty($request->side) && $request->side == 'episode')
            {
                Comics_episodes::where("id", $statusId)->update($data);
            }
            else
            {
                Comics_series::where("id", $statusId)->update($data);
            }
        }

        $response['status'] = true;

        echo json_encode($response);
    }

    public function viewComics($id)
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/comic/",'name'=>"Comics"]
        ];
        $comicInfo = Comics_series::where('id', $id)->first();
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
        return view('/content/Admin/comic/view-comics', ['breadcrumbs' => $breadcrumbs, 'comicInfo' => $comicInfo, 'id'=>$id, 'comicCatid'=>$comicCatid, 'getCatName'=>$getCatName,'avgSum'=>$avgSum,'countavg'=>$countavg]);
    }

    public function insert(Request $request)
    {

        do {
            $deep_id = Helper::generateRandomString();
        } while ( DB::table( 'comics_series' )->where( 'deep_id', $deep_id )->exists() );

        $rules = array(
            'name' => 'required',
            'featured_image' => 'required|mimes:jpeg,png,jpg',
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'name' => 'Comic Name',
            'featured_image' => 'Featured_image',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails()) {

            $response['status'] = false;
            $response['message'] = ERROR;
        }
        else
        {
            $image=$request->file('featured_image');
            $fileName=Helper::imageupload($image);
            $Comics_series = new Comics_series;
            $Comics_series->name = $request->name;
            $Comics_series->featured_image = $fileName;
            $Comics_series->slug = Str::slug($request->name, '-');
            $Comics_series->slug = Helper::makeSlug("comics_series", $request->name);

            $Comics_series->is_featured = 0;
            $Comics_series->access_type = 0;
            $Comics_series->approve = 0;
            $Comics_series->status = 0;

            if(!empty($request->adSide) && $request->adSide == 1)
            {
                $Comics_series->is_featured = $request->is_featured;
                $Comics_series->access_type = $request->access_type;
                $Comics_series->approve = 1;
                $Comics_series->status = 1;
            }


            $Comics_series->description = $request->description;
            $Comics_series->deep_url = env("APP_URL").'/comic/'.$deep_id;
            $Comics_series->deep_id = $deep_id;
            $Comics_series->created_by = Auth::user()->id;
            $Comics_series->updated_by = Auth::user()->id;


            $Comics_series->save();

            $category = $request->input('category');
            foreach($category as $id)
            {
                $Comics_categories_mapping = new Comics_categories_mapping;
                $Comics_categories_mapping->comics_id = $Comics_series->id;
                $Comics_categories_mapping->category_id = $id;
                $Comics_categories_mapping->save();
            }

              //email
              $adminemail =Helper::getAdminEmail();
              $pub_name= Helper::getCustomerName(Auth::user()->id);
              $mailData = [
                  'pub_name' => $pub_name,
                  'comic_name' => $request->name,
                  'link'=> env('APP_URL_SERVE').'/admin/login'
              ];
              Mail::to($adminemail)->send(new PublisherComicAdd($mailData));


            $response['status'] = true;
            $response['comic_id'] = $Comics_series->id;
            $response['message'] = COMICADD;
        }
        echo json_encode($response);

    }

    public function update(Request $request)
    {
     // print_r($request->all());exit;
        $rules = array(
            'name' => 'required|min:2',
            'description' => 'required|min:2',

        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'name' => 'Comic Name',
            'description' => 'Description',

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
            $Comics_series = Comics_series::find($request->updateid);

            $Comics_series->name = $request->name;
            $Comics_series->description = $request->description;

            if(empty($Comics_series->slug))
            $Comics_series->slug = Helper::makeSlug("comics_series", $request->name);


            if(!empty($request->is_featured))
            $Comics_series->is_featured = $request->is_featured;

            if(!empty($request->access_type))
            $Comics_series->access_type = $request->access_type;

            if(isset($request->status))
            {
                $Comics_series->status = $request->status;
            }

            if(isset($request->approve))
            {
                $Comics_series->approve = $request->approve;
                if($request->approve == 1)
                {
                    $Comics_series->status = 1;
                    $epiData = [
                        'access_type'=>$request->access_type
                    ];
                    Comics_episodes::where("comics_series_id", $request->updateid)->update($epiData);
                }

                $Comics_series->charge_coin_free_user = $request->charge_coin_free_user;
                $Comics_series->charge_coin_paid_user = $request->charge_coin_paid_user;

                $publisher = Helper::getCustomerName($Comics_series->created_by);
                $mailData = [
                    'publisher'=>$publisher ?? "",
                    'comic_name' => $Comics_series->name ?? "",
                ];
                $e = Helper::getCustomerEmail($Comics_series->created_by);
                Mail::to($e)->send(new PublisherComicApprove($mailData));
            }


            if($request->hasFile('featured_image')){
                $image=$request->file('featured_image');
                $fileName=Helper::imageupload($image);
                $Comics_series->featured_image = $fileName;
            }

            $Comics_series->save();

            $category = $request->input('category');
            foreach($category as $id)
            {
                Comics_categories_mapping::where('comics_id', $request->updateid)->whereNotIn('category_id',$category ?? [])->delete();

                $Comics_categories_mapping = Comics_categories_mapping::firstOrNew([
                    'category_id' => $id,
                    'comics_id'=>$request->updateid
                ]);

                $Comics_categories_mapping->comics_id = $request->updateid;
                $Comics_categories_mapping->category_id = $id;
                $Comics_categories_mapping->save();
            }

            $response['status'] = true;
            $response['message'] = COMICUPDATE;
        }
        echo json_encode($response);
    }

    public function delete(Request $request)
    {
        $rules = array(
            'deleteid' => 'required',
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'deleteid' => 'Name',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        $userId = Auth::user()->id;
        $cs=Comics_series::where('id', $request->deleteid)->where("created_by", $userId)->first();
        if(empty($cs) && empty($request->action))
        {
            $response['status'] = false;
            $response['message'] = "User not associate with Comic";
            echo json_encode($response);
            exit;
        }

        if ($validator->fails())
        {
            $response['status'] = false;
            $response['message'] = ERROR;
        }
        else
        {
            $category=Comics_series::where('id', $request->deleteid)->delete();
            $category=Comics_episodes::where('comics_series_id', $request->deleteid)->delete();
            $category=Comics_episode_page_mapping::where('comics_series_id', $request->deleteid)->delete();
            $category=Comics_categories_mapping::where('comics_id', $request->deleteid)->delete();

            $response['status'] = true;
            $response['message'] = COMICDELETE;
        }
        echo json_encode($response);
    }

}
