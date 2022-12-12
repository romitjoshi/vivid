@extends('content.front.layouts.master')
@section('title','Login')
@section('content')
<section class="formreglogin bg-dark searchpage">
   <div class="container">
      <div class="row">
         <div class="col-md-5 text-center">
            <img class="logmiddle centerlogo" src="{{asset('front/images/vivid-logo.svg')}}" />
            <p class="text-white mt-3">
            Long nights, countless hours, and YEARS spent perfecting your craft is what the foundation of your comic book journey is built on, yet there isn’t a platform that exists FOR you; that is, until now. Vivid Panel is a Pay per view, indie comic book platform made by creators for creators. You’ve made the comics, now make a living.
            </p>
            <a class="btn btn-primary mt-3 mb-5" href="{{ asset('/front/docs/PUBLISHER_PORTAL_.pdf') }}" target="_blank">Learn more</a>

         </div>
         <div class="col-md-7">
            <div class="formcard formcardpubrj">

               <form method="POST" action="{{ route('front-signup-publisher') }}">
                  @csrf
                  @if(Session::has('message'))
                  <p class="alert alert-info ">{!! Session::get('message') !!}</p>
                  @endif
                  <h3>Create your Publisher account</h3>
                  <div class="input-group mb-3">
                     <span class="input-group-text">
                        <i class="fa fa-user" aria-hidden="true"></i>
                     </span>
                     <input type="text" class="form-control" placeholder="Name/Business Name" name="name" value="{{ old('name') }}" />
                  </div>
                  @if(Session::has('errors') && !empty(Session::get('errors')->first('name'))) <p class="error-tag">{{ Session::get('errors')->first('name') }}</p> @endif
                  <div class="input-group mb-3">
                     <span class="input-group-text">
                        <i class="fa fa-briefcase" aria-hidden="true"></i>
                     </span>
                     <select class="form-control soption" placeholder="Select Business Type" name="businesstype" id="businesstype">
                        <option selected disabled>Select Business Type</option>
                        <option value="LLC">LLC</option>
                        <option value="Partnership">Partnership</option>
                        <option value="Sole Proprietorship">Sole Proprietorship</option>
                        <option value="Others">Others</option>
                     </select>

                  </div>
                  @if(Session::has('errors') && !empty(Session::get('errors')->first('businesstype'))) <p class="error-tag">{{ Session::get('errors')->first('businesstype') }}</p> @endif


                  <div class="input-group mb-3">
                     <span class="input-group-text">
                        <i class="fa fa-address-book" aria-hidden="true"></i>
                     </span>
                     <input type="text" class="form-control" placeholder="Address/Business Address" name="address" value="{{ old('address') }}" />
                  </div>
                  @if(Session::has('errors') && !empty(Session::get('errors')->first('address'))) <p class="error-tag">{{ Session::get('errors')->first('address') }}</p> @endif

                  <div class="input-group mb-3">
                     <span class="input-group-text">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                     </span>
                     <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" />
                  </div>
                  @if(Session::has('errors') && !empty(Session::get('errors')->first('email'))) <p class="error-tag">{{ Session::get('errors')->first('email') }}</p> @endif



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

                  <div class="input-group date mb-3">
                     <span class="input-group-text" id="input-group-left-example">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                     </span>
                     <span class="mobilenopubisher">+1</span>
                     <input type="text" class="form-control dt-full-name preventCharacters" style="padding-left:30px" id="Phonenumber" placeholder="Phone Number" name="Phonenumber" value="{{ old('Phonenumber') }}" maxlength="10">
                  </div>
                  @if(Session::has('errors') && !empty(Session::get('errors')->first('Phonenumber'))) <p class="error-tag">{{ Session::get('errors')->first('Phonenumber') }}</p> @endif


                  <!-- <div class="input-group mb-3">
                                    <span class="input-group-text">
                                       <i class="fa fa-user" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" class="form-control preventCharacters" placeholder="EIN/Social Security Number" name="ein" value="{{ old('ein') }}"maxlength="9"/>
                                 </div>
                                 @if(Session::has('errors') && !empty(Session::get('errors')->first('ein'))) <p class="error-tag">{{ Session::get('errors')->first('ein') }}</p> @endif -->



                  <button class="btn btn-primary w-100 ">Sign Up</button>
               </form><br>

               <p class="text-center mt-2 bottlogin">
                  <span>Already have an account?</span>
                  <a href="{{ route('front-login-view') }}">
                     <span><b>Sign In</b></span>
                  </a>instead
               </p>
            </div>
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