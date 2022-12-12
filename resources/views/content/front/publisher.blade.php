@extends('content.front.layouts.master')
@section('title','Home')
@section('content')

<section class="searchpage bg-dark slidercalss ">
   <div class="container">
         <div class="row p-10">
            <div class="col-6">
               <h1 class="searchhead">Publishers</h1>
            </div>
            <div class="col-6 text-end">
               @if(empty(Auth::User()->name))
               <a href="{{route('front-signup-view-publisher')}}" class="btn btn-primary me-2">Publish Your Comic</a>
               @endif
            </div>

         </div>
         <div class="row">
            <div class="navbtnsec">
               <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                     <li class="breadcrumb-item active" aria-current="page">Publisher</li>
                  </ol>
               </nav>
            </div>

         </div>
         <div class="row" id="appendPublisherData">

         </div>
   </div>
</section>

@endsection
@section('scripts')
<script>
      $(document).ready(function() {
            var minim = $('.headersearch input').val();
            $('.headersearch input').keyup(function() {
               if ($(this).val() > minim) {
                  $("body").css({"background-color": "#000", "opacity": "0.5"});
               }
               else {
                  $("body").css({"background-color": "#000", "opacity": "1"});
               }
            });
         });
      // $(".headersearch input").keyup(function(){
      //    $("body").css({"background-color": "#000", "opacity": "0.5"});
      //    });


   </script>
<script>
$(document).ready(function(){
   getPublisher(10,1);
});
function getPublisher(limit, page)
{
   $.ajax({
      'url':"{{route('get-publisher')}}",
      'type': "post",
      'dataType': "json",
      'data': {limit:limit,page:page,"_token": "{{ csrf_token() }}"},
      'success': function (response) {
         console.log(response);
         $("#appendPublisherData").empty();
         var html = '';
         if(response.status)
         {
            if(response.data.length > 0)
            {
               response.data.forEach(function(item, index){

                  html +=` <div class="item col-lg-2 col-md-2 col-sm-2 pubmaindivs">
                                    <div class="imgwithtag">
                                    <a href="{{ url('publisher-profile') }}/`+item.slug+`" >
                                    <img src="`+item.image+`" class="img-fluid circleImage" alt="avatar img">
                                       <div class="content-sec">
                                          <div class="contenttextsec w-100">
                                                <p>`+item.name+`</p>
                                          </div>
                                       </div>
                                       </a>
                                 </div>
                                 </div>
                              </div>`;

               });
            }
            else
            {
               html += `<div class="col-12">
                                    <div class="imgwithtag genres"><img class="tagimg tagblankimg" src="{{asset('front/images/imgnotfound.png')}}"></div>
                                 </div> `;
            }

            }
            $("#appendPublisherData").append(html);
            console.log(html)
      }
   });
}





 </script>
@endsection
