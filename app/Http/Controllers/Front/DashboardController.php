<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\admin\{Comics_series,Content_categories,Comics_categories_mapping,User_comic_notify,Comics_episodes,Comics_episode_page_mapping,User_comics,Comic_ratings,Store_products, Orders,Order_product_details,Coin_slab,Notification};
use Helper,Image,Storage,Str, DB, Stripe;
use App\Models\{User,Cart, User_details, User_address,User_wallet};
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
   public function notify(Request $request)
   {
        $getNotify = User_comic_notify::where("comic_id",$request->id)->where("user_id",      Auth::user()->id)->count();
            if($getNotify > 0)
            {
                User_comic_notify::where("comic_id",$request->id)->where("user_id", Auth::user()->id)->delete();
                return response()->json(['status' => true, 'message' => "Notify Removed Successfully"], 200);
            }
            else
            {
                $User_comic_notify = new User_comic_notify;
                $User_comic_notify->comic_id = $request->id;
                $User_comic_notify->user_id =Auth::user()->id;
                $User_comic_notify->save();
            }
            return response()->json(['status' => true, 'message' => "Notify Added Successfully"], 200);
   }


   public function rating(Request $request)
   {
       //print_r($request->all());exit;
       $comic_id = $request->input('comic_id');
       $ratings = $request->input('rate');
       $checkComic = Comics_series::where("id", $comic_id)->get();
       if($checkComic->isEmpty())
       {
           return response()->json(['status' => false, 'message' => "Comics not found"], 200);
       }
       $Comic_ratings = Comic_ratings::firstOrNew([
           'user_id'=>Auth::user()->id,
           'comic_id'=>$comic_id,
       ]);
       $Comic_ratings->ratings = $ratings;
       $Comic_ratings->save();
       return redirect()->back();
   }


   public function genres($slug)
   {
        $id = Helper::getGenersIdByslug($slug);
        if(empty($id))
        return redirect()->back();

        $limit  = 30;
        $page = 1;
        $offset = ($page - 1) * $limit;


        $comicList = DB::table('comics_series as cs')
        ->where("cs.status", 1)
        ->where("ce.status", 1)
        ->select("cs.id", "cs.name", "cs.featured_image", "cs.access_type", "cs.deep_url","cs.description" ,"cs.slug", DB::Raw('CAST(IFNULL( AVG( `cr`.`ratings`) , 0.0 ) AS DECIMAL(10,2)) as ratings'))
        ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
        ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')
        ->join('users as u', 'u.id', '=', 'cs.created_by')
        ->leftjoin('comic_ratings as cr', 'cr.comic_id', '=', 'cs.id');

        if(!empty($id) && $id != 0)
        {
            $comicList = $comicList->where("ccm.category_id", $id);
        }

        $comicList = $comicList->groupby('cs.id');
        $comicList = $comicList->take($limit)
        ->skip($offset)->get()->map(function ($comic) {
            $comic->name = $comic->name;
             return $comic;
         });

        $data=  Content_categories::where('status', 1)->get();
        $catName = Content_categories::where('id', $id)->value("category_name");
        return view('/content/front/genres-details',['category'=>$data, 'comicList'=>$comicList, 'catName'=>$catName]);
   }


    public function getComic(Request $request)
    {
        $userLoginCheck = false;
        $ud = [];
        if(!empty(Auth::user()->id)){
            $userLoginCheck = true;
            $ud = User_details::where('user_id',Auth::user()->id)->first();

        }
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;

        $comicList = DB::table('comics_series as cs')
        ->where("cs.status", 1)
        ->where("ce.status", 1)
        ->where("u.status", 1)
        ->select("cs.id", "cs.name", "cs.featured_image", "cs.access_type", "cs.deep_url", "cs.slug","cs.description" , DB::Raw('CAST(IFNULL( AVG( `cr`.`ratings`) , 0.0 ) AS DECIMAL(10,2)) as ratings'))
        ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
        ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')
        ->join('users as u', 'u.id', '=', 'cs.created_by')
        ->leftjoin('comic_ratings as cr', 'cr.comic_id', '=', 'cs.id');

        if(!empty($request->input('category')) && $request->input('category') != 0)
        {
            $comicList = $comicList->where("ccm.category_id", $request->input('category'));
        }

        if(!empty($request->input('serach_text')))
        {
            $searchTerm = $request->input('serach_text');
            $comicList = $comicList->where('cs.name', 'LIKE', "%{$searchTerm}%");
        }

        if(!empty($request->input('sort_by')))
        {
            $comicList = $comicList->orderBy('cs.id', $request->input('sort_by'));
            //$comicList = latest('cs.id');
        }

       $comicList = $comicList->groupby('cs.id');
       $comicList = $comicList->take($limit)
       ->skip($offset)->get()->map(function ($comic) {
           $comic->name = $comic->name;
            return $comic;
        });

       if(!empty($comicList))
       {

           return response()->json(['status'=> true, 'data'=>$comicList, 'userLoginCheck'=>$userLoginCheck, 'ud'=>$ud], 200);
       }

       return response()->json(['status'=> false, 'data'=>(object)[]], 200);
    }

    public function store()
    {
        return view('/content/front/product');
    }

    public function getProduct(Request $request)
    {
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;

        $productList = Store_products::select("id", "product_name", "featured_Image", "price","special_price", "is_special", "slug")
        ->where("remaining_qty", ">", 0)
        ->orderBy("id", "DESC")
        ->take($limit)
        ->skip($offset)->get()->map(function ($comic) {
                $comic->product_name = $comic->product_name;
                $comic->special_price = Helper::makeCurrency($comic->special_price);
                $comic->price = Helper::makeCurrency($comic->price);
            return $comic;
        });

        if(!empty($productList))
        {
            return response()->json(['status'=> true, 'data'=>$productList], 200);
        }
        return response()->json(['status'=> false, 'data'=>(object)[]], 200);
    }

    public function productDetailsNew($slug)
    {
        $id = Helper::getProductIdByslug($slug);
        if(empty($id))
        return redirect()->back();


        $getProduct = Store_products::where("id", $id)->get();
        if($getProduct->isEmpty())
        {
            return response()->json(['status' => false, 'message' => "Product Not Found"], 200);
        }
        $response = [];
        foreach($getProduct as $gP)
        {
            $response['details'] = [
                'id'=>$gP->id,
                'product_name'=>$gP->product_name,
                'featured_Image'=>$gP->featured_Image,
                'description'=>$gP->description,
                'remaining_qty'=>$gP->remaining_qty,
                'price'=>Helper::makeCurrencyWithoutSymbol($gP->price),
                'special_price'=>Helper::makeCurrencyWithoutSymbol($gP->special_price),
                'is_special'=>$gP->is_special
            ];



            $addImg = [];

            $addImg[] = $gP->featured_Image;
            $additional_images = json_decode($gP->additional_images);
            $addImg = array_merge($addImg,$additional_images);
            $response['details']['additional_images'] = (object)[];
            if(!empty($addImg))
            {
                $response['details']['additional_images'] = $addImg;
            }


            $getComic = (object)[];
            if(!empty($gP->comic_series_id))
            {
                $decodeId = json_decode($gP->comic_series_id);
                $getComic = Comics_series::select("id", "featured_image", "name")->whereIn("id", $decodeId)->get();

                $response['details']['configure']['comics'] = $getComic;
                $response['details']['configure']['max_comics'] = $gP->max_comics;
                $response['details']['configure']['min_comics'] = $gP->min_comics;

            }
        }
        $getLatesProduct = Store_products::latest('id')->take(10)->get();
        $getLatesProduct = $getLatesProduct->except($id);
        $productList = [];
        foreach($getLatesProduct as $gPl)
        {
            $productList[] = [
                'id'=>$gPl->id,
                'product_name'=>$gPl->product_name,
                'featured_Image'=>$gPl->featured_Image,
                'price'=>Helper::makeCurrencyWithoutSymbol($gPl->price),
                'special_price'=>Helper::makeCurrencyWithoutSymbol($gPl->special_price),
                'is_special'=>$gPl->is_special,
                'slug'=>$gPl->slug,
            ];
        }
        $shareButtons = \Share::page(
            env("APP_URL_SERVE"),
            'Vivid',
        )
        ->facebook()
        ->twitter()
        ->linkedin()
        ->telegram()
        ->whatsapp()
        ->reddit();

        $response['latest'] = $productList;

       

        return view('content/front/product-details',['data'=>$response,'shareButtons'=>$shareButtons]);
    }

    public function productDetails($slug)
    {
        $id = Helper::getProductIdByslug($slug);
        if(empty($id))
        return redirect()->back();

        $getProduct = Store_products::where("id", $id)->get();
        if($getProduct->isEmpty())
        {
            return response()->json(['status' => false, 'message' => "Product Not Found"], 200);
        }
        $response = [];
        foreach($getProduct as $gP)
        {
            $response['details'] = [
                'id'=>$gP->id,
                'product_name'=>$gP->product_name,
                'featured_Image'=>$gP->featured_Image,
                'description'=>$gP->description,
                'remaining_qty'=>$gP->remaining_qty,
                'price'=>Helper::makeCurrencyWithoutSymbol($gP->price),
                'special_price'=>Helper::makeCurrencyWithoutSymbol($gP->special_price),
                'is_special'=>$gP->is_special
            ];

            $addImg = [];

            $addImg[] = $gP->featured_Image;
            $additional_images = json_decode($gP->additional_images);
            $addImg = array_merge($addImg,$additional_images);
            $response['details']['additional_images'] = (object)[];
            if(!empty($addImg))
            {
                $response['details']['additional_images'] = $addImg;
            }

            $getComic = (object)[];
            if(!empty($gP->comic_series_id))
            {
                $decodeId = json_decode($gP->comic_series_id);
                $getComic = Comics_series::select("id", "featured_image", "name")->whereIn("id", $decodeId)->get();

                $response['details']['configure']['comics'] = $getComic;
                $response['details']['configure']['max_comics'] = $gP->max_comics;
                $response['details']['configure']['min_comics'] = $gP->min_comics;

            }

        }
        $getLatesProduct = Store_products::latest('id')->take(10)->get();
        $getLatesProduct = $getLatesProduct->except($id);
        $productList = [];
        foreach($getLatesProduct as $gPl)
        {
            $productList[] = [
                'id'=>$gPl->id,
                'product_name'=>$gPl->product_name,
                'featured_Image'=>$gPl->featured_Image,
                'price'=>Helper::makeCurrencyWithoutSymbol($gPl->price),
                'special_price'=>Helper::makeCurrencyWithoutSymbol($gPl->special_price),
                'is_special'=>$gPl->is_special,
                'slug'=>$gPl->slug,
            ];
        }
        $shareButtons = \Share::page(
            env("APP_URL_SERVE"),
            'Vivid',
        )
        ->facebook()
        ->twitter()
        ->linkedin()
        ->telegram()
        ->whatsapp()
        ->reddit();

        $response['latest'] = $productList;

       // echo "<pre>";print_r($response);exit;
        return view('content/front/product-details',['data'=>$response,'shareButtons'=>$shareButtons]);
    }


    public function buyNow()
    {
        $userid=Auth::user()->id;

        $userInfo = User::where("id", $userid)->with('getUserDetails','getUserAddress')->first();

        // echo"<pre>";
        // print_r($userid);
        // die();
        $getPaymentMethod = [];

        if(!empty($userInfo->getUserDetails->stripe_user_id))
        {
            $params = [
                'stripe_user_id'=>$userInfo->getUserDetails->stripe_user_id
            ];
            $getPaymentMethod = $this->getPaymentMethod($params);
        }

        $cart_items = DB::table('carts')
        ->where('user_id',$userid)
        ->join('store_products','store_products.id','=','carts.product_id')
        ->select('store_products.*','carts.qty','carts.comic_series_id','carts.custom_title_name')
        ->get()
        ->toArray();

        $sum = 0;
        foreach($cart_items as $cart_item){
            $sum += $cart_item->qty * $cart_item->special_price;
        }

        // echo "<pre>";
        // print_r($getPaymentMethod);
        // exit("g");
        return view('content/front/buy-now',['getPaymentMethod'=>$getPaymentMethod,'countCart'=>$cart_items,'sum'=>$sum,'userInfo'=>$userInfo]);
    }

    public function getPaymentMethod($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $resData =  $stripe->customers->allSources(
                $params['stripe_user_id'],
                ['object' => 'card', 'limit' => 3]
              );
            // $resData =  $stripe->customers->allPaymentMethods(
            //     $params['stripe_user_id'],
            //     ['type' => 'card']
            // );
            $stripeResponse = $resData->jsonSerialize();
        }
        catch (\Stripe\Exception\CardException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\RateLimitException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\InvalidRequestException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\AuthenticationException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\ApiConnectionException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\ApiErrorException $e)
        {
            $error = $e->getMessage();
        }
        catch (Exception $e)
        {
            $error = $e->getMessage();
        }
        if(!empty($error))
        {
            $error=array('error' => $error);
            $stripeResponse=$error;
        }

        return $stripeResponse;
    }



    public function updateCart()
    {

         $userid = Auth::user()->id;
         if(empty(request()->post('quantity'))){
             DB::table('carts')->where('user_id',$userid)->where('product_id',request()->post('product_id'))->delete();
         }else{

             DB::table('carts')->updateOrInsert([
                 'user_id'=>$userid,
                 'product_id'=>request()->post('product_id')
             ],
             [
                 'qty'=>request()->post('quantity'),
                 'created_at'=>now(),
                 'updated_at'=>now(),
             ]);
         }

         $cart_items = DB::table('carts')->where('user_id',$userid)->join('store_products','store_products.id','=','carts.product_id')->select('store_products.*','carts.qty')->get();
         $sum = 0;
         foreach($cart_items as $cart_item){
             $sum += $cart_item->qty * $cart_item->special_price;
         }
         return response()->json([
             'success'=>true,
             'sum'=>$sum
         ],200);
    }

    public function checkOut(Request $request)
    {
        //print_r($request->all());exit;
        $rules = array(
            'card_id' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }

        $user_id = Auth::user()->id;
        $ud = User_details::where("user_id", $user_id)->first();
        if(empty($ud->stripe_user_id))
        {
            return response()->json(['status' => false, 'message' => "Stripe user not found!"], 200);
        }

        $product_id = Cart::where("user_id", $user_id)->get();

        //$qty = Cart::where("user_id", $user_id)->pluck('qty');
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $card_id = $request->input('card_id');
        $stripe_user_id = $ud->stripe_user_id;
        $amount = $request->input('amount');

        // if(count($product_id) != count($qty))
        // {
        //     return response()->json(['status' => false, 'message' => "Product & Quantity mismatch"], 200);
        // }

        $q = 0;
        $price = [];
        foreach($product_id as $pi)
        {
            $product = Store_products::where("id", $pi->product_id)->first();
            if(empty($product))
            {
                return response()->json(['status' => false, 'message' => "Product Id $pi->product_id Not Found"], 200);
            }

            if($product->remaining_qty < $pi->qty)
            {
                return response()->json(['status' => false, 'message' => "Product Id $pi->product_id not available quantity"], 200);
            }

            $price[] = $product->special_price * $pi->qty;
            $q++;
        }

        $totalPrice = array_sum($price);

        //if($totalPrice != $amount)
        if($totalPrice != $totalPrice)
        {
            return response()->json(['status' => false, 'message' => "Price calculation mismatch"], 200);
        }

        $stripeAmount = $totalPrice * 100;
        $params = [
            'card_id'=>$card_id,
            'stripe_user_id'=>$stripe_user_id,
            'amount'=>$stripeAmount
        ];


        $responseCharge = $this->chargeCustomer($params);

        if(empty($responseCharge['error']))
        {

            DB::beginTransaction();

            try {

                $charge_id = $responseCharge['id'];
                $transaction_id = $responseCharge['balance_transaction'];
                $payment_method_id = $responseCharge['payment_method'];
                $dd = [
                    'address'=>$address,
                    'city'=>$city,
                    'state'=>$state,
                    'zip'=>$zip,
                ];
                $delivery_details = json_encode($dd);
                $order_status = 3;

                $Order = new Orders;
                $Order->user_id = $user_id;
                $Order->order_request_id = time().rand(2,50);
                $Order->charge_id = $charge_id;
                $Order->delivery_details = $delivery_details;
                $Order->payment_method_id = $payment_method_id;
                $Order->transaction_id = $transaction_id;
                $Order->order_status = $order_status;
                $Order->amount = $totalPrice;
                $Order->save();

                $q = 0;
                foreach($product_id as $pi)
                {
                    $product = Store_products::where("id", $pi->product_id)->first();

                    $priceProduct = $product->special_price * $pi->qty;

                    $Order_product_details = new Order_product_details;
                    $Order_product_details->order_id = $Order->id;
                    $Order_product_details->product_id = $pi->product_id;
                    $Order_product_details->product_name = $product->product_name;
                    $Order_product_details->price = $product->special_price;
                    $Order_product_details->total = $priceProduct;
                    $Order_product_details->qty = $pi->qty;

                    if(!empty($pi->comic_series_id))
                    {
                        $Order_product_details->comic_series_id = $pi->comic_series_id;
                        $Order_product_details->custom_title_name = $pi->custom_title_name;                        
                    }


                    $Order_product_details->save();

                    //decreade Quantity
                    $restQty = $product->remaining_qty - $pi->qty;
                    $product = Store_products::where("id", $pi->product_id)->update(['remaining_qty'=>$restQty]);

                    $q++;
                }

                DB::commit();

                $User_address = new User_address;
                $User_address->user_id = $user_id;
                $User_address->address = $address;
                $User_address->city = $city;
                $User_address->state = $state;
                $User_address->zip_code = $zip;
                $User_address->save();


                Cart::where("user_id", $user_id)->delete();
                return response()->json(['status' => true, 'message' => "Order Created Successfully"], 200);


            }
            catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => false, 'message' => $e->getMessage()], 200);

            }

        }
        return response()->json(['status' => false, 'message' => $responseCharge['error']], 200);
    }

    public function chargeCustomer($params)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $error="";
        $stripeResponse = "";
        try
        {
            $charge = \Stripe\Charge::create
            (
                array
                (
                    'card'=> $params['card_id'],
                    'customer' => $params['stripe_user_id'],
                    'amount' => $params['amount'],
                    'currency' => "gbp"
                )
            );
            $stripeResponse = $charge->jsonSerialize();
        }
        catch (\Stripe\Exception\CardException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\RateLimitException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\InvalidRequestException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\AuthenticationException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\ApiConnectionException $e)
        {
            $error = $e->getMessage();
        }
        catch (\Stripe\Exception\ApiErrorException $e)
        {
            $error = $e->getMessage();
        }
        catch (Exception $e)
        {
            $error = $e->getMessage();
        }
        if(!empty($error))
        {
            $error=array('error' => $error);
            $stripeResponse=$error;
        }

        return $stripeResponse;
    } 



   public function allComic()
    {
        $data= Content_categories::where('status', 1)->get();
        return view('content/front/all-comics',['category'=>$data]);
    }


   public function searchComics(Request $request)
   {
        $userLoginCheck = false;
        $ud = [];
        if(!empty(Auth::user()->id)){
            $userLoginCheck = true;
            $ud = User_details::where('user_id',Auth::user()->id)->first();
        }

       $limit  = 20;
       $page = 1;
       $offset = ($page - 1) * $limit;
       $catData = Content_categories::where('status', 1)->get();

       $comicList = DB::table('comics_series as cs')
       ->where("cs.status", 1)
       ->where("ce.status", 1)
       ->where("u.status", 1)
       ->select("cs.id", "cs.name", "cs.featured_image", "cs.access_type", "cs.slug", "cs.deep_url","cs.description", DB::Raw('CAST(IFNULL( AVG( `cr`.`ratings`) , 0.0 ) AS DECIMAL(10,2)) as ratings'))
       ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
       ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')
       ->join('users as u', 'u.id', '=', 'cs.created_by')
       ->leftjoin('comic_ratings as cr', 'cr.comic_id', '=', 'cs.id');

       $searchTerm = "";
       if(!empty($request->input('serach_text')))
       {
           $searchTerm = $request->input('serach_text');
           $comicList = $comicList->where('cs.name', 'LIKE', "%{$searchTerm}%");
       }
       $comicList = $comicList->groupby('cs.id');
       $comicList = $comicList->take($offset)
       ->paginate($limit);

        return view('content/front/search-comics',['comicList'=>$comicList,'searchTerm' =>$searchTerm,'cc'=>$catData,'userLoginCheck'=>$userLoginCheck,'ud'=>$ud]);
   }

   public function orderDetail($id)
   {
    $order = Orders::with("getOrderProductDetails")
    ->where("id", $id)
    ->where("user_id",Auth::user()->id)
    ->get();

    $res = (object)[];
    if($order->isEmpty())
    {
        return response()->json(['status'=> false, 'data'=>$res, 'message'=>'Order Not found'], 200);
    }

    foreach($order as $o)
    {
        $am = Helper::makeCurrencyWithoutSymbol($o->amount);
        $or = [
            'id'=>$o->id,
            'order_request_id'=>$o->order_request_id,
            'order_status'=>$o->order_status,
            'amount'=>$am,
        ];
        foreach($o->getOrderProductDetails as $pd)
        {
            $sp = Store_products::select("featured_Image")->where("id", $pd->product_id)->first();
            $price = Helper::makeCurrencyWithoutSymbol($pd->price);
            $total = Helper::makeCurrencyWithoutSymbol($pd->total);
            $pds[] = [
                'id'=>$pd->id,
                'product_name'=>$pd->product_name,
                'qty'=>$pd->qty,
                'amount'=>$price,
                'featured_Image'=>$sp->featured_Image,
                'total'=>$total,
                'comic_series_id'=>$pd->comic_series_id,
                'custom_title_name'=>$pd->custom_title_name,
            ];
        }
        $or['details'] = $pds;
    }

    $response = $or;
        return view('content/front/order-detail',['id'=>$id,'response'=>$response]);
   }


   public function deleteAccount()
   {
        if (Auth::user()->id) {

            $userId = Auth::user()->id;
            User::where("id", $userId)->delete();
            User_details::where("user_id", $userId)->delete();
            return response()->json(['status' => true, 'message' => "Your account is deleted"], 200);
        }
        else {

            return response()->json(['status' => false, 'message' => "User Not Found"], 200);
        }

   }

    public function privacyPolicy()
    {
        return view('content/front/privacy-policy');
    }

    public function termCondition()
    {
        return view('content/front/term-conditions');
    }

    public function copyright()
    {
        return view('content/front/copyright');
    }

    public function cookiePolicy()
    {
        return view('content/front/cookie-policy');
    }

    public function cancellationPolicy()
    {
        return view('content/front/cancellation-policy');
    }

    public function publisher()
    {
        return view('content/front/publisher');
    }


    public function about()
    {
       // die('aa');
        return view('content/front/about');
    }

    public function publisherprofile($slug)
    {
        $id = Helper::getPublisherIdByslug($slug);
        if(empty($id))
        return redirect()->back()->with('success', 'your message,here');

        $userLoginCheck = false;
        $ud = [];
        if(!empty(Auth::user()->id)){
            $userLoginCheck = true;
            $ud = User_details::where('user_id',Auth::user()->id)->first();
        }


        $userId = $id;
        $getUserData = User::with("getUserDetails")->where("id", $userId)->where("role", "!=",  2)->first();

        if(empty($getUserData))
        return response()->json(['status'=> false, 'message'=>"Publisher not found"], 200);

        $response['publisher'] = [
            'id'=>$getUserData->id,
            'name'=>$getUserData->name,
            'about'=>$getUserData->getUserDetails->about ?? '',
            'image'=>($getUserData->image) ? env("IMAGE_PATH_medium").$getUserData->image : env("APP_URL_SERVE").'/images/avatars/userdummy.png',
        ];


        $limit  = $request->limit ?? 20;
        $page = $request->page ?? 1;
        $offset = ($page - 1) * $limit;
        $comicList = DB::table('comics_series as cs')
        ->where("created_by", $userId)
        ->where("cs.status", 1)
        ->where("ce.status", 1)
        ->select("cs.id", "cs.name", "cs.featured_image", "cs.access_type", "cs.deep_url","cs.description","cs.slug",  DB::Raw('CAST(IFNULL( AVG( `cr`.`ratings`) , 0.0 ) AS DECIMAL(10,2)) as ratings'))
        ->join('comics_categories_mapping as ccm', 'ccm.comics_id', '=', 'cs.id')
        ->join('comics_episodes as ce', 'ce.comics_series_id', '=', 'cs.id')
        ->leftjoin('comic_ratings as cr', 'cr.comic_id', '=', 'cs.id');

        $comicList = $comicList->groupby('cs.id');
        $comicList = $comicList->take($offset)
        ->paginate($limit);

        $response['comic'] = (object)[];
        if(!empty($comicList))
        {
            $response['comic'] = $comicList;
        }

        return view('content/front/publisher-profile', ['data'=>$response, 'userLoginCheck'=>$userLoginCheck,'ud'=>$ud]);
    }

    public function getPublisher(Request $request)
    {
        $limit  = $request->limit ?? 10;
        $page = $request->page ?? 1;
        $offset = ($page - 1) * $limit;

        $publisher = User::select("users.id", "users.name", "users.image", "ud.slug")
        ->where("users.status", 1)
        ->where("users.role", 3)
        ->where("cs.status", 1)
        ->where("ce.status", 1)
        ->join("user_details as ud", "ud.user_id", "users.id")
        ->join("comics_series as cs", "cs.created_by", "users.id")
        ->join("comics_episodes as ce", "ce.comics_series_id", "cs.id")
        ->groupBy("users.id")
        ->take($offset)
        ->paginate($limit);

        $getData = $publisher->getCollection()->transform(function ($value) {

            if ( ! $value->image) {
                $value->image = env("APP_URL_SERVE").'/images/avatars/userdummy.png';
            }else
            {
                $value->image = env("IMAGE_PATH_medium").$value->image;
            }
            return $value;
        });

        if(!empty($getData))
        return response()->json(['status' => true, 'data' => $getData], 200);
        else
        return response()->json(['status' => false, 'data' => $getData], 200);
    }


    public function userCoins(Request $request)
    {
        $userid=Auth::user()->id;

        $userInfo = User::where("id", $userid)->with('getUserDetails','getUserAddress')->first();

        // echo"<pre>";
        // print_r($userid);
        // die();
        $getPaymentMethod = [];

        if(!empty($userInfo->getUserDetails->stripe_user_id))
        {
            $params = [
                'stripe_user_id'=>$userInfo->getUserDetails->stripe_user_id
            ];
            $getPaymentMethod = $this->getPaymentMethod($params);
        }
        $userWallet = User_wallet::where("user_id", $userid)->get();

        $Coin_slab = Coin_slab::get();
        return view("content/front/user-v-coins", ['userWallet'=>$userWallet, 'getPaymentMethod'=>$getPaymentMethod, 'Coin_slab'=>$Coin_slab]);
    }

   public function hidenotification(Request $request){
        $userid=Auth::user()->id;
        $usernotification = Notification::where("user_id", $userid)->update(['is_read' => 1]);
        return response(['status'=>true]);
   }
   public function countNotification(Request $request){
        $userid=Auth::user()->id;
        //$count = Notification::where("user_id", $userid)->where(['is_read' => 0])->count();

        $getNotification = Notification::select('notifications.id','notifications.title', 'notifications.description','notifications.comic_id',"cs.slug", 'notifications.is_read', 'notifications.created_at as date')
        ->leftjoin("comics_series as cs", "cs.id", "notifications.comic_id")
        ->where('notifications.user_id', $userid)
        ->orderby('notifications.id', 'DESC')
        ->get();


        $count = 0;
        $data = [];
        foreach($getNotification as $getNotify)
        {
            if($getNotify->is_read == 0)
            {
                $count++;
            }

            $slug = "#";
            if(!empty($getNotify->slug))
            {
                $slug = env("APP_URL_SERVE").'/comic-detail/'.$getNotify->slug;
            }

            $data[] = [
                'title' => $getNotify->title,
                'description' => $getNotify->description,
                'time'=> now()->create($getNotify->date)->diffForHumans(),
                'slug'=> $slug
            ];

        }

        return response(['status'=>true, 'count'=>$count, 'data'=>$data]);
   }

}
