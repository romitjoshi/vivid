@extends('content.front.layouts.master')
@section('title','Home')
@section('content')




    <section class="bannerimg">

        <div class="banner-slider owl-carousel homebanslidesr webshow">
            @foreach($bannerimage as $image)
            <div class="item">
               <a href="{{$image->link ?? '#'}}">
                  <img class="homebannerimg" src="{{env('IMAGE_PATH_orignal').$image->image}}" ></a>
               <div class="bannerhcontent text-white"></div>
            </div>
            @endforeach
         </div>

         <div class="banner-slider owl-carousel homebanslidesr d-none mobileshow">
            @foreach($bannerimage as $image)
            <div class="item">
               <a href="{{$image->link ?? '#'}}">
                  <img class="homebannerimg" src="{{env('IMAGE_PATH_orignal').$image->mobile_banner}}" ></a>
               <div class="bannerhcontent text-white"></div>
            </div>
            @endforeach
         </div>
    </section>
    @if(!empty($featured))
    <section class="featured_products bg-dark slidercalss comall">
        <div class="container">
            <div class="row">
             <div class="col-md-6">
               <h3>Indie Spotlight</h3>
             </div>
             <!-- <div class="col-md-6 seemore">
               <a href="{{url('all-comic')}}"><h4>See more</h4></a>
             </div> -->
            </div>
         <div class="owl-slider">
            <div id="featured-product" class="owl-carousel">

                @foreach ($featured ?? '' as $feature)

                  <div class="item">
                  <a href="{{url('comic-detail')}}/{{$feature->slug ?? 0}}">
                    <div class="imgwithtag contenthoveimage">
                    <div class="content-overlayimage"></div>
                      <img src="{{ env('IMAGE_PATH_medium').$feature->featured_image }}" class="img-fluid mediumImgSize" alt="avatar img">
                      <div class="content-details fadeIn-top">
                         <p>{{Str::words($feature->description,15)}}</p>
                      </div>

                    @if(empty($userLoginCheck))
                        @if($feature->access_type=="1")
                           <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                        @elseif($feature->access_type=="2")
                           <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                        @else
                        <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                        @endif
                     @else
                        @if(!empty($ud) && $ud->user_type != 2)
                             @if($feature->access_type=="1")
                                 <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                              @elseif($feature->access_type=="2")
                                 <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                              @else
                              <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                              @endif
                        @endif
                     @endif
                    </div>
                    <div class="content-sec">
                      <div class="contenttextsec">
                           <p class="line-clamp line-clamp-1">{{$feature->name}}</p>
                      </div>
                      <div class="contentratingsec">
                        <p><i class="fa fa-star" aria-hidden="true"></i>{{$feature->ratings}}</p>
                      </div>
                 </div>
               </a>
               </div>
               @endforeach
            </div>
         </div>
      </div>

   </section>
   @endif

    @if(!empty($recentlyadded))
   <section class="Recently_Added bg-dark slidercalss comall">
        <div class="container">
            <div class="row">
             <div class="col-md-6 mobrecc1">
               <h3>Recently Added</h3>
             </div>
             <div class="col-md-6 seemore mobrecc2">
               <a href="{{url('all-comic')}}"><h4>See All</h4></a>
             </div>
            </div>
         <div class="owl-slider">
            <div id="Recently_Added" class="owl-carousel">

                @foreach ($recentlyadded ?? '' as $recentlyadd)

                  <div class="item">
                  <a href="{{url('comic-detail')}}/{{$recentlyadd->slug ?? 0}}">
                    <div class="imgwithtag contenthoveimage">
                        <div class="content-overlayimage"></div>
                           <img src="{{ env('IMAGE_PATH_medium').$recentlyadd->featured_image }}" class="img-fluid mediumImgSize" alt="avatar img">
                           <div class="content-details fadeIn-top">
                               <p>{{Str::words($recentlyadd->description,15)}}</p>
                           </div>
                        @if(empty($userLoginCheck))
                              @if($recentlyadd->access_type=="1")
                                 <img class="tagimg" src="{{asset('front/images/free.png')}}"/>
                              @elseif($recentlyadd->access_type=="2")
                                 <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                              @else
                                 <img class="tagimg" src="{{asset('front/images/orignal.png')}}"/>
                              @endif
                           @else
                              @if(!empty($ud) && $ud->user_type != 2)
                                 @if($recentlyadd->access_type=="1")
                                 <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                                 @elseif($recentlyadd->access_type=="2")
                                 <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                                 @else
                                 <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                                 @endif
                              @endif
                        @endif
                    </div>

                     <div class="content-sec">
                        <div class="contenttextsec">

                              <p class="line-clamp line-clamp-1">{{$recentlyadd->name}}</p>
                        </div>
                        <div class="contentratingsec">
                           <p><i class="fa fa-star" aria-hidden="true"></i>{{$recentlyadd->ratings}}</p>
                        </div>
                     </div>
                     </a>
               </div>
               @endforeach
            </div>
         </div>
      </div>

   </section>
   @endif


   @if(!empty($populars))
   <section class="popular_products bg-dark slidercalss comall">
      <div class="container">
         <div class="row">
         <div class="col-md-6">
            <h3>Popular</h3>
         </div>
         <div class="col-md-6 seemore">
            <!-- <a href="{{url('all-comic')}}"><h4>See more</h4></a> -->
         </div>
         </div>
         <div class="owl-slider">
         <div id="popular-product" class="owl-carousel">
         @foreach ($populars as $popular)

               <div class="item">
                <a href="{{url('comic-detail')}}/{{$popular->slug ?? 0}}">
                  <div class="imgwithtag contenthoveimage">
                  <div class="content-overlayimage"></div>
                 <img src="{{ env('IMAGE_PATH_medium').$popular->featured_image }}" class="img-fluid mediumImgSize" alt="avatar img">
                 <div class="content-details fadeIn-top">
                               <p>{{Str::words($popular->description,15)}}</p>
                           </div>
                  @if(empty($userLoginCheck))
                     @if($popular->access_type=="1")
                        <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                     @elseif($popular->access_type=="2")
                        <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                     @else
                        <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                     @endif
                  @else
                     @if(!empty($ud) && $ud->user_type != 2)
                        @if($popular->access_type=="1")
                        <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                        @elseif($popular->access_type=="2")
                        <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                        @else
                        <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                        @endif
                     @endif
                  @endif
                  </div>
                  <div class="content-sec">
                     <div class="contenttextsec">
                        <p class="line-clamp line-clamp-1">{{$popular->name}}</p>
                     </div>
                     <div class="contentratingsec">
                     <p><i class="fa fa-star" aria-hidden="true"></i>{{$popular->ratings}}</p>
                     </div>

                   </div>
                   </a>
            </div>
            @endforeach

         </div>
         </div>
      </div>

   </section>
   @endif

   @if(!empty($publisher))
   <section class="Publisher_Added bg-dark slidercalss comall">
        <div class="container">
            <div class="row">
             <div class="col-md-6">
               <h3>Publishers </h3>
             </div>
             <!-- <div class="col-md-6 seemore">
               <a href="{{url('all-comic')}}"><h4>See more</h4></a>
             </div> -->
            </div>
         <div class="owl-slider">
            <div id="Publisher_Added" class="owl-carousel">

                @foreach ($publisher ?? '' as $publishers)

                  <div class="item">
                  <a href=" {{ url('publisher-profile') }}/{{ $publishers->slug ?? 0 }}">
                    <div class="imgwithtag  ">
                           <img src="{{ $publishers->image }}" class="img-fluid circleImage circleImageHome" alt="avatar img">


                    </div>

                     <div class="content-sec">
                        <div class="contenttextsec">

                              <p class="line-clamp line-clamp-1">{{$publishers->name}}</p>
                        </div>
                     </div>
                     </a>
               </div>
               @endforeach
            </div>
         </div>
      </div>

   </section>
   @endif
