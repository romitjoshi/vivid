@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
@php

@endphp

<section class="container publishertop">
   <div class="row">
   <h1 class="searchhead">Publisher Details</h1>
   <nav aria-label="breadcrumb">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
                  <li class="breadcrumb-item"><a href="{{route('publisher')}}">Publishers</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Publisher Details</li>

               </ol>
            </nav>
   </div>
</section>
<section class="container pubtopsec">
   <div class="row">
      <div class="col-md-2 publisher-profile">
         <img src="{{ $data['publisher']['image'] ?? ''}}" class="img-fluid circleImage" alt="avatar img">
      </div>
      <div class="col-md-10 publisher-desc">
         <h2>{{ $data['publisher']['name'] ?? ''}}</h2>
         <p>{{$data['publisher']['about'] ?? ''}} </p>
      </div>
   </div>
</section>
<section class="searchpage bg-dark slidercalss publishpagelslider pubpropage">
   <div class="container mb-5 marginbtm">
      <div class="row mb-5 mt-5">
         <h1 class="searchhead">Comics by {{$data['publisher']['name'] ?? ''}}</h1>
         @foreach($data['comic'] as $comicshow)
         <div class="item col-lg-2 col-md-4 col-sm-4">
            <a href="{{url('comic-detail')}}/{{$comicshow->slug}}">
               <div class="imgwithtag contenthoveimage">
               <div class="content-overlayimage"></div>
                  @if(empty($userLoginCheck))
                  @if($comicshow->access_type=="1")

                  <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                  @else
                  <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                  @endif
                  @else
                  @if(!empty($ud) && $ud->user_type != 2)
                  @if($comicshow->access_type=="1")
                  <img class="tagimg" src="{{asset('front/images/free.png')}}" />
                  @else
                  <img class="tagimg" src="{{asset('front/images/paid.png')}}" />
                  @endif
                  @endif
                  @endif

                  <div class="content-details fadeIn-top">
                  <p>{{Str::words($comicshow->description,15)}}</p>
                      </div>
                  <img class="f_img smallImgSize" src="{{ env('IMAGE_PATH_orignal').$comicshow->featured_image ?? '' }}">
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
         @endforeach;
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

      // $(".headersearch input").keyup(function(){
      //    $("body").css({"background-color": "#000", "opacity": "0.5"});
      //    });
      </script>
@endsection