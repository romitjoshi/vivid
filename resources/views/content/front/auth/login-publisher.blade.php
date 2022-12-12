@extends('content.front.layouts.master')
@section('title','Login')
@section('content')
<style>

</style>
<section class="formreglogin bg-dark">
   <div class="container">
      <div class="row">
         <div class="formcard">
            <img src="{{asset('front/images/vivid-logo.svg')}}"/>
            @if(Session::has('error') && !empty(Session::get('error'))) <p class="error-tag text-center mt-2 invalidmsgfnt">{{ Session::get('error') }}</p> @endif
               <form autocomplete="off" method="POST" action="{{url('/loginFrontPublisher')}}">
               @csrf
                  <h3>Log In Your Account</h3>
                  <div class="input-group mb-3">
                     <span class="input-group-text">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                     </span>
                     <input  autocomplete="false" type="email" class="form-control" placeholder="Email" name="email" />
                  </div>
                  @if(Session::has('errors') && !empty(Session::get('errors')->first('email'))) <p class="error-tag">{{ Session::get('errors')->first('email') }}</p> @endif
                  <div class="input-group mb-3">
                     <span class="input-group-text" id="input-group-left-example">
                        <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                     </span>
                     <input type="password" class="form-control" placeholder="Password" name="password"/>
                     <span class="input-group-text" id="input-group-left-example">
                        <i class="fa fa-eye eyeHide" aria-hidden="true" vlu="show"></i>
                        <i class="fa fa-eye-slash eyeHide d-none" aria-hidden="true" vlu="hide"></i>
                     </span>
                  </div>
                  @if(Session::has('errors') && !empty(Session::get('errors')->first('password'))) <p class="error-tag">{{ Session::get('errors')->first('password') }}</p> @endif
                  <div class="f-pass">
                     <a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModel" >
                        <small>Forgot Password?</small>
                     </a>
                  </div>

                  <button class="btn btn-primary w-100">Login</button>
               </form><br>

               <p class="text-center mt-2 bottlogin">
                  <span>New on our platform?</span>
                     <a href="{{ route('front-signup-view-publisher') }}">
                        <span><b>Register</b></span>
                     </a>Here
               </p>

         </div>
      </div>
   </div>
</section>
<!----Forgot Password Start--->
<div class="modal fade" id="resetPasswordModel">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <form method="post"  class="emailSendPasswordForm">
            <div class="modal-header text-center">
               <h4 class="modal-title w-100">Forgot Password</h4>
               <img class="cursor-pointer" data-bs-dismiss="modal" src="{{ asset('front/images/close.svg')}} " width="25"/>
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
        errorPlacement:function(error,element){
                element.parent().after(error)
                element.parent().removeClass("mb-3")
            },
        submitHandler: function (form) {
            var thisForm = $(this);
            $(".updatepassword").find('.importloader').removeClass('d-none');

            var email = $("#emailReset").val();
            $.ajax({
                'url':"{{route('send-email-forgot-password')}}",
                'type': "POST",
                'dataType': "json",
                'data': {"email":email,"_token": "{{ csrf_token() }}"},

                'success': function (response) {
                    if(response.status)
                    {
                        $("#emailReset").val('')
                        $("#resetPasswordModel").modal("toggle");
                        toastr["success"](response.message, "Success");

                    }
                    else
                    {
                        toastr["error"](response.message, "Error");
                    }
                },
                'error': function (error) {
                    console.log(error);
                },
                'complete':function(){
                    $(".updateprofileSubBtn2").find('.importloader').addClass('d-none');
                }
            });
        },
    });
</script>

@endsection