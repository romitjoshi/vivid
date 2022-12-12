@extends('content.front.layouts.master')
@section('title','Home')
@section('content')

@php

//echo "<pre>";print_r($countCart);exit;

$userLoginCheck = false;
if(!empty(Auth::user()->role) && Auth::user()->role == 2)
{
  $userLoginCheck = true;
}
@endphp

@if(empty($userLoginCheck))
<script>
var u = "{{ env('APP_URL_SERVE') }}";
location.href = u+'/login';
</script>
@endif


@endphp

@if(empty($countCart))
<script>
var u = "{{ env('APP_URL_SERVE') }}";
location.href = u+'/login';
</script>
@endif
<section class="deliverydetailpage bg-dark p-50">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-lg-8">
				<h1 class="searchhead">Delivery Details</h1>
				<nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
			 <li class="breadcrumb-item"><a href="{{route('store')}}">Store</a></li>
            <li class="breadcrumb-item active" aria-current="page">Buy Now</li>

         </ol>
      </nav>
				<form method="post" class="checkoutForm">
					@csrf

					<div class="row" >
						<div class="col-md-6 col-sm-6">
							<div class="form-group mb-3">
								<label class="form-label">Address</label>
								<input type="text" class="form-control" placeholder="Address" name="address" value="{{ $userInfo->getUserAddress->address ?? '' }}">
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group mb-3">
								<label class="form-label">City</label>
								<input type="text" class="form-control" placeholder="City" name="city" value="{{ $userInfo->getUserAddress->city ?? '' }}">
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group mb-3">
								<label class="form-label">State</label>
								<input type="text" class="form-control" placeholder="State" name="state" value="{{ $userInfo->getUserAddress->state ?? '' }}">
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group mb-3">
								<label class="form-label">Zip Code</label>
								<input type="text" class="form-control dt-full-name"  placeholder="Zip Code" name="zip" value="{{ $userInfo->getUserAddress->zip_code ?? '' }}">
							</div>
						</div>
					</div>


					<div class="paymentdtail">
						<h1 class="searchhead mt-3">Select Card</h1>
						<div class="existcradlist">
							@if(!empty($getPaymentMethod['data']))
								@foreach($getPaymentMethod['data'] as $gpm)
									<div class="form-check">
										<input id="credit" name="card_id" type="radio" class="form-check-input" value="{{ $gpm['id'] }}" checked>
										<label class="form-check-label" for="credit">
											<!-- <img src="{{ asset('front/images/mastercard.png')}}" width="40"/> -->
											<span class="carddesign">{{ $gpm['brand']}}</span>
										**** **** ****{{ $gpm['last4']}}</label>
									</div>
								@endforeach
							@else
							<div class="form-check">
								<img src="{{ asset('front/images/no-card.png') }}">
							</div>
							@endif
							<a class="btn btn-primary updateprofile" data-bs-toggle="modal" data-bs-target="#add-new-card">Add New Card</a>
						</div>
					</div>
				</div>
				<div class="col-md-12 col-lg-4 order-md-last orderlist">
					<div class="card">
						<div class="card-body">
							<h4 class="o_detail">Order Details</h4>
							<ul class="list-group mb-3">
								@foreach($countCart as $cart)
								<li class="list-group-item lh-sm">

									<img src="{{ env ('IMAGE_PATH_medium').$cart->featured_Image  ?? ''}}" class="float-start order-image productBuyImg"/>
									<div class="float-start orderdelist">
										<h6 class="my-0">{{Str::limit($cart->product_name,20)}}</h6>
										<p class="text-muted">{{ Helper::makeCurrency($cart->special_price) }} </p>
										@if(!empty($cart->comic_series_id))
											<div class="mt-1">
											<p class="">Custom Title : {{ $cart->custom_title_name ?? ''}}</p>
											@php
											$comicId = json_decode($cart->comic_series_id);
											$nmbr = 1;
											foreach($comicId as $cd)
											{
												$comicName = Helper::getComicNameById($cd);
												echo '<p>Comic '.$nmbr.': '.$comicName.'</p>';
												$nmbr++;
											}
											@endphp        
											
										</div>
										@endif
									</div>

									<div class="product-quantity float-end" data-productid="{{$cart->id}}">
										<p>
											<button type="button" class="decreamentqty buttonincdec">-</button>
											<span><input class="productqty text-center" type="text" name="productqty" value="{{ $cart->qty }}" style="width:25px"></span>
											<button type="button" class="increamentqty buttonincdec">+</button>
										</p>
									</div>
								</li>
								@endforeach
								<li class="list-group-item d-flex justify-content-between">
									<strong>Total Cost</strong>
									<strong class="cart-sum show">{{ Helper::makeCurrency($sum) }}</strong>
								</li>
								@if(empty($getPaymentMethod['data']))
								<a type="submit" class="btn btn-primary checkoutpro" data-bs-toggle="modal" data-bs-target="#add-new-card">Checkout</a>
								@else
								<button type="submit" class="btn btn-primary checkoutpro"><div class="spinner-border text-light importloader d-none" role="status"></div>Checkout</button>
								@endif
							</ul>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
</section>

