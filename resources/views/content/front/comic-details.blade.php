@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
@php

//echo '<pre>';print_r($response);exit;

$userLoginCheck = false;
$user_type = $response['details']['user_type'];
$comic_access_type = $response['details']['access_type'];
$episode_readed = $response['details']['episode_readed'];
$episode_readed_id = $response['details']['episode_readed_id'];

$firstEpisode = $response['episode'][0] ?? [];

$totalCoins = 0;

if(!empty(Auth::user()->role))
{
   $userLoginCheck = true;
}


if(!empty(Auth::user()->role) && Auth::user()->role == 2)
{
   if(empty($episode_readed_id))
   {
      $episode_readed_id = $response['episode'][0]['id'] ?? 0;
   }
   $totalCoins = Helper::getcoinsTotal(Auth::user()->id);
}
@endphp
<style>
.contenttextsec {width: 100% !important;}
</style>
<section class="productdetailpage bg-dark">
         <div class="container">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>

                  <li class="breadcrumb-item active" aria-current="page">Comic Detail</li>
               </ol>
            </nav>
            <div class="row">
               <div class="col-md-5 productimgsec">
                  <div class="productimgsecinner">
                  <img src="{{env('IMAGE_PATH_orignal').$response['details']['featured_image'] ?? '' }}" class="w-100 squareImgSize"/>

                  @if(empty($userLoginCheck))
                     @if($comic_access_type == 1)
                     <span class="badge bg-primary rounded-pill"> Free</span>
                     @elseif($comic_access_type == 2)
                     <span class="badge bg-danger rounded-pill"> Premium</span>
                     @else
                     <span class="badge orignl-badge rounded-pill"> Original</span>
                     @endif
                  @else
                     @if(!empty($user_type) && $user_type != 2)
                        @if($comic_access_type == 1)
                        <span class="badge bg-primary rounded-pill"> Free</span>

                        @elseif($comic_access_type == 2)
                        <span class="badge bg-danger rounded-pill"> Premium</span>

                        @else
                        <span class="badge orignl-badge rounded-pill"> Original</span>
                        @endif
                     @endif
                  @endif

               </div>
               </div>
               <div class="col-md-7 productinfosec">
                  <div class="card">
                     <div class="card-body">
                       <h3>{{ $response['details']['name'] ?? ''}}</h3>
                       <div class="tages">
                          @php
                          $goldCls = '';
                          if($comic_access_type == 3)
                          {
                           $goldCls = 'orignl-badge';
                          }
                          @endphp
                        @foreach($response['category'] as $category)
                        <span class="badge bg-primary rounded-pill mt-1 {{ $goldCls }}">{{ $category['name'] ?? ''}}</span>
                        @endforeach
                       </div>
                       <p>{{ $response['details']['description']  ?? '' }}</p>
                     </div>
                   </div>
                   <div class="productinfo">
                      <div class="row">
                        <div class="col-6 readingsubtitle">
                           <input type="hidden" id="previewEpiLink" value="{{ url('customer/episode-detail') }}/{{ $episode_readed_id }}/{{ $response['details']['id'] }}/viv-v2w-episode" >

                           @if(empty($userLoginCheck))
                           <a class="btn askLoginBtn" href="{{ route('front-login-view') }}">Login to read now</a>

                           @elseif(!empty(Auth::user()->role) && Auth::user()->role != 2)
                           <a class="btn askLoginBtn" href="#" data-bs-toggle="modal" data-bs-target="#PubAskAcc">Create a customer account</a>

                           @elseif($user_type == 1 && $firstEpisode['access_type'] != 1 && $response['details']['preview_pages'] != 0 && $firstEpisode['is_paid_coins'] == 2)
                           <a class="btn askLoginBtn subscriptionPopup" data-bs-toggle="modal" totalCoins="{{$totalCoins}}" charge_coin_free_user="{{$firstEpisode['charge_coin_free_user']}}" charge_coin_paid_user="{{$firstEpisode['charge_coin_paid_user']}}" episode_id="{{ $firstEpisode['id'] }}" coins="{{ $firstEpisode['coins'] }}" preview_pages="{{ $firstEpisode['preview_pages'] }}" data-bs-target="#subscriptionAndCoinsPurchase">Experience now for {{$firstEpisode['coins']}} <img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15"></a>
                           <a class="btn" href="{{ url('customer/episode-detail') }}/{{ $episode_readed_id }}/{{ $response['details']['id'] }}/viv-v1w-episode">Preview</a>

                           @elseif($user_type == 1 && $firstEpisode['access_type'] != 1 && $response['details']['preview_pages'] == 0 && $firstEpisode['is_paid_coins'] == 2)
                           <a class="btn askLoginBtn subscriptionPopup" data-bs-toggle="modal" totalCoins="{{$totalCoins}}" charge_coin_free_user="{{$firstEpisode['charge_coin_free_user']}}" charge_coin_paid_user="{{$firstEpisode['charge_coin_paid_user']}}" episode_id="{{ $firstEpisode['id'] }}" coins="{{ $firstEpisode['coins'] }}" preview_pages="{{ $firstEpisode['preview_pages'] }}" data-bs-target="#subscriptionAndCoinsPurchase">Experience now for {{$firstEpisode['coins']}} <img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15"></a>


                           @elseif(($user_type == 1 && $firstEpisode['access_type'] != 1) && $firstEpisode['is_paid_coins'] == 2)
                           <a class="btn subscriptionPopup" data-bs-toggle="modal" totalCoins="{{$totalCoins}}" charge_coin_free_user="{{$firstEpisode['charge_coin_free_user']}}" charge_coin_paid_user="{{$firstEpisode['charge_coin_paid_user']}}" episode_id="{{ $firstEpisode['id'] }}" coins="{{ $firstEpisode['coins'] }}" preview_pages="{{ $firstEpisode['preview_pages'] }}" data-bs-target="#subscriptionAndCoinsPurchase">Experience now for {{$firstEpisode['coins']}} <img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15"></a>

                           @elseif(($user_type == 2) && ($totalCoins <= $firstEpisode['coins']) && $firstEpisode['is_paid_coins'] == 2 && $firstEpisode['access_type'] != 1)
                           <a class="btn" data-bs-toggle="modal" data-bs-target="#CoinsPurchase">Experience now for {{$firstEpisode['coins']}} <img class="v-coin-sub" src="{{ asset('front/images/v-coins.png') }}" width="15"></a>

                           @elseif(($firstEpisode['access_type'] == 1) || ($totalCoins >= $firstEpisode['coins']))
                           <a class="btn" href="{{ url('customer/episode-detail') }}/{{ $episode_readed_id }}/{{ $response['details']['id'] }}/viv-v2w-episode">@if($episode_readed != 0) Continue Reading From Episode {{ $episode_readed  }} @else Start Reading From Episode 1 @endif </a>

                           @else
                           <a class="btn" href="{{ url('customer/episode-detail') }}/{{ $episode_readed_id }}/{{ $response['details']['id'] }}/viv-v2w-episode">@if($episode_readed != 0) Continue Reading From Episode {{ $episode_readed  }} @else Start Reading From Episode 1 @endif </a>

                           @endif
                        </div>
                        <div class="col-6 sharenotsec shareicondetail">
                           <!-- <a href="#"><i class="fa fa-share-alt" aria-hidden="true"></i></a>
                           <a href="#"><i class="fa fa-bell" aria-hidden="true"></i></a> -->
                           <div class="sociallinks d-none">
                              {!! Share::page(url('/comic-detail/'. $response['details']['id']))->facebook()->twitter()->whatsapp()->linkedin() !!}
                           </div>
                           <img class="cursor-pointer shareIcon" src="{{asset('front/images/share.svg')}}" width="35"/>

                              <img class="cursor-pointer @if(!empty($userLoginCheck)) bellicon  @endif  @if(!empty($response['details']['notify'])) dnoneCstm @endif" notify="False" id="notifyFalse" src="{{asset('front/images/bell.svg')}}" width="35" @if(empty($userLoginCheck)) data-bs-toggle="modal" data-bs-target="#signuppopup" @endif />


                              <img class="cursor-pointer @if(!empty($userLoginCheck)) bellicon  @endif @if(empty($response['details']['notify'])) dnoneCstm @endif" notify="True" id="notifyTrue" src="{{asset('front/images/bellcheck.png')}}" width="35"/>


                        </div>

                        @if(!empty(Auth::user()->role) && Auth::user()->role == 2)
                           <p>
                           @php
                              for ($i =1; $i <= 5; $i++)
                              {
                                 if(Auth::check())
                                 {
                                    $COLOR = 'style="color:white"';
                                    if($response['details']['is_user_rating'] >= $i )
                                    {
                                       $COLOR = '';
                                    }
                                    echo ' <i class="fa fa-star" aria-hidden="hidden" data-bs-toggle="modal" data-bs-target="#ratingmodal" '.$COLOR.'></i>';
                                 }
                              }
                           @endphp
                           </p>
                        @endif
                        <div class="comicpubmain mt-3">
                        <h5 style="color:white"><b>Publisher</b></h5>
                        <div class="comicpubinner">
                        <a class="authorName" href="{{ url('publisher-profile') }}/{{ $response['publisher'][0]['slug'] ?? 0 }}" >
                        <img src="{{ $response['publisher'][0]['image'] ?? ''}}" alt="avatar img" >
                        </a>
                        <p><a class="authorName" href="{{ url('publisher-profile') }}/{{ $response['publisher'][0]['slug'] ?? 0 }}" > {{ $response['publisher'][0]['name'] ?? '' }} </a></p>
                        </div>
                        </div>
                     </div>
                   </div>
               </div>

            </div>

            <div class="row">
               <div class="productnextepisodes">
                  <div class="owl-slider">

                  @if(!empty($response['episode']))
                     <h3>Next In Series</h3>
                  @endif
                     <div id="product-nextepi" class="owl-carousel">
                       @foreach($response['episode'] as $episode)
                           @php

                              @if(empty($userLoginCheck))
                              <a href="#" data-bs-toggle="modal" data-bs-target="#signuppopup">

                              @elseif(!empty(Auth::user()->role) && Auth::user()->role != 2)
                              <a href="#" data-bs-toggle="modal" data-bs-target="#PubAskAcc">

                              @elseif($user_type == 1 && $episode['access_type'] != 1 && $episode['preview_pages'] != 0 && $episode['is_paid_coins'] == 2)
                              <a href="{{ url('customer/episode-detail') }}/{{ $episode['id'] }}/{{ $response['details']['id'] }}/viv-v1w-episode">

                              @elseif($user_type == 1 && $episode['access_type'] != 1 && $episode['preview_pages'] == 0 && $episode['is_paid_coins'] == 2)
                              <a href="#" totalCoins="{{$totalCoins}}" charge_coin_free_user="{{$episode['charge_coin_free_user']}}" charge_coin_paid_user="{{$episode['charge_coin_paid_user']}}" episode_id="{{ $episode['id'] }}" coins="{{ $episode['coins'] }}" preview_pages="{{ $episode['preview_pages'] }}" class="subscriptionPopup" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">

                              @elseif(($user_type == 1 && $episode['access_type'] != 1) && $episode['is_paid_coins'] == 2)
                              <a href="#" totalCoins="{{$totalCoins}}" charge_coin_free_user="{{$episode['charge_coin_free_user']}}" charge_coin_paid_user="{{$episode['charge_coin_paid_user']}}" episode_id="{{ $episode['id'] }}" coins="{{ $episode['coins'] }}" preview_pages="{{ $episode['preview_pages'] }}" class="subscriptionPopup" data-bs-toggle="modal" data-bs-target="#subscriptionAndCoinsPurchase">

                              @elseif(($user_type == 2) && ($totalCoins <= $episode['coins']) && $episode['is_paid_coins'] == 2 && $episode['access_type'] != 1)
                              <a data-bs-toggle="modal" data-bs-target="#CoinsPurchase">


                              @elseif(($episode['access_type'] == 1) || ($totalCoins >= $episode['coins']))
                              <a href="{{ url('customer/episode-detail') }}/{{ $episode['id'] }}/{{ $response['details']['id'] }}/viv-v2w-episode">


                              @else
                              <a href="{{ url('customer/episode-detail') }}/{{ $episode['id'] }}/{{ $response['details']['id'] }}/viv-v2w-episode">

                              @endif


                        <div class="item">
                           <div class="imgwithtag ">
                              <img class="smallImgSize" src="{{env('IMAGE_PATH_medium').$episode['image'] ?? '' }}">


                            </div>
                            @if(empty($userLoginCheck))
                              @if($episode['access_type'] == 1)
                                 <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                              @elseif($episode['access_type'] == 2)
                                 <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                              @else
                                 <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                              @endif
                           @else
                              @if(!empty($ud) && $ud->user_type != 2)
                                 @if($episode['access_type'] == 1)
                                 <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                                 @elseif($episode['access_type'] == 2)
                                 <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                                 @else
                                 <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                                 @endif
                              @endif
                           @endif
                           <div class="content-sec">
                              <div class="contenttextsec line-clamp line-clamp-1">
                                    <p >{{ $episode['name'] ?? ''}}</p>
                              </div>
                              @if($episode['access_type'] != 1 && $episode['is_paid_coins'] == 2)
                              <div class="comicdetailsclass">
                              <img class="" src="{{ asset('front/images/v-coins.png') }}" >
                              <p>{{ $episode['coins'] ?? 0}}</p>
                              </div>
                              @endif

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

