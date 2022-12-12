@extends('layouts/contentLayoutMaster')
@section('title', 'User View')
@section('vendor-style')
  <link rel="stylesheet" href="{{asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css'))}}">
  <link rel="stylesheet" href="{{asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css'))}}">
  <link rel="stylesheet" href="{{asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-invoice-list.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-user.css'))}}">
  <link rel="stylesheet" href="{{ URL::asset('js/custom/library/toastr.min.css') }}">
@endsection
@section('content')
<div class="content-wrapper ">
  <div class="content-body">
      <section class="app-user-view-account">
        <div class="row">
            <!-- User Sidebar -->
              <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                  <!-- User Card -->
                  <div class="card">
                      <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                              <img
                                  class="img-fluid rounded mt-3 mb-2"
                                  src="{{ asset('images/avatars/userdummy.png') }}"
                                  height="80"
                                  width="80"
                                  alt="User avatar"
                                  />
                              <div class="user-info text-center">
                                  <h4></h4>
                              </div>
                            </div>
                        </div>
                        <h4 class="fw-bolder border-bottom pb-50 mb-1"></h4>
                        <div class="info-container">
                            <ul class="list-unstyled">
                              <li class="mb-75">
                                  <span class="fw-bolder me-25">Username:</span>
                                  <span>{{$getData->name ?? ''}}</span>
                              </li>
                              <li class="mb-75">
                                  <span class="fw-bolder me-25">Email:</span>
                                  <span>{{$getData->email ?? ''}}</span>
                              </li>

                              <li class="mb-75">
                                  <span class="fw-bolder me-25">Status:</span>
                                  <!-- "status" =>($singleRow->status == 1 ) ? '<span class="badge badge-light-success">Active</span>' : '<span class="badge badge-light-danger">In Active</span>',    -->

                                  <!-- <span class="badge bg-light-success">{{$getData->status }} </span> -->
                                  @if($getData->status==1)
                                  <span class="badge badge-light-success">Active</span>
                                  @else
                                  <span class="badge badge-light-danger">In Active</span>
                                  @endif
                              </li>
                              <li class="mb-75">
                                  <span class="fw-bolder me-25">DOB:</span>
                                  <span> {!! date('d-M-y', strtotime($getData->getUserDetails['dob'])) !!} </span>
                              </li>
                              <li class="mb-75">
                              @if(!empty($getData->getUserSubscriptionDetails->subscription_type))
                                  <span class="fw-bolder me-25">Subscription Type:</span>
                                  {!! Helper::subscriptionType($getData->getUserSubscriptionDetails->subscription_type) !!}
                              </li>
                              @endif
                              <li class="mb-75">
                              @if(!empty($getData->getUserSubscriptionDetails->current_period_start))
                                  <span class="fw-bolder me-25">Subscription Start Date:</span>
                                  <span>{{$getData->getUserSubscriptionDetails->current_period_start}} </span>
                                  @endif
                              </li>

                              <li class="mb-75">
                              @if(!empty($getData->getUserSubscriptionDetails->current_period_end))
                                  <span class="fw-bolder me-25">Subscription Renew Date:</span>
                                  <span>{{$getData->getUserSubscriptionDetails->current_period_end}} </span>
                                  @endif
                              </li>
                            </ul>
                            <!-- <div class="d-flex justify-content-center pt-2">
                              <a href="javascript:;" class="btn btn-primary me-1" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update">Edit</a>
                            </div> -->
                        </div>
                      </div>
                  </div>
                  <!-- /User Card -->
              </div>
            <!--/ User Sidebar -->
            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1 ">
              <ul class="nav nav-pills mb-2">
                  <li class="nav-item">
                    <a class=" nav-link tabSectionBusiness active" sectionVal="first"  href="#">
                      <i data-feather='refresh-ccw' class="font-medium-3 me-50"></i>
                      <span class="fw-bold">Comics</span>
                    </a>
                  </li>
                  <li class="nav-item notes">
                    <a class=" nav-link tabSectionBusiness" sectionVal="second"  href="#">
                      <i data-feather="file-text" class="font-medium-3 me-50"></i>
                      <span class="fw-bold">Products</span>
                    </a>
                  </li>
                  <li class="nav-item notes">
                    <a class=" nav-link tabSectionBusiness " sectionVal="third"  href="#">
                      <i data-feather="star" class="font-medium-3 me-50"></i>
                      <span class="fw-bold">Ratings</span>
                    </a>
                  </li>
                  <li class="nav-item notes">
                    <a class=" nav-link tabSectionBusiness " sectionVal="fourth"  href="#">
                      <i data-feather="dollar-sign" class="font-medium-3 me-50"></i>
                      <span class="fw-bold">Coin</span>
                    </a>
                  </li>
              </ul>
              <section>
                <div class=" card tabSectionHideBusiness " id="firstSection">
                    <h4 class="card-header">Comics
                      <!-- <button class="btn ripple btn-primary btn-block btnradius add-media float-right"><i data-feather='plus'></i>Generate Summary</button></button> -->
                    </h4>
                      <div class="card-datatable table-responsive p-2">
                          <table class="table displayDataComic" >
                              <thead>
                                  <tr>
                                    <th>id</th>
                                    <th>Comic Name</th>
                                    <th>Episode Name</th>
                                    <th>Page Number</th>
                                    <th>Action</th>
                                  </tr>
                              </thead>
                          </table>
                      </div>
                    </form>
                </div>
              </section>
              <section>
                <div class="card d-none tabSectionHideBusiness" id="secondSection">
                  <div class="row">
                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                      <h4 class="card-header w-100">
                        Products
                        <!-- <a class="btn ripple btn-primary btn-block btnradius add-media float-right" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addNoteModal"><i data-feather='plus'></i>Add Note</a> -->
                      </h4>
                    </div>
                  </div>
                      <div class="card-datatable table-responsive p-2 ">
                        <table class="table displayDataLibrary"  >
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Order Request Id</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
              </section>

              <section>
                <div class="card d-none tabSectionHideBusiness" id="thirdSection">
                  <div class="row">
                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                      <!-- <h4 class="card-header w-100">
                      Reviews
                        <a class="btn ripple btn-primary btn-block btnradius add-media float-right" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addNoteModal"><i data-feather='plus'></i>Add Note</a>
                      </h4> -->
                    </div>
                  </div>
                      <div class="card-datatable table-responsive p-2 ">

                        <table class="getActiveBusinessDeal table displayDataReview" >
                            <thead>
                                <tr>
                                  <th>id</th>
                                  <th>Comic Name </th>
                                  <th>Ratings</th>
                                  <th>Created At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
              </section>

              <section>
                <div class=" card d-none tabSectionHideBusiness" id="fourthSection">
                    <h4 class="card-header">Coins History
                      <button class="btn ripple btn-primary btn-block btnradius add-media float-right" data-bs-toggle="modal" data-bs-target="#modals-slide-add-coins"><i data-feather='plus'></i>Add Coins</button></button>
                    </h4>
                      <div class="card-datatable table-responsive p-2">
                          <table class="table displayCoin" >
                              <thead>
                                  <tr>
                                    <th>id</th>
                                    <th>Date </th>
                                    <th>Coin</th>
                                    <th>description</th>
                                    <th>Transaction</th>
                                  </tr>
                              </thead>
                          </table>
                      </div>
                    </form>
                </div>
              </section>
                <!-- /Project table -->
            </div>
          </div>
            <!--/ User Content -->
        </div>
      </div>
    </section>
  </div>
</div>

<input type="hidden" id="userid" value="{{$getData->id}}">

<!-- Modal to add new record -->
<div class="modal modal-slide-in fade" id="modals-slide-add-coins">
    <div class="modal-dialog sidebar-sm">
    <form class="modal-content pt-0" id="addCoinsFirst">
      <input type="hidden" name="userCoinId" value="{{$getData->id}}">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title" id="exampleModalLabel">Add Coins</h5>
        </div>
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label class="form-label">Coins</label>
            <input type="text" class="form-control" name="coins" placeholder="coins" />
          </div>

          <div class="button-section mt-5">
            <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
</div>



@endsection

@section('vendor-script')
<script src="{{asset(mix('vendors/js/extensions/moment.min.js'))}}"></script>
<script src="{{asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js'))}}"></script>
<script src="{{asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js'))}}"></script>
<script src="{{asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js'))}}"></script>
<script src="{{asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js'))}}"></script>
<script src="{{asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js'))}}"></script>
<script src="{{asset(mix('vendors/js/tables/datatable/buttons.bootstrap5.min.js'))}}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script src="{{ URL::asset('js/custom/library/toastr.min.js') }}"></script>
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
  <script src="{{ asset('js/custom/admin/user.js') }}"></script>
@endsection
