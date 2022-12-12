@extends('content.front.layouts.master')
@section('title','Home')
@section('content')

@php
$getOrder = !empty($getOrder->toArray()['data']) ? $getOrder->toArray()['data'] : [];
@endphp

<section class="orderdetailpage bg-dark p-10 orderdetailpagemin">
         <div class="container">
            <div class="row p-10">
               <h1 class="searchhead">My Orders</h1>
                   <nav aria-label="breadcrumb">
                     <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                        <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                        <li class="breadcrumb-item active" aria-current="page">My Orders</li>

                     </ol>
                  </nav>
               @if(!empty($getOrder))
                    @foreach($getOrder as $gto)
                    <div class="orderrepeat">
                        <div class="col-md-8 com-sm-7 float-start">
                          <a href="{{route('order-detail',['id' => $gto->id])}}"><h4>#{{ $gto->order_request_id }}</h4> </a>

                            <p>Order Date: {{ $gto->created_at }}</p>
                        </div>
                        <div class="col-md-4 sm-dm-5 float-end text-end">
                            <p class="shippedcolor">{{ Helper::orderStatus($gto->order_status) }}</p>
                            <p>{{ Helper::makeCurrency($gto->amount) }}</p>
                        </div>
                    </div>
                    @endforeach

               @else
                <div class="col-12">
                                 <div class="imgwithtag genres"><img class="tagimg tagblankimgmyorder" src="{{asset('front/images/noorder.png')}}"></div>
                              </div>


               @endif

               <!-- <div class="orderrepeat">
                  <div class="col-md-8 com-sm-7 float-start">
                     <h4>Wanda Vision</h4>
                     <p>Author: Chris Evans</p>
                     <p>Order Date: 21-03-2021</p>
                  </div>
                  <div class="col-md-4 sm-dm-5 float-end text-end">
                     <p class="shippedcolor">Shipped</p>
                     <p>$110.00</p>
                  </div>
               </div>
               <div class="orderrepeat">
                  <div class="col-md-8 com-sm-7 float-start">
                     <h4>Wanda Vision</h4>
                     <p>Author: Chris Evans</p>
                     <p>Order Date: 21-03-2021</p>
                  </div>
                  <div class="col-md-4 sm-dm-5 float-end text-end">
                     <p class="shippedcolor">Shipped</p>
                     <p>$110.00</p>
                  </div>
               </div> -->

            </div>

         </div>

      </section>


@endsection
@section('scripts')
<?php
	if ($_SERVER['REQUEST_URI']=="/customer/my-order")
	{
	?>
   <script>
      $(document).ready(function(){
         let currentUrl = location.href;
        // window.location.href('http://127.0.0.1:8000/');
});
   </script>

   <?php } ?>


@endsection