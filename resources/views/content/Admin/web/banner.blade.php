
@extends('layouts/contentLayoutMaster')

@section('title','Banner')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  @endsection


@section('page-style')
<link rel="stylesheet" href="{{ URL::asset('js/custom/library/toastr.min.css') }}">
@endsection
@section('content')
<!-- Basic table -->
<section id="basic-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <table class="datatables-banner table displayData">
          <thead>
            <tr>
              <th>id</th>
              <th>Image</th>
              <th>status</th>
              <th>Display ordering</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

<!-- Modal to add new record -->
  <div class="modal modal-slide-in fade" id="modals-slide-create">
    <div class="modal-dialog sidebar-sm">
    <form class="modal-content pt-0" id="insertForm">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title" id="exampleModalLabel">Create Banner</h5>
        </div>
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label class="form-label">Web image [Recommended size 1920 x 450 pixels]</label>
            <input type="file" class="form-control dt-full-name" name="image"  accept="image/png, image/gif, image/jpeg, image/jpg" />
          </div>
          <div class="mb-1">
            <label class="form-label">Mobile image [Recommended size 500 x 200 pixels]</label>
            <input type="file" class="form-control dt-full-name" name="mobile_banner"  accept="image/png, image/gif, image/jpeg, image/jpg" />
          </div>
          <div class="mb-1">
          <label class="form-label">Status</label>
           <select class="form-control"  name="status" required>
                  <option value=""selected disabled>Select</option>
                  <option value="2" >InActive</option>
                  <option value="1" >Active</option>
             </select>
          </div>
          <div class="mb-1">
            <label class="form-label">Ordering</label>
            <input type="text" class="form-control dt-full-name preventCharactersAndDot" name="order">
          </div>
          <div class="mb-1">
            <label class="form-label">Name</label>
            <input type="text" class="form-control dt-full-name " name="name">
          </div>
          <div class="mb-1">
            <label class="form-label">Link</label>
            <input type="text" class="form-control dt-full-name" name="link">
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
          <h5 class="modal-title" id="exampleModalLabel">Update Banner</h5>
        </div>
        <input type="hidden" name="updateid" id="updateid" value="">
        <div class="modal-body flex-grow-1">

          <div class="mb-1">
          <label class="form-label">Status</label>
           <select class="form-control"  name="status" id="status" required>
                  <option value=""selected disabled>Select</option>
                  <option value="2" >InActive</option>
                  <option value="1" >Active</option>
             </select>
          </div>
          <div class="mb-1">
            <label class="form-label">Ordering</label>
            <input type="text" class="form-control dt-full-name preventCharactersAndDot" id="order" name="order">
          </div>
          <div class="mb-1">
            <label class="form-label">Name</label>
            <input type="text" class="form-control dt-full-name" id="name" name="name">
          </div>
          <div class="mb-1">
            <label class="form-label">Link</label>
            <input type="text" class="form-control dt-full-name" id="link" name="link">
          </div>
          <div class="mb-1">
            <label class="form-label">Web image [Recommended size 1920 x 450 pixels]</label>
            <input type="file" class="form-control dt-full-name" name="image" id="image" accept="image/png, image/gif, image/jpeg, image/jpg" />
            <div class="previewImageSection">
                  <img src="" class="img-fluid rounded" id="previewBannerImg">
              </div>
          </div>
          <div class="mb-1">
            <label class="form-label">Mobile image[Recommended size 500 x 200 pixels]</label>
            <input type="file" class="form-control dt-full-name" name="mobile_banner" id="mobile_banner" accept="image/png, image/gif, image/jpeg, image/jpg" />
            <div class="previewImageSection">
                  <img src="" class="img-fluid rounded" id="previewMobileImg">
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
</section>


@endsection


@section('vendor-script')
  {{-- vendor files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
  <script src="{{ URL::asset('js/custom/library/toastr.min.js') }}"></script>
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')

  <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/banner.js') }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
@endsection
