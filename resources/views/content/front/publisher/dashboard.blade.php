@extends('content.front.layouts.master')
@section('title','My Comic')
@section('content')

@php
$lic_doc = $user->getUserDetails->lic_doc ?? '';
$tax_doc = $user->getUserDetails->tax_doc ?? '';

$lic_notes = $user->getUserDetails->lic_notes ?? '';
$tax_notes = $user->getUserDetails->tax_notes ?? '';


$docSec = 'd-none';
$tax_doc_Sec = 'd-none';
$lic_doc_Sec = 'd-none';
if(empty($tax_doc) || empty($lic_doc))
{
   $docSec = '';
}
if(empty($tax_doc))
{
   $tax_doc_Sec = '';
}
if(empty($lic_doc))
{
   $lic_doc_Sec = '';
}


@endphp
<style>
.alert-danger {
    padding: 11px;
    margin-left: 12px;
    margin-top: 10px;
}
p.note_par {
    font-weight: 400;
    margin-top: 10px;
}
</style>
<link rel="stylesheet" type="text/css" href="{{asset('front/css/publisher.css')}}">

<section class="searchpage bg-dark slidercalss  ">
   <div class="container">
      <div class="row p-10">
         <h3 >Publisher Portal</h3>
            <!-- <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li>
               <li class="breadcrumb-item active" aria-current="page">Publisher Portal</li>

            </ol>
         </nav> -->
         <div class="main-content">
            <div class="header bg-gradient-primary pb-8 pt-md-8">
               <div class="container-fluid1">
                  <div class="header-body">

                     <div class="card {{ $docSec }}">
                           <div class="card-header">
                              <h4 class="card-title"> Upload the Following Documents to create a comic:</h4>
                           </div>
                           <div class="card-body">
                              <p class="card-text text-white">First you need to download the Licensing agreement and Tax agreement pdf by clicking the download button and after it is downloaded, you need to sign both the document and then re-upload the signed document for submission and once the document is approved from Admin then you can start creating your comic and if rejected, you need to again upload the signed document.
                              </p>
                              <form method="post" class="uploadDocumentForm">
                                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                 <div class="row gy-2">
                                    <div class="col-12 {{ $lic_doc_Sec }}">
                                       <div class="bg-light-secondary position-relative rounded p-2">

                                          <div class="d-flex align-items-center flex-wrap">
                                             <h4 class="mb-1 me-1">Licencing Agreement</h4>
                                             <span class="badge badge-light-primary mb-1"><a class="downloadanc" href="{{ asset('/front/docs/Comic_Book_Digital_Distribution_Agreement.docx.pdf') }}" download>Download</a></span>
                                          </div>
                                          <h6 class="row fw-bolder updocmain">
                                             <div class="col-md-7 d-flex align-items-center">
                                                <span class="text-white updoc">Upload Document</span>
                                                <span class="me-50 docmargin updocimg">
                                                   <input class="form-control" type="file" name="lic_doc" id="lic_doc" accept="application/pdf" >
                                                </span>
                                             </div>

                                             @if(!empty($lic_notes))
                                             <div class="col-md-5 p-0">
                                                <div class="alert alert-danger">
                                                   <strong>Reject Reason</strong>
                                                   <p class="note_par">{{ $lic_notes ?? ''}}</p>
                                                </div>
                                             </div>
                                             @endif



                                          </h6>

                                       </div>
                                    </div>
                                    <div class="col-12 {{ $tax_doc_Sec }}">
                                       <div class="bg-light-secondary position-relative rounded p-2">

                                          <div class="d-flex align-items-center flex-wrap">
                                          <h4 class="mb-1 me-1">Tax Agreement</h4>
                                          <span class="badge badge-light-primary mb-1"><a class="downloadanc" href="{{ asset('/front/docs/W-9_2022.pdf') }}" download>Download</a></span>
                                          </div>
                                          <h6 class="row align-items-center fw-bolder updocmain">
                                             <div class="col-md-7 d-flex align-items-center">
                                                <span class="text-white updoc">Upload Document</span>
                                                <span class="me-50 docmargin updocimg">
                                                   <input type="file" class="form-control " name="tax_doc" id="tax_doc" accept="application/pdf">
                                                </span>
                                             </div>

                                             @if(!empty($tax_notes))
                                             <div class="col-md-5 p-0 {{ $tax_doc_Sec }}">
                                                <div class="alert alert-danger">
                                                   <strong >Reject Reason</strong>
                                                   <p class="note_par">{{ $tax_notes ?? ''}}</p>
                                                </div>
                                             </div>
                                             @endif



                                          </h6>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary float-start">
                                       <div class="spinner-border text-light importloader d-none" role="status"></div>
                                       Submit Document
                                    </button>
                                 </div>
                              </form>
                           </div>
                        </div>






                     <div class="row mt-5">

                        <div class="col-xl-3 col-lg-6">
                           <div class="card1 card-stats mb-4 mb-xl-0">
                            <a class="dashboardclass" href="{{ route('my-comic') }}">
                              <div class="card-body">
                                  <div class="row">
                                    <div class="col">
                                       <h5 class="card-title text-uppercase text-muted mb-0 fff mb-4">Comics</h5>
                                       <span class="h2 font-weight-bold mb-0 fff">{{ $comicCount ?? ''}}</span>
                                    </div>
                                    <div class="col-auto">
                                       <div class="icon icon-shape bg-primary1 text-white rounded-circle shadow dashIcon">
                                          <i class="fas fa-book" aria-hidden="true"></i>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              </a>
                           </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                           <div class="card1 card-stats mb-4 mb-xl-0">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col">
                                       <h5 class="card-title text-uppercase text-muted mb-0 fff mb-4">Views</h5>
                                       <span class="h2 font-weight-bold mb-0 fff">{{ $viewsCount ?? ''}}</span>
                                    </div>
                                    <div class="col-auto">
                                       <div class="icon icon-shape bg-primary1 text-white rounded-circle shadow dashIcon">
                                          <i class="fas fa-eye"></i>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                           <div class="card1 card-stats mb-4 mb-xl-0">
                              <a class="dashboardclass" href="{{ route('my-wallet') }}">
                                 <div class="card-body">
                                    <div class="row">
                                       <div class="col">
                                          <h5 class="card-title text-uppercase text-muted mb-0 fff mb-4">Wallet</h5>
                                          <span class="h2 font-weight-bold mb-0 fff">{{ Helper::makeCurrency($wallet) ?? ''}}</span>
                                       </div>
                                       <div class="col-auto">
                                          <div class="icon icon-shape bg-primary1 text-white rounded-circle shadow dashIcon">
                                             <i class="fas fa-usd"></i>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </a>
                           </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
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
                  </div>
               </div>
            </div>
            <!-- Page content -->
         </div>
      </div>
      <div class="row mt-5 mb-5 mtcustom5">
         <div class="main-content">
            <div class="header bg-gradient-primary pb-8 pt-md-8">
               <div class="container-fluid1">
                  <div class="header-body">
                     <div class="row">
                        <div class="col-md-6">
                           <h4 class="mt-2 mb-2">Top Comics</h4>
                           <div class="table-section">
                              <table class="table table-dark">
                                 <thead>
                                    <tr>
                                       <th scope="col">Name</th>
                                       <th scope="col">Episode</th>
                                       <th scope="col">Views</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach($topComics as $top)
                                    <tr>
                                       <td>{{ $top->comic_name }}</td>
                                       <td>{{ $top->episode_name }}</td>
                                       <td>{{ $top->view }}</td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="heading-sec d-flex justify-content-between seemore">
                              <h4 class="mt-2 mb-2">Last 5 Withdrawals</h4>
                              <a class="seeallLink mt-2" href="{{ route('my-wallet') }}">See withdrawal history</a>
                           </div>
                           <div class="table-section">
                              <table class="table table-dark">
                                 <thead>
                                    <tr>
                                       <th scope="col">Date</th>
                                       <th scope="col">Amount</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach($payoutRequest as $req)
                                    <tr>
                                       <td>{{ $req->created_at }}</td>
                                       <td>{{ Helper::makeCurrency($req->amount) }}</td>
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

@endsection
@section('scripts')
<script>
$(".uploadDocumentForm").validate({
        rules: {
            'lic_doc': {
                required: true,
            },
            'tax_doc': {
                required: true,
            },
        },
         submitHandler: function (form) {
            var thisForm = $(this);
            $(".uploadDocumentForm").find('.importloader').removeClass('d-none');
            var formData = new FormData($(".uploadDocumentForm").get(0));
            $.ajax({
               'url':"{{route('upload-document')}}",
               'type': "POST",
               'dataType': "json",
               'data': formData,
               'processData':false,
               'contentType':false,
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
</script>
@endsection