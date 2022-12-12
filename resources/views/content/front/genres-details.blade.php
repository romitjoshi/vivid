@extends('content.front.layouts.master')
@section('title','Home')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<section class="searchpage bg-dark p-10" id="searchpage" >
   <div class="containerdmenu d-none">
      <ul class="recaddmenu owl-carousel" id="categoriesName">
         @foreach($category as $value)
         <li>
            <a href="javascript:void(0);" class="catList" cat_id="{{ $value->id}}">
               {{$value->category_name}}</a>
         </li>
         @endforeach
      </ul>
   </div>
   <div class="container">
    <div class="row p-10">

     <h1 class="searchhead">{{$catName ?? ''}}</h1>
           <nav aria-label="breadcrumb">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                  <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                  <li class="breadcrumb-item active" aria-current="page">genres</li>
                  <li class="breadcrumb-item active" aria-current="page">{{ $catName ?? ''}}</li>

               </ol>

            </nav>
        </div>
        <div class="row justify-content-start" id="comicMainDiv">
            @if(count($comicList) == 0)
            <div class="imgCont blankImg">
                        <img src="{{asset('front/images/no_content.png')}}" alt="no_content">
                     </div>
            @else
            @foreach($comicList as $comicshow)
            <div class="item col-lg-2 col-md-4 col-sm-4">

                <a href="{{url('comic-detail')}}/{{$comicshow->slug ?? 0}}">
                    <div class="imgwithtag">


                        @if(empty($userLoginCheck))
                        @if($comicshow->access_type=="1")
                        <img class="tagimg " src="{{asset('front/images/free.png')}}" />
                        @elseif($comicshow->access_type=="2")
                        <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                        @else
                        <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                        @endif
                        @else
                        @if(!empty($ud) && $ud->user_type != 2)
                        @if($comicshow->access_type=="1")
                        <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                        @elseif($comicshow->access_type=="2")
                        <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                        @else
                        <img class="tagimg" src="{{asset('front/images/orignal.png')}}" />
                        @endif
                        @endif
                        @endif
                        <div class="imgwithtag contenthoveimage">
                            <div class="content-overlayimage"></div>
                            <img class="f_img smallImgSize" src="{{ env('IMAGE_PATH_medium').$comicshow->featured_image ?? '' }}">
                            <div class="content-details fadeIn-top">

                                <p>{{Str::words($comicshow->description,15)}}</p>
                            </div>
                        </div>
                </a>

                <div class="content-sec">
                    <div class="contenttextsec">
                        <p class="line-clamp line-clamp-1">{{$comicshow->name }}</p>
                    </div>
                    <div class="contentratingsec">
                        <p><i class="fa fa-star" aria-hidden="true"></i>{{$comicshow->ratings ?? '' }}</p>
                    </div>
                </div>
            </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>

@endsection
@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<script>
// $(document).ready(function() {
//    getallComic(10, 1, 0);
// });
// $(document).on("click", ".catList", function() {
//    $(".catList").removeClass("activemenugeners");
//    $(this).addClass("activemenugeners");
//    var cat_id = $(this).attr('cat_id');
//    getallComic(10, 1, cat_id);
// })

// function getallComic(limit, page, category) {
//    $.ajax({
//       'url': "{{route('get-comic')}}",
//       'type': "post",
//       'dataType': "json",
//       'data': {
//          limit: limit,
//          page: page,
//          category: category,
//          "_token": "{{ csrf_token() }}"
//       },
//       'success': function(response) {
//          console.log(response);
//          $("#comicMainDiv").empty();
//          var comicHtml = '';
//          if (response.status) {
//             if (response.data.length > 0) {
//                var t = 1;
//                var cls = '';
//                response.data.forEach(function(item, index) {
//                   var img = "";
//                   var imgPath = "{{ env('IMAGE_PATH_large') }}" + item.featured_image;
//                   var checkimg = item.access_type;
//                   if (!response.userLoginCheck) {
//                      if (checkimg == 1) {
//                         img = '<img class="tagimg" src="{{asset("front/images/free.png")}}"> ';
//                      } else if (checkimg == 2) {
//                         img = '<img class="tagimg" src="{{asset("front/images/orignal.png")}}"> ';
//                      } else {
//                         img = '<img class="tagimg" src="{{asset("front/images/orignal.png")}}"> ';
//                      }
//                   } else {
//                      if (response.ud != '' && response.ud.user_type != 2) {
//                         if (checkimg == 1) {
//                            img = '<img class="tagimg" src="{{asset("front/images/free.png")}}"> ';
//                         } else if (checkimg == 2) {
//                            img = '<img class="tagimg" src="{{asset("front/images/paid.png")}}"> ';
//                         } else {
//                            img = '<img class="tagimg" src="{{asset("front/images/orignal.png")}}">';
//                         }
//                      }
//                   }

//                   if (t > 6)
//                      cls = 'mt-3';
//                   comicHtml += `<div class="comicGrid col-lg-2 col-md-4 col-sm-4 ` + cls + `">
//                <a href ="{{url('comic-detail')}}/` + item.id + `">
//                <div class="imgwithtag contenthoveimage">
//                 <div class="content-overlayimage"></div>
//                   <img class="f_img" src="` + imgPath + `">
//                      ` + img + `
//                      <div class="content-details fadeIn-top">
//                         <p> `+item.description.slice(0,50) + `...</p>
//                      </div>
//                   </div>
//                <div class="content-sec">
//                      <div class="contenttextsec">
//                            <p class="line-clamp line-clamp-1">` + item.name + `</p>
//                      </div>
//                      <div class="contentratingsec">
//                         <p><i class="fa fa-star" aria-hidden="true"></i>` + item.ratings + `</p>
//                      </div>
//                </div></a>
//             </div>`;
//                   t++;
//                });
//             } else {

//                comicHtml += `<div class="col-12">
//                               <div class="imgwithtag genres"><img class="tagimg tagblankimg" src="{{asset('front/images/imgnotfound.png')}}"></div>
//                            </div>  `;

//             }
//          }

//          $("#comicMainDiv").append(comicHtml);


//       }
//    });
// }


// $('#categoriesName').slick({
//    dots: true,
//    arrow: true,
//    infinite: false,
//    speed: 300,
//    slidesToShow: 8,
//    slidesToScroll: 1,
//    //   variableWidth: true,
//    dots: false,
//    responsive: [{
//          breakpoint: 1024,
//          settings: {
//             slidesToShow: 3,
//             slidesToScroll: 3,
//             infinite: true,
//             dots: true
//          }
//       },
//       {
//          breakpoint: 600,
//          settings: {
//             slidesToShow: 2,
//             slidesToScroll: 2
//          }
//       },
//       {
//          breakpoint: 480,
//          settings: {
//             slidesToShow: 1,
//             slidesToScroll: 1
//          }
//       }
//       // You can unslick at a given breakpoint now by adding:
//       // settings: "unslick"
//       // instead of a settings object
//    ]
// });
</script>
@endsection