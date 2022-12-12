<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\Validator;
use  Illuminate\Support\Facades\Auth;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,Comics_episodes,Comics_episode_page_mapping,Store_products};
use Helper,Image,Storage, DB;

class ProductController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['link' => "/admin", 'name' => "Home"],['link'=>"admin/product",'name'=>"Product"]
        ];
        return view('/content/Admin/product/product', ['breadcrumbs' => $breadcrumbs]);
    }

    public function get(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'featured_Image',
            2 => 'product_name',
            3 => 'price',
            4 => 'special_price',
            5 => 'available_qty',
            6 => 'sold_qty',
            7 => 'action',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $getData = Store_products::select('*');
        $recordsFiltered =$getData->count();


        if (!empty($request->input('search.value'))) {
            $getData->where(
                function ($q) use ($search) {
                    return $q->where('store_products.product_name', 'LIKE', "%{$search}%");
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
            $action = '<a href="'.url('/admin/edit-product').'/'.$singleRow->id.'"><i data-feather="edit"></i></a>&nbsp;&nbsp;&nbsp;<a href="#" class="item-delete" data-bs-toggle="modal" data-bs-target="#modals-slide-in-delete"><i data-feather="trash-2"></i></a>';

            $name = "<a href='".url('/admin/edit-product/'.$singleRow->id)."'><span class='dataField' id='".$singleRow->id."' product_name='".$singleRow->product_name."' price='".$singleRow->price."' special_price='".$singleRow->special_price."' available_qty='".$singleRow->available_qty."' description='".$singleRow->description."'>$singleRow->product_name</span>";
            $image='<img src="'.env("IMAGE_PATH_medium").$singleRow->featured_Image.'" width="60" height="60"></a>';

            $description=Helper::get_words($singleRow->description, 7);

            $price = Helper::makeCurrency($singleRow->price);
            $special_price = Helper::makeCurrency($singleRow->special_price);
            $sold_qty = ($singleRow->available_qty)-($singleRow->remaining_qty);

            if($singleRow->is_special == 0)
            {
                $special= 'NA';

            }
            else{
                $special = $special_price;
            }
            //print_r( $isspecial );
            //die('adfa');
            $data[] = [
                "id" => $singleRow->id,
                "featured_Image" => $image,
                "product_name"=>$name,
                "price" =>$price,
                "special_price" =>$special,
                "available_qty" =>$singleRow->available_qty,
                "sold_qty" =>$sold_qty,
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

    public function insertProductView(){

        $breadcrumbs = [
            ['link' => "/admin/product", 'name' => "Home"],['link'=>"admin/product/insert-product-view",'name'=>"Product"]
        ];
        return view('/content/Admin/product/add-product', ['breadcrumbs' => $breadcrumbs]);
    }

    public function insert(Request $request)
    {
        //print_r($request->all());
        //die('a');
        do {
            $deep_id = Helper::generateRandomString();
        } while ( DB::table( 'store_products' )->where( 'deep_id', $deep_id )->exists() );

        $rules = array(
            'product_name' => 'required',
            'featured_Image' => 'required',
            'description' => 'required',
            'price' => 'required',
            'available_qty' => 'required',


        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'product_name'=>'Product Name',
            'featured_Image'=> 'Featured Image',
            'description'=>'Description',
            'price'=>'Price',
            'available_qty'=>'Available Qty',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = ERROR;
        }
        else
        {
            $currentUserId = Auth::user()->id;

            $image=$request->file('featured_Image');
            $fileName=Helper::imageupload($image);

            $addionalName ='[]';

            if(!empty($request->file('additional_images'))){
                $additional_images = $request->file('additional_images');
                $filenametwo = [];
                foreach($additional_images as $value)
                {
                    $filenametwo[]=Helper::imageupload($value);
                }
                $addionalName = json_encode($filenametwo);
            }


            $Store_products = new Store_products;

            $Store_products->product_name = $request->product_name;
            $Store_products->featured_Image = $fileName;
            $Store_products->additional_images = $addionalName;
            $Store_products->description = $request->description;
            $Store_products->price = $request->price;
            $Store_products->special_price = $request->price;
            $Store_products->slug = Helper::makeSlug("store_products", $request->product_name);

            if(!empty($request->special_price))
            {
                $Store_products->is_special = 1;
                $Store_products->special_price = $request->special_price;
            }

            $Store_products->available_qty = $request->available_qty;
            $Store_products->remaining_qty = $request->available_qty;
            $Store_products->deep_url = env("APP_URL").'/product/'.$deep_id;
            $Store_products->deep_id = $deep_id;
            $Store_products->added_by = $currentUserId;
            $Store_products->updated_by = $currentUserId;

            if(!empty($request->product_type) && $request->product_type == 2)
            {
                $Store_products->type = $request->product_type;
                $Store_products->min_comics = $request->min_comics;
                $Store_products->max_comics = $request->max_comics;
                if(!empty($request->comic))
                {
                    $c_id = json_encode($request->comic);
                    $Store_products->comic_series_id = $c_id;
                }
            }

            $Store_products->save();

            $response['status'] = true;
            $response['message'] = PRODUCTADD;
        }
        echo json_encode($response);

    }

    public function editProduct($id)
    {

        $getData = Store_products::where('id', $id)->first();
        $getComicData = Comics_series::get();
        $breadcrumbs = [
            ['link' => "/admin/home", 'name' => "Home"],['link'=>"admin/product/",'name'=>"Product"]
        ];
        return view('/content/Admin/product/edit-product', ['breadcrumbs' => $breadcrumbs, 'getData'=>$getData , 'id'=>$id, 'getComicData'=>$getComicData]);
    }

    public function update(Request $request)
    {
        // print_r($request->all());
        // exit;
       $rules = array(
        'product_name' => 'required',
        'description' => 'required',
        'price' => 'required',
        'available_qty' => 'required',
         );
      $messages = array(
        'required' => ':attribute is required.'
     );
     $fieldNames = array(
          'product_name'=>'Product Name',
          'featured_Image'=> 'Featured Image',
          'description'=>'Description',
          'price'=>'Price',
          'available_qty'=>'Available Qty',
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
            $getData = Store_products::where('id','=', $request->product_id)->first();


            $file="";
            if($request->hasFile('featured_Image')){
                $image=$request->file('featured_Image');
                $file=Helper::imageupload($image);
            }

            if($request->hasFile('additional_images')){

                $additional_images = $request->file('additional_images');
                $filenametwo = [];
                foreach($additional_images as $value)
                {
                    $filenametwo[]=Helper::imageupload($value);
                }

                $getimages = $getData->additional_images;
                $array = json_decode($getimages);

                $totalImage = array_merge($array, $filenametwo);
                $imageName = json_encode($totalImage);

            }

            $currentUserId = Auth::user()->id;

            $Store_products = Store_products::find($request->product_id);
            $Store_products->product_name = $request->product_name;

            if($request->hasFile('featured_Image'))
            $Store_products->featured_Image = $file;

            if($request->hasFile('additional_images'))
            $Store_products->additional_images = $imageName;

            $Store_products->description = $request->description;
            $Store_products->price = $request->price;
            $Store_products->special_price = $request->price;

            if(empty($Store_products->slug))
            $Store_products->slug = Helper::makeSlug("store_products", $request->product_name);


            if(!empty($request->special_price))
            {
                $Store_products->is_special = 1;
                $Store_products->special_price = $request->special_price;
            }
            else
            {
                $Store_products->is_special = 0;
            }

            $new = $request->available_qty;
            $avlQty = $getData->available_qty;
            $rmnQty = $getData->remaining_qty;


            $soldQty = $avlQty - $rmnQty;

            if($avlQty > $new)
            {
                if($soldQty > $new)
                {
                    $response['status'] = false;
                    $response['message'] = "Allready sold $soldQty Quantity.";
                    echo json_encode($response);
                    exit;
                }

                $marginQty = $avlQty - $new;
                $remaining_qty = $rmnQty - $marginQty;
            }
            else
            {
                $marginQty = $new - $avlQty;
                $remaining_qty = $rmnQty + $marginQty;
            }

            $Store_products->available_qty = $new;
            $Store_products->remaining_qty = $remaining_qty;

            $Store_products->added_by = $currentUserId;
            $Store_products->updated_by = $currentUserId;

            if(!empty($request->product_type) && $request->product_type == 2)
            {
                $Store_products->type = $request->product_type;
                $Store_products->min_comics = $request->min_comics;
                $Store_products->max_comics = $request->max_comics;
                if(!empty($request->comic))
                {
                    $c_id = json_encode($request->comic);
                    $Store_products->comic_series_id = $c_id;
                }
            }

            $Store_products->save();

            $response['status'] = true;
            $response['message'] = PRODUCTUPDATE;
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
            $category=Store_products::where('id', $request->deleteid)->delete();
            // $category=Comics_categories_mapping::where('comics_id', $request->deleteid)->delete();
            $response['status'] = true;
            $response['message'] = PRODUCTDELETE;
        }
        echo json_encode($response);
    }

    public function deleteimage(Request $request){
        $product_id = $request->product_id;
        $getImageName = $request->thisVal;
        $getData = Store_products::select('additional_images')->where('id','=', $product_id)->first();
        $getimages = $getData->additional_images;
        $array = json_decode($getimages);
        if(!empty($array))
        {
            if (($key = array_search($getImageName, $array)) !== false) {
                unset($array[$key]);
            }

            $imageName = [];
            foreach($array as $a)
            {
                $imageName[] = $a;
            }

            $imageArr = json_encode($imageName);
            $data = [
                'additional_images'=>$imageArr
            ];
            Store_products::where('id','=', $product_id)->update($data);

        }
        $response['status'] = true;
        $response['message'] = IMAGEDELETE;
        echo json_encode($response);
    }

    public function getcomicbyname(Request $request){
        $getData = Content_categories::where("category_name","like","%".$request->input('term')."%")->orderby('category_name','asc')->limit(25)->get();

        $getData1 = DB::table('comics_series as cs')
        ->where("cs.status", 1)
        ->where("ce.status", 1)
        ->where("u.status", 1)
        ->select("cs.id", "cs.name", "cs.featured_image", "cs.access_type", "cs.deep_url", "cs.slug","cs.description")
        ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')
        ->join('users as u', 'u.id', '=', 'cs.created_by')
        ->groupBy("cs.id")
        ->limit(25)->get();

        if(!empty($getData1))
        {
            $getData = $getData1->toArray();
        }

        //echo "<pre>";print_r($getData);exit;
            $outdata = [];
            foreach ($getData as $value) {
                $indata = array();
                $indata['id'] = $value->id;
                $indata['text'] = $value->name;
                $outdata[] = $indata;
            }
        echo json_encode($outdata);
    }
}
