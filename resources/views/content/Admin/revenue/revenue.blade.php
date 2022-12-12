
@extends('layouts/contentLayoutMaster')

@section('title', 'Revenue')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
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
        <table class="datatables-business table displayData">
          <thead>
            <tr>
              <th>Id</th>
              <th>Date</th>
              <th>Price</th>
              <th>Customer Name </th>
              <th>Type</th>
              <th>Source</th>
            </tr>
          </thead>
        </table>
      </div>
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
@endsection
@section('page-script')
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
<script>

$(document).ready(function(){
  $(function() {
    var dt_ajax_table = $('.displayData');
    if (dt_ajax_table.length) {
          var dt_ajax = dt_ajax_table.dataTable({
              ajax: {
                  url: ajaxUrl + "/admin/get-revenue",
                  method: "get",
                  dataType: "json",
                  cache: false,
              },
              processing: true,
              serverSide: true,
              columns: [
                {
                    data: "id"
                },
                {
                    data: "created_at"
                },
                {
                    data: "price"
                },
                {
                    data: "name"
                },
                {
                    data: "subscription_type"
                },
                {
                    data: "source"
                },


            ],
            order: [
                [0, "desc"]
            ],
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }, { "bSortable": false, "aTargets": [ 0, 5] },],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: [{
                extend: "collection",
                className: "btn btn-outline-secondary dropdown-toggle me-2",
                text: feather.icons["share"].toSvg({
                    class: "font-small-4 me-50",
                }) + "Export",
                buttons: [{
                        extend: "csv",
                        text: feather.icons["file-text"].toSvg({
                            class: "font-small-4 me-50",
                        }) + "Csv",
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5],
                        },
                        "action": newexportaction,
                    },
                    {
                        extend: "excel",
                        text: feather.icons["file"].toSvg({
                            class: "font-small-4 me-50",
                        }) + "Excel",
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5],
                        },
                        "action": newexportaction,
                    },
                ],
            },
            ],

            dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    // remove previous & next text from pagination
                    previous: "&nbsp;",
                    next: "&nbsp;",
                },
            },
          });
          $("div.head-label").html('<h6 class="mb-0">Total Number of Revenue</h6>');
    }
    });
});

</script>
@endsection
