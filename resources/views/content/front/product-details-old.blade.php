@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
@php
$userLoginCheck = false;
if(!empty(Auth::user()->role) && Auth::user()->role == 2)
{
   $userLoginCheck = true;
}
@endphp
 <section class="productdetailpage bg-dark">
         <div class="container">
          <nav aria-label="breadcrumb">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                  <li class="breadcrumb-item"><a href="{{route('store')}}">Store</a></li>

                  <li class="breadcrumb-item active" aria-current="page">Store Detail</li>
               </ol>
            </nav>

            <div class="row">
               <div class="col-md-5 productimgsec productimgseccro">
                  <div class="productimgsecinner owl-carousel"  id="productetc-nextepi">
                     @foreach($data['details']['additional_images'] as $adImage)
                     <div class="item">
                        <img src="{{env('IMAGE_PATH_large').$adImage ?? '' }}" class="w-100 productBigImgSize"/>
                     </div>
                     @endforeach
                  </div>
               </div>

               <div class="col-md-7 productinfosec productpage">
                  <div class="card">
                     <div class="card-body">
                       <h3>{{$data['details']['product_name']}}</h3>
                       <h3 class="d-inline">{{ Helper::makeCurrency($data['details']['special_price'])}}</h3>
                       <h5 class="d-inline @if($data['details']['is_special'] != 1) d-none @endif" style="text-decoration: line-through; color: grey">{{  Helper::makeCurrency($data['details']['price']) }}</h5>

                       <p class="spaceproduct">{{$data['details']['description']}}</p>

                     </div>
                   </div>
                   <div class="productinfo">
                      <div class="row">
                        <div class="col-7 addbutbtnn">
                           <button type="button" class="btn" @if(empty(Auth::user()->role) || Auth::user()->role != 2) data-bs-toggle="modal" data-bs-target="#signuppoppurchase" @else id="BuyNowBtnCart" productIdBuy="{{$data['details']['id']}}" @endif>Buy Now</button>

                           <button type="button" @if(empty($userLoginCheck)) data-bs-toggle="modal" data-bs-target="#signuppoppurchase" @else id="AddToCartBtn" @endif class="btn"  productId="{{$data['details']['id']}}"><div class="spinner-border text-light spinerFront btn-dnone" role="status"></div>Add to Cart</button>


                        </div>
                        <div class="col-5 sharenotsec addbutbtnn1">

                           <div class="sociallinks d-none">
                              {!! Share::page(url('/product-details/'. $data['details']['id']))->facebook()->twitter()->whatsapp()->linkedin() !!}
                           </div>

                           <img class="cursor-pointer shareIcon" src="{{asset('front/images/share.svg')}}" width="35"/>

                        </div>
                     </div>
                   </div>
               </div>

            </div>


            <div class="row">
               <div class="productnextepisodes">
                  <div class="owl-slider">

                     @if(!empty($data['latest']))
                     <h3>Most Popular</h3>
                     @endif

                     <div id="product-nextepi" class="owl-carousel">
                       @foreach($data['latest'] as $episode)
                        <a href="{{url('product-details')}}/{{$episode['slug'] ?? 0}}"><div class="item">
                           <div class="imgwithtag">
                              <img class="latestproductImgSize" src="{{env('IMAGE_PATH_medium').$episode['featured_Image'] ?? '' }}">
                            </div>

                          <div class="content-sec">
                              <div class="contenttextsec contenttextsecproduct">

                                    <p class="line-clamp line-clamp-1">{{$episode['product_name']}}</p>
                                    <p> {{  Helper::makeCurrency($episode['special_price']) }}
                                    <span class="d-inline @if($episode['is_special'] != 1) d-none @endif" style="text-decoration: line-through; color: grey"> {{  Helper::makeCurrency($episode['price']) }}</span>
                                    </p>
                              </div>
                           </div>
                        </div>
                        </a>
                        @endforeach
                     </div>

                  </div>

               </div>
            </div>
         </div>

      </section>



@endsection
@section('scripts')
 <script>
      // jQuery(".productimgseccro").owlCarousel({
      //    loop:false,
      //       margin:0,
      //       nav:false,
      //    dots: true,
      //    slideSpeed: 20000,
      //    paginationSpeed: 20000,
      //       //smartSpeed: 400,
      //       autoplay: true,
      //       responsive: {
      //       0: {
      //          items: 1
      //       },

      //       600: {
      //          items: 1
      //       },
      //       1024: {
      //          items: 1
      //       },

      //       1366: {
      //          items: 1
      //       }
      //    }
      // });

      jQuery("#product-nextepi").owlCarousel({
         loop:false,
			margin:30,
			nav:true,
         dots: false,
			smartSpeed: 500,
			autoplay: true,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
         responsive: {
            0: {
               items: 2
            },

            600: {
               items: 4
            },
            1024: {
               items: 6
            },

            1366: {
               items: 6
            }
         }
      });
      jQuery("#productetc-nextepi").owlCarousel({
         loop:false,
			margin:0,
			nav:false,
         dots: true,
         slideSpeed: 20000,
         paginationSpeed: 20000,
			//smartSpeed: 400,
			autoplay: true,
			responsive: {
            0: {
               items: 1
            },

            600: {
               items: 1
            },
            1024: {
               items: 1
            },

            1366: {
               items: 1
            }
         }
      });

      $(document).on("click", "#BuyNowBtnCart", function(){
         var ele = $(this);
         ele.find('.spinner-border').removeClass('btn-dnone');
         var p = ele.attr('productIdBuy');
         $.ajax({
            'url':" {{route('add-to-cart')}} ",
            'type': "post",
            'dataType': "json",
            'data': {id:p,"_token": "{{ csrf_token() }}"},
            'success': function (response) {
               console.log(response.url)
               window.location.href = response.url;

            },
         });

      });

      $(document).on("click", "#AddToCartBtn", function(){
         var ele = $(this);
         ele.find('.spinner-border').removeClass('btn-dnone');
         var p = ele.attr('productId');
         $.ajax({
            'url':" {{route('add-to-cart')}} ",
            'type': "post",
            'dataType': "json",
            'data': {id:p,"_token": "{{ csrf_token() }}"},
            'success': function (response) {
               console.log(response)
               if(response.status)
               {
                  toastr["success"](response.message, "Success");
                  setTimeout(function(){
                     location.reload();
                  }, 1000)
               }
               else
               {
                  toastr["warning"](response.message, "Warning");
               }
               setTimeout(function(){
                  ele.find('.spinner-border').addClass('btn-dnone');
               }, 1000)
            },
            'error': function (error) {
               ele.find('.spinner-border').addClass('btn-dnone');
            },
         });

      });
   </script>

@endsection


