@extends('content.front.layouts.master')
@section('title','My Account')
@section('content')
<style>
    p{
        color: #fff !important;
    }
    label,h5,h4 {
    color: #fff;
}
.account-settings .user-profile {
    margin: 0 0 1rem 0;
    padding-bottom: 1rem;
    text-align: center;
}
.account-settings .user-profile .user-avatar {
    margin: 0 0 1rem 0;
}
.account-settings .user-profile .user-avatar img {
    width: 90px;
    height: 90px;
    -webkit-border-radius: 100px;
    -moz-border-radius: 100px;
    border-radius: 100px;
    object-fit: cover;
}
.account-settings .user-profile h5.user-name {
    margin: 0 0 0.5rem 0;
}
.account-settings .user-profile h6.user-email {
    margin: 0;
    font-size: 0.8rem;
    font-weight: 400;
    color: #fff;
}
.account-settings .about {
    margin: 2rem 0 0 0;
    text-align: center;
}
.account-settings .about h5 {
    margin: 0 0 15px 0;
    color: #fff;
}
.account-settings .about p {
    font-size: 0.825rem;
}
.form-control {
    border: 1px solid #cfd1d8;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    font-size: .825rem;
    background: #ffffff;
    color: #fff;
    background-color: #161616;
}

.card {
    background: #161616;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    border: 0;
    margin-bottom: 1rem;
}
label.error {
    top: 0px;
}
option {
    color: #fff !important;
}
</style>
@if(!empty($userData->getUserDetails->refer_code))

<!-- <div class="refersec text-white d-flex justify-content-between">
    <div class="copy-link" id="myItem" data-page_id="{{ $userData->getUserDetails->refer_code }}">Copy Link</div>
    &nbsp;&nbsp;
    <h5>Refer Code : {{ $userData->getUserDetails->refer_code }}</h5>
</div> -->
@endif
<!-- <section class="container publishertop mt-50">
   <div class="row">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
             <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Account</li>

         </ol>
      </nav>
   </div>
</section> -->
<section class="searchpage bg-dark slidercalss ">
<div class="container">
<div class="row gutters p-10">
<h3  >My Account</h3>
<nav aria-label="breadcrumb">
         <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
             <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
            <li class="breadcrumb-item active" aria-current="page">My Account</li>
         </ol>
      </nav>
<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
<div class="card h-100">
	<div class="card-body">
		<div class="account-settings">
			<div class="user-profile">
				<div class="user-avatar">
					<!-- <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Maxwell Admin"> -->
                    @if(!empty($userData->image))
                           <img src="{{ env('IMAGE_PATH_large').$userData->image }}"   alt="avatar img" >
                           @else
                           <img src="{{ asset('images/avatars/userdummy.png') }}" alt="avatar img" >
                           @endif
				</div>
				<h5 class="user-name">{{ $userData->name ?? ''}}  </h5>
				<h6 class="user-email">{{ $userData->email ?? ''}} </h6>
			</div>
			<div class="about">
				<h5>About</h5>
				<p>{{$userData->getUserDetails->about ?? ''}}</p>
			</div>
		</div>
	</div>
</div>
</div>
<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
<div class="card h-100">
	<div class="card-body">
        <form action="{{route('publisher-update-profile')}}" method="post" class="updateProfileForm" novalidate="novalidates">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-3 d-flex justify-content-between">
                    <h5 class="mb-2 text-primary text-white"><strong>Personal Details</strong></h5>
                    <button type="button" class="btn btn-primary changpass" data-bs-toggle="modal" data-bs-target="#changepasswordmod">Change password</button>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group">
                        <label for="fullName">Business Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Please enter your Name" value=" {{ $userData->name ?? ''}} ">
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group">
                        <label for="eMail">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Please enter your Email" required value=" {{ $userData->email ?? '' }} ">
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text"  class="form-control dt-full-name preventCharacters" name="Phone_number" placeholder="Please enter your Phone number" value="{{ $userData->getUserDetails->Phone_number ?? '' }}"  maxlength="10">
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group ">
                        <label for="ciTy">Image [ Recommended size 400 * 600 ] </label>
                        <input type="file" class="form-control" name="image" accept="image/png, image/gif, image/jpeg" id="image" placeholder="image" value=" {{ $userData->image ?? '' }} "/>
                    </div>
                </div>
                <!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group">
                        <label for="website">EIN</label>
                        <input type="text" class="form-control" name="EIN" placeholder="Please enter your EIN number" value="{{$userData->getUserDetails->EIN ?? '' }}">
                    </div>
                </div> -->
            </div>
            <div class="row gutters">
                <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <h6 class="mt-3 mb-2 text-primary">Address</h6>
                </div> -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group">
                        <label for="Street">Business Address</label>
                        <input type="text" class="form-control" name="business_address" placeholder="Please enter Business Address" value=" {{ $userData->getUserDetails->business_address ?? '' }}">
                    </div>
                </div>


                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group">

                        <label for="sTate">Select Business Type</label>
                        <select class="form-control soption" placeholder="Select Business Type"  name="business_type" id="business_type">
                                    <option  selected disabled>Select Business Type</option>
                                    <option value="LLC" @if($userData->getUserDetails->business_type ?? '' == 'LLC') selected @endif >LLC  </option>
                                    <option value="Partnership" @if($userData->getUserDetails->business_type ?? '' == 'Partnership') selected  @endif >Partnership</option>
                                    <option value="Sole Proprietorship" @if($userData->getUserDetails->business_type ?? '' == 'Sole Proprietorship') selected  @endif >Sole Proprietorship</option>
                                    <option value="Others" @if($userData->getUserDetails->business_type ?? '' == 'Others') selected  @endif >Others</option>
                                </select>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mt-3">
                    <div class="form-group textareaacount">
                        <label for="ciTy">About</label>
                        <textarea name="about" class="form-control" placeholder="Please enter the Description" id="about" rows="5" >{{$userData->getUserDetails->about ?? ''}}</textarea>
                    </div>
                </div>



            </div>
            <div class="row gutters mt-5">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-3">
                    <div class="text-right">
                        <button type="submit" id="submit" name="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </form>
	</div>
