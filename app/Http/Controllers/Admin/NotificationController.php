<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Helper,DB,Storage;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping,PushNotification};
use App\Models\{Device_info};
 
class NotificationController extends Controller
{
    //
    public function index()
    {
        
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link' => "admin/notification", 'name' => "Notification"]
        ];
        return view('/content/Admin/push-notification', ['breadcrumbs' => $breadcrumbs]);
    }
    
    public function get(Request $request) {
        
        $columns = array(
            0 => 'id',
            1 => 'push_title',
            2 => 'push_description',
            3 => 'user',
            4 => 'status',
            5 => 'updated_at',
            6 => 'action',
            
        );


        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columns[$request->input('order.0.column')];

        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = DB::table('push_notifications');

        $dataFilter =$getData->count();


        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('push_notifications.push_title', 'LIKE', "%{$search}%")
                     ->orwhere('push_notifications.push_description', 'LIKE', "%{$search}%")
                    ->orwhere('push_notifications.updated_at', 'LIKE', "%{$search}%");
                }
            );
            $dataFilter =$getData->count();
        }
        $getFilterData = $getData->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
        $data = [];
        foreach ($getFilterData as $data) {
           
            $action = "";

            $action .= '<a href="#" class="item-edit pushView" action="view" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </a>';

            if($data->status == 0)
            {
                $action .= '&nbsp;&nbsp;<a href="#" class="item-edit pushView" action="edit" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update">
                            <svg xmlns=" http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit font-small-4">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>';
            }
            
            $push_conditions = json_decode($data->push_conditions);

            $inactiveDays = "";
            if(!empty((array)$push_conditions))
            {
                $inactiveDays = $push_conditions->inactiveDays;
            }
            $title = '<span class="pushDetails" id="'.$data->id.'" 	push_title="'.$data->push_title.'"	push_description="'.$data->push_description.'" send_type="'.$data->send_type.'" inactiveDays="'.$inactiveDays.'" send_datetime="'.$data->send_datetime.'" status="'.$data->status.'" user="'.$data->user.'">'.$data->push_title.'</span>';

            $send_type = "Instant";
            if($data->send_type == 2)
            $send_type = "Scheduled";

            $status = '<span class="badge bg-primary">Pending</span>';
            if($data->status == 1)
            $status = '<span class="badge bg-success">Sent</span>';


            $description = implode(' ', array_slice(str_word_count($data->push_description,1), 0, 10));

            $number_of_users = "-";
            if(!empty($data->number_of_users))
            {
                $number_of_users = $data->number_of_users;
            }

            $dataArray[] = [
                "id" => $data->id,
                "push_title" => $title,
                "push_description" => $description,
                "user"=>$send_type,
                "status" => $status,
                "updated_at" => $data->updated_at,
                "action" => $action,
            ];
        }

        $response = [];
        $response['recordsTotal'] = intval($getData->count());
        $response['recordsFiltered'] = intval($dataFilter ?? 0);
        $response['draw'] = intval($request->input('draw'));
        if (!empty($dataArray)) {
            $response['status'] = true;
            $response['data'] = $dataArray;
            
        } else {
            $response['status'] = false;
            $response['data'] = [];
        }
        echo json_encode($response);
    }


    public function insertNotification(Request $request)
    {
        $today = date("Y-m-d H:i:s");
        // Helper::pd($request->all());
       // $today = Dateset::currentDateGet();
        $PushNotification = new PushNotification;
        $PushNotification->send_type = $request->input('pushType');


        if($request->input('pushType') == 1)
        {
            if($request->input('user') == 1)
            {
                // $getTokenData = Device_info::get();
                // foreach ($getTokenData as $data)
                // {
                //     $title= $request->input('push_title');
                //     $final_msg= $request->input('push_description'); 
                //     $checkSend = Helper::sendPush($data->push_token, $final_msg, $title);   
                // }

                $title= $request->input('push_title');
                $final_msg= $request->input('push_description'); 
                $push_token = "";
                Helper::sendPushToAll($push_token, $final_msg, $title);
            }
            else
            {
                $makeLastData = date('Y-m-d 00:00:00', strtotime('-'.$request->input('inactiveDays').' days'));

                $getTokenData = User_detail::select("user_id")->where("last_active", '<', $makeLastData)->get();
                foreach ($getTokenData as $cron)
                {
                    $checkTokenExsist = Device_info::where('user_id', $cron->user_id)->orderBy('id','desc')->first();
                    if(!empty($checkTokenExsist))
                    {
                        $title= $request->input('push_title');
                        $final_msg= $request->input('push_description');
                        $checkSend = Helper::sendPush($checkTokenExsist->push_token, $final_msg, $title);
                    } 
                }
            }
            $PushNotification->status = 1;
        }


        $PushNotification->send_datetime = $request->input('pushDateTime');
        $PushNotification->push_title = $request->input('push_title');
        $PushNotification->push_description = $request->input('push_description');
        $PushNotification->user = $request->input('user');

        $pc = "{}";
        if(!empty($request->input('inactiveDays')))
        {
            $push_conditions['inactiveDays'] = $request->input('inactiveDays');
            $pc = json_encode($push_conditions);
        }
        $PushNotification->push_conditions = $pc;


        $PushNotification->created_at = $today;
        $PushNotification->updated_at = $today;



        $PushNotification->save();

       if(!empty($PushNotification))
       {    
            $response['status'] = true;
            $response['message'] = "Notification added successfully";
       }
       else
       {
            $response['status'] = false;
            $response['message'] = "Error with added data";
       }
       echo json_encode($response);
       exit;
    }

    public function updateNotification(Request $request)
    {
        //$today = Dateset::currentDateGet();
        $today = date("Y-m-d H:i:s");
        $updatedId = $request->input("updatedId");

        $updateData['push_title'] = $request->input('push_title');
        $updateData['push_description'] = $request->input('push_description');
        $updateData['send_type'] = $request->input('pushType');


        if($request->input('pushType') == 1)
        {
            if($request->input('user') == 1)
            {
                $title= $request->input('push_title');
                $final_msg= $request->input('push_description'); 
                $push_token = "";
                Helper::sendPushToAll($push_token, $final_msg, $title);
            }
            else
            {
                $makeLastData = date('Y-m-d 00:00:00', strtotime('-'.$request->input('inactiveDays').' days'));
                
                $getTokenData = User_detail::select("user_id")->where("last_active", '<', $makeLastData)->get();
                foreach ($getTokenData as $cron)
                {
                    $checkTokenExsist = Device_info::where('customer_id', $cron->user_id)->orderBy('id','desc')->first();
                    if(!empty($checkTokenExsist))
                    {
                        $title= $request->input('push_title');
                        $final_msg= $request->input('push_description');
                        $checkSend = Helper::sendPush($checkTokenExsist->push_token, $final_msg, $title);
                    } 
                }
            }
            $updateData['status'] = 1;
        }


        $updateData['send_datetime'] = $request->input('pushDateTime');
        $updateData['user'] = $request->input('user');

        $pc = "{}";
        if(!empty($request->input('inactiveDays')))
        {
            $push_conditions['inactiveDays'] = $request->input('inactiveDays');
            $pc = json_encode($push_conditions);
        }
        $updateData['push_conditions'] = $pc;

       $updateData['updated_at'] = $today;

       $PushNotification = PushNotification::where("id", $updatedId)->update($updateData);

       if(!empty($PushNotification))
       {    
            $response['status'] = true;
            $response['message'] = "Notification Updated successfully";
       }
       else
       {
            $response['status'] = false;
            $response['message'] = "Error with update data";
       }
       echo json_encode($response);
       exit;
    }
    

}
