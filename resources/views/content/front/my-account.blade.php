@extends('content.front.layouts.master')
@section('title','Home')
@section('content')

<section class="accountdetailpage bg-dark orderdetailpagemin  p-10">
    <div class="container">
        <div class="row p-10">
            <h1 class="searchhead">My Account</h1>
            <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
            <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
            <li class="breadcrumb-item active" aria-current="page">My Account</li>

         </ol>
      </nav>
            <form action="javascript:void(0)" method="post" class="updateProfileForm" novalidate="novalidates">
                @csrf
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </span>
                            <input type="text" class="form-control" name="name" placeholder="Name" value=" {{ $userData->name ?? ''}} ">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                            </span>
                            <input type="email" class="form-control" name="email" placeholder="Email" required @if($userData->login_type != 1) readonly @endif value=" {{ $userData->email ?? '' }} ">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="input-group mb-md-4 mb-1">
                            <span class="input-group-text">
                                <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                            </span>
                            <input type="password" class="form-control" id="pass" placeholder="Password" disabled value=" {{ $userData->password ?? '' }} " required>
                        </div>
                        @if($userData->login_type == 1)
                        <div class="f-pass text-end mb-md-0 mb-2">
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#changepasswordmod">
                                <small>Change Password</small>
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="input-group date mb-4">
                            <span class="input-group-text">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                            <input type="text" class="form-control" name="dob" id="dob" placeholder="Date of Birth" value=" {{ $userData->getUserDetails->dob ?? '' }} " required />
                            <span class="input-group-append">

                            </span>
                        </div>
                    </div>
                    <div class="col-12 update-btn-ct">

                        <button type="submit" class="btn btn-primary data-submit me-1 updatebtn">
                            <div class="spinner-border text-light importloader dNone" role="status"></div>Update Profile
                        </button>

                        <a href="" class="btn btn-primary data-submit me-1 updatebtn" data-bs-toggle="modal" data-bs-target="#deleteAccountmodel">
                            Delete Account </a>
                    </div>
            </form>
        </div>

        @if(!empty($userData->getUserSubscriptionDetails))
        @if($userData->getUserSubscriptionDetails->plan_id == 1 && $userData->getUserSubscriptionDetails->status == 1)
        <div class="row">
            <h5 class="planpurchase">My Plan</h5>
            <div class="myplanlist">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('front/images/plan.png') }}" />
                        <div class="planname">
                            <h5>Monthly Subscription</h5>
                            <h4>{{ Helper::makeCurrency($userData->getUserSubscriptionDetails->price); }}<span>/month</span></h4>
                            <h6>Renewal : {{date("d-m-Y H:i:s", strtotime($userData->getUserSubscriptionDetails->current_period_end)) }}</h6>
                        </div>
                    </div>
                </div>
                @if($userData->getUserSubscriptionDetails->subscription_type == 1)
                @if($userData->getUserSubscriptionDetails->cancel == 1)
                <a href="{{ route('cancel-subscription') }}">
                    <button class="btn btn-primary updateprofileSubBtn cancelsubbtn">Pause subscription</button> </a>
                @else
                <a href="{{ route('reactive-cancel-subscription') }}">
                    <button class="btn btn-primary updateprofileSubBtn cancelsubbtn">Reactivate subscription</button> </a>
                @endif
                @else
                <a href="javascript:void(0)">
                    <button class="btn btn-primary updateprofileSubBtn cancelsubbtn" data-bs-toggle="modal" data-bs-target="#cancelSubAlert">Pause subscription</button> </a>
                @endif
            </div>
        </div>
        @elseif($userData->getUserSubscriptionDetails->plan_id == 2 && $userData->getUserSubscriptionDetails->status == 1)
        <div class="row">
            <h5 class="planpurchase">My Plan</h5>
            <div class="myplanlist">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('front/images/plan.png') }}" />
                        <div class="planname">
                            <h5>Yearly Subscription</h5>
                            <h4>{{ Helper::makeCurrency($userData->getUserSubscriptionDetails->price); }}<span>/year</span></h4>
                            <h6>Renewal : {{date("d-m-Y H:i:s", strtotime($userData->getUserSubscriptionDetails->current_period_end)) }}</h6>
                        </div>
                    </div>
                </div>
                @if($userData->getUserSubscriptionDetails->subscription_type == 1)
                @if($userData->getUserSubscriptionDetails->cancel == 1)
                <a href="{{ route('cancel-subscription') }}">
                    <button class="btn btn-primary updateprofileSubBtn cancelsubbtn">Pause subscription</button> </a>
                @else
                <a href="{{ route('reactive-cancel-subscription') }}">
                    <button class="btn btn-primary updateprofileSubBtn cancelsubbtn">Reactivate subscription</button> </a>
                @endif
                @else
                <a href="javascript:void(0)">
                    <button class="btn btn-primary updateprofileSubBtn cancelsubbtn" data-bs-toggle="modal" data-bs-target="#cancelSubAlert">Pause subscription</button> </a>
                @endif
            </div>
        </div>
        @else
        <div class="row">
            <h5 class="planpurchase">My Plan</h5>
            <div class="myplanlist">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('front/images/plan.png') }}" />
                        <div class="planname">
                            <h5>Free Subscription</h5>
                            <h4>{{ Helper::makeCurrency(0); }} <span></span></h4>
                        </div>
                    </div>
                </div>
                <a href="{{ route('subscription-page') }}">
                    <button class="btn btn-primary updateprofileSubBtn">Upgrade Your Plan</button>
                </a>
            </div>
        </div>
        @endif
        @else
        <div class="row">
            <h5 class="planpurchase">My Plan</h5>
            <div class="myplanlist">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('front/images/plan.png') }}" />
                        <div class="planname">
                            <h5>Free Subscription</h5>
                            <h4>{{ Helper::makeCurrency(0); }} <span></span></h4>
                        </div>
                    </div>
                </div>
                <a href="{{ route('subscription-page') }}">
                    <button class="btn btn-primary updateprofileSubBtn">Upgrade Your Plan</button>
                </a>
            </div>
        </div>
        @endif


    </div>

