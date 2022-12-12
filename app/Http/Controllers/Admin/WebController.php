<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\Validator;
use  Illuminate\Support\Facades\Auth;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping,Banner};
use Helper,Image,Storage;
class WebController extends Controller
{
    //
    public function index(){
        $breadcrumbs = [
            ['link' => "/admin/banner", 'name' => "Home"],['link'=>"admin/user/ ",'name'=>"Banner"]
        ];
        return view('/content/Admin/web/banner', ['breadcrumbs' => $breadcrumbs]);
    }
    public function get(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'image',
            2 => 'status',
            3 => 'order',
            4 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Banner::select('*');
        $recordsFiltered =$getData->count();


        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('banners.order', 'LIKE', "%{$search}%");
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
            $action = '<a href="'.url('/admin/edit-banner').'/'.$singleRow->id.'" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><i data-feather="edit"></i></a>&nbsp;&nbsp;&nbsp;';

            if(count($getArrayData) > 1)
            {
                $action .='<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i data-feather="trash-2"></i></a>';
            }

            $banner = '<span class="dataField" id="'.$singleRow->id.'" name="'.$singleRow->name.'" link="'.$singleRow->link.'" order="'.$singleRow->order.'" image="'.env("IMAGE_PATH_medium").$singleRow->image.'" mobile_banner ="'.env("IMAGE_PATH_medium").$singleRow->mobile_banner.'"status="'.$singleRow->status.'">'.$singleRow->order.'</span>';

            $image='<img src="'.env("IMAGE_PATH_medium").$singleRow->image.'" width="60" height="60"></a>';
                $status= Helper::lightbadge($singleRow->status);

            $data[] = [
                "id" => $singleRow->id,
                "image" =>$image,
                "order"=>$banner,

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
    public function insert(Request $request)
    {
        $rules = array(
            'order' => 'required',
            'status' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg',
            'mobile_banner'=>'required|mimes:jpeg,png,jpg',

        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'mobile_banner'=>'mobileimage',
            'image' => 'Image',
            'status' => 'Status',
            'order' => 'Order',

        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails()) {

            $response['status'] = false;
            $response['message'] = ERROR;
        }
        else
        {
            $image=$request->file('image');
            $fileName=Helper::imageupload($image);

            $mobileimage=$request->file('mobile_banner');
            $mobileimagename=Helper::imageupload($mobileimage);
            $banner = new Banner;

            $banner->image = $fileName;
            $banner->mobile_banner= $mobileimagename;
            $banner->status = $request->status;
            $banner->order = $request->order;
            $banner->name = $request->name;
            $banner->link = $request->link;
            $banner->save();
            $response['status'] = true;
            $response['message'] = BANNERADD;
        }
        echo json_encode($response);

    }

    public function update(Request $request)
    {
       //print_r($request->all());exit;
        $rules = array(
            'order' => 'required',
            'status' => 'required',

        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(

            'status' => 'Status',
            'order' => 'Order',
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
            $banner = Banner::find($request->updateid);

            $banner->order = $request->order;
            $banner->status=$request->status;
            $banner->name=$request->name;
            $banner->link=$request->link;
            if($request->hasFile('image')){
                $image=$request->file('image');
                $fileName=Helper::imageupload($image);
                $banner->image = $fileName;
            }


            if($request->hasFile('mobile_banner')){
                $mobileimage=$request->file('mobile_banner');
                $mobilefileName=Helper::imageupload($mobileimage);
                $banner->mobile_banner = $mobilefileName;
            }

            $banner->save();

            $response['status'] = true;
            $response['message'] = BANNERUPDATE;
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

        if ($validator->fails())
        {
            $response['status'] = false;
            $response['message'] = ERROR;
        }
        else
        {
            $category=Banner::where('id', $request->deleteid)->delete();


            $response['status'] = true;
            $response['message'] = BANNERDELETE;
        }
        echo json_encode($response);
    }

}
