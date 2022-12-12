@extends('content.front.layouts.master')
@section('title','My Comic')
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('front/css/publisher.css')}}">
<!-- <section class="container publishertop mt-50">
   <div class="row">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Referral</li>
         </ol>
      </nav>
   </div>
</section> -->
<section class="searchpage bg-dark slidercalss p-10 ">
   <div class="container">
      <div class="row p-10">
         <div class="titile-section ">
            <h3>My Earnings</h3>
             <nav aria-label="breadcrumb">
               <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
                  <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                  <li class="breadcrumb-item active" aria-current="page">My Earnings</li>
               </ol>
            </nav>
         </div>

          <!-- <div class="row mb-5">
            <div class="col-12">
               <div class="card card-stats mb-4 mb-xl-0">
                     <div class="card-body">
                        <p class="text-white">
                           Here you can manage your referrals. Have your readers use your unique referral code to receive commissions on their subscription.
                        </p>
                        <p>
                        @if(!empty($UserDetails->getUserDetails->refer_code))
                           <div class="row refercodestyle">
                              <div class="col-md-4">
                                    <h5 color:white>Refer Code </h5>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-5">
                                 <div class="input-group mb-3 referral_rj">
                                       <input type="text" class="form-control referclass" value="{{ $UserDetails->getUserDetails->refer_link }}"/>
                                          <span class="input-group-text referclass">
                                          <i class="fa fa-copy" aria-hidden="true" id="myItem" data-page_id="{{ $UserDetails->getUserDetails->refer_link }}"></i>
                                       </span>
                                 </div>
                              </div>
                           </div>

                        @endif
                        </p>
                     </div>
               </div>
            </div>
          </div> -->

         <div class="main-content">
            <div class="header bg-gradient-primary pb-8 pt-md-8">
               <div class="container-fluid1">
                  <div class="header-body">
                     <div class="row mb-5">
                        <div class="col-md-12">
                           <div class="titile-section d-flex justify-content-between">
                           <h4 class="mt-2 mb-2">Coins History</h4>
                           </div>

                           <div class="table-section  comic-table">
                              <table class="table table-dark walletTable">
                                 <thead>
                                    <tr>
                                       <th scope="col">Date</th>
                                       <th scope="col">Comic [ Episode name]</th>
                                       <th scope="col">Customer</th>
                                       <th scope="col">Amount</th>
                                    </tr>
                                 </thead>
                                 <tbody>

                                    @foreach($wallet as $wal)
                                    <tr>
                                       <td>{{ $wal->created_at }}</td>
                                       <td>{{ $wal->comic }} [ {{ $wal->episode_name }} ]</td>
                                       <td>{{ Helper::getCustomerName($wal->customer_id) }}</td>
                                       <td>{{ Helper::makeCurrency($wal->amount) }}</td>
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
       <div class="modal-body text-center">
            <form class="modal-content1 pt-0" id="insertForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="modal-body flex-grow-1">
                <div class="mt-2">
                    <label class="form-label">Amount</label>
                    <input type="text" class="form-control dt-full-name preventCharactersAndDot" name="amount" placeholder="Amount" />
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
   $('.walletTable').DataTable({
        order: [[0, 'desc']]

    });

const copyToClipboard = str => {
  const el = document.createElement('textarea');
  el.value = str;
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
};

//const url = ajaxUrl+'/refer/';

document.getElementById('myItem').addEventListener('click', function(e){
 // let myUrl =  url + e.target.dataset.page_id;
  let myUrl =  e.target.dataset.page_id;
  copyToClipboard( myUrl );
  toastr["success"]("Copied Link Successfully", "Success");
});
</script>
@endsection