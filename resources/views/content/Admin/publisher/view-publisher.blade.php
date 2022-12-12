@extends('layouts/contentLayoutMaster')
@section('title', 'Publisher View')
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
              <div class="col-xl-4 col-lg-4 col-md-4 order-1 order-md-0">
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
                                  <span>{{$getData->name}}</span>
                              </li>
                              <li class="mb-75">
                                  <span class="fw-bolder me-25">Email:</span>
                                  <span>{{$getData->email}}</span>
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
                              <!-- <li class="mb-75">
                                  <span class="fw-bolder me-25">Dob:</span>
                                  <span> {!! date('d-M-y', strtotime($getData->getUserDetails['dob'])) !!} </span>
                              </li> -->
                              <li class="mb-75">
                                  <span class="fw-bolder me-25">Business Type:</span>
                                  <span>{{$getData->getUserDetails['business_type']}} </span>
                              </li>

                              <!--   -->
                              <li class="mb-75">
                                  <span class="fw-bolder me-25">Mobile Number:</span>
                                  <span>{{$getData->getUserDetails['Phone_number']}} </span>
                              </li>
                              <li class="mb-75">
                                  <span class="fw-bolder me-25">Bussiness Address:</span>
                                  <span>{{$getData->getUserDetails['business_address']}} </span>
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

                            @php
                            $tax_doc = $getData->getUserDetails['tax_doc'];
                            $lic_doc = $getData->getUserDetails['lic_doc'];
                            $is_approve_docs = $getData->getUserDetails['is_approve_docs'];

                            $docSec = 'd-none';
                            $tax_doc_Sec = 'd-none';
                            $lic_doc_Sec = 'd-none';
                            if(!empty($tax_doc) || !empty($lic_doc))
                            {
                              $docSec = '';
                            }
                            if(!empty($tax_doc))
                            {
                              $tax_doc_Sec = '';
                            }
                            if(!empty($lic_doc))
                            {
                              $lic_doc_Sec = '';
                            }


                            @endphp

                            <div class="docsection {{ $docSec }}">
                              <h4 class="fw-bolder border-bottom pb-50 mb-1"></h4>
                              <ul class="list-unstyled">
                                  <li class="mb-75 {{ $lic_doc_Sec }}">
                                      <span class="fw-bolder me-25">Licencing Agreement:</span>
                                      <a href="{{ env('PDF_PATH') }}{{ $getData->getUserDetails['lic_doc'] }}" target="_blank" class="btn btn-primary btn-sm"><i data-feather='eye'></i></a>
                                      <span class="btn btn-primary btn-sm doctype" data-bs-toggle="modal" data-bs-target="#modals-slide-in-lic-docs" doctype="lic_doc"><i data-feather='x'></i> </span>
                                  </li>
                                  <li class="mb-75 {{ $tax_doc_Sec }}">
                                  <span class="fw-bolder me-25">Tax Agreement:</span>
                                      <a href="{{ env('PDF_PATH') }}{{ $getData->getUserDetails['tax_doc']}}" target="_blank" class="btn btn-primary btn-sm"><i data-feather='eye'></i></a>
                                      <span class="btn btn-primary btn-sm doctype" data-bs-toggle="modal" data-bs-target="#modals-slide-in-tax-docs" doctype="tax_doc"><i data-feather='x'></i> </span>
                                  </li>
                              </ul>
                            </div>

                            @if(!empty($tax_doc) && !empty($lic_doc))
                              @if($is_approve_docs == 0)
                              <div class="d-flex justify-content-center pt-2 {{ $docSec }}">
                                <a href="javascript:;" class="btn btn-primary me-1 docStatus" doctype="approve">Approve Document</a>
                              </div>
                              @else
                              <!-- <div class="d-flex justify-content-center pt-2 {{ $docSec }}">
                                <a href="javascript:;" class="btn btn-danger me-1 docStatus" doctype="unapprove">Unapprove Document</a>
                              </div> -->
                              @endif
                            @endif
                        </div>
                      </div>
                  </div>
                  <!-- /User Card -->
              </div>
            <!--/ User Sidebar -->
            <!-- User Content -->
            <div class="col-xl-8 col-lg-8 col-md-8 order-0 order-md-1 ">
              <!-- <ul class="nav nav-pills mb-2">
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
              </ul> -->
              <section>
                <div class=" card tabSectionHideBusiness " id="firstSection">

                    <!-- <h4 class="card-header">Comics -->
                      <!-- <button class="btn ripple btn-primary btn-block btnradius add-media float-right"><i data-feather='plus'></i>Generate Summary</button></button> -->
                   <!-- </h4> -->
                      <div class="card-datatable table-responsive">
                        <div class="col-12">
                          <div class="card">
                            <table class="table displayDataComic" >
                                <thead>
                                    <tr>
                                    <th>id</th>
                                      <th>Image</th>
                                      <th>Name</th>
                                      <th>Rating</th>
                                      <th>Featured</th>
                                      <th>Access Type</th>
                                      <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                          </div>
                        </div>
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
                      <a class="btn ripple btn-primary btn-block btnradius add-media float-right" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addNoteModal"><i data-feather='plus'></i>Add Note</a>
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
                      <h4 class="card-header w-100">
                      Reviews
                        <a class="btn ripple btn-primary btn-block btnradius add-media float-right" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addNoteModal"><i data-feather='plus'></i>Add Note</a>
                      </h4>
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

