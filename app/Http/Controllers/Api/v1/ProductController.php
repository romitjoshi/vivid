<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\admin\{Store_products, Orders, Order_product_details, Comics_series};
use App\Models\{User, User_details};
use Helper, Stripe, DB;
class ProductController extends Controller
{
    public $isUserLoggedIn = false;
    public $currentUserId = false;

    function __construct()
    {
        if(Auth::guard('api')->check())
        {
            $this->isUserLoggedIn = true;
            $this->currentUserId = Auth::guard('api')->user()->id;
        }
    }

    public function getProduct(Request $request)
    {
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;

        $productList = Store_products::select("id", "product_name", "featured_Image", "price", "special_price", "deep_url", "is_special", "type")
        ->where("remaining_qty", ">", 0)
        ->take($offset)
        ->orderBy("id", "DESC")
        ->paginate($limit); 

        if(!empty($productList))
        {
            return response()->json(['status'=> true, 'data'=>$productList], 200);
        }

        return response()->json(['status'=> false, 'data'=>(object)[]], 200);
    }


    public function myOrder(Request $request)
    {
        $limit  = $request->limit;
        $page = $request->page;
        $offset = ($page - 1) * $limit;

        $getOrder =DB::table("orders as o")
        ->select("o.id", "o.order_request_id", "o.created_at", "o.order_status", "o.amount")
        ->join("order_product_details as opd", "opd.order_id", "o.id")
        ->where("o.user_id", $this->currentUserId)
        ->groupBy("o.id")
        ->latest("id")
        ->take($offset)
        ->paginate($limit);

        if(!empty($getOrder))
        {
            return response()->json(['status'=> true, 'data'=>$getOrder], 200);
        }

        return response()->json(['status'=> false, 'data'=>(object)[]], 200);
    }

    public function orderDetails(Request $request)
    {
        $rules = array(
            'id' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }
        $id = $request->input("id");

        $order = Orders::with("getOrderProductDetails")
        ->where("id", $id)
        ->where("user_id", $this->currentUserId)
        ->get();

       // print_r($order->toArray());exit;

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
            $oi = 0;
            foreach($o->getOrderProductDetails as $pd)
            {
                $sp = Store_products::select("featured_Image")->where("id", $pd->product_id)->first();
                $price = Helper::makeCurrencyWithoutSymbol($pd->price);
                $total = Helper::makeCurrencyWithoutSymbol($pd->total);

                $getComic = [];
                if(!empty($pd->comic_series_id))
                {
                    $decodeId = json_decode($pd->comic_series_id);     
                    $stringId = implode(",", $decodeId);   

                    $getComic = Comics_series::select("id", "featured_image", "name")
                    ->whereIn("id", $decodeId)
                    ->orderBy(DB::raw("FIELD(ID, $stringId)"))
                    ->get();    
        
                }

                $pds[$oi] = [
                    'id'=>$pd->id,
                    'product_name'=>$pd->product_name,
                    'qty'=>$pd->qty,
                    'amount'=>$price,
                    'featured_Image'=>$sp->featured_Image,
                    'total'=>$total,
                    'configure'=>$getComic,
                    'custom_title_name'=>$pd->custom_title_name ?? '',
                ];

               
            $oi++;
            }
            $or['details'] = $pds;
        }

        $response = $or;

