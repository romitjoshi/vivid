<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\admin\{Content_categories,Plan};
use Helper;
class PlanController extends Controller
{
    //
    public function index()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link' => "admin/plan", 'name' => "Plan"]
        ];
        return view('/content/Admin/plan/plan', ['breadcrumbs' => $breadcrumbs]);
    }
    public function get(Request $request)
    {   
        $columns = array(
            0 => 'id',
            1 => 'price',
            2 => 'type',
            3 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Plan::select('*');
        $recordsFiltered =$getData->count();

        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('plans.id', 'LIKE', "%{$search}%")
                    ->orwhere('plans.price', 'LIKE', "%{$search}%");
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
            $action = '<a href="'.url('/admin/view-comics').'/'.$singleRow->id.'" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><i data-feather="edit"></i></a>&nbsp; ';
            $name = '<span class="dataField" id="'.$singleRow->id.'" price="'.$singleRow->price.'" type="'.$singleRow->type.'">'.Helper::makeCurrency($singleRow->price).'</span>';
           $type=Helper::planType($singleRow->type);
            $data[] = [
                "id" => $singleRow->id,
                "price" =>$name,
                "type" => $type,
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

    public function update(Request $request)
    {
        $rules = array(
            'price' => 'required',
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'price' => 'Price',
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
            $plan=Plan::find($request->updateid);
            $plan->price = $request->price;
            $plan->save();
            $response['status'] = true;
            $response['message'] = PLANUPDATE;
        }
        echo json_encode($response);
    } 
}
 

