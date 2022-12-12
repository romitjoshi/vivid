
@extends('layouts/contentLayoutMaster')

@section('title','Unapproved Comics')

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
<style>
    .dt-buttons {
    display: none;
}
</style>
@endsection
@section('content')
<!-- Basic table -->
<section id="basic-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <table class="datatables-comic table displayPendingComic">
          <thead>
            <tr>
              <th>id</th>
              <th>Image</th>
              <th>Name</th>
              <th>Publisher Name</th>
              <th>Description</th>
              <th>Approve Status</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

<!-- Modal to add new record -->
  <!-- <div class="modal modal-slide-in fade" id="modals-slide-create">
    <div class="modal-dialog sidebar-sm">
    <form class="modal-content pt-0" id="insertForm">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title" id="exampleModalLabel">Create Comic</h5>
        </div>
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label class="form-label">Name</label>
            <input type="text" class="form-control dt-full-name" name="name" placeholder="Name" />
          </div>
          <div class="mb-1">
            <label class="form-label">Cover image</label>
            <input type="file" class="form-control dt-full-name" name="featured_image" placeholder="featured_image" accept="image/png, image/gif, image/jpeg, image/jpg" />
          </div>
          <div class="mb-1">
          <label class="form-label">Is Featured</label>
           <select class="form-control"  name="is_featured" required>
                  <option value=""selected disabled>Select</option>
                  <option value="2" >Yes</option>
                  <option value="1" >No</option>
             </select>
          </div>

          <div class="mb-1">
                <label class="form-label">Access Type</label>
                <select class="form-select" name="access_type">
                  <option value="" disabled selected>Select</option>
                  <option value="1">Free</option>
                  <option value="2">Premium</option>
                </select>
            </div>

          <div class="mb-1">
          <label class="form-label">Category [Search Category name]</label>
            <select class="form-control select2 form-select"  name="category[]" multiple>

            </select>
          </div>
          <div class="mb-1">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="6" placeholder="" name="description" aria-invalid="Description"></textarea>
          </div>
          <div class="button-section mt-5">
            <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div> -->
 <!-- Modal to Update record -->
  <div class="modal modal-slide-in fade" id="modals-slide-in-update">
    <div class="modal-dialog sidebar-sm">
     <form class="add-new-record modal-content pt-0" id="updateForm">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
        <div class="modal-header mb-1">
          <h5 class="modal-title" id="exampleModalLabel">Update Comic</h5>
        </div>
        <input type="hidden" name="updateid" id="updateid" value="">
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label class="form-label">Name</label>
            <input type="text" class="form-control dt-full-name" name="name" id="name" placeholder="Name" />
          </div>
          <div class="mb-1">
            <label class="form-label">Approve Comic</label>
            <select class="form-control" id="approve" name="approve" required>
            <option value=""selected disabled>Select</option>
                  <option value="1" selected>Yes</option>
                  <option value="0">No</option>
             </select>
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
              <option value="3">Original</option>
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

          <div class="row">
           <h5 class="mt-1">Episode V Coin</h5>
                       <div class="col-md-6">
                          <div class="mb-1">
                            <label class="form-label">Charge Coin For Free User</label>
                            <input
                              type="text"
                              class="form-control dt-full-name preventCharactersAndDot"
                              name="charge_coin_free_user" required/>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="mb-1">
                            <label class="form-label" >Charge Coin For Paid User</label>
                            <input
                              type="text"
                              name="charge_coin_paid_user"
                              class="form-control dt-full-name preventCharactersAndDot" required/>
                          </div>
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
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('page-script')

  <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/comic.js') }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
@endsection
