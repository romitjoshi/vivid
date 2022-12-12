@extends('content.front.layouts.master')
@section('title','My Comic')
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('front/css/publisher.css')}}">
<style>
table.table {
    color: #fff;
}
.requestPopup{max-width: 300px;width: 100%;}
</style>
<!-- <section class="container publishertop mt-50">
   <div class="row">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
             <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Wallet</li>

         </ol>
      </nav>
   </div>
</section> -->
<section class="searchpage bg-dark slidercalss p-10">
   <div class="container">
      <div class="row p-10">
         <div class="titile-section">
            <h3  >My Wallet</h3>
               <nav aria-label="breadcrumb">
                     <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
                        <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                        <li class="breadcrumb-item active" aria-current="page">My Wallet</li>
                     </ol>
                 </nav>
         </div>
         <div class="main-content">
            <div class="header bg-gradient-primary pb-8 pt-md-8">
               <div class="container-fluid1">
                  <div class="header-body">
                  <div class="row">
                        <div class="col-xl-4 col-lg-4">
                           <div class="card1 card-stats mb-4 mb-xl-0">
                              <div class="card-body">
                                  <div class="row">
                                    <div class="col">
                                       <h5 class="card-title text-uppercase text-muted mb-0 fff mb-4">Total Amount</h5>
                                       <span class="h2 font-weight-bold mb-0 fff">{{ Helper::makeCurrency($UserDetails->getUserDetails->wallet ?? 0) }}</span>
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
                        </div>
                     </div>
                     <div class="row mb-5 mt-5">
                        <div class="col-md-12 col-md-offset-1">
                           <div class="heading-sec d-flex justify-content-between">
                              <h4 class="mt-2 mb-2">Payout History</h4>
                              <div class="btnsec text-end mb-4">
                                 <button data-bs-toggle="modal" data-bs-target="#modals-slide-create" class="btn btn-primary" @if($restAmount < 50) disabled @endif>Request Payout</button>
                                 <p class="text-white mt-1">(Payouts must be $50 minimum)</p>
                              </div>                              
                           </div>
                           <div class="table-section">
                              <table class="table table-dark payoutTable">
                                 <thead>
                                    <tr>
                                       <th scope="col">Date</th>
                                       <th scope="col">Amount</th>
                                       <th scope="col">Status</th>
                                       <th scope="col">Notes</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach($payoutRequest as $req)
                                    <tr>
                                       <td>{{ $req->created_at }}</td>
                                       <td>{{ Helper::makeCurrency($req->amount) }}</td>
                                       <td>{{ html_entity_decode(Helper::statusPayoutFront($req->status)) }}</td>
                                       <td>{{ $req->admin_note }}</td>
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


<div class="modal fade modelbutton addFornt" id="modals-slide-create">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content">
       <!-- Modal Header -->
       <div class="modal-header text-center">
         <h4 class="modal-title w-100">Request Payout</h4>
         <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>
       </div>
       <!-- Modal body -->
       <div class="modal-body text-center requestPopup">
            <form class="modal-content1 pt-0" id="insertForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="modal-body flex-grow-1">
                <div class="mt-2">
                    <label class="form-label">Amount</label>
                    <input type="text" class="form-control preventCharactersAndDot" name="amount" placeholder="Amount" maxlength="5" />
                </div>

                <div class="button-section mt-5">
                    <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
                </div>
            </form>
       </div>
     </div>
   </div>
 </div>


@endsection
@section('scripts')
<script>

$('.payoutTable').DataTable({
        order: [[0, 'desc']],
        responsive: true
    });

$("#insertForm").validate({
   rules: {
      'amount': {
         required: true,
         min:50
      },
      },
      submitHandler: function (form) {
         var thisForm = $(this);

         $("#insertForm").find('.importloader').removeClass('dNone');
         var formData = new FormData($("#insertForm").get(0));
         $.ajax({
               url: ajaxUrl + "/publisher/payout-request",
               type: "POST",
               dataType: "json",
               processData:false,
               contentType:false,
               data: formData,
               success: function (response) {
                  console.log(response)
                  if(response.status)
                  {
                     toastr["success"](response.message, "Success");
                     setTimeout(function(){
                        location.reload();
                     }, 1000)

                  }
                  else{
                     toastr["error"](response.message, "Error");
                  }

               },
               'error': function (error) {
                  console.log(error);
               },
               'complete':function(){
                  $("#insertForm").find('.importloader').addClass('dNone');
               }
         });
      },
});

</script>
@endsection