<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Helper,Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PublisherApproved;
use App\Mail\PublisherActiveAccount;
use App\Mail\PublisherInactiveAccount;
use App\Mail\DocumentApprove;
use App\Mail\DocumentUnapprove;
use App\Models\admin\{User_comics,Comic_ratings,Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping};
use App\Models\{User_details, User};
use Illuminate\Support\Facades\DB;
class PublisherController extends Controller
{
    //
    public function index()
    {
        // $mailData = [
        //     'user'=>"asdasd",
        //     'link'=> "asdas"
        // ];
        // Mail::to("raj123@mailinator.com")->send(new PublisherApproved($mailData));

        // exit("Run");
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/publisher",'name'=>"Publisher"]
        ];
        return view('/content/Admin/publisher/publishers', ['breadcrumbs' => $breadcrumbs]);
    }

    public function pendingIndex()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/publisher",'name'=>"Publisher"]
        ];
        return view('/content/Admin/publisher/pendingpublisher', ['breadcrumbs' => $breadcrumbs]);
    }

    public function get(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'document_uploded',
            4 => 'document_approval',
            5 => 'status',
            6 => 'created_at',
            7 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = User::with("getUserDetails")->where('is_approve',1)->where('role',3);
        //print_r($getData->toArray());
        //die('a');
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
            $action = '<a href="'.url('/admin/view-publisher').'/'.base64_encode($singleRow->id).'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;';

            $status= Helper::lightbadge($singleRow->status);
            $status .='<i data-feather="edit" action="status" updateid="'.$singleRow->id.'" class="confirm-text  statuschange"></i>';

            // $typeuser= Helper::planUser($singleRow->getUserDetails->user_type);
            //  $status .='&nbsp;<i data-feather="edit" updateid="'.$singleRow->id.'" class="confirm-text  statuschange"></i>';

            $document_uploded = '<span class="badge badge-light-danger">No</span>';
            $document_approval = '<span class="badge badge-light-danger">No</span>';
            if(!empty($singleRow->getUserDetails->tax_doc) && !empty($singleRow->getUserDetails->lic_doc))
            {
                $document_uploded = '<span class="badge badge-light-success">Yes</span>';
            }

            if(!empty($singleRow->getUserDetails->is_approve_docs))
            {
                $document_approval = '<span class="badge badge-light-success">Yes</span>';
            }


            $data[] = [
                "id" => $singleRow->id,
                "name" =>$singleRow->name,
                "email"=>$singleRow->email,
                "document_uploded"=>$document_uploded,
                "document_approval"=>$document_approval,
                "status"=>$status,
                "created_at" =>date('Y-m-d', strtotime($singleRow->created_at)),
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

    public function getPendingPublisher(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'created_at',
            4 => 'status',
            5 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = User::with("getUserDetails")->where('is_approve',0)->where('role',3);
        //print_r($getData->toArray());
        //die('a');
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
           $action = '<a href="'.url('/admin/view-publisher').'/'.base64_encode($singleRow->id).'" class="item-edit"><i data-feather="eye"></i></a>&nbsp;&nbsp;&nbsp;';

            //$status= Helper::lightbadge($singleRow->status);
            $status='<a href="javascript:void(0);"><i data-feather="user-check" updateid="'.$singleRow->id.'" action="approve" class="confirm-text  statuschange"></i></a>';
            // $typeuser= Helper::planUser($singleRow->getUserDetails->user_type);
            //  $status .='&nbsp;<i data-feather="edit" updateid="'.$singleRow->id.'" class="confirm-text  statuschange"></i>';
            //$client['created_at'] = date('Y-m-d');
            $data[] = [
                "id" => $singleRow->id,
                "name" =>$singleRow->name,
                "email"=>$singleRow->email,
                "created_at" =>date('Y-m-d', strtotime($singleRow->created_at)),
                "status"=>$status,
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

    public function publisherStatusChange(Request $request)
    {
        //print_r(($request->all()));exit;
        $statusId = $request->statusId;
        $action = $request->action;
        $getpublisher = User::where("id", $statusId)->first();
        if(!empty($getpublisher))
        {
            if($action == 'approve')
            {
                $data = [
                    'is_approve' => 1,
                    'status' => 1
                ];
            }
            else
            {
                if($getpublisher->status == 2)
                $status = 1;
                else
                $status = 2;
                $data = [
                    'status' => $status
                ];
            }

            User::where("id", $statusId)->update($data);

        }

        $response['status'] = true;
        if($action == 'approve')
        {
            $mailData = [
                'user'=>$getpublisher->name ?? "",
                'link'=> env('APP_URL_SERVE').'/login'
            ];
            Mail::to($getpublisher->email)->send(new PublisherApproved($mailData));
        }
        else
        {
            if($getpublisher->status == 2)
            {
                $mailData2 = [
                    'user'=>$getpublisher->name ?? "",
                    'link'=> env('APP_URL_SERVE').'/login'
                ];
                Mail::to($getpublisher->email)->send(new PublisherActiveAccount($mailData2));

            }
            else
            {
                $mailData1 = [
                    'user'=>$getpublisher->name ?? "",
                    'link'=> env('APP_URL_SERVE').'/login'
                ];
                Mail::to($getpublisher->email)->send(new PublisherInactiveAccount($mailData1));
            }

        }

        echo json_encode($response);
        exit();
    }



    public function docsStatus(Request $request)
    {
        $data = [];
        $doctype = $request->input('doctype');

        $ud = User::where("id", $request->userid)->first();

        if($doctype == 'approve')
        {
            $data = ['is_approve_docs'=>1];

            $mailData = [
                'publisher_name' => $ud->name,
                'link'=> env('APP_URL_SERVE').'/login'
            ];
            Mail::to($ud->email)->send(new DocumentApprove($mailData));

        }

        // if($doctype == 'unapprove')
        // {
        //     $data = ['is_approve_docs'=>0];

        //     $mailData = [
        //         'publisher_name' => $ud->name,
        //     ];
        //     Mail::to($ud->email)->send(new DocumentUnapprove($mailData));

        // }

        //$data['tax_notes'] = '';
        //$data['lic_notes'] = '';

        User_details::where("user_id", $request->userid)->update($data);
        return response()->json(['status'=>true, 'message'=>'Document update successfully']);
    }

    public function rejectlicDocsForm(Request $request)
    {
        $data = [
            'lic_doc'=>'',
            'is_approve_docs'=>0,
            'lic_notes'=>$request->notes
        ];
        User_details::where("user_id", $request->pub_id)->update($data);

        $ud = User::where("id", $request->pub_id)->first();
        $mailData = [
            'publisher_name' => $ud->name,
            'reason' => $request->notes,
            'link'=> env('APP_URL_SERVE').'/login'
        ];
        Mail::to($ud->email)->send(new DocumentUnapprove($mailData));

        return response()->json(['status'=>true, 'message'=>'Document update successfully']);
    }
    public function rejecttaxDocsForm(Request $request)
    {
        $data = [
            'tax_doc'=>'',
            'is_approve_docs'=>0,
            'tax_notes'=>$request->notes
        ];
        User_details::where("user_id", $request->pub_id)->update($data);


        $ud = User::where("id", $request->pub_id)->first();
        $mailData = [
            'publisher_name' => $ud->name,
            'reason' => $request->notes,
            'link'=> env('APP_URL_SERVE').'/login'
        ];
        Mail::to($ud->email)->send(new DocumentUnapprove($mailData));

        return response()->json(['status'=>true, 'message'=>'Document update successfully']);
    }

    public function viewPublisher($id)
    {

        $id=base64_decode($id);

        $getData = User::with('getUserDetails')->where('id', $id)->first();
        $breadcrumbs = [
            ['link' => "/admin/publisher", 'name' => "Home"],['link'=>"admin/publisher/ ",'name'=>"Publisher"]
        ];

         return view('/content/Admin/publisher/view-publisher', ['breadcrumbs' => $breadcrumbs,'id'=>$id,'getData'=>$getData]);
    }

    public function getPublisherComic(Request $request)
    {

        $columns = array(
            0 => 'id',
            1 => 'featured_image',
            2 => 'name',
            2 => 'avg',
            3 => 'is_featured',
            4 => 'access_type',
            5 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $pagename = $request->input('pagename');


        $getData = Comics_series::select('*')->where('status',1)->where("created_by", $request->input("userid"));

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

            $name = "<a href='".url('/admin/view-comics/'.$singleRow->id)."'><span class='dataField' id='".$singleRow->id."' name='".$singleRow->name."' description='".$singleRow->description."' access_type='".$singleRow->access_type."' is_featured='".$singleRow->is_featured."' comicCatid='".$comicCatid."' getCatName='".$getCatName."' imagePath='".asset('storage/images/thumbnail/'.$singleRow->featured_image)."'>$singleRow->name</span></a>";

            $image='<img src="'.env("IMAGE_PATH_medium").$singleRow->featured_image.'" width="60" height="60"></a>';


            $description=Helper::get_words($singleRow->description, 6);
            $accessType=Helper::accessType($singleRow->access_type);

            $data[] = [
                "id" => $singleRow->id,
                "featured_image"=>$image,
                "name" => $name,
                "avg" => $avgSum,
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


}