@endsection
@section('scripts')
<script>
      jQuery("#featured-product").owlCarousel({
         loop:false,
			margin:30,
			nav:true,
         dots: false,
			smartSpeed: 400,
			autoplay: false,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
         responsive: {
            0: {
               items: 2,
               margin:10,
               stagePadding:30
            },
            600: {
               items:2,
               margin:10,
               stagePadding:30
            },
            768: {
               items:3,
               margin:10,
               stagePadding:30
            },
            1024: {
               items: 5
            },
            1366: {
               items: 5
            }
         }
      });


      jQuery("#Recently_Added").owlCarousel({
         loop:false,
			margin:30,
			nav:true,
         dots: false,
			smartSpeed: 800,
			autoplay: false,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
         responsive: {
            0: {
               items:2,
               margin:10,
               stagePadding:30
            },

            600: {
               items:2,
               margin:10,
               stagePadding:30
            },
            768: {
               items:3,
               margin:10,
               stagePadding:30
            },
            1024: {
               items: 5
            },

            1366: {
               items: 5
            }
         }
      });
      jQuery("#Publisher_Added").owlCarousel({
         loop:false,
			margin:30,
			nav:true,
         dots: false,
			smartSpeed: 300,
			autoplay: false,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
         responsive: {
            0: {
               items: 3
            },

            600: {
               items:3
            },
            1024: {
               items: 7
            },

            1366: {
               items: 7
            }
         }
      });
      jQuery("#popular-product").owlCarousel({
         loop:false,
			margin:30,
			nav:true,
         dots: false,
			smartSpeed: 600,
			autoplay: false,
			navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
         responsive: {
            0: {
               items: 2,
               margin:10,
			      stagePadding:30
            },

            600: {
               items: 2,
               margin:10,
			      stagePadding:30
            },
            768: {
               items:3,
               margin:10,
               stagePadding:30
            },
            1024: {
               items: 5
            },

            1366: {
               items: 5
            }
         }
      });
      jQuery(".banner-slider").owlCarousel({
            loop:true,
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
   </script>
@endsection
