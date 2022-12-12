@extends('layouts/contentLayoutMaster')

@section('title','Product')

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
        <table class="datatables-comic table displayData">
          <thead>
            <tr>

              <th>id</th>
              <th>Cover Image</th>
              <th>Name</th>
              <th>price</th>
              <th>Special Price</th>
              <th>Available Qty</th>
              <th>Sold Qty</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
 <!-- Modal to Update record -->
  <div class="modal modal-slide-in fade" id="modals-slide-in-update">
    <div class="modal-dialog sidebar-sm">
     <form class="add-new-record modal-content pt-0" id="updateForm">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title" id="exampleModalLabel">Update Product</h5>
        </div>
        <input type="hidden" name="updateid" id="updateid" value="">
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control dt-full-name" name="product_name" id="product_name"  />
          </div>
          <div class="mb-1">
            <label class="form-label">Cover image [ Recommended size 800 * 1200 ]</label>
            <input type="file" class="form-control dt-full-name" name="featured_Image" id="featured_Image"   accept="image/png, image/gif, image/jpeg, image/jpg" />
          </div>
          <div class="mb-1">
            <label class="form-label">Additional Images [ Recommended size 800 * 1200 ]</label>
            <input type="file" class="form-control dt-full-name" name="additional_images" id="additional_images"   accept="image/png, image/gif, image/jpeg, image/jpg" />
          </div>
          <div class="mb-1">
            <label class="form-label">Price</label>
            <input type="number" class="form-control dt-full-name" name="price" id="price"   />
          </div>
          <div class="mb-1">
            <label class="form-label">Special Price</label>
            <input type="text" class="form-control dt-full-name" name="special_price" id="special_price"   />
          </div>
          <div class="mb-1">
            <label class="form-label">Available Qty </label>
            <input type="text" class="form-control dt-full-name" name="available_qty" id="available_qty"   />
          </div>
          <div class="mb-1">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="6" placeholder="" name="description" id="description" aria-invalid="Description"></textarea>
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
  <script src="{{ asset('js/custom/admin/product.js') }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
@endsection
