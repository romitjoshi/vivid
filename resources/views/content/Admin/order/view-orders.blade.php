
@extends('layouts/contentLayoutMaster')

@section('title', 'Order Details')

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
<link rel="stylesheet" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" href="{{asset('css/base/pages/app-invoice.css')}}">
<link rel="stylesheet" href="{{ URL::asset('js/custom/library/toastr.min.css') }}">
<style>
div#DataTables_Table_0_info {
    display: none;
  }
div#DataTables_Table_0_length {
    display: none;
  }

.card-header.border-bottom.p-1 {
    display: none;
}
</style>
@endsection

@section('content')
<section class="invoice-preview-wrapper">
  <div class="row invoice-preview">
    <div class="col-xl-8 col-md-8 col-12">
      <div class="card invoice-preview-card">
        <div class="card-body invoice-padding pb-0">
          <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
            <div>
              <h6 class="mb-2">Address:</h6>
              @php
                  $addressJs=json_decode($getData->delivery_details);
                  @endphp
                  @foreach($addressJs as $pic)
                    <p class="card-text mb-25"> {{ $pic ?? '' }}</p>
                  @endforeach
            </div>
            <div class="mt-md-0 mt-2">
              <div class="invoice-date-wrapper">
                <p class="invoice-date-title">Order Id:</p>
                <p class="invoice-date"> #{{ $getData->order_request_id ?? ""}} </p>
              </div>
              <div class="invoice-date-wrapper">
                <p class="invoice-date-title">Order Date:</p>
                <p class="invoice-date"> {{ $getData->created_at ?? ""}} </p>
              </div>
              <!-- <h6 class="mb-1">Payment Details:</h6>
              <table>
                <tbody>
                  <tr>
                    <td class="pe-1">Trnansaction Id:</td>
                    <td><span class="fw-bold">{{ $getData->transaction_id ?? ""}}</span></td>
                  </tr>
                </tbody>
              </table> -->
            </div>
          </div>
        </div>
        <!-- Invoice Description starts -->
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th class="py-1">Product description</th>
                <th class="py-1">Cost</th>
                <th class="py-1">Qty</th>
                <th class="py-1">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                @foreach($getData->getOrderProductDetails as $getproduct)
                <td class="py-1">
                  <p class="card-text fw-bold mb-25">{{   $getproduct->product_name ?? '' }}</p>
                  @if(!empty($getproduct['comic_series_id']))
                                 <div class="">
                                 <p class="">Custom Title : {{ $getproduct['custom_title_name'] ?? ''}}</p>
                                 @php
                                 $comicId = json_decode($getproduct['comic_series_id']);
                                 $nmbr = 1;
                                 foreach($comicId as $cd)
                                 {
                                    $comicName = Helper::getComicNameById($cd);
                                    echo '<p>Comic '.$nmbr.': '.$comicName.'</p>';
                                    $nmbr++;
                                 }
                                 @endphp

                              </div>
                              @endif

                </td>
                <td class="py-1">
                  <span class="fw-bold">  {{ CURRENCYSYMBOL.$getproduct->price ?? '' }}</span>
                </td>
                <td class="py-1">
                  <span class="fw-bold">{{ $getproduct->qty ?? '' }}</span>
                </td>
                <td class="py-1">
                  <span class="fw-bold">{{ CURRENCYSYMBOL.$getproduct->total ?? '' }}</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <hr class="invoice-spacing" />
        <div class="card-body invoice-padding pb-0">
          <div class="row invoice-sales-total-wrapper">
            <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
              <!-- <p class="card-text mb-0">
                <span class="fw-bold">Price:</span> <span class="ms-75">{{ $getData->getOrderNotes->note ?? '' }} </span>
              </p> -->
            </div>
            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
              <div class="invoice-total-wrapper">
                <!-- <div class="invoice-total-item">
                  <p class="invoice-total-title">Subtotal:</p>
                  <p class="invoice-total-amount"> {{ CURRENCYSYMBOL.$getData->amount}}</p>
                </div> -->
                <!-- <div class="invoice-total-item">
                  <p class="invoice-total-title">Discount:</p>
                  <p class="invoice-total-amount"> {{ CURRENCYSYMBOL }}0</p>
                </div> -->
                <!-- <div class="invoice-total-item">
                  <p class="invoice-total-title">Tax:</p>
                  <p class="invoice-total-amount"> {{ CURRENCYSYMBOL }}0</p>
                </div> -->
                <hr class="my-50" />
                <div class="invoice-total-item">
                  <p class="invoice-total-title">Total:</p>
                  <p class="invoice-total-amount"> {{ CURRENCYSYMBOL.$getData->amount}}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Invoice Description ends -->
        <hr class="invoice-spacing" />
      </div>
    </div>
    <!-- /Invoice -->
    <!-- Invoice Actions -->
    <div class="col-xl-4 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
      <div class="card">
        <div class="card-body">
        <div class="mb-1">
          <label class="form-label">Order Status</label>
            <select class="form-select"  id="order_status">
              <option value="" selected disabled>Select</option>
              <option value="1"  @if($getData->order_status == 1) selected @endif>Pending Payment</option>
              <option value="2"  @if($getData->order_status == 2) selected @endif>Refunded</option>
              <option value="3" @if($getData->order_status == 3) selected @endif>Processing</option>
              <option value="4" @if($getData->order_status == 4) selected @endif>On Hold</option>
              <option value="5" @if($getData->order_status == 5) selected @endif>Cancelled</option>
              <option value="6" @if($getData->order_status == 6) selected @endif>Completed</option>
              <option value="7" @if($getData->order_status == 7) selected @endif>Failed</option>
            </select>
            <input type="hidden" id="orderId" value="{{ $id }}">
        </div>
          <button type="submit" id="statusUpdateBtn" class="btn btn-primary w-100 mb-75"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <button class="btn btn-primary w-100 mb-75" data-bs-toggle="modal" data-bs-target="#send-invoice-sidebar">
          Add Notes
          </button>
        </div>
        <section id="basic-datatable">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <table class="datatables-comic table displayNotesData">
                <thead>
                  <tr>
                    <th>id</th>
                    <th>Recently Added Notes</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
        <div class="modal modal-slide-in fade" id="modals-slide-in-view">
          <div class="modal-dialog sidebar-sm">
            <form class="modal-content pt-0" id="updateForm">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
              <div class="modal-header mb-1">
                <h5 class="modal-title">Order Notes</h5>
              </div>
              <div class="modal-body flex-grow-1">
                <div class="mb-1">
                    <label for="Note" class="form-label">Note</label>
                      <textarea class="form-control"  name="note"  id="note"  cols="3" rows="7"    >   </textarea>

                </div>
                <div class="mb-1">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="notify_customer" id="notify_customer" />
                          <label class="form-check-label" for="notify_customer">Is User Notify</label>
                        </div>
                      </div>
                <div class="button-section mt-5">
                  <button type="reset" class="btn btn-primary data-submit me-1" data-bs-dismiss="modal"><div class="spinner-border text-light importloader dNone" role="status"></div>Close</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </section>
      </div>
    </div>
    <!-- /Invoice Actions -->
  </div>