</section>

<!----Rating Modal Start--->
<div class="modal fade" id="changepasswordmod">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" class="updatepassword">
                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100">Change Password</h4>
                    <img class="cursor-pointer" data-bs-dismiss="modal" src="{{ asset('front/images/close.svg')}} " width="25" />
                </div>
                <!-- Modal body -->
                <div class="modal-body text-center">
                    @csrf
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                        </span>
                        <input type="password" class="eye-field form-control" placeholder="Current Password" name="old_password" required />
                        <span class="input-group-text">
                            <i class="fa fa-eye-slash eyeHides" aria-hidden="true" vlu="oshow"></i>
                            <i class="fa fa-eye eyeHides d-none" aria-hidden="true" vlu="hide"></i>
                        </span>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                        </span>
                        <input type="password" class="eye-field form-control" placeholder="New Password" name="password" required />

                        <span class="input-group-text">
                            <i class="fa fa-eye-slash eyeHide" aria-hidden="true" vlu="show"></i>
                            <i class="fa fa-eye eyeHide d-none" aria-hidden="true" vlu="hide"></i>
                        </span>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                        </span>
                        <input type="password" class="eye-field form-control" placeholder="Confirm New Password" name="cpassword" required />
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
<!----Rating Modal End-->

<!----Cancel Subscription Alert Modal Start--->

<div class="modal fade modelbutton" id="cancelSubAlert">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100">Cancel Subscription Alert</h4>
                <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
            </div>
            @php
            $subscriptionType = "";
            if(!empty($userData->getUserSubscriptionDetails->subscription_type))
            {
            if($userData->getUserSubscriptionDetails->subscription_type == 1)
            $subscriptionType = "Web";
            elseif($userData->getUserSubscriptionDetails->subscription_type == 2)
            $subscriptionType = "IOS";
            else
            $subscriptionType = "Android";
            }
            @endphp
            <div class="modal-body text-center">
                <p class="colorwhite fw-bolder">You have purchased this subscription from {{ $subscriptionType }} platform. So you can't cancel this subscription from here. To Cancel Subscription, Please use the particular platform that you have used to purchase your subscription.</p>
            </div>
        </div>
    </div>
</div>
<!----Cancel Subscription Alert Modal Start-->

@endsection
@section('scripts')
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script>
    $(".updateProfileForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
            },
            email: {
                required: true,
            },
            pass: {
                required: true,
                minlength: 5,
            },
            dob: {
                required: true,

            },

        },
        errorPlacement: function(error, element) {
            element.parent().after(error)
            element.parent().removeClass("mb-3")
        },
        submitHandler: function(form) {
            var thisForm = $(this);
            $(".updateProfileForm").find('.importloader').removeClass('d-none');

            var forms = $(".updateProfileForm");
            var formData = forms.serialize();
            $.ajax({
                'url': "{{route('update-profile')}}",
                'type': "POST",
                'dataType': "json",
                'data': formData,

                'success': function(response) {
                    if (response.status) {
                        toastr["success"](response.message, "Success");

                    } else {
                        toastr["error"](response.message, "Error");
                    }
                },
                'error': function(error) {
                    console.log(error);
                },
                'complete': function() {
                    $(".updatebtn").find('.importloader').addClass('d-none');
                }
            });
        },
    });

    $(".updatepassword").validate({
        rules: {
            password: {
                required: true,
                minlength: 2,
            },
            old_password: {
                required: true,

            },
            cpassword: {
                required: true,

            }
        },
        errorPlacement: function(error, element) {
            element.parent().after(error)
        },
        submitHandler: function(form) {
            var thisForm = $(this);
            $(".updatepassword").find('.importloader').removeClass('d-none');

            var forms = $(".updatepassword");
            var formData = forms.serialize();
            $.ajax({
                'url': "{{route('change-password')}}",
                'type': "POST",
                'dataType': "json",
                'data': formData,

                'success': function(response) {
                    if (response.status) {
                        toastr["success"](response.message, "Success");
                        setTimeout(function() {
                            location.reload();
                        }, 1000)
                    } else {
                        toastr["error"](response.message, "Error");
                    }
                },
                'error': function(error) {
                    console.log(error);
                },
                'complete': function() {
                    $(".updateprofileSubBtn").find('.importloader').addClass('d-none');
                }
            });
        },
    });
    $(document).on("click", ".eyeHides", function() {
        var thi = $(this).attr("vlu");
        $(".eyeHides").removeClass("d-none")
        $(this).addClass("d-none")

        if (thi == "oshow")
            $("input[name='old_password']").prop("type", "text");
        else
            $("input[name='old_password']").prop("type", "password");
    });
    $(document).on("click", ".eyeHidess", function() {
        var thi = $(this).attr("vlu");
        $(".eyeHidess").removeClass("d-none")
        $(this).addClass("d-none")

        if (thi == "cshow")
            $("input[name='cpassword']").prop("type", "text");
        else
            $("input[name='cpassword']").prop("type", "password");
    });

    $(function() {
        $('#dob').flatpickr({
            maxDate: new Date()
        });
    });
</script>
@endsection