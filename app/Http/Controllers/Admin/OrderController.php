<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Mail\NotesNotify;
use Illuminate\Support\Facades\Mail;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Orders,Comics_episodes,Comics_episode_page_mapping,Order_notes};
use Helper,Image,Storage;
use App\Models\{User};

class OrderController extends Controller
{

    public function index()
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/order",'name'=>"Orders"]
        ];
        return view('/content/Admin/order/order', ['breadcrumbs' => $breadcrumbs]);
    }

    public function get(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'price',
            3 => 'order_status',
            4 => 'created_at',
            5 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Orders::with('getOrderProductDetails','getUserDetails');

        $recordsFiltered =$getData->count();
        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('orders.amount', 'LIKE', "%{$search}%")
                    ;
                }
            ); $recordsFiltered = $getData->count();
        }
        $getArrayData = $getData->offset($start)->limit($limit)->orderBy($order, $dir) ->get();
        $data = [];
        foreach($getArrayData as $singleRow)
        {
            $action = '<a href="'.url('/admin/view-orders').'/'.$singleRow->id.'" class="item-edit"><i data-feather="eye"></i>';
            $amount = Helper::makeCurrency($singleRow->amount);
            $status="-";
            if($singleRow->order_status ==1){
               $status= '<span class="badge badge-light-secondary">Pending Payment</span>';
             }
            elseif($singleRow->order_status ==2){
                $status='<span class="badge badge-light-info">Refunded</span>';
             }
            elseif($singleRow->order_status ==3){
                $status= '<span class="badge badge-light-success">Processing</span>';
            }
            elseif($singleRow->order_status ==4){
                $status= '<span class="badge badge-light-primary"> On Hold</span>';
            }
            elseif($singleRow->order_status ==5){
                $status= '<span class="badge badge-light-danger">Cancelled</span>';
            }
            elseif($singleRow->order_status ==6){
                $status= '<span class="badge badge-light-success">Completed</span>';
            }
            elseif($singleRow->order_status ==7){
                $status= '<span class="badge bg-warning">Failed</span>';
            }
            $data[] = [
                "id" => $singleRow->id,
                "name" => $singleRow->getUserDetails->name ?? 'user',
                "price" => $amount,
                "order_status"=> $status,
                "created_at"=> $singleRow->created_at->format('Y-m-d'),
                "action" => $action
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

    public function getOrderNotes(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'created_at',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $getData = Order_notes::select('*')->where('order_id','=', $request->id);
        $recordsFiltered =$getData->count();
        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('order_notes.note', 'LIKE', "%{$search}%");
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
            $action = '<i data-feather="message-square"></i>&nbsp;&nbsp;<a href="#" class="item-edit" data-note-id="'.$singleRow->id.'" notify_customer="'.$singleRow->notify_customer.'"  note="'.$singleRow->note.'"  data-bs-toggle="modal" data-bs-target="#modals-slide-in-view"> '.$singleRow-> created_at.'</a>';
            $data[] = [
                "id" => $singleRow->id,
                "created_at"=> $action,
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

    public function viewOrders($id)
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/orders/",'name'=>"Orders"]
        ];
          $getData = Orders::with('getOrderProductDetails','getUserDetails')->where('id', $id)->first();
        //   echo "<pre>";
        //   print_r($getData->toArray());
        //   die();

       return view('/content/Admin/order/view-orders', ['breadcrumbs' => $breadcrumbs,'getData' => $getData, 'id'=>$id]);
    }

    public function orderStatus(Request $request){
         $order_status=$request->order_status;
          $orderid=$request->orderId;
          $order_series = Orders::find($request->orderId);
         $order_series->order_status = $request->order_status;
          $order_series->save();
          $response['status'] = true;
          $response['message'] = OrderStatus;
          echo json_encode($response);
    }

    public function insertOrderNote(Request $request)
    {
        $rules = array(
            'note' => 'required',

        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'note' => 'Note ',

        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails()) {

            $response['status'] = false;
            $response['message'] = ERROR;
        }
        else
        {
            $Order_note = new Order_notes;
            $Order_note->note = $request->note;
            $Order_note->order_id = $request->orderId;
            if($request->notify_customer == true)
            {
                $Order_note->notify_customer = 2;
            }
            //  $Comics_series->description = $request->description;
            $Order_note->save();

            if(!empty($Order_note) && $request->notify_customer == true)
            {
                $cd = Orders::with("getUserDetails")->where("id", $request->orderId)->first();
                $mailData = [
                    'user'=>$cd->getUserDetails->name ?? "",
                    'orderId' => $cd->order_request_id ?? "",
                    'notes' => $request->note
                ];

                $mailResponse = Mail::to($cd->getUserDetails->email)->send(new NotesNotify($mailData));
            }

            $response['status'] = true;
            $response['message'] = ORDERNOTEADD;
        }
        echo json_encode($response);

    }
}