</section>
<!-- Send Invoice Sidebar -->
<div class="modal modal-slide-in fade" id="send-invoice-sidebar" aria-hidden="true">
  <div class="modal-dialog sidebar-lg">
    <form class="modal-content pt-0" id="insertForm">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
      <div class="modal-header mb-1">
        <h5 class="modal-title" id="exampleModalLabel">
          <span class="align-middle">Order Notes</span>
        </h5>
      </div>
      <input type="hidden" name="orderId" id="orderId" value="{{ $id }}">
        <div class="modal-body flex-grow-1">
          <div class="mb-1">
            <label for="Note" class="form-label">Note</label>
            <textarea
              class="form-control"
              name="note"
              cols="3"
              rows="7" required
            >
           </textarea>
          </div>
          <div class="mb-1">
               <div class="form-check">
                   <input type="checkbox" class="form-check-input" name="notify_customer"  />
                    <label class="form-check-label" for="notify_customer">Is User Notify</label>
                  </div>
                </div>
          <div class="mb-1 d-flex flex-wrap mt-2">
              <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
              <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /Send Invoice Sidebar -->

        </div>
      </div>
    </div>
    <!-- END: Content-->
@endsection
@section('vendor-script')
  <script src="{{asset('vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
  <script src="{{asset('vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
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
<script src="{{asset('js/scripts/pages/app-invoice.js')}}"></script>
<script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/order.js') }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
@endsection