<div class="modal fade" id="add-new-card">
   	<div class="modal-dialog modal-dialog-centered">
     	<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h4 class="modal-title w-100">Add New Card</h4>
				<img class="cursor-pointer" data-bs-dismiss="modal" src="{{ asset('front/images/close.svg') }}" width="25"/>
			</div>

			<!-- Modal body -->
			<div class="modal-body text-center">
				<form method="POST" id="payment-form" action="">
					<input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
						<div class="row">
							<div class="col-md-12 text-start">
								<div class="form-group mb-3">
									<label class="form-label">Name on Card</label>
									<input type="text" id="cardname" class="form-control" placeholder="Enter Name" name="cardname" required>
								</div>
							</div>
							<div class="col-md-12 text-start mt-4 cardInput">
								<div id="card-element" class="stripepaymentform">

								</div>
								<!-- Used to display Element errors. -->
							</div>
							<div class="col-md-12 text-start">
								<div id="card-errors" role="alert" class="card-errors-css">

								</div>
						</div>
						</div>
						<div class="modal-footer-section">
							<button class="btn btn-primary updateprofile cardAddBtn"><div class="spinner-border text-light importloader d-none" role="status"></div>Done</button>
						</div>
				</form>
			</div>

     	</div>
   	</div>
 </div>

 <div class="modal fade" id="succsessModel" data-controls-modal="succsessModel" data-backdrop="static" data-keyboard="false" href="#">
   	<div class="modal-dialog modal-dialog-centered">
     	<div class="modal-content">
			<!-- Modal body -->
			<div class="modal-body text-center">
				 <div class="imageSection">
					 <img src="{{ asset('front/images/ckeck.png') }}">
				 </div>
				 <div class="titletext"><h3>Congratulations!</h3></div>
				 <div class="infotext"><h5>Your Order has been Confirmed</h5></div>
				 <div class="btnDone"><a href="{{ route('my-order') }}">Done</a></div>
			</div>

     	</div>
   	</div>
 </div>
@endsection



@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
 <script>

$(document).on('click','.buttonincdec', function(event){
	var oldValue = $(this).parent().find('input').val();

	if ($(this).is('.increamentqty')) {
		var newquantity = parseFloat(oldValue) + 1;
	} else {
		var newquantity = parseFloat(oldValue) - 1;
	}
	$(this).closest('.product-quantity').find('.productqty').val(Math.max(newquantity,0));
	var product_id = $(this).closest('.product-quantity').attr('data-productid');

	if(newquantity == 0 ){
		$(this).parent().parent().parent().remove();
	}

	$.ajax({
		'url':'{{route("update-cart")}}',
		'type':'post',
		'dataType':'json',
		'data':{
			'_token':'{{csrf_token()}}',
			'product_id':product_id,
			'quantity':newquantity
		},
		'beforeSend':function(){},
		'success':function(response){
			console.log(response)
			if(response.success){
				$('.cart-sum').html('$'+response.sum.toFixed(2));
			}
			if(response.sum == 0)
			{
				var u = "{{ env('APP_URL_SERVE') }}";
				location.href = u+'/store';
			}
		},
		'error':function(){},
		'complete':function(){}
	});
});

$(".checkoutForm").validate({
	rules: {
		address: {
			required: true,
		},
		city: {
			required: true,
		},
		state: {
			required: true,
		},
		zip: {
			required: true,
			number:true,
		},
	},
	submitHandler: function (form) {
		var thisForm = $(this);
		$(".checkoutpro").find('.importloader').removeClass('d-none');

		var forms = $(".checkoutForm");
		var formData = forms.serialize();
		$.ajax({
			'url':"{{route('checkout')}}",
			'type': "POST",
			'dataType': "json",
			'data': formData,
			'success': function (response) {
				if(response.status)
				{
					//toastr["success"](response.message, "Success");
					$('#succsessModel').modal({
						backdrop: 'static',
						keyboard: false
					})
					$('#succsessModel').modal('show');
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
				$(".checkoutpro").find('.importloader').addClass('d-none');
			}
		});
	},
});

/*****Stripe Start*****/
var stripe = Stripe('{{env("STRIPE_KEY")}}');
var elements = stripe.elements();
var form = document.getElementById('payment-form');
var style = {
	base: {
		fontSize: '16px',
		color: '#fff',
		background: '#161616',
		'::placeholder': {
			color: '#fff',
		},
	},
};

var card = elements.create('card', {hidePostalCode: true, style: style});
card.mount('#card-element');
var form = document.getElementById('payment-form');
	form.addEventListener('submit', function(event) {
		//$(".cardAddBtn").attr("disabled", true);
        event.preventDefault();

      stripe.createToken(card).then(function(result) {
        if (result.error) {
          var errorElement = document.getElementById('card-errors');
          errorElement.textContent = result.error.message;
        } else {
            stripeTokenHandler(result.token);
        }
    });
});
function stripeTokenHandler(token) {
	$("#payment-form").find('.importloader').removeClass('d-none');
	$(".cardAddBtn").attr("disabled", true);
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);

	var forms = $("#payment-form");
	var formData = forms.serialize();
	$.ajax({
		'url':'{{route("add-payment-method")}}',
		'type':'post',
		'dataType':'json',
		'data':formData,
		'beforeSend':function(){},
		'success':function(response){
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
			$(".cardAddBtn").attr("disabled", false);
		},
		'error':function(){},
		'complete':function(){
			$("#payment-form").find('.importloader').addClass('d-none');
		}
	});

}


</script>
@endsection