<!-- Modal to reject Docs -->
<div class="modal modal-slide-in fade" id="modals-slide-in-lic-docs">
    <div class="modal-dialog sidebar-sm">
     <form class="add-new-record modal-content pt-0" id="rejectlicDocsForm">
        <input type="hidden" name="pub_id" value="{{$getData->id}}">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title docsrejectTitle">Reject Licencing Agreement</h5>
        </div>
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
              <label class="form-label">Reject Reason</label>
              <textarea class="form-control" rows="6" placeholder="" name="notes"></textarea>
            </div>
          <div class="button-section mt-5">
            <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal modal-slide-in fade" id="modals-slide-in-tax-docs">
    <div class="modal-dialog sidebar-sm">
     <form class="add-new-record modal-content pt-0" id="rejecttaxDocsForm">
        <input type="hidden" name="pub_id" value="{{$getData->id}}">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title docsrejectTitle">Reject Tax Agreement</h5>
        </div>
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
              <label class="form-label">Reject Reason</label>
              <textarea class="form-control" rows="6" placeholder="" name="notes"></textarea>
            </div>
          <div class="button-section mt-5">
            <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<!-- Modal to Update record -->
<div class="modal modal-slide-in fade" id="modals-slide-in-update">
    <div class="modal-dialog sidebar-sm">
     <form class="add-new-record modal-content pt-0" id="updateForm">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title">Update Comic</h5>
        </div>
        <input type="hidden" name="updateid" id="updateid" value="">
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label class="form-label">Name</label>
            <input type="text" class="form-control dt-full-name" name="name" id="name" placeholder="Name" />
          </div>
          <div class="mb-1">
          <label class="form-label">Is Featured</label>
           <select class="form-control" id="is_featured" name="is_featured" required>
           <option value=""selected disabled>Select</option>
                  <option value="1" >No</option>
                  <option value="2" selected>Yes</option>
             </select>
          </div>
       <div class="mb-1">
          <label class="form-label">Access Type</label>
            <select class="form-select" name="access_type" id="access_type">
              <option value="" selected disabled>Select</option>
              <option value="1">Free</option>
              <option value="2">Premium</option>
            </select>
        </div>
          <div class="mb-1">
          <label class="form-label">Category [Search Category name]</label>
            <select class="form-control select2 form-select"  name="category[]" id="comicCategory" multiple>

            </select>
          </div>
          <div class="mb-1">
            <label class="form-label">Description</label>
            <textarea class="form-control" id="description" rows="6" placeholder="" name="description" aria-invalid="Description"></textarea>
          </div>

          <div class="mb-1">
            <label class="form-label">Cover Image[ Recommended size 800 * 1200 ]</label>
            <input type="file" class="form-control dt-full-name" id="featured_image" name="featured_image" placeholder="featured_image" accept="image/png, image/gif, image/jpeg, image/jpg" />
             <div class="previewImageSection">
                  <img src="" class="img-fluid rounded" id="previewComicImg" alt="avatar img">
              </div>
          </div>
          <div class="button-section mt-5">
            <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Update</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<!--   Modal to delete record -->
<div class="modal fade modal-danger text-start" id="modals-slide-in-delete" tabindex="-1" aria-labelledby="myModalLabel120" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content pt-0" id="deleteForm">
              <input type="hidden" name="deleteid" value="" id="deleteid">
          <div class="modal-content">
          <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel120">Delete Series</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              Are you sure you want to delete this record?
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Delete</button>
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
<script src="{{ URL::asset('js/custom/library/toastr.min.js') }}"></script>
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection
@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
  <script src="{{ asset('js/custom/admin/publisher.js') }}"></script>
@endsection
