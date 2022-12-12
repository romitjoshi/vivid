@extends('layouts/contentLayoutMaster')

@section('title', 'Comic View')

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
                              src="{{ env('IMAGE_PATH_medium') }}{{ $comicInfo->featured_image }}"
                              height="70"
                              width="80"
                              alt="User avatar"
                              />
                          <div class="user-info text-center">
                              <h4>{{ $comicInfo->name ?? ""}}</h4>
                          </div>
                        </div>
                    </div>
                    <h4 class="fw-bolder border-bottom pb-50 mb-1"></h4>
                    <div class="info-container">
                        <ul class="list-unstyled">
                          <li class="mb-75">
                                <span class="fw-bolder me-25">Status:</span>
                                @php
                                if($comicInfo->status == 1)
                                {
                                    $status = '<span class="badge bg-light-success">Active</span>';
                                }
                                else if($comicInfo->status == 0)
                                {
                                    $status = '<span class="badge bg-light-danger">Inactive</span>';
                                }
                                @endphp
                                {!! $status !!}
                            </li>
                          <li class="mb-75">
                              <span class="fw-bolder me-25">Access Type:</span>
                              @php
                              if($comicInfo->access_type == 3)
                              {
                                $access_type = '<span class="badge badge-light-success">Original</span>';
                              }
                              else if($comicInfo->access_type == 2)
                              {
                                $access_type = '<span class="badge badge-light-primary">Premium</span>';
                              }
                              else
                              {
                                $access_type = '<span class="badge badge-light-danger">Free</span>';
                              }
                              @endphp
                              {!! $access_type !!}
                          </li>
                          <li class="mb-75">
                              <span class="fw-bolder me-25">Featured:</span>
                              @php
                              if($comicInfo->is_featured == 2)
                              {
                                $isFeatured = '<span class="badge badge-light-success">Yes</span>';
                              }
                              else{
                                $isFeatured = '<span class="badge badge-light-danger">No</span>';
                              }
                              @endphp
                              {!! $isFeatured !!}
                          </li>
                          <li class="mb-75">
                              <span class="fw-bolder me-25">Rating:</span>
                              <span>{{ $avgSum?? ""}} [{{$countavg ?? ""}}] </span>
                          </li>

                          <li class="mb-75">
                              <span class="fw-bolder me-25">Description:</span>
                              <span>{{ $comicInfo->description ?? ""}}</span>
                          </li>
                        </ul>
                        <div class="d-flex justify-content-center pt-2">
                          <a href="#" class="btn btn-primary me-1" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update">Edit</a>
                        </div>
                    </div>
                  </div>
              </div>
              <!-- /User Card -->
            </div>
            <!--/ User Sidebar -->
            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
              <!-- User Pills -->
              <!-- <ul class="nav nav-pills mb-2">
                  <li class="nav-item">
                    <a class="nav-link active" href="#">
                    <i data-feather="user" class="font-medium-3 me-50"></i>
                    <span class="fw-bold">Episodes</span></a
                        >
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                    <i data-feather="lock" class="font-medium-3 me-50"></i>
                    <span class="fw-bold">Other</span>
                    </a>
                  </li>
              </ul> -->
              <div class="card">
                  <div class="card-header">
                    <h4 class="card-title mb-50">Episode List</h4>
                    <a href="{{ url('/admin/add-episode').'/'.$id}}" class="btn btn-primary waves-effect waves-float waves-light"><i data-feather="plus" class="font-medium-1 me-50"></i>Add</a>
                  </div>
                  <div class="table-responsive padding-left-right">
                      <table class="datatables-comic table displayData">
                        <thead>
                          <tr>
                            <th>id</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>impression</th>
                            <th>Description</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                      </table>
                  </div>
              </div>
            </div>
        </div>
      </section>
  </div>
</div>


 <!-- Modal to Update Series -->
 <div class="modal modal-slide-in fade" id="modals-slide-in-update">
    <div class="modal-dialog sidebar-sm">
     <form class="add-new-record modal-content pt-0" id="updateForm">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title" id="exampleModalLabel">Update Comic</h5>
        </div>
        <input type="hidden" name="updateid" id="updateid" value="{{ $comicInfo->id ?? ''}}">
        <input type="hidden" id="page_refresh" value="1">
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label class="form-label">Name</label>
            <input type="text" class="form-control dt-full-name" name="name" value="{{ $comicInfo->name ?? ''}}" placeholder="Name" />
          </div>
          <div class="mb-1">
          <label class="form-label">Featured</label>
           <select class="form-control" name="is_featured" required>
                  <option value="">Select Is featured </option>
                  <option value="1" @if($comicInfo->is_featured == 1) selected @endif>No</option>
                  <option value="2" @if($comicInfo->is_featured == 2) selected @endif>Yes</option>
             </select>
          </div>
          <div class="mb-1">
          <label class="form-label">Status</label>
           <select class="form-control" name="status" required>
                  <option value="">Select Status </option>
                  <option value="1" @if($comicInfo->status == 1) selected @endif>Active</option>
                  <option value="0" @if($comicInfo->status == 0) selected @endif>Inactive</option>
             </select>
          </div>
          <div class="mb-1">
          <label class="form-label">Category [Search Category name]</label>
            <select class="form-control select2 form-select"  name="category[]" multiple>
                @php
                $autoIn = 0;
                foreach($comicCatid as $cid)
                {
                  echo '<option value="'.$cid.'" selected>'.$getCatName[$autoIn].'</option>';
                  $autoIn++;
                }

                @endphp
            </select>
          </div>
          <div class="mb-1">
                <label class="form-label">Access Type</label>
                <select class="form-select" name="access_type" id="access_type">
                  <option value="" disabled selected>Select</option>
                  <option value="1"@if($comicInfo->access_type == 1) selected @endif>Free</option>
                  <option value="2"@if($comicInfo->access_type == 2) selected @endif>Paid</option>
                  <option value="3"@if($comicInfo->access_type == 3) selected @endif>Original</option>
                </select>
          </div>
          <div class="mb-1">
            <label class="form-label">Description</label>
            <textarea class="form-control" id="description" rows="6" placeholder="" name="description">{{ $comicInfo->description ?? ''}}</textarea>
          </div>

          <div class="mb-1">
              <label class="form-label">Featured Image[ Recommended size 800 * 1200 ]</label>
              <input type="file" class="form-control dt-full-name" id="featured_image" name="featured_image" placeholder="featured_image" accept="image/png, image/gif, image/jpeg, image/jpg" />
              <div class="previewImageSection">
                  <img src="{{ env('IMAGE_PATH_medium') }}{{ $comicInfo->featured_image }}" class="img-fluid rounded" alt="avatar img">
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
          <input type="hidden" name="action" value="adminSide" >
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
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
  <script src="{{ asset('js/custom/admin/episode.js') }}"></script>
@endsection
