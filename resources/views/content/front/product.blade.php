@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
<style>
.contenttextsec {width: 100% !important;}
</style>
     <section class="searchpage bg-dark slidercalss">

        <!--  <div class="container">
           <div class="row"id="productMainDiv">
              <h1 class="searchhead">Store </h1>


             <div class="item col-lg-3 col-md-3 col-sm-3">
                  <div class="imgwithtag">
                     <img class="f_img roundedten" src="images/next-episodes.jpg">
                 <div class="content-sec">
                     <div class="contenttextsec w-100">
                        <p>Thor Love and thunder</p>
                        <p>$25</p>
                     </div>

                 </div>
               </div>
             </div>


            </div>
         </div> -->
         <div class="container">
         <div class="row p-10">
            <h1 class="searchhead">Store</h1>
            <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
            <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
            <li class="breadcrumb-item active" aria-current="page">Store</li>

         </ol>
      </nav>
         </div>
           <div class="row" id="productMainDiv">



           </div>
         </div>
     </section>


@endsection
@section('scripts')
<script>
$(document).ready(function(){
   getProduct(30,1,0);
});
function getProduct(limit, page)
{
   $.ajax({
      'url':"{{route('get-product')}}",
      'type': "post",
      'dataType': "json",
      'data': {limit:limit,page:page,"_token": "{{ csrf_token() }}"},
      'success': function (response) {
         console.log(response);
         $("#productMainDiv").empty();

         var comicHtml = '';
         if(response.status)
         {
            if(response.data.length > 0)
            {
            var t = 1;
            var cls = '';

            response.data.forEach(function(item, index){
               var is_special = 'd-none';
               if(item.is_special > 0)
               {
                  is_special = '';
               }
               var imgPath =  "{{ env('IMAGE_PATH_medium') }}"+item.featured_Image;
               if(t > 6)
               cls = 'mt-3';
               comicHtml +=`<div class="item col-lg-3 col-md-3 col-sm-3">

                  <div class="imgwithtag productImg">
                   <a href="{{url('product-details')}}/`+item.slug+`"><img class="f_img roundedten productImgSize" src="`+imgPath+`"></a>

                 <div class="content-sec">
                     <div class="contenttextsec w-100">
                        <p class="line-clamp line-clamp-1">`+item.product_name+`</p>
                        <p> `+item.special_price+` <span  class="productfontcolor `+cls+`  `+is_special+`">`+item.price+`</span></p>

                     </div>

                 </div>
               </div>
             </div>`;
             t++;
            });
         }
         else
         {
            comicHtml += ` <div class="col-12">
                                 <div class="imgwithtag genres"><img class="tagimg tagblankimg" src="{{asset('front/images/imgnotfound.png')}}"></div>
                              </div> `;
         }

         //   $("#productMainDiv").append(comicHtml);
         }
         $("#productMainDiv").append(comicHtml);
      }
   });
}





 </script>

@endsection