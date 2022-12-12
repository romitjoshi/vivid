@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
@php
//echo "<pre>";print_r($data);exit;
   $userLoginCheck = false;

   if(!empty(Auth::user()->role) && Auth::user()->role == 2)
   {
      $userLoginCheck = true;
   }
@endphp
<!-- <script>
   var u = "{{ env('APP_URL_SERVE') }}";
   location.href = u + '/login';
</script> -->
<style>
   .draggable input[type="checkbox"][id^="myCheckbox"] {
      display: none;
   }
   .content-select-sec {
    margin: 0px;
    text-align: center;
}
   .draggable label {
      padding: 0px;
      display: block;
      position: relative;
      margin: 10px 0px;
      cursor: pointer;
   }

   .draggable label:before {
      background-color: white;
      color: white;
      content: " ";
      display: block;
      border-radius: 50%;
      border: 1px solid grey;
      position: absolute;
      top: -5px;
      left: 0px;
      width: 25px;
      height: 25px;
      text-align: center;
      line-height: 28px;
      transition-duration: 0.4s;
      transform: scale(0);
   }

   .draggable label img {
      height: 260px;
      transition-duration: 0.2s;
      transform-origin: 50% 50%;
      border-radius: 10px;
   }

   :checked+label {
      border-color: #ddd;
   }

   :checked+label:before {
      content: "✓";
      background-color: #0898D9;
      transform: scale(1);
      z-index: 99;
   }

   :checked+label img {
      transform: scale(0.9);
      /* box-shadow: 0 0 5px #333; */
      z-index: -1;
   }

   input#custom_title_name {
      background: #000 !important;
      border: unset;
   }
   span.input-group-text.cstm {
      background: #000;
      border: unset;
   }
   .card-body.dragdropImgSec {
      /* width: 164px; */
      padding:0;
    height: 237px;
}

.card-body.dragdropImgSec img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

p.selected-comic-title {
    width: 100%;
    color: #fff;
}
#arrangepop .modal-dialog {
    max-width: 660px;
}
i.fa.fa-arrow-right {
    color: #fff !important;
    font-size: 11px;
}
.line-clamp-1 {
	-webkit-line-clamp: 2;
}
.modelbutton .modal-body {
    margin: 0px auto;
}
#image-next {
    margin-left: 10px;
}
button#BuyNowBtnCart, button#AddToCartBtn {
    padding: 5px 0px;
    width: 200px;
}
@media(min-width:767px) {
   .col-lg-3.col-6.draggable {
      padding: 0px;
      margin: 0px;
   }
}
@media(max-width:767px) {
   .draggable label img {height: 200px;}
   .modelbutton .modal-footer .btn {padding: 5px 20px; width: 100%;}
   button#arrangeComicBtn {font-size: 16px;}
   h5.text-white.mobileFont {font-size: 16px;}
   .card-body.dragdropImgSec { width: 100px; height: 150px;}
   .addbutbtnn {width: 100%;}
   button#BuyNowBtnCart, button#AddToCartBtn {padding: 5px 0px;width: 100%;}
}
</style>
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
            <div class="productimgsecinner owl-carousel" id="productetc-nextepi">
               @foreach($data['details']['additional_images'] as $adImage)
               <div class="item">
                  <img src="{{env('IMAGE_PATH_large').$adImage ?? '' }}" class="w-100 productBigImgSize" />
               </div>
               @endforeach
            </div>
         </div>

         <div class="col-md-7 productinfosec productpage">
            <div class="card">
               <div class="card-body">
                  <h3>{{$data['details']['product_name']}}</h3>
                  <h3 class="d-inline">{{ Helper::makeCurrency($data['details']['special_price'])}}</h3>
                  <h5 class="d-inline @if($data['details']['is_special'] != 1) d-none @endif" style="text-decoration: line-through; color: grey">{{ Helper::makeCurrency($data['details']['price']) }}</h5>

                  <p class="spaceproduct">{{$data['details']['description']}}</p>

               </div>
            </div>
            <div class="productinfo">
               @if(empty($data['details']['configure']['comics']))
                <div class="row">
                <div class="col-7 addbutbtnn mt-4 mb-4">
                           <button type="button" class="btn" @if(empty(Auth::user()->role) || Auth::user()->role != 2) data-bs-toggle="modal" data-bs-target="#signuppoppurchase" @else id="BuyNowBtnCart" productIdBuy="{{$data['details']['id']}}" product_typ="normal" @endif>Buy Now</button>

                           <button type="button" @if(empty($userLoginCheck)) data-bs-toggle="modal" data-bs-target="#signuppoppurchase" @else id="AddToCartBtn" @endif class="btn"  productId="{{$data['details']['id']}}" product_typ="normal"><div class="spinner-border text-light spinerFront btn-dnone" role="status"></div>Add to Cart</button>


                        </div>
                     <div class="col-5 sharenotsec addbutbtnn1">

                           <div class="sociallinks d-none">
                              {!! Share::page(url('/product-details/'. $data['details']['id']))->facebook()->twitter()->whatsapp()->linkedin() !!}
                           </div>

                           <img class="cursor-pointer shareIcon" src="{{asset('front/images/share.svg')}}" width="35"/>

                        </div>
               </div>
               @else
               <form action="#" id="arrangeComicForm">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" >
                  <input type="hidden" name="product_id" value="{{ $data['details']['id'] }}" >
               <div class="row ">
                  <div class="draggable">
                     <div class="owl-slider">
                        <h5 class="text-white mobileFont">Select your favorite Vivid Panel Original stories</h5>
                        <div id="image-next" class="owl-carousel ">
                           @foreach($data['details']['configure']['comics'] as $cs)
                           <div class="item">
                              <div class="imgwithtag">
                              <input type="checkbox" value="{{ $cs->id }}" id="myCheckbox{{$loop->index}}" name="comic_series_id[]" />
                                 <label for="myCheckbox{{$loop->index}}">
                                    <img src="{{ env('IMAGE_PATH_medium')}}{{$cs->featured_image}}">
                                 </label>
                              </div>
                              <div class="content-sec content-select-sec">
                                 <div class="comic-select-cls">
                                    <p class="line-clamp line-clamp-1">{{ $cs->name }}</p>
                                 </div>
                              </div>
                           </div>
                           @endforeach

                        </div>
                     </div>

                  </div>
               </div>
               <button type="button" class="btn" id="arrangeComicBtn">Arrange    <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
               </form>
               @endif

            </div>
         </div>
      </div>

      @if(empty($data['details']['configure']['comics']))
      <div class="row">
         <div class="productnextepisodes">
            <div class="owl-slider">

               @if(!empty($data['latest']))
               <h3>Most Popular</h3>
               @endif

               <div id="product-nextepi" class="owl-carousel">
                  @foreach($data['latest'] as $episode)
                  <a href="{{url('product-details')}}/{{$episode['slug'] ?? 0}}">
                     <div class="item">
                        <div class="imgwithtag">
                           <img class="latestproductImgSize" src="{{env('IMAGE_PATH_medium').$episode['featured_Image'] ?? '' }}">
                        </div>

                        <div class="content-sec">
                           <div class="contenttextsec contenttextsecproduct">

                              <p class="line-clamp line-clamp-1">{{$episode['product_name']}}</p>
                              <p> {{ Helper::makeCurrency($episode['special_price']) }}
                                 <span class="d-inline @if($episode['is_special'] != 1) d-none @endif" style="text-decoration: line-through; color: grey"> {{ Helper::makeCurrency($episode['price']) }}</span>
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
      @endif
   </div>
