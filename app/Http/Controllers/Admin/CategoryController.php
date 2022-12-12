<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\admin\{Content_categories, Comics_categories_mapping};
use Helper, Str;

class CategoryController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link' => "admin/category", 'name' => "Category"]
        ];
        return view('/content/Admin/category', ['breadcrumbs' => $breadcrumbs]);
    }

    public function get(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'category_name',
            2 => 'status',
            3 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Content_categories::select('*');

        $recordsFiltered =$getData->count();

        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('content_categories.category_name', 'LIKE', "%{$search}%")
                    ->orwhere('content_categories.status', 'LIKE', "%{$search}%");
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
            $status = Helper::lightbadge($singleRow->status);
            $action = Helper::actionBtn();

            $name = '<span class="dataField" id="'.$singleRow->id.'" category_name="'.$singleRow->category_name.'" status="'.$singleRow->status.'">'.$singleRow->category_name.'</span>';

            $data[] = [
                "id" => $singleRow->id,
                "category_name" => $name,
                "status" => $status,
                "created_at" => $singleRow->created_at,
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
            'category_name' => 'required|min:2',
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'category_name' => 'Category Name',
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
            $category = new Content_categories;
            $category->category_name = strtolower($request->category_name);
            $category->slug = Helper::makeSlug("content_categories", $request->category_name);
            $category->save();
            $response['status'] = true;
            $response['message'] = CATEGORYADD;
        }
        echo json_encode($response);
    }

    public function update(Request $request)
    {
        $rules = array(
            'category_name' => 'required|min:2',
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'category_name' => 'Category Name',
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
            $category=Content_categories::find($request->updateid);
            //$category = new Content_categories;
            $category->category_name = strtolower($request->category_name);
            $category->status = $request->status;

            if(empty($category->slug))
            $category->slug = Helper::makeSlug("content_categories", $request->category_name);

            $category->save();

            $response['status'] = true;
            $response['message'] = CATEGORYUPDATE;
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
            'deleteid' => 'Category Name',
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
            $ccm = Comics_categories_mapping::where("category_id", $request->deleteid)->get();
            if($ccm->isNotEmpty())
            {
                $response['status'] = false;
                $response['message'] = " You have already used this category while creating a content and it can't be deleted";
                echo json_encode($response);
                exit;
            }

            $category = Content_categories::where("id", $request->deleteid)->delete();        

            $response['status'] = true;
            $response['message'] = CATEGORYDELETE;
        }
        echo json_encode($response);
       
    }

    public function getcategorybyname(Request $request)
    {
        $getData = Content_categories::where("category_name","like","%".$request->input('term')."%")->orderby('category_name','asc')->limit(25)->get();

        // print_r($getData->toArray());
        // exit;
        $outdata = [];
        foreach ($getData as $value) {
            $indata = array();
            $indata['id'] = $value->id;
            $indata['text'] = $value->category_name;
            $outdata[] = $indata;
        }
        echo json_encode($outdata);
    }
}
