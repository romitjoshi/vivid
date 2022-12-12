@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
<section class=" subscriptiondetailpage searchpage bg-dark p-10">
  <!-- <section class="subscriptiondetailpage bg-dark p-10 "> -->
  <div class="container">
    <div class="row p-10">
      <h1 class="searchhead">Premium Subscription</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
           <li class="breadcrumb-item"><a href="{{route('my-account')}}">My Account</a></li>
          <li class="breadcrumb-item active" aria-current="page">Subscription</li>

        </ol>
      </nav>
    </div>
    <div class="row p-10 fontsizerj" >
       <h1 class="searchhead font-weight-bold  mb-4 ">GET MORE WITH <span style="color:red">PREMIUM!</span></h1>
      <div class="col-md-7 d-flex align-items-center subscriptionrj">
        <h4 class="mb-0 line_spacing">
          <div class="subimgrj">
             <img src="{{ asset('front/images/Checked.svg')}}" width="30px">
             <span>Use Less Coins For Premium Content</span>
            </div>
          <div  class="subimgrj"> <img src="{{ asset('front/images/Checked.svg')}}" width="30px"><span>Get A Custom Copy Of This Year's Anthology Mailed To You</span></div>
          <div class="subimgrj"> <img src="{{ asset('front/images/Checked.svg')}}" width="30px"><span>Premium Member Discounts On Select Shop Items</span></div>
          <div class="subimgrj"> <img src="{{ asset('front/images/Checked.svg')}}" width="30px"><span>Bonus V-Coins!</span></div>
        </h4>
      </div>
      <div class="col-md-5 palndivimg">
        <div class="text-white font-weight-bold mb-2 px-3 ">PREMIUM SUBSCRIPTION</div>
        <div class="row mx-0">
          <!-- <div class="col-md-6">
            <div class="mysublist mysublistmon plans">
              <div class="card activeClass planBox mb-2 mb-md-0 h-100" planCheck="1">
                <div class="card-body">
                  <div class="plan_img_wrapper">
                    <img src="{{ asset('front/images/plan.png')}}">
                  </div>
                  <div class="planname">
                    <h5>3 Days Free Trial</h5>
                    <h4>${{ $planData[0]->price ?? '-'}}<span>/month</span></h4>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <div class="col-md-12">
            <div class="mysublist mysublistyear plans ">
              <div class="card planBox h-100" planCheck="2">
                <div class="card-body">
                  <div class="plan_img_wrapper">
                    <img src="{{ asset('front/images/Frame.png')}}">
                  </div>
                  <div class="planname saveplanpoint">
                    <h5>PREMIUM</h5>
                    <h4>${{ $planData[1]->price ?? '-'}}<span>/year</span></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 text-center agreewidth" style="margin-top:65px;">
              <ul class="unstyled centered">
              <li>
                <input class="styled-checkbox " id="styled-checkbox-1" type="checkbox" value="value1">
                <label for="styled-checkbox-1">I Agree to the Terms & Conditions</label>
              </li>
            </ul>
            <button class="btn btn-primary updateprofile d-none" id="checkout-button-ll-premium" disabled>Confirm Payment</button>
            <button class="btn btn-primary updateprofile" id="checkout-button-ll-premium2" disabled>Confirm Payment</button>
        </div>


        </div>
      </div>
    </div>
    <!-- <div class="checklistsub">
      <div class="row customrowwidth customrowborder">
        <div class="col-4">
          <img src="{{ asset('front/images/check.png') }}" class="facees" />
          <p>Full Access</p>
        </div>
         <div class="col-4">
                    <img src="{{ asset('front/images/check.png') }}" class="noads"/>
                    <p>No ads</p>
                  </div>
        <div class="col-4">
          <img src="{{ asset('front/images/check.png') }}" class="apepisodes" />
          <p>Auto play episodes</p>
        </div>
        <div class="col-4">
          <img src="{{ asset('front/images/check.png') }}" class="scomics" />
          <p>Support indie comics!</p>
        </div>



      </div>

    </div> -->
    <!-- <div class="row m-t-10">
         <div class="col-md-6 dnoneextraj"> </div>

    </div> -->


  </div>


</section>

@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
  $(document).on("click", ".planBox", function() {
    $(".card").removeClass('activeClass');
    $(this).addClass('activeClass');
    var planCheck = $(this).attr('planCheck');

    $('.updateprofile').addClass('d-none');
    if (planCheck == 1)
      $("#checkout-button-ll-premium").removeClass('d-none');
    else
      $("#checkout-button-ll-premium2").removeClass('d-none');
  });

  $(document).on("click", "#checkout-button-ll-premium", function() {
    $.ajax({
      url: "{{ route('subscription-month') }}",
      type: "POST",
      dataType: "json",
      data: {
        "_token": "{{ csrf_token() }}"
      },
      success: function(response) {
        console.log(response)
        if (response.status) {
          window.location = response.data.url;
        } else {
          alert(response.message)
        }

      },
      'error': function(error) {
        console.log(error);
      },
    });
  });

  $(document).on("click", "#checkout-button-ll-premium2", function() {
    $.ajax({
      url: "{{ route('subscription-year') }}",
      type: "POST",
      dataType: "json",
      data: {
        "_token": "{{ csrf_token() }}"
      },
      success: function(response) {
        console.log(response)
        if (response.status) {
          window.location = response.data.url;
        } else {
          alert(response.message)
        }

      },
      'error': function(error) {
        console.log(error);
      },
    });
  });

  $('#styled-checkbox-1').click(function() {
    if ($(this).is(':checked')) {
      $('#checkout-button-ll-premium').removeAttr('disabled');
      $('#checkout-button-ll-premium2').removeAttr('disabled');
    } else {
      $('#checkout-button-ll-premium').attr('disabled', 'disabled');
      $('#checkout-button-ll-premium2').attr('disabled', 'disabled');

    }
  });
</script>
@endsection