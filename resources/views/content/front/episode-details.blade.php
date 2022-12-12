@extends('content.front.layouts.master')
@section('title','Home')
@section('content')

@php

//echo "<pre>";print_r($response);exit;

$userLoginCheck = false;
if(!empty(Auth::user()->role) && Auth::user()->role == 2)
{
  $userLoginCheck = true;
}

$user_type = $response['episode']['user_type'];

$view = $response['episode']['view'];

$current_access_type = $response['episode']['access_type'];
$current_preview_pages = $response['episode']['preview_pages'];
$current_charge_coin_free_user = $response['episode']['charge_coin_free_user'];
$current_charge_coin_paid_user = $response['episode']['charge_coin_paid_user'];


$next_episode = $response['play']['next_episode'];
$next_episode_preview = $response['play']['next_episode_preview'];
$next_episode_access_type = $response['play']['next_episode_access_type'];
$next_charge_coin_free_user = $response['play']['next_charge_coin_free_user'];
$next_charge_coin_paid_user = $response['play']['next_charge_coin_paid_user'];
$next_is_paid_coins = $response['play']['next_is_paid_coins'];
$next_coins = $response['play']['next_coins'];

$previous_episode = $response['play']['previous_episode'];
$previous_episode_preview = $response['play']['previous_episode_preview'];
$previous_episode_access_type = $response['play']['previous_episode_access_type'];
$previous_charge_coin_free_user = $response['play']['previous_charge_coin_free_user'];
$previous_charge_coin_paid_user = $response['play']['previous_charge_coin_paid_user'];
$previous_is_paid_coins = $response['play']['previous_is_paid_coins'];
$previous_coins = $response['play']['previous_coins'];

$comic_id = $response['play']['comic_id']  ;
$user_pending_coins = $response['episode']['user_pending_coins'];

@endphp

@if(empty($userLoginCheck))
<script>
var u = "{{ env('APP_URL_SERVE') }}";
location.href = u+'/login';
</script>
@endif
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
<style>
    .slide {
      text-align: center;
    }
    .height {
      height: 10px;
    }

    /* Image-container design */
    .image-container {
      max-width: 900px;
      position: relative;
      margin: auto;
    }

    .next {
      right: 0;
    }

    /* Next and previous icon design */
    .previous,
    .next {
      cursor: pointer;
      position: absolute;
      top: 50%;
      padding: 10px;
      margin-top: -25px;
    }

    /* caption decorate */
    .captionText {
      color: #000000;
      font-size: 14px;
      position: absolute;
      padding: 12px 12px;
      bottom: 8px;
      width: 100%;
      text-align: center;
    }

    /* Slider image number */
    .slideNumber {
      background-color: #5574C5;
      color: white;
      border-radius: 25px;
      right: 0;
      opacity: .5;
      margin: 5px;
      width: 30px;
      height: 30px;
      text-align: center;
      font-weight: bold;
      font-size: 24px;
      position: absolute;
    }


    .fa:hover {
      transform: rotate(360deg);
      transition: 1s;
      color: white;
    }

    .footerdot {
      cursor: pointer;
      height: 15px;
      width: 15px;
      margin: 0 2px;
      background-color: #bbbbbb;
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.5s ease;
    }

    .active,
    .footerdot:hover {
      background-color: black;
    }
   .detailbottom {max-width: 800px;}
   i.fa.fa-chevron-circle-right, i.fa.fa-chevron-circle-left {font-size: 44px;}
   .comicImageSlide {
      width: 670px;
      height: 780px;
      object-fit: contain;
   }
   .zoombtn {
      display: inline-grid;
      position: fixed;
      right: 0;
      z-index: 99999;
      top: 20%;
   }
   button#out {
      margin-top: 3px;
   }
   .anchorbtnCls {
      cursor: not-allowed;
      pointer-events: none;
      /* background-color: #009bd54f; */
   }
   #TrgigerPreviewModal a.coinsbtn {
      background-color: #0898D9;
      color: #fff;
      border-radius: 30px;
      margin-bottom: 12px;
      padding: 9px 17px 9px 17px;
      font-weight: bold;
   }
   @media(max-width:767px) {
      .comicImageSlide {
         width: 100%;
         height: 577px;
         object-fit: contain;
      }
      .detailbottom .backbtnEpisode{
         width: 31px;
         margin-right: 5px;
      }
   }


