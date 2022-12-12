<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\Validator;
use  Illuminate\Support\Facades\Auth;
use App\Models\{User_details, User,User_subscription_details,Payout_requests};
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping, Comic_ratings,Orders};
use Helper,Image,Storage, DB;

class PayoutController extends Controller
{
    //
    public function index()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/payout",'name'=>"Payout"]
        ];
        return view('/content/Admin/payout/payout', ['breadcrumbs' => $breadcrumbs]);
    }
    public function indexrevenue()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/revenue",'name'=>"Revenue"]
        ];
        return view('/content/Admin/revenue/revenue', ['breadcrumbs' => $breadcrumbs]);
    }

    public function getrevenue(Request $request)
    {
       // die('ghihi');
        $columns = array(
            'created_at',
            'created_at',
            'price',
            'name',
            'subscription_type'
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = User_subscription_details::join('users','users.id','user_subscription_details.user_id')->select('users.name','user_subscription_details.created_at','user_subscription_details.price','user_subscription_details.subscription_type')->groupBy('user_subscription_details.id');

        $getDatas = Orders::join('users','users.id','orders.user_id')->select('users.name','orders.created_at','orders.amount as price',DB::raw('"" as subscription_type'))->groupBy('orders.id');

        $recordstotal = (clone $getData)->union(clone $getDatas)->count();


       if (!empty($request->input('search.value'))) {

            $searchvals = explode(' ',strtolower($request->input('search.value')));
            foreach($searchvals as $searchval){
                $getData->where(
                    function ($q) use ($searchval) {
                        return $q->where('users.name','LIKE','%'.$searchval.'%')
                        ->orWhere('user_subscription_details.price','LIKE','%'.$searchval.'%')
                        ->orWhere(DB::raw('CASE WHEN user_subscription_details.subscription_type = 1 THEN "Web" WHEN user_subscription_details.subscription_type = 2 THEN "Ios" ELSE "Android" END'),'LIKE','%'.$searchval.'%')->orWhere(DB::raw('"subscription"'),'LIKE','%'.$searchval.'%');
                    }
                );
                $getDatas->where(
                    function ($q) use ($searchval) {
                        return $q->where('users.name','LIKE','%'.$searchval.'%')
                        ->orWhere('orders.amount','LIKE','%'.$searchval.'%')->orWhere(DB::raw('"store purchase"'),'LIKE','%'.$searchval.'%');
                    }
                );
            }
        }

        $recordsFiltered = (clone $getData)->union(clone $getDatas)->count();
        $alldata = (clone $getData)->union($getDatas);
        $getArrayData = $alldata
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

        //print_r($getArrayData->toArray());exit;

        $data = [];

        foreach($getArrayData as $singleRow)
        {

            $subscription = '-';
           if(!empty($singleRow->subscription_type)){
                $subscription = Helper::subscriptionType($singleRow->subscription_type);
           }


           $price=Helper::makeCurrency($singleRow->price);

           $source = '<span class="badge bg-secondary">Store Purchase</span>';
           if(!empty($singleRow->subscription_type))
           $source = '<span class="badge bg-dark">Subscription</span>';

            $data[] = [
                "id" => $singleRow->id,
                "created_at" =>date('Y-m-d', strtotime($singleRow->created_at)),
                "price" => $price,
                "name" => $singleRow->name,
                "subscription_type" => $subscription,
                "source" => $source
            ];
        }

        $response = [];
        $response['recordsTotal'] = intval($recordstotal);
        $response['recordsFiltered'] = intval($recordsFiltered);
        $response['draw'] = intval($request->input('draw'));
        $response['status'] = true;
        $response['data'] = $data;
        echo json_encode($response);
    }


    public function get(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'amount',
            3 => 'status',
            4 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Payout_requests::with("getUser");
        $recordsFiltered =$getData->count();


        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('payout_requests.amount', 'LIKE', "%{$search}%")
                    ->orwhere('payout_requests.admin_note', 'LIKE', "%{$search}%");
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
            $action = '<a href="#" class="item-edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update"><i data-feather="edit"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i data-feather="trash-2"></i></a>';

            $amount = "<span class='dataField' id='".$singleRow->id."' amount='".$singleRow->amount."' status='".$singleRow->status."' admin_note='".$singleRow->admin_note."' >".Helper::makeCurrency($singleRow->amount)."</span>";

            $status=Helper::statusPayout($singleRow->status);

            $data[] = [
                "id" => $singleRow->id,
                "name" => $singleRow->getUser->name,
                "amount" => $amount,
                "status" => $status,
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
        //print_r($request->all());exit;
        $rules = array(
            'amount' => 'required',
            'note' => 'required',
            'status'=> 'required',

        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'amount' => 'amount',
            'note' => 'note',
            'status' => 'status',

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
            $payout = Payout_requests::find($request->updateid);

            $payout->amount = $request->amount;
            $payout->admin_note = $request->note;
            $payout->status=$request->status;
            $payout->save();

            if(!empty($request->status) && ($request->status == 1))
            {
                DB::table('user_details')
                ->where('user_id', $payout->user_id )
                ->increment('withdrawal', $request->amount);
            }


            $response['status'] = true;
            $response['message'] = PAYOUTUPDATE;
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
            $payout=Payout_requests::where('id', $request->deleteid)->delete();

            $response['status'] = true;
            $response['message'] = PAYOUTDELETE;
        }
        echo json_encode($response);
    }

}