        return response()->json(['status'=> true, 'data'=>$response, 'message'=>'order details'], 200);
    }

    public function productdetails(Request $request)
    {
        $rules = array(
            'id' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.'
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()], 200);
        }
        $id = $request->input("id");
        if(!empty($request->input('deep_url')))
        {
            $link = explode("/product/", $request->input('deep_url'));
            if(!empty($link[1]))
            {
                $dp = Store_products::select("id")->where("deep_id", $link[1])->first();
            }
            if(!empty($dp->id))
            $id = $dp->id;
        }

        $getProduct = Store_products::where("id", $id)->get();
    //    echo"<pre>";print_r($getProduct);die;
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
                'deep_url'=>$gP->deep_url,
                'price'=>Helper::makeCurrencyWithoutSymbol($gP->price),
                'special_price'=>Helper::makeCurrencyWithoutSymbol($gP->special_price),
                'is_special'=>$gP->is_special,
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

        $getLatesProduct = Store_products::latest('id')->take(2)->get();
        $getLatesProduct = $getLatesProduct->except($id);
        $productList = [];
        foreach($getLatesProduct as $gPl)
        {
            $productList[] = [
                'id'=>$gPl->id,
                'product_name'=>$gPl->product_name,
                'featured_Image'=>$gPl->featured_Image,
                'deep_url'=>$gPl->deep_url,
                'price'=>Helper::makeCurrencyWithoutSymbol($gPl->price),
                'special_price'=>Helper::makeCurrencyWithoutSymbol($gPl->special_price),
                'is_special'=>$gPl->is_special,
            ];
        }
        $response['latest'] = $productList;

        return response()->json(['status' => true, 'data'=>$response, 'message' => "Product Details"], 200);
    }

    public function createStripeCustomer(Request $request)
    {
        $stripe_user_id = "";
        $getUser = User::with('getUserDetails')->where("id", $this->currentUserId)->first();

        if(!empty($getUser->getUserDetails->stripe_user_id))
        {
            $stripe_user_id = $getUser->getUserDetails->stripe_user_id;
        }
        else
        {
            $userData = [
                'email' => $getUser->email
            ];
            $stripeResponse = $this->stripeCustomer($userData);
            $stripe_user_id = $stripeResponse['id'];
            User_details::where("user_id", $this->currentUserId)->update(['stripe_user_id'=>$stripe_user_id]);
        }

        if(empty($stripeResponse['error']))
        {
            return response()->json(['status'=> true, 'message'=> 'User created successfully', 'stripe_user_id'=>$stripe_user_id], 200);
        }

        return response()->json(['status'=> false, 'message'=> $stripeResponse['error'], 'stripe_user_id'=>$stripe_user_id], 200);
    }

    public function stripeCustomer($userData)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $error = "";
        $stripeResponse = "";
        try
        {
            $customer = \Stripe\Customer::create(
                [
                    "email" => $userData['email']
                ]
            );
            $stripeResponse = $customer->jsonSerialize();
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

    public function order(Request $request)
    {
        //print_r($request->all());exit("g");
        
        $rules = array(
            'product_id' => 'required',
            'card_id' => 'required',
            'stripe_user_id' => 'required',
            'amount' => 'required',
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

        $product_id = $request->input('product_id');
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $card_id = $request->input('card_id');
        $stripe_user_id = $request->input('stripe_user_id');
        $amount = $request->input('amount');   

        $price = [];
        foreach($product_id as $pi)
        {
            $pid = $pi['productId'];
            $product = Store_products::where("id", $pid)->first();
            if(empty($product))
            {
                return response()->json(['status' => false, 'message' => "Product Id $pid Not Found"], 200);
            }

            if($product->remaining_qty < $pi['productQuantity'])
            {
                return response()->json(['status' => false, 'message' => "Product Id $pid not available quantity"], 200);
            }

            $price[] = $product->special_price * $pi['productQuantity'];
        }

        $totalPrice = array_sum($price);

        if($totalPrice != $amount)
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
                $user_id = $this->currentUserId;

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

                foreach($product_id as $pi)
                {
                    $product = Store_products::where("id", $pi['productId'])->first();

                    $priceProduct = $product->special_price * $pi['productQuantity'];

                    $Order_product_details = new Order_product_details;
                    $Order_product_details->order_id = $Order->id;
                    $Order_product_details->product_id = $pi['productId'];
                    $Order_product_details->product_name = $product->product_name;
                    $Order_product_details->price = $product->special_price;
                    $Order_product_details->total = $priceProduct;
                    $Order_product_details->qty = $pi['productQuantity'];

                    if(!empty($pi['customComicList']))
                    {
                        $cclId = [];
                        foreach($pi['customComicList'] as $ccl)
                        {
                            $cclId[] = $ccl['productId'];
                        }
                        if(!empty($cclId))
                        {
                            $comic_series_id = [];
                            $comic_series_id = json_encode($cclId);
                            $Order_product_details->comic_series_id = $comic_series_id;
                            $Order_product_details->custom_title_name = $pi['customTitleName'];
                        }
                        
                    }
                    $Order_product_details->save();

                    //decrease Quantity
                    $restQty = $product->remaining_qty - $pi['productQuantity'];
                    $product = Store_products::where("id", $pi['productId'])->update(['remaining_qty'=>$restQty]);
                }

                DB::commit();

                return response()->json(['status' => true, 'message' => "Order Created Successfully"], 200);


            }
            catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
                // $params = [
                //     'charge'=>$charge_id
                // ];
                // $refundsReq = $this->refunds($params);
                // if(empty($refundsReq['error']))
                // {
                //     return response()->json(['status' => false, 'message' => "order not created.your payment is refund"], 200);
                // }
                // else
                // {
                //     return response()->json(['status' => false, 'message' => "Something Wend wrong"], 200);
                // }

            }

        }
        return response()->json(['status' => false, 'message' => $responseCharge['error']], 200);
    }

    public function order_old(Request $request)
    {
       //echo  Auth::guard('api')->user()->id;exit;
        $rules = array(
            'product_id' => 'required',
            'card_id' => 'required',
            'stripe_user_id' => 'required',
            'amount' => 'required',
            'qty' => 'required',
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

        $product_id = $request->input('product_id');
        $qty = $request->input('qty');
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $card_id = $request->input('card_id');
        $stripe_user_id = $request->input('stripe_user_id');
        $amount = $request->input('amount');

        if(count($product_id) != count($qty))
        {
            return response()->json(['status' => false, 'message' => "Product & Quantity mismatch"], 200);
        }

        $q = 0;
        $price = [];
        foreach($product_id as $pi)
        {
            $product = Store_products::where("id", $pi)->first();
            if(empty($product))
            {
                return response()->json(['status' => false, 'message' => "Product Id $pi Not Found"], 200);
            }

            if($product->remaining_qty < $qty[$q])
            {
                return response()->json(['status' => false, 'message' => "Product Id $pi not available quantity"], 200);
            }

            $price[] = $product->special_price * $qty[$q];
            $q++;
        }

        $totalPrice = array_sum($price);

        if($totalPrice != $amount)
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
                $user_id = $this->currentUserId;

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
                    $product = Store_products::where("id", $pi)->first();

                    $priceProduct = $product->special_price * $qty[$q];

                    $Order_product_details = new Order_product_details;
                    $Order_product_details->order_id = $Order->id;
                    $Order_product_details->product_id = $pi;
                    $Order_product_details->product_name = $product->product_name;
                    $Order_product_details->price = $product->special_price;
                    $Order_product_details->total = $priceProduct;
                    $Order_product_details->qty = $qty[$q];
                    $Order_product_details->save();

                    //decrease Quantity
                    $restQty = $product->remaining_qty - $qty[$q];
                    $product = Store_products::where("id", $pi)->update(['remaining_qty'=>$restQty]);

                    $q++;
                }

                DB::commit();

                return response()->json(['status' => true, 'message' => "Order Created Successfully"], 200);


            }
            catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
                // $params = [
                //     'charge'=>$charge_id
                // ];
                // $refundsReq = $this->refunds($params);
                // if(empty($refundsReq['error']))
                // {
                //     return response()->json(['status' => false, 'message' => "order not created.your payment is refund"], 200);
                // }
                // else
                // {
                //     return response()->json(['status' => false, 'message' => "Something Wend wrong"], 200);
                // }

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

    public function refunds($params)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $error="";
        $stripeResponse = "";
        try
        {
            $charge = $stripe->refunds->create([
                'charge' => $params['charge'],
            ]);
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
}
