@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
<!-- <section class="container publishertop mt-50">
   <div class="row">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li>
            <li class="breadcrumb-item active" aria-current="page">About Us</li>
         </ol>
      </nav>
   </div>
</section> -->
<section class="aboutus bg-dark slidercalss aboutusrj">
   <div class="container">
      <div class="row p-10">
         <h1 class="searchhead">About Us</h1>
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
               <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
               <li class="breadcrumb-item active" aria-current="page">About Us</li>

            </ol>
         </nav>
      </div>
      <!-- <div class="row">
         <div class="col-12 text-white">
            <h1 >About US</h1>
         </div>
      </div> -->

      <div class="row text-white align-items-center mb-md-5 mb-3">
         <div class="col-md-12">
            <div class="col-img d-flex justify-content-center aboutimgsection">
               <img src="{{asset('front/images/rayan.jpg')}}" alt="">
            </div>
            <h5 class="d-flex justify-content-center mt-3">Thomas Ryan, Founder</h5>
         </div>
         <div class="col-md-12">
            <div class="col-text mt-5">

               <p><b>After my first comic production I was hooked. “I can actually tap into my creative side and produce work sustainably??!” It was a dream come true. I’ve tried several things in the past that either my heart really wasn’t into or that I only wanted to make “big money” from, but this was different. I remember saying to myself “Comics are for me”. I could take the ideas that I had reserved for super expensive animated series and video games and put them out in the world for people to enjoy all the same. This medium really made the impossible possible for an Army vet who just wanted to get my ideas out.</b> </p>
               <p><b>
                  Then came crowdfunding. I love the collaborative spirit that comes with it. You could put your dream out there and see it form right before your eyes. People who believe in you could get behind you, by showing support and helping get your book done. My first successful campaign was magic, there isn’t a feeling quite like it. A few years and a couple successful crowdfunding campaigns later, I began to seek more from my productions. I asked myself “Shouldn’t the goal be to get to a point where you don’t HAVE TO run crowdfunding campaigns?”
                  </b></p>
               <p><b>
                  After creating, crowdfunding, and fulfilling project after project, I was always left feeling like there was something missing. I'd tell my backers “See ya in a few months when I finish the next one!” but I wanted something more. I wanted to create a deeper relationship between my supporters and myself. I tried several platforms but none of them felt like they were built FOR me. None of them felt like they were BUILT FOR INDIE COMICS. That is when I decided to build the Vivid Panel platform.
                  </b></p>

               <p><b>
                  I wanted Vivid Panel to be a platform where the indie comic community could get paid each time their comic is read, and also exist as a platform where comic book fans could get an experience that they don't necessarily get on other apps. I wanted to build a site that is fun to use and where the stories never stop. At the end of the day I can say that I am proud of what we built here. My commitment is that for as long as I am around, my goals for this platform will always be founded on one simple question: What can I GIVE to the indie comic community?
                  </b></p>
               <p><b>
                  I hope you love what you read,
                  </b> </p>
               <p><b>
                  -Thomas Ryan </b></p>
            </div>
         </div>

      </div>

   </div>
</section>

@endsection
@section('scripts')

@endsection