/*****New Css */
.section-box-container {
    position: relative;
    text-align: left;
    width: 100%;
}


.section-boxes {
    display: flex;
    flex-wrap: nowrap;
    justify-content: center;
    width: 100%;
    gap: 30px;

}
.interact-zoom-header{
  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  margin:25px;
  font-size:24px;
}
.section-boxes-wrap{
   padding: 0px 0;
    width: 80%;
    height: auto;
}
.section-title{
    position: relative;
    transition: .3s ease;
}

.section-title::before{
    content: "";
    position: absolute;
    top: 8%;
    left: 0%;
    background: #35385330;
    width: 100%;
    height: 100%;
    z-index: -1;
    transition: .3s ease all;
    border-radius: 5px;
}



.section-title:hover::before{
    top: 0%;
    left: 0%;
    transform: scale(1.03);
}

.title-area{
    position: relative;
}

.section-projects-overflow{
    transition: .3s ease;
}

.swiper3{
    transition: .5s ease all;
}


.project-type {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 0;
    font-size: 16px;
    color: #6c6f8a;
    white-space: nowrap;
    transition: .3s ease all;
}

.project-title {
    white-space: nowrap;
    font-size: 22px;
    color: #f1f1f1;
    transition: .3s ease;
}
.section-box {
    overflow: hidden;
    width: 100%;
    border-radius: 5px;
}

.active .swiper3{
    height: 550px ;
}
.interact-zoom-header{
    display: flex;
    align-items: center;
    padding: 30px 0 0;
    color: #888ba3;
  font-size:22px;
}

i{
  margin-right:5px;
}
.project-date{
  font-size:16px;
    color: #6c6f8a;
}

