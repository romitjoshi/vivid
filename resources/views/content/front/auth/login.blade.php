@extends('content.front.layouts.master')
@section('title','Login')
@section('content')

<section class="formreglogin bg-dark   loginpage searchpage " style="background-image:url('front/images/loginpop.png');">
   <div class="container searchpage">
      <div class="row">
         <div class="formcard formcardpop">
            <img class="logmiddle" src="{{asset('front/images/vivid-logo.svg')}}" />
            @if(Session::has('error') && !empty(Session::get('error'))) <div class="error-tag text-center mt-2 invalidmsgfnt">{!! Session::get('error') !!}</div> @endif
            @if(Session::has('errors') && !empty(Session::get('errors')->first('email'))) <p class="error-tag text-center mt-2 invalidmsgfnt">{!! Session::get('errors')->first('email') !!}</p> @endif
            @if(Session::has('errors') && !empty(Session::get('errors')->first('password'))) <p class="error-tag text-center mt-2 invalidmsgfnt">{!! Session::get('errors')->first('password') !!}</p> @endif
            <form autocomplete="off" method="POST" action="{{url('/loginFront')}}">
               @csrf
               <h3>Log In Your Account</h3>
               <div class="input-group mb-3">
                  <span class="input-group-text">
                     <i class="fa fa-envelope" aria-hidden="true"></i>
                  </span>
                  <input autocomplete="off" type="email" class="form-control" placeholder="Email" name="email" />
               </div>

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

               <div class="f-pass">
                  <a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModel">
                     <small>Forgot Password?</small>
                  </a>
               </div>

               <button class="btn btn-primary w-100">Login</button>
            </form>
            <div class="socialSection ">
               <p>or login with</p>
               <div class="imgSocaal">
                  <a href="{{route('google-auth')}}"><img width="30" src="{{ asset('front/images/google.png') }}"></a>
                  <a href="{{route('facebook-auth')}}"><img width="30" src="{{ asset('front/images/facebook.png') }}"></a>
               </div>
            </div>
            <p class="text-center mt-2 bottlogin">
               <span>New to our platform?</span>
               <a href="{{ route('front-signup-view') }}">
                  <span><b>Sign Up Here</b></span>
               </a>
            </p>
         </div>
      </div>
   </div>
</section>
<!----Forgot Password Start--->
<div class="modal fade" id="resetPasswordModel">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <form method="post" class="emailSendPasswordForm">
            <div class="modal-header text-center">
               <h4 class="modal-title w-100">Forgot Password</h4>
               <img class="cursor-pointer" data-bs-dismiss="modal" src="{{ asset('front/images/close.svg')}} " width="25" />
            </div>
            <div class="modal-body text-center">
               @csrf
               <div class="input-group mb-4">
                  <span class="input-group-text">
                     <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                  </span>
                  <input type="email" class="eye-field form-control" placeholder="Enter your email" name="email" id="emailReset">
               </div>

            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary data-submit me-1 updateprofileSubBtn updateprofileSubBtn2">Submit</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!----Forgot Password End-->

@endsection
@section('scripts')
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script>
   // $(document).on("click", ".eyeHide", function(){
   //    var thi = $(this).attr("vlu");
   //    $(".eyeHide").removeClass("d-none")
   //    $(this).addClass("d-none")

   //    if(thi == "show")
   //    $("input[name='password']").prop("type", "text");
   //    else
   //    $("input[name='password']").prop("type", "password");
   // });
   $(".emailSendPasswordForm").validate({
      rules: {
         email: {
            required: true,
            email: true,
         }
      },
      errorPlacement: function(error, element) {
         element.parent().after(error)
         element.parent().removeClass("mb-3")
      },
      submitHandler: function(form) {
         var thisForm = $(this);
         $(".updatepassword").find('.importloader').removeClass('d-none');

         var email = $("#emailReset").val();
         $.ajax({
            'url': "{{route('send-email-forgot-password')}}",
            'type': "POST",
            'dataType': "json",
            'data': {
               "email": email,
               "_token": "{{ csrf_token() }}"
            },

            'success': function(response) {
               if (response.status) {
                  $("#emailReset").val('')
                  $("#resetPasswordModel").modal("toggle");
                  toastr["success"](response.message, "Success");

               } else {
                  toastr["error"](response.message, "Error");
               }
            },
            'error': function(error) {
               console.log(error);
            },
            'complete': function() {
               $(".updateprofileSubBtn2").find('.importloader').addClass('d-none');
            }
         });
      },
   });
</script>

@endsection