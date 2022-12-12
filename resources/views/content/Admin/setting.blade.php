
@extends('layouts/contentLayoutMaster')

@section('title', 'Settings')

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

 <section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <!-- <div class="card-header">
          <h4 class="card-title">Multiple Column</h4>
        </div> -->
        <div class="card-body">
          <form class="form" id="updateForm">
          @csrf
            <div class="row">
              <div class="col-md-3 col-12">
                <div class="mb-1">
                  <label class="form-label" for="first-name-column">Publisher Coins Percentage</label>
                  <input
                    type="text"
                    id="reseller_payout_percentage"
                    class="form-control preventCharacters"
                    placeholder="Reseller Payout Percentage"
                    name="reseller_payout_percentage"

                    value="{{ Helper::makeCurrencyWithoutSymbol($getData['reseller_payout_percentage'])}}"
                  />
                </div>
              </div>
              <div class="col-md-3 col-12">
                <div class="mb-1">
                  <label class="form-label" for="first-name-column">Free Coin For New User</label>
                  <input
                    type="text"
                    id="free_coin_for_new_user"
                    class="form-control preventCharacters"
                    placeholder="Free coin for new user"
                    name="free_coin_for_new_user"
                    value="{{ $getData['free_coin_for_new_user'] }}"
                  />
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="email-id-column">Notification Email</label>
                  <input
                    type="email"
                    class="form-control"
                    name="email"
                    placeholder="Email"
                    value="{{$getData['email_notification']}}"
                  />
                </div>
              </div>
              <div class="col-md-4 col-12 mt-1">
                <div class="mb-1">
                  <label class="form-label" for="price_v_coin">Price Of 1 V-Coin</label>
                  <div class="input-group input-group-merge">
                   <span class="input-group-text inputrj"><i data-feather="dollar-sign"></i></span>
                  <input
                    type="text"
                    class="form-control dt-full-name preventCharacters"
                    name="price_v_coin"
                    value="{{$getData['price_v_coin'] ?? ''}}"
                  />
                  </div>
                </div>
              </div>
              <div class="col-md-4 col-12 mt-1">
                <div class="mb-1">
                  <label class="form-label" for="new_subscription_free_coin">Free Coins For New Subscription</label>
                  <div class="input-group input-group-merge">
                  <input
                    type="text"
                    class="form-control dt-full-name preventCharactersAndDot"
                    name="new_subscription_free_coin"
                    value="{{ $getData['new_subscription_free_coin'] }}"
                  />
                </div>
                </div>
              </div>

              <div class="col-md-4 col-12 mt-1">
                <div class="mb-1">
                  <label class="form-label" for="subscription_renewal">Free Coins For Subscription Renewal</label>
                  <div class="input-group input-group-merge">
                  <input
                    type="text"
                    class="form-control dt-full-name preventCharactersAndDot"
                    name="renewal_subscription_free_coin"
                    value="{{$getData['renewal_subscription_free_coin'] }}"
                  />
                </div>
                </div>
              </div>
            </div>
            <div class="row">
              <h5 class="mt-1">Coin Purchase Slabs</h5>
                <div class="col-md-6">
                    <div class="row">
                      @foreach($getsetting as $getsett)
                       <div class="col-md-6">
                          <div class="mb-1">
                            <label class="form-label" for="first-name-icon"></label>
                            <div class="input-group input-group-merge">
                              <span class="input-group-text"><i data-feather="dollar-sign"></i></span>
                              <input type="text" class="form-control"
                                    name="slabs[]" value="{{$getsett['slabs']}}" readonly
                                  />
                            </div>
                          </div>
                       </div>
                      <div class="col-md-6">
                          <div class="mb-1">
                            <label class="form-label"></label>
                            <input name="coins[]"
                              type="text"
                              class="form-control preventCharactersAndDot"
                              value="{{$getsett['coins']}}"
                            />
                          </div>
                      </div>
                      @endforeach
                    </div>
                </div>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Update</button>
                <!-- <button type="reset" class="btn btn-outline-secondary">Reset</button> -->
              </div>
            </div>
          </form>
        </div>
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
  <script src="{{ asset('js/custom/admin/setting.js') }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
<script>
 $("#updateForm").validate({
        rules: {
          reseller_payout_percentage: {
                required: true,
            },
            email: {
                required: true,
            },
            price_v_coin:{
              required:true,
            },
            new_subscription_free_coin:{
              required:true,
            },
            subscription_renewal:{
              required:true,
            },
            slabs:{
              required:true,
           },
           coins:{
             required:true,
           },

        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#updateForm").find('.importloader').removeClass('dNone');
            var forms = $("#updateForm");
           // alert('hi');
           //var id=
            var formData = forms.serialize();
            $.ajax({
                'url': ajaxUrl + "/admin/update-setting",
                'type': "POST",
                'dataType': "json",
                'data': formData,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");

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
                    $("#updateForm").find('.importloader').addClass('dNone');
                }
            });
        },
      });


</script>

@endsection