</div>
</div>
</div>
</div>
</section>

<div class="modal fade" id="changepasswordmod">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <form method="post" class="updatepassword">
            <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100">Change Password</h4>
                    <img class="cursor-pointer" data-bs-dismiss="modal" src="{{ asset('front/images/close.svg')}} " width="25"/>
                </div>
            <!-- Modal body -->
            <div class="modal-body text-center">
                @csrf
                <div class="input-group mb-3">
                    <span class="input-group-text">
                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                    </span>
                    <input type="password" class="eye-field form-control" placeholder="Current Password" name="old_password"required/>
                    <span class="input-group-text">
                    <i class="fa fa-eye-slash eyeHides" aria-hidden="true" vlu="oshow"></i>
                    <i class="fa fa-eye eyeHides d-none" aria-hidden="true" vlu="hide"></i>
                    </span>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                    </span>
                    <input type="password" class="eye-field form-control" placeholder="New Password" name="password"required/>
                    <span class="input-group-text">
                    <i class="fa fa-eye-slash eyeHide" aria-hidden="true" vlu="show"></i>
                    <i class="fa fa-eye eyeHide d-none" aria-hidden="true" vlu="hide"></i>
                    </span>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                    </span>
                    <input type="password" class="eye-field form-control" placeholder="Confirm New Password"  name="cpassword"required/>
                    <span class="input-group-text">
                    <i class="fa fa-eye-slash eyeHidess" aria-hidden="true" vlu="cshow"></i>
                    <i class="fa fa-eye eyeHidess d-none" aria-hidden="true" vlu="hide"></i>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary data-submit me-1 updateprofileSubBtn updateprofileSubBtn2">
                    <div class="spinner-border text-light importloader d-none" role="status"></div>
                    Update Password
                </button>
            </div>
        </form>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script>

$.validator.addMethod("pwcheck", function(value) {
    let password = value;
    if (!(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@#$%&])(.{8,20}$)/.test(password))) {
        return false;
    }
    return true;
}, function (value, element) {
    let password = $(element).val();
    if (!(/^(.{7,20}$)/.test(password))) {
        return 'Password must be between 8 to 20 characters long.';
    }
    else if (!(/^(?=.*[A-Z])/.test(password))) {
        return 'Password must contain at least one uppercase.';
    }
    else if (!(/^(?=.*[a-z])/.test(password))) {
        return 'Password must contain at least one lowercase.';
    }
    else if (!(/^(?=.*[0-8])/.test(password))) {
        return 'Password must contain at least one digit.';
    }
    else if (!(/^(?=.*[@#$%&])/.test(password))) {
        return "Password must contain special characters from @#$%&.";
    }
    return false;
});

    $(".updateProfileForm").validate({
        rules: {
            'name': {
                required: true,
                minlength: 2,
            },
            'Phone_number': {
                required: true,
                minlength:10,
                maxlength:10,
               },
            'email': {
                required: true,
                email: true,
            },

            'about': {
                required: true,
            },

        },
         submitHandler: function (form) {
            var thisForm = $(this);
            $(".updateProfileForm").find('.importloader').removeClass('d-none');
            var formData = new FormData($(".updateProfileForm").get(0));
            $.ajax({
                'url':"{{route('publisher-update-profile')}}",
                'type': "POST",
                'dataType': "json",
                'data': formData,
                processData:false,
                contentType:false,

                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        setTimeout(function(){
                           location.reload();
                        }, 500)
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
                    $(".updatebtn").find('.importloader').addClass('d-none');
                }
            });
        },
    });

    $(".updatepassword").validate({
        rules: {
            password: {
                required: true,
                pwcheck: true,
            },
            old_password:{
                required: true,
            },
            cpassword:{
                required: true,
                pwcheck: true,

            }

        },
        errorPlacement:function(error,element){
                element.parent().after(error)
            },
        submitHandler: function (form) {
            var thisForm = $(this);
            $(".updatepassword").find('.importloader').removeClass('d-none');

            var forms = $(".updatepassword");
            var formData = forms.serialize();
            $.ajax({
                'url':"{{route('change-password')}}",
                'type': "POST",
                'dataType': "json",
                'data': formData,

                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        setTimeout(function(){
                     location.reload();
                  }, 1000)
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
                    $(".updateprofileSubBtn").find('.importloader').addClass('d-none');
                }
            });
        },
    });
    $(document).on("click", ".eyeHides", function(){
   var thi = $(this).attr("vlu");
   $(".eyeHides").removeClass("d-none")
   $(this).addClass("d-none")

   if(thi == "oshow")
   $("input[name='old_password']").prop("type", "text");
   else
   $("input[name='old_password']").prop("type", "password");
});
$(document).on("click", ".eyeHidess", function(){
   var thi = $(this).attr("vlu");
   $(".eyeHidess").removeClass("d-none")
   $(this).addClass("d-none")

   if(thi == "cshow")
   $("input[name='cpassword']").prop("type", "text");
   else
   $("input[name='cpassword']").prop("type", "password");
});

$(function(){
    $('#dob').flatpickr({
        maxDate: new Date()
    });
});

$('#about').on('input', function () {
               this.style.height = 'auto';

               this.style.height =
                       (this.scrollHeight) + 'px';
           });

   </script>
@endsection