</section>


<!----subscriptionModel Modal Start--->
<div class="modal fade modelbutton" id="arrangepop">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header text-center">
            <h4 class="modal-title w-100">Arrange</h4>
            <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
         </div>
         <!-- Modal body -->
         <form action="#" id="comicsSelectForm">
            <div class="modal-body text-center">

               <div class="input-group">
               <label class="text-start text-white mb-2">Your Name</label>
               </div>
               <div class="input-group mb-4">
                  <span class="input-group-text cstm">
                     <i class="fa fa-user" aria-hidden="true"></i>
                  </span>
                  <input type="text" class="form-control" placeholder="enter name for remarks section" name="custom_title_name" id="custom_title_name" required>
               </div>
               <p class="text-white">Drag and drop to arrange your stories in any order you like!</p>
               <div class="row" id="card-drag-area">

               </div>
            </div>
         </form>
         <!-- Modal footer -->
         <div class="modal-footer">
                <div class="row addbutbtnn mb-4 mt-4">
                   <div class="col-6 buynowbtn">
                        <button type="button" class="btn" @if(empty(Auth::user()->role) || Auth::user()->role != 2) data-bs-toggle="modal" data-bs-target="#signuppoppurchase" @else id="BuyNowBtnCart" productIdBuy="{{$data['details']['id']}}" product_typ="configure" @endif>Buy Now</button>
                   </div>
                  <div class="col-6 addtocartbtn">
                        <button type="button" @if(empty($userLoginCheck)) data-bs-toggle="modal" data-bs-target="#signuppoppurchase" @else id="AddToCartBtn" @endif class="btn"  productId="{{$data['details']['id']}}" product_typ="configure"><div class="spinner-border text-light spinerFront btn-dnone" role="status"></div>Add to Cart</button>
                  </div>

               </div>

         </div>
      </div>
   </div>