.experince-header {
  text-align:center;
    color:#fff;
    position: relative;
    font-size: 22px;
  margin:50px 0;
}
.experince-header-set{
    padding:10px;
    background: #111324;
}
.experince-header::before{
    content: "";
    position: absolute;
    display: flex;
    top: 50%;
    left: 0;
    background: #272a47;
    height: 10%;
    width: 100%;
    z-index: -1;
}
@media(max-width:767px) {
   .previous,.next { z-index: 99999;padding: 0px;}
   .section-box {width: 100%;}
   .swiper3 {margin: 10px 24px;}
   .section-boxes-wrap {width: 100%;}
   i.fa.fa-chevron-circle-right, i.fa.fa-chevron-circle-left {font-size: 25px;margin-right: 0px;}

}
</style>
<section class="searchpage readdetailpage bg-dark slidercalss">
   <!-- <div class="buttons zoombtn">

      <button class="btn btn-primary zoom-in" id="in"><i class="fa fa-plus" aria-hidden="true"></i></button>
      <button class="btn btn-primary zoom-out" id="out"><i class="fa fa-minus" aria-hidden="true"></i></button>
      <div class="col-md-12" style="text-align:center;margin-top: 1em;margin-bottom: 1em;">
         <button class="reset btn btn-primary"><i class="fa fa-refresh" aria-hidden="true"></i></button>
      </div>
   </div> -->

   <div class="container">
      <input type="hidden" id="backLink" value="{{url('back-comic-detail')}}/{{ $comic_id }}/{{ $response['episode']['id'] }}">
      <div class="detailbottom websection mt-3 mb-3">
         <div class="d-flex">
            <div class="f-column col-md-3 backbtnnn">
               <a class="backLoginBtn" href="{{ url('back-comic-detail') }}/{{ $comic_id }}/{{ $response['episode']['id'] }}/{{ $response['episode']['pages'][0]['id'] }}">Go Back</a>
            </div>
            <div class="f-column col-md-6 text-center backbtnnn1">
            @if(empty($userLoginCheck))
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signuppopup" class="@if(empty($previous_episode)) btndisble @endif">Previous Episode</a>

               @elseif(!empty(Auth::user()->role) && Auth::user()->role != 2)
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#PubAskAcc" class="@if(empty($previous_episode)) btndisble @endif">Previous Episode</a>

               @elseif($user_type == 1 && $previous_episode_access_type != 1 && $previous_episode_preview != 0 && $previous_is_paid_coins == 2)
               <a href="{{ url('customer/episode-detail') }}/{{ $previous_episode }}/{{ $comic_id }}/viv-v1w-episode" class="@if(empty($previous_episode)) btndisble @endif">Previous Episode</a>

               @elseif($user_type == 1 && $previous_episode_access_type != 1 && $previous_episode_preview == 0 && $previous_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$previous_charge_coin_free_user}}" charge_coin_paid_user="{{$previous_charge_coin_paid_user}}" episode_id="{{ $previous_episode }}" coins="{{ $previous_coins }}" preview_pages="{{ $previous_episode_preview }}" class="subscriptionPopup @if(empty($previous_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Previous Episode</a>

               @elseif(($user_type == 1 && $previous_episode_access_type != 1) && $previous_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$previous_charge_coin_free_user}}" charge_coin_paid_user="{{$previous_charge_coin_paid_user}}" episode_id="{{ $previous_episode }}" coins="{{ $previous_coins }}" preview_pages="{{ $previous_episode_preview }}" class="subscriptionPopup @if(empty($previous_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Previous Episode</a>

               @elseif(($user_type == 2) && ($user_pending_coins <= $previous_coins) && $previous_is_paid_coins == 2 && $previous_episode_access_type != 1)
               <a data-bs-toggle="modal" data-bs-target="#CoinsPurchase" class="@if(empty($previous_episode)) btndisble @endif">Previous Episode</a>

               @elseif(($previous_episode_access_type == 1) || ($user_pending_coins >= $previous_coins))
               <a href="{{ url('customer/episode-detail') }}/{{ $previous_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($previous_episode)) btndisble @endif">Previous Episode</a>

               @else
               <a href="{{ url('customer/episode-detail') }}/{{ $previous_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($previous_episode)) btndisble @endif">Previous Episode</a>

            @endif

            @if(empty($userLoginCheck))
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signuppopup" class="@if(empty($next_episode)) btndisble @endif">Next Episode</a>

               @elseif(!empty(Auth::user()->role) && Auth::user()->role != 2)
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#PubAskAcc" class="@if(empty($next_episode)) btndisble @endif">Next Episode</a>

               @elseif($user_type == 1 && $next_episode_access_type != 1 && $next_episode_preview != 0 && $next_is_paid_coins == 2)
               <a href="{{ url('customer/episode-detail') }}/{{ $next_episode }}/{{ $comic_id }}/viv-v1w-episode" class="@if(empty($next_episode)) btndisble @endif">Next Episode</a>

               @elseif($user_type == 1 && $next_episode_access_type != 1 && $next_episode_preview == 0 && $next_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$next_charge_coin_free_user}}" charge_coin_paid_user="{{$next_charge_coin_paid_user}}" episode_id="{{ $next_episode }}" coins="{{ $next_coins }}" preview_pages="{{ $next_episode_preview }}" class="subscriptionPopup @if(empty($next_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Next Episode</a>

               @elseif(($user_type == 1 && $next_episode_access_type != 1) && $next_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$next_charge_coin_free_user}}" charge_coin_paid_user="{{$next_charge_coin_paid_user}}" episode_id="{{ $next_episode }}" coins="{{ $next_coins }}" preview_pages="{{ $next_episode_preview }}" class="subscriptionPopup @if(empty($next_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Next Episode</a>

               @elseif(($user_type == 2) && ($user_pending_coins <= $next_coins) && $next_is_paid_coins == 2 && $next_episode_access_type != 1)
               <a data-bs-toggle="modal" data-bs-target="#CoinsPurchase" class="@if(empty($next_episode)) btndisble @endif">Next Episode</a>

               @elseif(($next_episode_access_type == 1) || ($user_pending_coins >= $next_coins))
               <a href="{{ url('customer/episode-detail') }}/{{ $next_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($next_episode)) btndisble @endif">Next Episode</a>

               @else
               <a href="{{ url('customer/episode-detail') }}/{{ $next_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($next_episode)) btndisble @endif">Next Episode</a>
            @endif
            </div>
            @php
            $headers = @get_headers($response['episode']['audio_file']);
            @endphp
            @if(!empty($headers[0]) && strpos($headers[0],'200') != false)
            <div class="f-column col-md-3 text-right backbtnnn2">
               <div class="form-check form-switch">
                  <label class="form-check-label">Autoplay</label>
                  <input class="form-check-input float-end autoplay" type="checkbox" id="autoplay">
               </div>
            </div>
            @endif


         </div>
      </div>
      <div class="detailbottom mobileSection mt-2">
         <div class="d-flex justify-content-between">
            <div class="f-column col-md-3 backbtnnn">
               <a class="backLoginBtn backbtnEpisode" href="{{ url('back-comic-detail') }}/{{ $comic_id }}/{{ $response['episode']['id'] }}/{{ $response['episode']['pages'][0]['id'] }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> </a>
            </div>
            <div class="f-column col-md-6 text-center backbtnnn1">
            @if(empty($userLoginCheck))
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signuppopup" class="@if(empty($previous_episode)) btndisble @endif">Previous</a>

               @elseif(!empty(Auth::user()->role) && Auth::user()->role != 2)
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#PubAskAcc" class="@if(empty($previous_episode)) btndisble @endif">Previous</a>

               @elseif($user_type == 1 && $previous_episode_access_type != 1 && $previous_episode_preview != 0 && $previous_is_paid_coins == 2)
               <a href="{{ url('customer/episode-detail') }}/{{ $previous_episode }}/{{ $comic_id }}/viv-v1w-episode" class="@if(empty($previous_episode)) btndisble @endif">Previous</a>

               @elseif($user_type == 1 && $previous_episode_access_type != 1 && $previous_episode_preview == 0 && $previous_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$previous_charge_coin_free_user}}" charge_coin_paid_user="{{$previous_charge_coin_paid_user}}" episode_id="{{ $previous_episode }}" coins="{{ $previous_coins }}" preview_pages="{{ $previous_episode_preview }}" class="subscriptionPopup @if(empty($previous_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Previous</a>

               @elseif(($user_type == 1 && $previous_episode_access_type != 1) && $previous_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$previous_charge_coin_free_user}}" charge_coin_paid_user="{{$previous_charge_coin_paid_user}}" episode_id="{{ $previous_episode }}" coins="{{ $previous_coins }}" preview_pages="{{ $previous_episode_preview }}" class="subscriptionPopup @if(empty($previous_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Previous</a>

               @elseif(($user_type == 2) && ($user_pending_coins <= $previous_coins) && $previous_is_paid_coins == 2 && $previous_episode_access_type != 1)
               <a data-bs-toggle="modal" data-bs-target="#CoinsPurchase" class="@if(empty($previous_episode)) btndisble @endif">Previous</a>

               @elseif(($previous_episode_access_type == 1) || ($user_pending_coins >= $previous_coins))
               <a href="{{ url('customer/episode-detail') }}/{{ $previous_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($previous_episode)) btndisble @endif">Previous</a>

               @else
               <a href="{{ url('customer/episode-detail') }}/{{ $previous_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($previous_episode)) btndisble @endif">Previous</a>

            @endif

            @if(empty($userLoginCheck))
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signuppopup" class="@if(empty($next_episode)) btndisble @endif">Next</a>

               @elseif(!empty(Auth::user()->role) && Auth::user()->role != 2)
               <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#PubAskAcc" class="@if(empty($next_episode)) btndisble @endif">Next</a>

               @elseif($user_type == 1 && $next_episode_access_type != 1 && $next_episode_preview != 0 && $next_is_paid_coins == 2)
               <a href="{{ url('customer/episode-detail') }}/{{ $next_episode }}/{{ $comic_id }}/viv-v1w-episode" class="@if(empty($next_episode)) btndisble @endif">Next</a>

               @elseif($user_type == 1 && $next_episode_access_type != 1 && $next_episode_preview == 0 && $next_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$next_charge_coin_free_user}}" charge_coin_paid_user="{{$next_charge_coin_paid_user}}" episode_id="{{ $next_episode }}" coins="{{ $next_coins }}" preview_pages="{{ $next_episode_preview }}" class="subscriptionPopup @if(empty($next_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Next</a>

               @elseif(($user_type == 1 && $next_episode_access_type != 1) && $next_is_paid_coins == 2)
               <a href="javascript:void(0)" totalCoins="{{$user_pending_coins}}" charge_coin_free_user="{{$next_charge_coin_free_user}}" charge_coin_paid_user="{{$next_charge_coin_paid_user}}" episode_id="{{ $next_episode }}" coins="{{ $next_coins }}" preview_pages="{{ $next_episode_preview }}" class="subscriptionPopup @if(empty($next_episode)) btndisble @endif" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">Next</a>

               @elseif(($user_type == 2) && ($user_pending_coins <= $next_coins) && $next_is_paid_coins == 2 && $next_episode_access_type != 1)
               <a data-bs-toggle="modal" data-bs-target="#CoinsPurchase" class="@if(empty($next_episode)) btndisble @endif">Next</a>

               @elseif(($next_episode_access_type == 1) || ($user_pending_coins >= $next_coins))
               <a href="{{ url('customer/episode-detail') }}/{{ $next_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($next_episode)) btndisble @endif">Next</a>

               @else
               <a href="{{ url('customer/episode-detail') }}/{{ $next_episode }}/{{ $comic_id }}/viv-v2w-episode" class="@if(empty($next_episode)) btndisble @endif">Next</a>
            @endif
            </div>
            @php
            $headers = @get_headers($response['episode']['audio_file']);
            @endphp
            @if(!empty($headers[0]) && strpos($headers[0],'200') != false)
            <div class="f-column col-md-3 text-right backbtnnn2">
               <div class="form-check form-switch d-flex">
                  <label class="form-check-label">Autoplay</label>
                  <input class="form-check-input float-end mt-1 autoplay" type="checkbox" id="autoplay">
               </div>
            </div>
            @endif
         </div>
      </div>
      @php
      $page_readed_id_html = "";
      @endphp
      <div class="image-container mb-3">
         @foreach($response['episode']['pages'] as $key=>$pd)
            @php
            //get Page Id
            if(!empty($response['episode']['page_readed_id']) && $pd['id'] == $response['episode']['page_readed_id'])
            {
               $page_readed_id_html = '<input type="hidden" class="page_readed_id" value="'.$pd['page_number'].'">';
            }
            if(!empty($current_preview_pages) && $loop->index >= $current_preview_pages && $response['episode']['user_type'] != 2 && $view == 'viv-v1w-episode')
            {
               break;
            }
            @endphp
            <!-- <div class="slide">
               <div class="slideNumber d-none">{{ $loop->iteration }}</div>
               <img class="comicImageSlide panzoom" src="{{ $pd['image_url'] ?? '' }}">
            </div> -->
            <div class="slide">
               <div class="section-boxes">
                  <div class="section-boxes-wrap">
                     <div class="section-title">
                           <div class="section-projects-overflow">
                              <div class="section-box ">
                                 <div class="swiper-container swiper3">
                                       <div class="swiper-wrapper">
                                          <div class="swiper-zooms swiper-slide"><div class="swiper-zoom-container"><img src="{{ $pd['image_url'] ?? '' }}" /></div></div>
                                       </div>
                                 </div>
                              </div>
                           </div>
                     </div>
                  </div>
               </div>
            </div>

         @endforeach
         <!-- Next and Previous icon to change images -->
         <a class="previous" onclick="moveSlides(-1)">
         <i class="fa fa-chevron-circle-left"></i>
         </a>
         <a class="next" onclick="moveSlides(1)">
         <i class="fa fa-chevron-circle-right"></i>
         </a>
      </div>
      @php
      echo $page_readed_id_html;
      @endphp
   </div>
   <audio id="audioFile" ontimeupdate="myFunction(this)">
      <source src="{{ $response['episode']['audio_file'] ?? '' }}" type="audio/mpeg" playsinline="" muted>
      <source src="{{ $response['episode']['audio_file'] ?? '' }}" type="audio/ogg" playsinline="" muted>
      Your browser does not support HTML5 audio.
   </audio>
   </div>
</section>

<!----TrgigerPreviewModal and coins purchase Modal Start--->
<div class="modal fade modelbutton" id="TrgigerPreviewModal">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
               <h4 class="modal-title w-100">Get the FULL experience</h4>
               <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25">
            </div>
            <!-- Modal body -->
            <div class="modal-body text-center">
               <a class="btn askLoginBtn subscription-page-btn" href="{{ url('customer/subscription-page') }}">Subscribe now and get this for {{ $current_charge_coin_paid_user }} <img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15"></a>
               <p class="colorwhite fw-bolder">You have already Viewed all the pages that are available for preview. To continue Reading this Episode you need to pay 10 Coins </p>
            </div>
            <!-- Modal footer -->

            <div class="modal-footer1 text-center mb-4">
               @if($user_pending_coins >= $current_charge_coin_free_user)
                  <a class="coinsbtn"  href="{{ url('customer/episode-detail') }}/{{ $response['episode']['id'] }}/{{ $comic_id }}/viv-v2w-episode">No, continue and experience for {{ $current_charge_coin_free_user ?? 0 }}<img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15">  </a>
               @else
                  <a class="coinsbtn" href="#" data-bs-toggle="modal" data-bs-target="#CoinsPurchase" id="hidesubscriptionMidelTriger">No, continue and experience for {{ $current_charge_coin_free_user ?? 0 }}<img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15">  </a>
               @endif
            </div>
         </div>
   </div>
</div>
<!----TrgigerPreviewModal and coins purchase Modal Start--->


<!----subscription and coins purchase Modal Start--->
<div class="modal fade modelbutton" id="CoinsPurchase">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header text-center">
            <h4 class="modal-title w-100">Insufficient coins</h4>
            <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-center">
            <p class="colorwhite fw-bolder">Recharge your wallet to continue reading</p>
         </div>
         <!-- Modal footer -->
         <div class="modal-footer pb-5 pt-5">
            <a class="btn askLoginBtn" href="{{ route('my-coins') }}">Add Coins  </a>
         </div>
      </div>
   </div>
</div>

<div class="modal fade modelbutton" id="subscriptionAndCoinsPurchase">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header text-center">
            <h4 class="modal-title w-100">Get the FULL experience</h4>
            <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-center">
            <a class="btn askLoginBtn subscription-page-btn" href="{{ route('subscription-page') }}" id="subscribedTextBtn"> </a>
            <p class="colorwhite fw-bolder">Become a subscriber to access Premium member discounts on episodes as well as select merch. If you join today you'll get $10 in bonus coins on us!</p>
         </div>
         <!-- Modal footer -->
         <div class="modal-footer1 text-center mb-4" id="directBtnEpisode">

         </div>
      </div>
   </div>
</div>
<!----subscription and PreviewModel Modal Start-->
<input type="hidden" id="comicIds" value="{{ $comic_id }}">
<input type="hidden" id="current_preview_pages" value="{{ $current_preview_pages }}">
<input type="hidden" id="view" value="{{ $view }}">




@endsection
@section('scripts')
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.panzoom/3.2.2/jquery.panzoom.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-mousewheel/1.0.5/mousewheel.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script>

var page_readed_id = $(".page_readed_id").val();
var second = []; //audio time
//var secondIndx = 0; //start from zero
var slideIndex = 1;
var manulClick = false;
var fnBrowserDetect = fnBrowserDetect();

var pageArray = {pages: <?php echo json_encode($response['episode']['pages']); ?>};
if(pageArray.pages.length > 0)
{
   pageArray.pages.forEach(function(item, index){
      const prop = {};
      var hms = item.audio_start;
      var a = hms.split(':');
      var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);

      prop.slide = index + 1;
      prop.time = seconds;

      second[index] = seconds;

   });
}

window.onload = function () {
   if(page_readed_id)
   {
      if(page_readed_id > 1)
      {
         slideIndex = page_readed_id - 1;
         //slideIndex = page_readed_id;
         displaySlide(slideIndex, 'timer');
         var audio = document.getElementById("audioFile");
         audio.currentTime = second[page_readed_id];

         if(fnBrowserDetect != 'safari')
         $(".autoplay").trigger('click');
      }
      else
      {
         displaySlide(slideIndex, 'timer');
         if(fnBrowserDetect != 'safari')
         $(".autoplay").trigger('click');
      }
   }
   else
   {
      displaySlide(slideIndex, 'timer');
      if(fnBrowserDetect != 'safari')
      $(".autoplay").trigger('click');
   }
};

$(document).on("change", ".autoplay", function(){

   if($(this).is(':checked'))
   {
      document.getElementById('audioFile').play();
   }
   else
   {
      document.getElementById('audioFile').pause();
   }
});

function moveSlides(n, option='') {
   //alert("click")
   if(option != 'auto')
   manulClick = true;

   displaySlide(slideIndex += n);
}


/* Main function */
function displaySlide(n) {
   slideIndex = parseInt(slideIndex)
   var i;
   var totalslides = document.getElementsByClassName("slide");

   console.log('totalslides')
   console.log(totalslides.length)

   //restriction next prev btn
   $(".next").removeClass('anchorbtnCls');
   $(".previous").removeClass('anchorbtnCls');
   if(slideIndex == totalslides.length)
   {
      $(".next").addClass('anchorbtnCls');
   }
   if(slideIndex == 1)
   {
      $(".previous").addClass('anchorbtnCls');
   }

   if (n > totalslides.length) {
      slideIndex = 1;
   }
   if (n < 1) {
      slideIndex = totalslides.length;
   }
   for (i = 0; i < totalslides.length; i++) {
      totalslides[i].style.display = "none";
   }
   totalslides[slideIndex - 1].style.display = "block";

   makeBackLink = $("#backLink").val()+'/'+pageArray.pages[slideIndex - 1].id;
   $(".backLoginBtn").attr("href", makeBackLink);

   var audio = document.getElementById("audioFile");
   audio.currentTime = second[slideIndex - 1];

}


function myFunction(event) {

   console.log(event.currentTime)
   console.log(second)

   console.log(slideIndex);

   var current_preview_pages = $("#current_preview_pages").val();
   var view = $("#view").val();
   if(current_preview_pages > 0 && view == 'viv-v1w-episode')
   {
      if(event.currentTime > second[current_preview_pages])
      {
         document.getElementById('audioFile').pause();
         $("#TrgigerPreviewModal").modal("show");
      }
   }

   slideIndex = parseInt(slideIndex)
   if(!manulClick)
   {
      if(event.currentTime > second[slideIndex])
      {
         //$(".reset").trigger("click");
         if(page_readed_id)
         {
            moveSlides(1,'auto');
         }
         else
         {
            moveSlides(1,'auto');
         }
      }
   }
   else
   {
      //$(".reset").trigger("click");
      manulClick = false;
   }
}
var swiper3 = new Swiper('.swiper3', {
                lazy:true,
                zoom: true,
                direction: 'horizontal',
                loop: false,
                grabCursor: true,


});

// (function() {
//     $('.panzoom').panzoom({
//       $zoomIn: $(".zoom-in"),
//       $zoomOut: $(".zoom-out"),
//       $zoomRange: $(".zoom-range"),
//       $reset: $(".reset")
//     });

// })();

function fnBrowserDetect(){
      let userAgent = navigator.userAgent;
      let browserName;

      if(userAgent.match(/chrome|chromium|crios/i)){
         browserName = "chrome";
         }else if(userAgent.match(/firefox|fxios/i)){
         browserName = "firefox";
         }  else if(userAgent.match(/safari/i)){
         browserName = "safari";
         }else if(userAgent.match(/opr\//i)){
         browserName = "opera";
         } else if(userAgent.match(/edg/i)){
         browserName = "edge";
         }else{
         browserName="No browser detection";
         }

      return browserName;
}



$(document).on("click", ".subscriptionPopup", function (e) {
  // $(".directBtnEpisode").hide();
  var totalCoins = $(this).attr('totalCoins');
  var charge_coin_free_user = $(this).attr('charge_coin_free_user');
  var charge_coin_paid_user = $(this).attr('charge_coin_paid_user');
  var episode_id = $(this).attr('episode_id');
  var coins = $(this).attr('coins');
  var comicIds = $("#comicIds").val();

  var preview_pages = $(this).attr('preview_pages');
  var prShowLink = 'viv-v2w-episode';
  if(preview_pages > 0)
  {
      prShowLink = 'viv-v1w-episode';
  }



  $("#directBtnEpisode").empty();
  $("#subscribedTextBtn").empty();

  var html = '';
  var subhtml = `Subscribe now and get this for `+charge_coin_paid_user+`<img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15">`;
  if(parseInt(totalCoins) >= parseInt(coins))
  {
      html = `<a class="coinsbtn"  href="`+ajaxUrl+`/customer/episode-detail/`+episode_id+`/`+comicIds+`/`+prShowLink+`">No, continue and experience for `+charge_coin_free_user+` <img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15">  </a>`;
  }
  else
  {
      html =`<a class="coinsbtn"  href="#" data-bs-toggle="modal" data-bs-target="#CoinsPurchase" id="hidesubscriptionMidel">No, continue and experience for `+charge_coin_free_user+` <img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15">  </a>`;
  }

  $("#directBtnEpisode").append(html);
  $("#subscribedTextBtn").append(subhtml);


});
$(document).on("click", "#hidesubscriptionMidel", function (e) {
   $("#subscriptionAndCoinsPurchase").modal('hide')
})
$(document).on("click", "#hidesubscriptionMidelTriger", function (e) {
   $("#subscriptionAndCoinsPurchase").modal('hide')
})
</script>
@endsection