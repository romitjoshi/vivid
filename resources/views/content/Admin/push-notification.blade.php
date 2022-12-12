@extends('layouts/contentLayoutMaster')

@section('title', 'Notification')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')) }}">

  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
<link rel="stylesheet" href="{{ URL::asset('js/custom/library/toastr.min.css') }}">
 <link rel="stylesheet" type="text/css" href="{{ URL::asset('js/custom/library/daterangepicker.css'); }}" />
 <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-pickadate.css')) }}">
@endsection
@section('content')
<style>
    @media(min-width:768px)
    {
        .modal-slide-in .modal-dialog.sidebar-sm {
        width: 30% !important;
        }
    }
</style>
<!-- Basic table -->
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <table class="notificationDataTable table">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Last updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Add -->

    <div class="modal modal-slide-in fade" id="modals-slide-in">
        <div class="modal-dialog sidebar-sm">
            <form class="add-new-record modal-content pt-0" id="insertForm" name="insertForm">
                @csrf
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                <div class="modal-header mb-1">
                    <h5 class="modal-title">Add New Notification</h5>
                </div>
                <div class="modal-body flex-grow-1">

                    <div class="mb-1">
                        <h5 class="modal-title">Push Type</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                    <div class="mb-1">
                                        <label class="form-label">Type</label>
                                        <select class="form-select pushType" name="pushType" >
                                            <option selected="" disabled="" value="">Select Option</option>
                                            <option value="1">Instant</option>
                                            <option value="2">Scheduled</option>
                                        </select>
                                    </div>
                                <div class="d-none pushDateTime">
                                    <div class="mb-1 position-relative">
                                        <label class="form-label" for="pd-default">Select Date Time</label>
                                        <input type="text" class="form-control flatpickr-date-time-new flatpickr-input" name="pushDateTime"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="mb-1 mt-2">
                        <h5 class="modal-title">Description Info</h5>
                    </div>
                    <div class="row">
                        <div class="mb-1">
                            <label class="form-label" >Title</label>
                            <input type="text" class="form-control dt-full-name" name="push_title" placeholder="Title" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" >Description</label>
                            <textarea type="text" name="push_description" class="form-control" placeholder="Description" required>
                            </textarea>
                        </div>
                    </div>

                    <div class="mb-1 mt-2">
                        <h5 class="modal-title">User By</h5>
                    </div>
                    <div class="row">
                            <div class="mb-1">
                                  <label class="form-label">User</label>
                                    <select class="form-select user" name="user" >
                                        <option selected="" disabled="" value="">Select User</option>
                                        <option value="1">All</option>
                                        <option value="2">Inactive Users</option>
                                    </select>
                            </div>
                        <div class="d-none selectUserType">
                             <div class="mb-1">
                                <label class="form-label">Select Number of inactive days</label>
                                <input type="text" name="inactiveDays" class="form-control preventCharactersAndDot" placeholder="Select Number of inactive days" />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                       <div class="col-12">
                       <!-- <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button> -->
                            <button type="submit" class="btn btn-primary data-submit me-1" style="width:42%"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="width:42%">Cancel</button>
                        </div>
                    </div>


                </div>
            </form>
        </div>
    </div>

    <!---Update--->
    <div class="modal modal-slide-in fade" id="modals-slide-in-update">
        <div class="modal-dialog sidebar-sm">
            <form class="add-new-record modal-content pt-0" id="updateForm" name="updateForm">
                <input type="hidden" id="updatedId" name="updatedId" value="">
                @csrf
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                <div class="modal-header mb-1">
                    <h5 class="modal-title" id="pushHeading">Update Notification</h5>
                </div>
                <div class="modal-body flex-grow-1">

                    <div class="mb-1">
                        <h5 class="modal-title">Push Type</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                    <div class="mb-1">
                                        <label class="form-label">Type</label>
                                        <select class="form-select pushType" id="send_type" name="pushType" >
                                            <option selected="" disabled="" value="">Select Option</option>
                                            <option value="1">Instant</option>
                                            <option value="2">Scheduled</option>
                                        </select>
                                    </div>
                                <div class="d-none pushDateTime">
                                    <div class="mb-1 position-relative">
                                        <label class="form-label" for="pd-default">Select Date Time</label>
                                        <input type="text" class="form-control flatpickr-date-time-new flatpickr-input" id="send_datetime" name="pushDateTime"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="mb-1 mt-2">
                        <h5 class="modal-title">Description Info</h5>
                    </div>
                    <div class="row">
                        <div class="mb-1">
                            <label class="form-label" >Title</label>
                            <input type="text" class="form-control dt-full-name" id="push_title" name="push_title" placeholder="Title" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" >Description</label>
                            <textarea type="text" name="push_description" id="push_description" class="form-control" placeholder="Description">
                            </textarea>
                        </div>
                    </div>

                    <div class="mb-1 mt-2">
                        <h5 class="modal-title">User By</h5>
                    </div>
                    <div class="row">
                            <div class="mb-1">
                                  <label class="form-label">User</label>
                                    <select class="form-select user" id="user" name="user" >
                                        <option selected="" disabled="" value="">Select User</option>
                                        <option value="1">All</option>
                                        <option value="2">Inactive Users</option>
                                    </select>
                            </div>
                        <div class="d-none selectUserType">
                             <div class="mb-1">
                                <label class="form-label">Select Number of inactive days</label>
                                <input type="text" name="inactiveDays" class="form-control preventCharactersAndDot" id="inactiveDays" placeholder="Select Number of inactive days" />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2" id="btnSection">
                       <div class="col-12">
                            <button type="submit" class="btn btn-primary data-submit me-1" style="width:42%">Update</button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="width:42%">Cancel</button>
                        </div>
                    </div>


                </div>
            </form>
        </div>
    </div>

</section>
<!--/ Basic table -->




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
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>


<script src="{{ URL::asset('js/custom/library/toastr.min.js') }}"></script>
@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
<script src="{{ URL::asset('js/custom/admin/pushNotification.js') }}"></script>
@endsection