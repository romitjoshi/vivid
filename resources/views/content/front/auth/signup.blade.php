@extends('content.front.layouts.master')
@section('title','Login')
@section('content')
<!-- style="background-image: url('img_girl.jpg');">
style="background-image:public/front/images/detail-page.jpg" -->
<section class="formreglogin bg-dark loginpage searchpage" style="background-image:url('front/images/loginpop.png')">
   <div class="container searchpage">
      <div class="row">
         <div class="formcard formcardpop">
            <img class="logmiddle" src="{{asset('front/images/vivid-logo.svg')}}" />
            <form method="POST" action="{{ route('front-signup-action') }}">
               @csrf
               <h3>Create Your Account</h3>
               <div class="input-group mb-3">
                  <span class="input-group-text">
                     <i class="fa fa-user" aria-hidden="true"></i>
                  </span>
                  <input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name') }}" />
               </div>
               @if(Session::has('errors') && !empty(Session::get('errors')->first('name')))
               <p class="error-tag">{{ Session::get('errors')->first('name') }}</p>
               @endif
               <div class="input-group mb-3">
                  <span class="input-group-text">
                     <i class="fa fa-envelope" aria-hidden="true"></i>
                  </span>
                  <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" />
               </div>
               @if(Session::has('errors') && !empty(Session::get('errors')->first('email')))
               <p class="error-tag">{{ Session::get('errors')->first('email') }}</p>
               @endif
               <div class="input-group mb-3">
                  <span class="input-group-text" id="input-group-left-example">
                     <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                  </span>
                  <input type="password" class="form-control" placeholder="Password" name="password" />
                  <span class="input-group-text" id="input-group-left-example">
                     <i class="fa fa-eye-slash eyeHide" aria-hidden="true" vlu="show"></i>
                     <i class="fa fa-eye eyeHide d-none" aria-hidden="true" vlu="hide"></i>
                  </span>
               </div>
               <div class="passworderror"><span>Password must be at least 8 characters including, Upper case letter, 1 Number, and 1 Special Character</span></div>
               @if(Session::has('errors') && !empty(Session::get('errors')->first('password'))) <p class="error-tag">{{ Session::get('errors')->first('password') }}</p> @endif
               <div class="input-group mb-3">
                  <span class="input-group-text">
                     <i class="fa fa-calendar" aria-hidden="true"></i>
                  </span>
                  <input type="text" class="form-control" id="d_o_b" placeholder="Date of Birth" name="dob" value="{{ old('dob') }}">
               </div>
               @if(Session::has('errors') && !empty(Session::get('errors')->first('dob')))
               <p class="error-tag">{{ Session::get('errors')->first('dob') }}</p>
               @endif

               <!-- <div class="input-group date">
                  <span class="input-group-text">
                     <i class="fa fa-bullhorn" aria-hidden="true"></i>
                  </span>
                  @php

                  $referCode = "";
                  if(isset($_COOKIE['virefvier'])) {
                  $referCode = $_COOKIE['virefvier'];
                  }

                  @endphp
                  <input type="text" class="form-control" placeholder="Refer Code" name="refer_code" value="{{ $referCode }}">
               </div>
               @if(Session::has('errors') && !empty(Session::get('errors')->first('refer_code')))
               <p class="error-tag">{{ Session::get('errors')->first('refer_code') }}</p>
               @endif -->


               <button class="btn btn-primary w-100">Sign Up</button>
            </form>
            <div class="socialSection">
               <p>or login with</p>
               <div class="imgSocaal">
                  <a href="{{route('google-auth')}}">
                     <img width="30" src="{{ asset('front/images/google.png') }}">
                  </a>
                  <a href="{{route('facebook-auth')}}">
                     <img width="30" src="{{ asset('front/images/facebook.png') }}">
                  </a>
               </div>
            </div>
            <p class="text-center mt-2 bottlogin">
               <span>Already have an account?</span>
               <a href="{{ route('front-login-view') }}">
                  <span>
                     <b>Sign In</b>
                  </span>
               </a>instead
            </p>
         </div>
      </div>
   </div>
</section>

@endsection
@section('scripts')
<script>
   $(function() {
      $('#d_o_b').flatpickr({
         maxDate: new Date()
      });
   });
</script>
@endsection