<!----Rating Modal Start--->
<div class="modal fade modelbutton" id="ratingmodal">
   <form action="{{ route ('rating') }}" id="rating_id" method="post">
      @csrf
      <input type="hidden" name="comic_id" value="{{ $response['details']['id'] }}">
      <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">


       <!-- Modal Header -->
       <div class="modal-header text-center">
         <h4 class="modal-title w-100">How did we do?</h4>
         <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>

       </div>

       <!-- Modal body -->
       <div class="modal-body text-center">
         <p class="colorwhite fw-bolder">Let us know with a rating!</p>
         <div class="rate">
            @for($i=5;$i>0;$i--)
            <input type="radio" class="selectstar" id="star{{$i}}" name="rate" value="{{$i}}" {{ (ceil($response['details']['is_user_rating'] ?? 0) == $i) ? 'checked="checked"' : '' }}>
            <label for="star{{$i}}" title="text">{{$i}} stars</label>
            @endfor
          </div>
       </div>

       <!-- Modal footer -->
       <div class="modal-footer">
         <button type="submit" class="btn" data-bs-dismiss="modal" id="ratingButton">@if($response['details']['is_user_rating'] > 0) Update @else Done @endif</button>
       </div>

      </div>
      </div>
   </form>
</div>
<!----Rating Modal End-->
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
<input type="hidden" id="comicIds" value="{{ $response['details']['id'] }}">
<!-- <input type="hidden" id="preview_pages" value="{{ $response['details']['preview_pages'] }}"> -->
@endsection
@section('scripts')
<script>
// $("#previewModelLink").attr("href", $("#previewEpiLink").val());

jQuery("#product-nextepi").owlCarousel({
   loop:false,
   margin:30,
   nav:true,
   dots: false,
   smartSpeed: 500,
   autoplay: false,
   navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
   responsive: {
      0: {
         items: 2.5,
         margin:10
      },

      600: {
         items: 2.5,
         margin:10
      },
      1024: {
         items: 6
      },

      1366: {
         items: 6
      }
   }
});

$(document).on("click", ".bellicon", function (e) {
   $(".bellicon").addClass('dnoneCstm');
   var notify = $(this).attr('notify');
   if(notify == "True")
   $("#notifyFalse").removeClass('dnoneCstm');
   else
   $("#notifyTrue").removeClass('dnoneCstm');
   $.ajax({
      'url': "{{ route('notify') }}",
            'type': "POST",
            'dataType': "json",
            'data': {"_token": "{{ csrf_token() }}","id":"{{$response['details']['id']}}"},
            'success': function (response) {
            },
            'error': function (error) {
               console.log(error);
            },
      });
});

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
</script>

@endsection