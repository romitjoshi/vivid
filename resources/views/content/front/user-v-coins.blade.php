@extends('content.front.layouts.master')
@section('title','My V-Coins')
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('front/css/publisher.css')}}">
<style>
.modal-dialog {
    max-width: 800px;
}
table.table {
    color: #fff;
}
.requestPopup{max-width: 300px;width: 100%;}
.radio-toolbar {
  margin: 0px;
}
.radiolabelplan img{margin-top:-2px;}
.ttclass{background:#161616;}
.radio-toolbar input[type="radio"] {
  opacity: 0;
  position: fixed;
  width: 0;
}
.radio-toolbar label {
   display: inline-block;
   background-color: #ddd;
    padding: 5px 20px;
    font-family: sans-serif, Arial;
    font-size: 16px;
    border: 2px solid #444;
    border-radius: 6px;
    width: 100%;
    margin: 10px 0px;
    cursor: pointer;
}

.radio-toolbar label:hover {
  background-color: #dfd;
}

.radio-toolbar input[type="radio"]:focus + label {
    border: 2px dashed #444;
}

.radio-toolbar input[type="radio"]:checked + label {
   background-color: #009bd5;
    border-color: #009bd5;
}
.radiolabelplan{display: flex !important;justify-content: space-between;}
</style>
<section class="searchpage bg-dark slidercalss p-10">
   <div class="container">
      <div class="row p-10">
         <div class="titile-section">
            <h3>My V-Coins</h3>
               <nav aria-label="breadcrumb">
                     <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
                        <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                        <li class="breadcrumb-item active" aria-current="page">My V-Coins</li>
                     </ol>
                 </nav>
         </div>
         <div class="main-content">
            <div class="header bg-gradient-primary pb-8 pt-md-8">
               <div class="container-fluid1">
                  <div class="header-body">
                  <div class="row">
                        <div class="col-xl-4 col-lg-4">
                           <div class="card111 card-stats mb-4 mb-xl-0">
                              <div class="card-body coincss" style="background-image:url({{ asset('front/images/bluebox.png') }} )">

                                  <div class="row">
                                    <div class="col">
                                       <h5 class="card-title cointitle fff mb-1">Total Coins</h5>
                                       <div class="coinsecc">
                                       <span class="coinspan h2 font-weight-bold mb-0 fff">{{ Helper::getcoinsTotal(Auth::user()->id) ?? 0 }}  <img src="{{ asset('front/images/v-coins.png') }}"></span>
                                       <span class="coinbuy btn-primary btn" data-bs-toggle="modal" data-bs-target="#PurchaseCoinsAppModel"><i class="fa fa-plus" aria-hidden="true" ></i>ADD COINS</span>
                                       </div>
                                    </div>
                                    <div class="col-auto">
                                          <!-- <img src="{{ asset('front/images/v-coins.png') }}"> -->
                                    </div>
                                 </div>

                              </div>
                           </div>
                        </div>

                        <!-- <div class="col-xl-4 col-lg-4">
                           <div class="card1 card-stats mb-4 mb-xl-0">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col">
                                       <h5 class="card-title text-uppercase text-muted mb-0 fff mb-4">Withdrawal Amount</h5>
                                       <span class="h2 font-weight-bold mb-0 fff">{{ Helper::makeCurrency($UserDetails->getUserDetails->withdrawal ?? 0) }}</span>
                                    </div>
                                    <div class="col-auto">
                                       <div class="icon icon-shape bg-primary1 text-white rounded-circle shadow dashIcon">
                                          <i class="fas fa-usd"></i>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-xl-4 col-lg-4">
                           <div class="card1 card-stats mb-4 mb-xl-0">
                            <a class="dashboardclass" href="{{ route('my-referral') }}">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col">
                                       <h5 class="card-title text-uppercase text-muted mb-0 fff mb-4">Referral</h5>
                                       <span class="h2 font-weight-bold mb-0 fff">{{ $referralCount ?? ''}}</span>
                                    </div>
                                    <div class="col-auto">
                                       <div class="icon icon-shape bg-primary1 text-white rounded-circle shadow dashIcon">
                                          <i class="fas fa-user"></i>
                                       </div>
                                    </div>
                                 </div>
                               </div>
                              </a>
                           </div>
                        </div> -->
                     </div>
                     <div class="row mb-5 mt-5">
                        <div class="col-md-12 col-md-offset-1">
                           <div class="heading-sec d-flex justify-content-between">
                              <h4 class="mt-2 mb-5">V-Coins History</h4>
                              <!-- <div class="btnsec text-end mb-4">
                                 <button data-bs-toggle="modal" data-bs-target="#modals-slide-create" class="btn btn-primary" @if($restAmount ?? '' < 50) disabled @endif>Request Payout</button>
                                 <p class="text-white mt-1">(Payouts must be $50 minimum)</p>
                              </div>                               -->
                           </div>
                           <div class="table-section table-responsive ttclass ">
                              <table class="table table-dark payoutTable ">
                                 <thead>
                                    <tr>
                                       <th scope="col">Date</th>
                                       <th scope="col">Coins</th>
                                       <th scope="col">Description</th>
                                       <th scope="col">Transaction Type</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach($userWallet ?? [] as $req)
                                    <tr>
                                       <td>{{ $req->created_at }}</td>
                                       <td><img src="{{ asset('front/images/v-coins.png') }}" width="17">  {{ $req->coins }}</td>
                                       <td>{{ $req->comic }} @if($req->episode_name) [ {{ $req->episode_name }} ] @endif</td>
                                       <td>{!!  Helper::transactionType($req->transaction_type) !!}</td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Page content -->
         </div>
      </div>
   </div>
</section>


<!---Purchase COins Our App model--->
<div class="modal fade modelbutton" id="PurchaseCoinsAppModel">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header text-center">
            <h4 class="modal-title w-100">Add Coins</h4>
            <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>
         </div>
         <!-- Modal body -->
         <form class="mx-1 mx-md-4" id="payment-form">
         <div class="modal-body">
            <div class="row d-flex justify-content-center align-items-center h-100">
               <div class="row justify-content-center">
                  <div class="col-md-5 ">
                     <p class="text-white">Add more V coins to your wallet and keep supporting indie comic creators!</p>
                     <div class="radio-toolbar">
                        @foreach($Coin_slab as $slab)
                        <input type="radio" id="coins{{$loop->index}}" name="price" value="{{ $slab->slabs }}" @if($loop->index == 0) checked @endif>
                        <label class="radiolabelplan" for="coins{{$loop->index}}">
                           <span>
                           <img src="{{ asset('front/images/v-coins.png') }}" width="17"> {{ $slab->coins }}
                           </span>
                            <span>${{ $slab->slabs }}</span>
                        </label>
                        @endforeach


                     </div>
                  </div>
                  <div class="col-md-7">
                     <input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
                     <div class="row">
                        <div class="col-md-12 text-start">
                           <div class="form-group mb-3">
                              <label class="form-label">Name on Card</label>
                              <input type="text" id="cardname" class="form-control" placeholder="Enter Name" name="cardname">
                           </div>
                        </div>
                        <div class="col-md-12 text-start cardInput">
                           <div id="card-element" class="stripepaymentform">

                           </div>
                           <!-- Used to display Element errors. -->
                        </div>
                        <div class="col-md-12 text-start">
                           <div id="card-errors" role="alert" class="card-errors-css">

                           </div>
                        </div>
                           @if(!empty($getPaymentMethod['data']))
                           <p class="text-white mt-3">Or Choose Card</p>
                           <div class="existcradlist">
                              @foreach($getPaymentMethod['data'] as $gpm)
                                 <div class="form-check">
                                    <input id="credit" name="card_id" type="radio" class="form-check-input" value="{{ $gpm['id'] }}" checked>
                                    <label class="form-check-label" for="credit">
                                       <!-- <img src="{{ asset('front/images/mastercard.png')}}" width="40"/> -->
                                       <span class="carddesign">{{ $gpm['brand']}}</span>
                                    **** **** ****{{ $gpm['last4']}}</label>
                                 </div>
                              @endforeach
                           </div>
                           @endif
                     </div>
                     <div class="modal-footer-section">
                        <button class="btn btn-primary updateprofile cardAddBtn"><div class="spinner-border text-light importloader d-none" role="status"></div>Add coins</button>
                     </div>


                  </div>
               </div>
            </div>
         </div>
         </form>
         <!-- Modal footer -->
         <div class="modal-footer">
         </div>
      </div>
   </div>
</div>
<!----Purchase COins Our App End-->
@endsection
@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script>

// $(document).ready(function(){

//    $("#PurchaseCoinsAppModel").modal("show");
// });

$('.payoutTable').DataTable({
        order: [[0, 'desc']],
        responsive: true
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
      var cardname = $("#cardname").val();
      if(cardname)
      {
         stripe.createToken(card).then(function(result) {
            if (result.error) {
               var errorElement = document.getElementById('card-errors');
               errorElement.textContent = result.error.message;
            } else {
                  purchaseToken(result.token);
            }
         });
      }
      else
      {
         var tk = '';
         BuyCoins(tk);
      }

});

function BuyCoins(token) {
	$("#payment-form").find('.importloader').removeClass('d-none');
	$(".cardAddBtn").attr("disabled", true);
    //var form = document.getElementById('payment-form');
    //var hiddenInput = document.createElement('input');
    //hiddenInput.setAttribute('type', 'hidden');
    //hiddenInput.setAttribute('name', 'stripeToken');
    //hiddenInput.setAttribute('value', token.id);
    //form.appendChild(hiddenInput);

	var forms = $("#payment-form");
	var formData = forms.serialize();
   //alert(formData)
	$.ajax({
		'url':'{{route("purchase-coins")}}',
		'type':'post',
		'dataType':'json',
		'data':formData,
		'beforeSend':function(){},
		'success':function(response){
         console.log(response)
			if(response.status)
			{
				toastr["success"](response.message, "Success");
            if(response.url)
            {
               location.href = response.url;
            }
				setTimeout(function(){
					location.reload();
				}, 2000)
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

function purchaseToken(token) {
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
   //alert(formData)
	$.ajax({
		'url':'{{route("purchase-coins")}}',
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