</div>
</div>
<!----subscriptionModel Modal End-->
@endsection
@section('scripts')
<script>
   jQuery("#product-nextepi").owlCarousel({
      loop: false,
      margin: 30,
      nav: true,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      navText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
      responsive: {
         0: {
            items: 2
         },

         600: {
            items: 3
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
      loop: false,
      margin: 0,
      nav: false,
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

   $(document).on("click", "#arrangeComicBtn", function() {
      var html, imgPath = '';
      var formData = $("#arrangeComicForm").serialize();
      $.ajax({
         'url': " {{route('selectedComicget')}} ",
         'type': "post",
         'dataType': "json",
         'data': formData,
         'success': function(response) {
            html = '';
            $("#card-drag-area").empty();
            if(response.status)
            {
               response.data.forEach(function(item, index){
                  imgPath = "{{ env('IMAGE_PATH_medium') }}" + item.featured_image;
                  // html += `<div class="col-lg-3 col-4 draggable">
                  //                <input class="comic_arrange_series_id" type="hidden" value="`+item.id+`" name="comic_arrange_series_id[]">
                  //                <div class="card-body dragdropImgSec">
                  //                   <img class="dragdropImg" src="`+imgPath+`">
                  //                </div>
                  //                <p class="selected-comic-title line-clamp line-clamp-1">`+item.name+`</p>
                  //          </div>`;
                  html += `<div class="col-lg-3 col-4 draggable mt-3">
                                 <input class="comic_arrange_series_id" type="hidden" value="`+item.id+`" name="comic_arrange_series_id[]">
                                 <div class="card-body dragdropImgSec">
                                    <img class="dragdropImg" src="`+imgPath+`">
                                 </div>
                           </div>`;


               });
               $("#arrangepop").modal("show")
            }
            else
            {
               toastr["warning"](response.message, "Warning");
               html = `Comic Not Found`;
            }

            $("#card-drag-area").append(html);
         },
      });

   });

   $(document).on("click", "#BuyNowBtnCart", function() {
      var ele = $(this);
      ele.find('.spinner-border').removeClass('btn-dnone');
      var p = ele.attr('productIdBuy');
      var product_typ = ele.attr('product_typ');
      var comic_arrange_series_id =  $("#comicsSelectForm").serialize();
      var formData = comic_arrange_series_id+'&id='+p+'&_token='+"{{ csrf_token() }}";

      var custom_title_name = $("#custom_title_name").val();
      $("#custom_title_name").css("border", "0px solid red");
      if(product_typ == 'configure' && custom_title_name == '')
      {
         $("#custom_title_name").css("border", "1px solid red");
         ele.find('.spinner-border').addClass('btn-dnone');
         return;
      }

      $.ajax({
         'url': " {{route('add-to-cart')}} ",
         'type': "post",
         'dataType': "json",
         'data': formData,
         'success': function(response) {
            console.log(response.url)
            window.location.href = response.url;
         },
      });

   });

   $(document).on("click", "#AddToCartBtn", function() {
      var ele = $(this);
      ele.find('.spinner-border').removeClass('btn-dnone');
      var p = ele.attr('productId');
      var product_typ = ele.attr('product_typ');

      var comic_arrange_series_id =  $("#comicsSelectForm").serialize();
      var formData = comic_arrange_series_id+'&id='+p+'&_token='+"{{ csrf_token() }}";

      var custom_title_name = $("#custom_title_name").val();

      $("#custom_title_name").css("border", "0px solid red");
      if(product_typ == 'configure' && custom_title_name == '')
      {
         $("#custom_title_name").css("border", "1px solid red");
         ele.find('.spinner-border').addClass('btn-dnone');
         return;
      }

      $.ajax({
         'url': " {{route('add-to-cart')}} ",
         'type': "post",
         'dataType': "json",
         'data': formData,
         'success': function(response) {
            console.log(response)
            // if (response.status) {
            //    toastr["success"](response.message, "Success");
            //    setTimeout(function() {
            //       location.reload();
            //    }, 1000)
            // } else {
            //    toastr["warning"](response.message, "Warning");
            // }
            console.log(response.url)
               window.location.href = response.url;
            setTimeout(function() {
               ele.find('.spinner-border').addClass('btn-dnone');
            }, 1000)
         },
         'error': function(error) {
            ele.find('.spinner-border').addClass('btn-dnone');
         },
      });

   });
</script>
<script>
   jQuery("#image-next").owlCarousel({
      loop: false,
      margin: 15,
      nav: false,
      dots: false,
      smartSpeed: 500,
      autoplay: false,
      navText: ['<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>'],
      responsive: {
         0: {
            items: 3
         },

         600: {
            items: 3
         },
         1024: {
            items: 4
         },

         1366: {
            items: 4
         }
      }
   });
</script>

@endsection