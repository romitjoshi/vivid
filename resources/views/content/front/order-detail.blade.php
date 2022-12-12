@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
@php
//echo "<pre>";print_r($response);exit;
@endphp

<section class="orderdetailpage bg-dark p-10 orderdetailpagemin">
   <div class="container">
      <div class="row p-10">
         <h1 class="searchhead">Orders Details</h1>
         <nav aria-label="breadcrumb">
                     <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{route('my-order')}}">My Order</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Orders Details</li>

                     </ol>
                  </nav>
         <div class="col">
            <div class="orderTitle colorfff">
               <h4>#{{$response['order_request_id']}}</h4>
               <p>Order Status:{{Helper::orderStatus($response['order_status'])}}</p>

            </div>
         </div>

         <div class="row">
            <div class="col-md-12 colorfff">
               @foreach($response['details'] as $data)
                  <div class="row repeatSection">
                     <div class="col-9 col-lg-6 innerRepet">
                        <div class="row">
                           <div class="col-3">
                           <img class="orderThum" src="{{ env('IMAGE_PATH_thumbnail') }}{{ $data["featured_Image"] }}">
                           </div>
                           <div class="col-9 odds mb-2">
                              <p>{{$data['product_name']}}</p>
                              <p>Unit Price {{ Helper::makeCurrency($data['amount'])}}</p>
                              <p>Qty {{$data['qty']}}</p>
                              @if(!empty($data['comic_series_id']))
                                 <div class="">
                                 <p class="">Custom Title : {{ $data['custom_title_name'] ?? ''}}</p>
                                 @php
                                 $comicId = json_decode($data['comic_series_id']);
                                 $nmbr = 1;
                                 foreach($comicId as $cd)
                                 {
                                    $comicName = Helper::getComicNameById($cd);
                                    echo '<p>Comic '.$nmbr.': '.$comicName.'</p>';
                                    $nmbr++;
                                 }
                                 @endphp        
                                 
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-3 col-lg-6 ">
                        <div class="row">
                           <div class="col-4 d-none">
                           <img class="orderThum" src="{{ env('IMAGE_PATH_thumbnail') }}{{ $data["featured_Image"] }}">
                           </div>
                           <div class="col-8">
                              <p class="d-none">{{$data['product_name']}}</p>
                              <p class="d-none">Unit Price{{$data['amount']}}</p>
                              <p class="">{{ Helper::makeCurrency($data['total'])}}</p>
                           </div>
                        </div>
                     </div>
                  </div>
               @endforeach
               <div class="row">
                  <div class="col-9 col-lg-6">
                        <h3><strong>Total</strong></h3>
                  </div>
                  <div class="col-3 col-lg-6">
                        <h3 class="text-right-m"><strong>{{ Helper::makeCurrency($response['amount'])}}</strong></h3>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>


@endsection
@section('scripts')


@endsection