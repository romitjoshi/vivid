@extends('content.front.layouts.master')
@section('title','Home')
@section('content')


<section class="searchpage bg-dark slidercalss">
   <div class="container">
      <div class="row p-10">
         <h1 class="searchhead">Recently Added </h1>
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
               <!-- <li class="breadcrumb-item"><a href="{{route('my-order')}}">My Order</a></li> -->
               <li class="breadcrumb-item active" aria-current="page">Recently Added </li>
            </ol>
         </nav>
         <!-- <h1 class="searchhead">Serach Result For Spiderhead </h1> -->
         <div class="sortsearchby">
            <ul class="d-flex">
               <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle cat" href="#" data-bs-toggle="dropdown" aria-expanded="false">Select Category
                     <i class="fa fa-chevron-down" aria-hidden="true"></i>
                  </a>
                  <ul class="dropdown-menu">
                     @foreach($category as $value)
                     <li><a class="dropdown-item catList" href="#" catId="{{ $value->id }}">{{ $value->category_name }}</a></li>
                     @endforeach
                  </ul>
               </li>
               <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle sort" href="#" data-bs-toggle="dropdown" aria-expanded="false">Sort By
                     <i class="fa fa-chevron-down" aria-hidden="true"></i>
                  </a>
                  <ul class="dropdown-menu">
                     <li><a class="dropdown-item sortLink" href="#" val="DESC">A-Z</a></li>
                     <li><a class="dropdown-item sortLink" href="#" val="ASC">Z-A</a></li>

                  </ul>
               </li>
            </ul>
         </div>
         <div class="row" id="comicMainDiv">

         </div>

      </div>
   </div>

</section>
<input type="hidden" id="catids" value="">
@endsection
@section('scripts')
<script type="text/javascript">
   $(document).ready(function() {
      getComic(12, 1, 0, "");
   });

   $(document).on("click", ".catList", function() {
      var cat_id = $(this).attr('catId');
      var cat = $(this).text() + '  <i class="fa fa-chevron-down" aria-hidden="true"></i>';
      $('.cat').html(cat);
      $("#catids").val(cat_id)
      getComic(12, 1, cat_id, "");
   });

   $(document).on("click", ".sortLink", function(e) {
      var sort_by = $(this).attr('val');
      var cat = $(this).text() + '  <i class="fa fa-chevron-down" aria-hidden="true"></i>';
      $('.sort').html(cat);
      var cat_id = $("#catids").val();

      getComic(12, 1, cat_id, sort_by);
   });

   function getComic(limit, page, category, sort_by) {
      $.ajax({
         'url': "{{route('get-comic')}}",
         'type': "post",
         'dataType': "json",
         'data': {
            limit: limit,
            page: page,
            category: category,
            sort_by: sort_by,
            "_token": "{{ csrf_token() }}"
         },
         'success': function(response) {
            $("#comicMainDiv").empty();
            var comicHtml = '';
            if (response.status) {
               if (response.data.length > 0) {
                  var t = 1;
                  var cls = '';
                  response.data.forEach(function(item, index) {
                     var img = "";
                     var imgPath = "{{ env('IMAGE_PATH_medium') }}" + item.featured_image;
                     var checkimg = item.access_type;
                     if (!response.userLoginCheck) {
                        if(checkimg == 1) {
                           img = '<img class="tagimg" src="{{asset("front/images/free.png")}}"></a>';
                        } else if (checkimg == 2) {
                           img = '<img class="tagimg " src="{{asset("front/images/paid.png")}}"></a>';
                        } else {
                           img = '<img class="tagimg " src="{{asset("front/images/orignal.png")}}"></a>';
                        }
                     } else {
                        if (response.ud != '' && response.ud.user_type != 2) {
                           if (checkimg == 1) {
                              img = '<img class="tagimg" src="{{asset("front/images/free.png")}}"></a>';
                           } else if (checkimg == 2) {
                              img = '<img class="tagimg " src="{{asset("front/images/paid.png")}}"></a>';
                           } else {
                              img = '<img class="tagimg" src="{{asset("front/images/orignal.png")}}"></a>';
                           }
                        }
                     }
                     if (t > 6)
                        cls = 'mt-3';

                     comicHtml += `<div class="seeallstyle comicGrid col-lg-2 col-md-4 col-sm-4 ` + cls + `">
               <a href ="{{url('comic-detail')}}/` + item.slug + `">
                  <div class="imgwithtag contenthoveimage">
                   <div class="content-overlayimage"></div>

                    <img class="f_img smallImgSize" src="` + imgPath + `">
                    ` + img + `
                    <div class="content-details fadeIn-top">
                        <p ">` + item.description.slice(0, 50) + `</p>
                      </div>
                  </div>
                 <div class="content-sec">
                     <div class="contenttextsec">
                           <p class="line-clamp line-clamp-1">` + item.name + `</p>
                     </div>
                     <div class="contentratingsec">
                        <p><i class="fa fa-star" aria-hidden="true"></i>` + item.ratings + `</p>
                     </div>

                 </div>
                 </a>
             </div> `;
                     t++;
                  });
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

   // var page = 1;
   // $(window).scroll(function() {
   //     if($(window).scrollTop() == $(document).height() - $(window).height()) {

   //       getComic(12,page,0,"");
   //       page++;
   //     }
   // });
</script>



@endsection