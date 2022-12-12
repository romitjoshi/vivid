<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use  Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Helper,Image,Storage,Imagick,DB, File;
use App\Models\admin\{Comics_episodes, Comics_episode_page_mapping, Comics_series, User_comic_notify};
use App\Models\{Device_info};
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PublisherAddEpisodeComic;
use App\Mail\ComicFavoriteAddEpisode;

class EpisodeController extends Controller
{
    public function index($id)
    {
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/comic/",'name'=>"Comics"]
        ];
        return view('/content/Admin/comic/add-episode', ['breadcrumbs' => $breadcrumbs, 'id'=>$id]);
    }

    public function editEpisode($id)
    {
        $getData = Comics_episodes::with('episode_page_mapping')->where('id', $id)->first();
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/comic/",'name'=>"Comics"]
        ];
        return view('/content/Admin/comic/edit-episode', ['breadcrumbs' => $breadcrumbs, 'getData'=>$getData , 'id'=>$id]);
    }

    public function get(Request $request)
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
            $action = '<a href="'.url('/admin/edit-episode').'/'.$singleRow->id.'"><i data-feather="edit"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i data-feather="trash-2"></i></a>';
            $image='<img src="'.env("IMAGE_PATH_medium").$singleRow->image.'" width="60" height="60">';

            $name = '<span class="dataField" id="'.$singleRow->id.'">'.$singleRow->name.'</span>';

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

    public function uploadComicPdf(Request $request)
    {
        $imagick = new Imagick();
        $imagick->readImage($request->file('comic_pdf'));
        $noOfPagesInPDF = $imagick->getNumberImages();

        // $imageName = [];
        // for ($i = 0; $i < $noOfPagesInPDF; $i++)
        // {
            //     $imageName[] = $fileName = time().'.jpg';
            //     $downloadPath = public_path('storage/images/').$fileName;
            //     $imagick->writeImages($downloadPath, true);
            // }
            return response()->json(['status' => true, 'pages' => $noOfPagesInPDF]);
    }

    public function insert(Request $request)
    {
       // print_r($request->all());exit;
        $rules = array(
            'total_page_count' => 'required',
            'name' => 'required',
            'description' => 'required',
            'image' => 'required',
            'comic_pdf' => 'required',
        );

        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'total_page_count' => 'Total Page Count',
            'name' => 'Name',
            'description' => 'Description',
            'image' => 'Image',
            'comic_pdf' => 'Comic Pdf',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {

            $response['status'] = false;
            $response['message'] = $validator->messages()->first();
        }
        else
        {
            $noOfPagesInPDF = 1;

            $audio_file = "";
            if($request->hasFile('audio_file')){
                $fileName = $request->file('audio_file');
                $audio_file = Helper::audioupload($fileName);
            }

            $comic_pdf = "";
            if($request->hasFile('comic_pdf')){
                $fileName = $request->file('comic_pdf');
                $comic_pdf = Helper::pdfupload($fileName);
            }

            $imageName = "";
            if($request->hasFile('image')){
                $fileName=$request->file('image');
                $imageName=Helper::imageupload($fileName);
            }

            $pdfToImage = [];
            if($request->hasFile('comic_pdf')){
                $imagick = new Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($request->file('comic_pdf'));
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompression(imagick::COMPRESSION_JPEG);
                $imagick->setImageCompressionQuality(60);
                $imagick->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                $noOfPagesInPDF = $imagick->getNumberImages();
                $time = time();
                $fnm = $time.'.jpg';
                $saveImagePath = public_path('/storage/pdf/images/'.$fnm);
                $imagick->writeImages($saveImagePath, true);
                $imagick->clear();
                $imagick->destroy();

                //echo $noOfPagesInPDF;exit;

                //Move Spaces
                $getPdfImg = Storage::disk('public')->listContents('pdf/images/');
                foreach($getPdfImg as $gpi)
                {
                    $basename = Storage::disk('public')->path('pdf/images/').$gpi['basename'];
                    Storage::disk('do_spaces')->put('public/pdf/images/'.'/'.$gpi['basename'], file_get_contents($basename), 'public');
                    File::delete($basename);
                }

                if($noOfPagesInPDF > 1)
                {
                    for ($i = 0; $i < $noOfPagesInPDF; $i++)
                    {
                        $pdfToImage[] = $time.'-'.$i.'.jpg';
                         //$pdfToImage[] = uniqid().time().uniqid().'.jpg';
                        //Storage::disk('local')->put('public/pdf/images'.'/'.$fileName, $imagick, 'public');
                    }
                }
                else
                {
                    $pdfToImage[] = $fnm;
                }

            }

            $getComic_access_type = Comics_series::where("id", $request->comics_series_id)->value("access_type");

            $Comics_episodes = new Comics_episodes;
            $Comics_episodes->comics_series_id = $request->comics_series_id;
            $Comics_episodes->name = $request->name;
            $Comics_episodes->description = $request->description ?? 'description';
            $Comics_episodes->image = $imageName;
            $Comics_episodes->comic_pdf = $comic_pdf;
            $Comics_episodes->total_page_count = $request->total_page_count;
            $Comics_episodes->audio_file = $audio_file;
            $Comics_episodes->slug = Helper::makeSlug("comics_episodes", $request->name);
            $Comics_episodes->status = 1;
            $Comics_episodes->approve = 1;

            if(!empty($request->adSide) && $request->adSide == 1)
            {
                $Comics_episodes->access_type = $request->access_type;
                $Comics_episodes->charge_coin_free_user = $request->charge_coin_free_user;
                $Comics_episodes->charge_coin_paid_user =$request->charge_coin_paid_user;

            }
            else
            {
                $Comics_episodes->access_type = $getComic_access_type ?? 0;
            }

            if(!empty($request->unable_preview))
            $Comics_episodes->preview_pages = $request->preview_pages;

            $countEpi = Comics_episodes::where("comics_series_id", $request->comics_series_id)->count();

            $Comics_episodes->episode_no = $countEpi + 1;

            $Comics_episodes->save();


            $cepmData = [];
            for($m = 0; $m < $noOfPagesInPDF; $m++)
            {
                $cepmData[] = [
                    'episode_id'=>$Comics_episodes->id,
                    'comics_series_id'=>$request->comics_series_id,
                    'page_number'=>$request->pageNumber[$m] ?? '1',
                    'image_url'=>$pdfToImage[$m] ?? "",
                    'audio_start'=>$request->starttime[$m] ?? '00:00:00',
                    'audio_end'=>$request->endtime[$m] ?? '00:00:00',
                ];
            }

            if(!empty($cepmData))
            Comics_episode_page_mapping::insert($cepmData);

            //push notification
            $getTokenData = User_comic_notify::select("user_id")->where("comic_id", $request->comics_series_id)->get();

            $comicName = comics_series::where("id", $request->comics_series_id)->value("name");

            $pub_name=Helper::getCustomerName(Auth::user()->id);
            foreach ($getTokenData as $cron)
            {
                $checkTokenExsist = Device_info::where('user_id', $cron->user_id)->orderBy('id','desc')->first();
                if(!empty($checkTokenExsist))
                {
                    $title= "New episode!";
                    $final_msg= "Check out the newest episode of $comicName !";
                    Helper::sendPush($checkTokenExsist->push_token, $final_msg, $title, $request->comics_series_id);
                    Helper::seveNotification($cron->user_id,$final_msg,$title,$request->comics_series_id);

                    //email send
                    $pubemail = Helper::getCustomerEmail($cron->user_id);
                    $mailData2 = [
                        'publisher_name' => $pub_name,
                        'comic_name' =>$comicName,
                        'link'=> env('APP_URL_SERVE').'/login'
                    ];
                    Mail::to($pubemail)->send(new ComicFavoriteAddEpisode($mailData2));
                }
            }

            //email

            $adminemail =Helper::getAdminEmail();
            $mailData = [
                'publisher_name' => $pub_name,
                'episode_name' =>$request->input('name'),
                'comic_name' =>$comicName,
                'link'=> env('APP_URL_SERVE').'/admin/login'
            ];
            Mail::to($adminemail)->send(new PublisherAddEpisodeComic($mailData));

            $response['status'] = true;
            $response['message'] = EPISODEADD;
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

        $getEpisode = Comics_episodes::where('id', $request->deleteid)->first();
        $cs = Comics_series::where("id", $getEpisode->comics_series_id)->where("created_by", Auth::user()->id)->first();

        if(empty($cs) && empty($request->action))
        {
            $response['status'] = false;
            $response['message'] = "User not associate with episode";
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
            Comics_episodes::where('id', $request->deleteid)->delete();
            Comics_episode_page_mapping::where('episode_id', $request->deleteid)->delete();
            $response['status'] = true;
            $response['message'] = EPISODEDELETE;
        }
        echo json_encode($response);
    }

    public function update(Request $request)
    {
        // print_r($request->all());exit;
        $rules = array(
            'total_page_count' => 'required',
            'name' => 'required',
            'description' => 'required',
        );

        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'total_page_count' => 'Total Page Count',
            'name' => 'Name',
            'description' => 'Description'
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {

            $response['status'] = false;
            $response['message'] = $validator->messages()->first();
        }
        else
        {
            $noOfPagesInPDF = $request->total_page_count;
            //Audio File
            $audio_file = "";
            if($request->hasFile('audio_file')){
                $audio_file = Helper::audioupload($request->file('audio_file'));
            }

            //Pdf File
            $comic_pdf = "";
            if($request->hasFile('comic_pdf')){
                $comic_pdf = Helper::pdfupload($request->file('comic_pdf'));
            }

            //Image Code
            $imageName = "";
            if($request->hasFile('image')){
                $image=$request->file('image');
                $imageName=Helper::imageupload($image);
            }

            $pdfToImage = [];
            if($request->hasFile('comic_pdf')){

                $imagick = new Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($request->file('comic_pdf'));
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompression(imagick::COMPRESSION_JPEG);
                $imagick->setImageCompressionQuality(60);
                $imagick->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                $noOfPagesInPDF = $imagick->getNumberImages();
                $time = time();
                $fnm = $time.'.jpg';
                $saveImagePath = public_path('/storage/pdf/images/'.$fnm);
                $imagick->writeImages($saveImagePath, true);
                $imagick->clear();
                $imagick->destroy();

                //Move Spaces
                $getPdfImg = Storage::disk('public')->listContents('pdf/images/');
                foreach($getPdfImg as $gpi)
                {
                    $basename = Storage::disk('public')->path('pdf/images/').$gpi['basename'];
                    Storage::disk('do_spaces')->put('public/pdf/images/'.'/'.$gpi['basename'], file_get_contents($basename), 'public');
                    File::delete($basename);
                }


                if($noOfPagesInPDF > 1)
                {
                    for ($i = 0; $i < $noOfPagesInPDF; $i++)
                    {
                        $pdfToImage[] = $time.'-'.$i.'.jpg';
                    }
                }
                else
                {
                    $pdfToImage[] = $fnm;
                }
            }

            $Comics_episodes = Comics_episodes::firstOrNew([
                'id' => $request->episode_id,
            ]);
            $Comics_episodes->comics_series_id = $request->comics_series_id;
            $Comics_episodes->name = $request->name;
            $Comics_episodes->description = $request->description;

            if(!empty($Comics_episodes->slug))
            $Comics_episodes->slug = Helper::makeSlug("comics_episodes", $request->name);

            if(!empty($imageName))
            $Comics_episodes->image = $imageName;

            if(!empty($comic_pdf))
            $Comics_episodes->comic_pdf = $comic_pdf;

            $Comics_episodes->total_page_count = $noOfPagesInPDF;

            if(!empty($audio_file))
            $Comics_episodes->audio_file = $audio_file;

            if(!empty($request->access_type))
            $Comics_episodes->access_type = $request->access_type;

            if(isset($request->status))
            $Comics_episodes->status = $request->status;

            if(!empty($request->unable_preview))
            $Comics_episodes->preview_pages = $request->preview_pages;

            if(!empty($request->charge_coin_free_user))
            $Comics_episodes->charge_coin_free_user = $request->charge_coin_free_user;

            if(!empty($request->charge_coin_paid_user))
            $Comics_episodes->charge_coin_paid_user = $request->charge_coin_paid_user;

            $Comics_episodes->save();

            $episode_page_mapping_id = $request->input('episode_page_mapping_id');

            if(!empty($episode_page_mapping_id))
            {
                Comics_episode_page_mapping::where('episode_id', $request->episode_id)->whereNotIn('id',$episode_page_mapping_id ?? [])->delete();
            }
            else
            {
                Comics_episode_page_mapping::where('episode_id', $request->episode_id)->whereNotIn('id',$episode_page_mapping_id ?? [])->delete();
            }

            for($m = 0; $m < $noOfPagesInPDF; $m++)
            {
                if(!empty($episode_page_mapping_id))
                {
                    $Comics_episode_page_mapping = Comics_episode_page_mapping::firstOrNew([
                        'id' => $request->episode_page_mapping_id[$m] ?? 0,
                        'episode_id'=>$request->episode_id,
                        'comics_series_id'=>$request->comics_series_id
                    ]);
                }
                else
                {
                    $Comics_episode_page_mapping = new Comics_episode_page_mapping;
                    $Comics_episode_page_mapping->episode_id = $Comics_episodes->id;
                    $Comics_episode_page_mapping->comics_series_id = $request->comics_series_id;
                }

                $Comics_episode_page_mapping->page_number = $request->pageNumber[$m] ?? '1';

                if(!empty($pdfToImage))
                $Comics_episode_page_mapping->image_url = $pdfToImage[$m] ?? "";

                $Comics_episode_page_mapping->audio_start = $request->starttime[$m] ?? '00:00:00';
                $Comics_episode_page_mapping->audio_end = $request->endtime[$m] ?? '00:00:00';
                $Comics_episode_page_mapping->save();
            }

            $response['status'] = true;
            $response['message'] = EPISODEUPDATE;
        }
        echo json_encode($response);
    }

}
