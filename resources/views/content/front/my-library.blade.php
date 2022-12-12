@extends('content.front.layouts.master')
@section('title','Home')
@section('content')

<section class="searchpage bg-dark slidercalss orderdetailpagemin p-10">
   <div class="container">

      <div class="row p-10">
         <div class="col-6">
            <h1 class="searchhead">My Library</h1>
            <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                     <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                     <li class="breadcrumb-item active" aria-current="page">My Library</li>

                  </ol>
               </nav>
         </div>
         <div class="col-6">
            <div class="sortsearchby d-none">

               <ul class="d-flex">
                  <li class="nav-item dropdown">
                     <a class="nav-link dropdown-toggle cat" href="#" data-bs-toggle="dropdown" aria-expanded="false">Select Category
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                     </a>
                     <ul class="dropdown-menu">
                        @foreach($cc as $c)
                        <li><a class="dropdown-item catName" href="#" catId="{{ $c->id }}">{{ $c->category_name }}</a></li>
                        @endforeach

                     </ul>
                  </li>
               </ul>
            </div>
         </div>
         <div class="row" id="comicMainDiv">

         </div>

      </div>
   </div>

</section>

@endsection
@section('scripts')
<script>
   $(document).ready(function() {
      getPageData(10, 1, 0);
   });

   $(document).on("click", ".catName", function() {
      var thisval = $(this).attr('catId');
      var cat = $(this).text() + '  <i class="fa fa-chevron-down" aria-hidden="true"></i>';
      $('.cat').html(cat);
      thisval = [thisval];
      getPageData(10, 1, thisval);
   });

   function getPageData(limit, page, catid) {
      $.ajax({
         'url': "{{route('get-library')}}",
         'type': "post",
         'dataType': "json",
         'data': {
            limit: limit,
            page: page,
            category: catid,
            "_token": "{{ csrf_token() }}"
         },
         'success': function(response) {
            //$(".sortsearchby").addClass("d-none");
            var comicHtml = '';
            if (response.status) {
               $("#comicMainDiv").empty();
               if (response.data.length > 0) {
                  //console.log(response.data.data);
                  var t = 1;
                  var cls = '';
                  response.data.forEach(function(item, index) {
                     var img = "";
                     var imgPath = "{{ env('IMAGE_PATH_large') }}" + item.featured_image;
                     var checkimg = item.access_type;
                     if (!response.userLoginCheck) {
                        if (checkimg == 1) {
                           img = '<img class="tagimg" src="{{asset("front/images/free.png")}}">';
                        } else {
                           img = '<img class="tagimg " src="{{asset("front/images/paid.png")}}">';
                        }
                     } else {
                        if (response.ud != '' && response.ud.user_type != 2) {
                           if (checkimg == 1) {
                              img = '<img class="tagimg" src="{{asset("front/images/free.png")}}">';
                           } else {
                              img = '<img class="tagimg" src="{{asset("front/images/paid.png")}}">';
                           }
                        }
                     }

                     if (t > 6)
                        cls = 'mt-3';

                     comicHtml += `<div class="col-lg-2 col-md-4 col-sm-4 col-6 mb-2` + cls + `">
                  <div class="deleteLibSection"> <i class="fa fa-trash deleteLibrary deleteLibBtn" aria-hidden="true" comicId="` + item.id + `"></i></div>
               <a href ="{{url('comic-detail')}}/` + item.slug + `">
                  <div class="imgwithtag contenthoveimage">
                   <div class="content-overlayimage"></div>
                     <img class="f_img" src="` + imgPath + `">
                     ` + img + `
                     <div class="content-details fadeIn-top">

                        <p> `+item.description.slice(0,50) + `...</p>
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
           </div>`;
                     t++;
                  });

                  $(".sortsearchby").removeClass("d-none");
               } else {
                  comicHtml += ` <div class="col-12">
                                 <div class="imgwithtag genres"><img class="tagimg tagblankimg" src="{{asset('front/images/library.png')}}"></div>
                              </div>  `;
               }
            }
            $("#comicMainDiv").append(comicHtml);
         }
      });
   }

   $(document).on("click", ".deleteLibBtn", function() {
      var e = $(this);
      var comicId = e.attr("comicId")
      $.ajax({
         'url': "{{route('delete-library')}}",
         'type': "post",
         'dataType': "json",
         'data': {
            "comic_id": comicId,
            "_token": "{{ csrf_token() }}"
         },
         'success': function(response) {
            if (response.status) {

               toastr["success"](response.message, "Success");
               e.parent().parent().remove();
            } else {
               toastr["error"](response.message, "Error");
            }
         }
      });
   });
</script>
@endsection