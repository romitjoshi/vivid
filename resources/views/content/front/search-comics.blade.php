@extends('content.front.layouts.master')
@section('title','Home')
@section('content')


<section class="searchpage bg-dark slidercalss searchpage searchtagpmm" id="searchpage">
   <div class="container">
      <div class="row p-10">
         <div class="col-md-6">
            <h1 class="searchhead">Search Result For {{$searchTerm}} </h1>
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                  <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                  <li class="breadcrumb-item active" aria-current="page">Search</li>

               </ol>
            </nav>
         </div>
         <div class="col-md-6">
            <div class="sortsearchby hideShoeCat @if(empty($comicList)) d-none @endif">
               <ul class="d-flex">
                  <li class="nav-item dropdown">
                     <a class="nav-link dropdown-toggle cat" href="#" data-bs-toggle="dropdown" aria-expanded="false">Select Category
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                     </a>
                     <ul class="dropdown-menu">
                        @foreach($cc as $c)
                        <li><a class="dropdown-item catList" href="#" catId="{{ $c->id }}">{{ $c->category_name }}</a></li>
                        @endforeach
                     </ul>
                  </li>
                  <li class="nav-item dropdown">
                     <a class="nav-link dropdown-toggle sort" href="#" data-bs-toggle="dropdown" aria-expanded="false">Sort By
                        <i class="fa fa-chevron-down " aria-hidden="true"></i>
                     </a>
                     <ul class="dropdown-menu">
                        <li><a class="dropdown-item sortLink" href="#" val="DESC">A-Z</a></li>
                        <li><a class="dropdown-item sortLink" href="#" val="ASC">Z-A</a></li>

                     </ul>
                  </li>
               </ul>
            </div>
         </div>

         <div class="row" id="comicMainDiv">
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
                     <img class="tagimg" src="{{asset('front/images/free.png')}}" />
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
   </div>
</section>
<input type="hidden" id="catids" value="">
@endsection
@section('scripts')
<script>
   $(document).on("click", ".catList", function() {
      var cat_id = $(this).attr('catId');
      var cat = $(this).text() + '  <i class="fa fa-chevron-down" aria-hidden="true"></i>';
      $('.cat').html(cat);
      $("#catids").val(cat_id)
      var serach_text = "{{$searchTerm}}";
      getallComic(10, 1, cat_id, serach_text, "");
   });

   $(document).on("click", ".sortLink", function(e) {
      var sort_by = $(this).attr('val');
      var cat = $(this).text() + '  <i class="fa fa-chevron-down" aria-hidden="true"></i>';
      $('.sort').html(cat);
      var cat_id = $("#catids").val();
      var serach_text = "{{$searchTerm}}";

      getallComic(12, 1, cat_id, serach_text, sort_by);
   });

   function getallComic(limit, page, category, serach_text, sort_by) {
      $.ajax({
         'url': "{{route('get-comic')}}",
         'type': "post",
         'dataType': "json",
         'data': {
            limit: limit,
            page: page,
            serach_text: serach_text,
            category: category,
            sort_by: sort_by,
            "_token": "{{ csrf_token() }}"
         },
         'success': function(response) {
            console.log(response);
            $("#comicMainDiv").empty();
            var comicHtml = '';
            if (response.status) {
               //$(".hideShoeCat").addClass("d-none");
               if (response.data.length > 0) {
                  var t = 1;
                  var cls = '';
                  response.data.forEach(function(item, index) {
                     var img = "";
                     var imgPath = "{{ env('IMAGE_PATH_medium') }}" + item.featured_image;
                     var checkimg = item.access_type;
                     if (!response.userLoginCheck) {
                        if (checkimg == 1) {
                           img = '<img class="tagimg" src="{{asset("front/images/free.png")}}"> ';
                        } else if (checkimg == 2) {
                           img = '<img class="tagimg" src="{{asset("front/images/paid.png")}}"> ';
                        } else {
                           img = '<img class="tagimg" src="{{asset("front/images/orignal.png")}}"> ';
                        }
                     } else {
                        if (response.ud != '' && response.ud.user_type != 2) {
                           if (checkimg == 1) {
                              img = '<img class="tagimg" src="{{asset("front/images/free.png")}}"> ';
                           } else if (checkimg == 2) {
                              img = '<img class="tagimg" src="{{asset("front/images/paid.png")}}"> ';
                           } else {
                              img = '<img class="tagimg" src="{{asset("front/images/orignal.png")}}"> ';
                           }
                        }
                     }
                     if (t > 6)
                        cls = 'mt-3';
                     comicHtml += `<div class="item col-lg-2 col-md-4 col-sm-4 ` + cls + `">
               <a href ="{{url('comic-detail')}}/` + item.id + `">
                  <div class="imgwithtag">
                   <img class="f_img smallImgSize" src="` + imgPath + `">
                    ` + img + `
                  </div>
                 <div class="content-sec">
                     <div class="contenttextsec">
                           <p class="line-clamp line-clamp-1">` + item.name + `</p>
                     </div>
                     <div class="contentratingsec">
                        <p><i class="fa fa-star" aria-hidden="true"></i>` + item.ratings + `</p>
                     </div>
                 </div></a>
             </div>`;
                     t++;
                  });

                  $(".hideShoeCat").removeClass("d-none");
               } else {
                  comicHtml += ` <div class="col-12">
                                 <div class="imgwithtag genres"><img class="tagimg tagblankimg" src="{{asset('front/images/imgnotfound.png')}}"></div>
                              </div>  `;
               }

            }
            $("#comicMainDiv").append(comicHtml);
         }
      });
   }
</script>
@endsection