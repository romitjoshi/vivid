@extends('layouts/contentLayoutMaster')

@section('title', 'Update Episode')

@section('vendor-style')
  <link rel="stylesheet" href="{{asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css'))}}">
  <link rel="stylesheet" href="{{asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css'))}}">
  <link rel="stylesheet" href="{{asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection
@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-invoice-list.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-user.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-pickadate.css')) }}">
  <link rel="stylesheet" href="{{ URL::asset('js/custom/library/toastr.min.css') }}">
@endsection

@section('content')

<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <!-- <h4 class="card-title">Multiple Column</h4> -->
        </div>
        <div class="card-body">
          <form class="form" id="UpdateEpisodeData">
            <input type="hidden" name="total_page_count" id="total_page_count" value="{{ $getData->total_page_count }}">
            <input type="hidden" name="comics_series_id" value="{{ $getData->comics_series_id }}">
            <input type="hidden" name="episode_id" value="{{ $getData->id }}">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="row">
                        <div class="mb-1">
                        <label class="form-label"> Name</label>
                        <input type="text" class="form-control" placeholder="Name" name="name" value="{{ $getData->name ?? ''}}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-1">
                          <label class="form-label"> Image [ Recommended size 800 * 1200 ]</label>
                          <div class="row">
                              <div class="col-10">
                                  <input type="file" class="form-control" name="image" accept="image/png, image/gif, image/jpeg"/>
                              </div>
                              <div class="col-2">
                                <div class="previewImageSection2 text-end w-100">
                                  <img src="{{ env('IMAGE_PATH_medium') }}{{ $getData->image }}" class="img-fluid rounded" alt="avatar img">
                                </div>
                              </div>
                          </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-1">
                        <label class="form-label"> Comic Pdf</label>
                            <div class="row">
                              <div class="col-10">
                                <input type="file" class="form-control mb-1" name="comic_pdf" id="comic_pdf" accept="application/pdf, application/vnd.ms-excel" />
                                      <div class="progress progress-bar-primary" id="progress_upload_folder" style="display:none">
                                        <div
                                          id="progress-bar_upload_folder"
                                          class="progress-bar progress-bar-striped progress-bar-animated"
                                          role="progressbar"
                                          aria-valuenow="20"
                                          aria-valuemin="20"
                                          aria-valuemax="100"
                                          style="width: 20%"
                                        ></div>
                                      </div>

                              </div>
                              <div class="col-2">
                                <div class="previewImageSection2   w-100">
                                    <a href="{{ env('PDF_PATH') }}{{$getData->comic_pdf}}" target="_blank"><i data-feather="eye" class="font-medium-5 me-50"></i></a>
                                </div>
                              </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-1">
                            <label class="form-label">Access Type</label>
                            <select class="form-select" name="access_type" id="access_type">
                                <option value="" disabled>Access Type</option>
                                <option value="1" @if($getData->access_type == 1) selected @endif>Free</option>
                                <option value="2" @if($getData->access_type == 2) selected @endif>Paid</option>
                                <option value="3" @if($getData->access_type == 3) selected @endif>Original</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-1">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="" disabled>Select Status</option>
                                <option value="0" @if($getData->status == 0) selected @endif>Inactive</option>
                                <option value="1" @if($getData->status == 1) selected @endif>Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                       <div class="col-md-6">
                          <div class="mb-1">
                            <label class="form-label">Charge Coin For Free User</label>
                            <input
                              type="text"
                              class="form-control dt-full-name  preventCharactersAndDot"
                              value="{{ $getData->charge_coin_free_user ?? ''}}"
                              name="charge_coin_free_user" required/>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="mb-1">
                            <label class="form-label" >Charge Coin For Paid User</label>
                            <input
                              type="text"
                              name="charge_coin_paid_user"
                              value="{{ $getData->charge_coin_paid_user ?? ''}}"
                              class="form-control dt-full-name preventCharactersAndDot" required/>
                          </div>
                      </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="row">
                        <div class="mb-1">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="5" placeholder="Description">{{ $getData->description ?? ''}}</textarea>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="mb-1">
                            <input class="form-label form-check-input uploadAudioCheck" type="checkbox" value="1" @if(!empty($getData->audio_file)) checked @endif/>
                            <label class="form-check-label" for="inlineCheckbox2">Do you want to upload audio file</label>
                        </div>
                    </div>
                    <div class="row comicAudioSection @if(empty($getData->audio_file)) d-none @endif">
                          <div class="mb-1">
                            <label class="form-label"> Audio File</label>
                              <div class="row">
                                      <div class="col-10">
                                          <input type="file" class="form-control" name="audio_file" id="audio_file" accept="audio/mp3,audio/*;capture=microphone"/>
                                      </div>
                                        <div class="col-2">
                                          <div class="previewImageSection2   w-100">
                                              <a href="{{ env('AUDIO_PATH') }}{{$getData->audio_file}}" target="_blank"><i data-feather="play" class="font-medium-5 me-50"></i></a>
                                            </div>
                                        </div>
                                </div>
                          </div>
                    </div>

                    <div class="previewSection @if(empty($getData->preview_pages)) d-none @endif">
                        <div class="row mt-1">
                            <div class="mb-1">
                                <input class="form-label form-check-input uploadPreviewCheck" type="checkbox" name="unable_preview" value="1" @if(!empty($getData->preview_pages)) checked @endif/>
                                <label class="form-check-label">Do you want to enable preview?</label>
                            </div>
                        </div>
                        <div class="row previewPagesSection @if(empty($getData->preview_pages)) d-none @endif">
                            <div class="col-md-2 col-12">
                                <div class="mb-1">
                                  <label class="form-label">From</label>
                                  <input type="text" class="form-control" readonly value="1" >
                                </div>
                            </div>
                            <div class="col-md-2 col-12">
                                <div class="mb-1">
                                  <label class="form-label">To</label>
                                  <select class="form-select" name="preview_pages" id="preview_pages">
                                    @php
                                      for($p = 1; $p <= $getData->total_page_count; $p++)
                                      {
                                        $selected = "";
                                        if($getData->preview_pages == $p)
                                        $selected = "selected";

                                        echo '<option value="'.$p.'" '.$selected.'>'.$p.'</option>';
                                      }
                                    @endphp
                                  </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                  <div class="col-md-6">
                  <div class="comicTimerSection @if(empty($getData->audio_file)) d-none @endif">
                            @foreach($getData->episode_page_mapping as $episode_page_mapping)
                              <div class="comicTimerMain">
                                  <div class="row">
                                    <div class="col-md-2 col-12">
                                          <div class="mb-1">
                                            <label class="form-label">Page No</label>
                                            <input type="hidden" name="episode_page_mapping_id[]" value="{{ $episode_page_mapping->id ?? '' }}" />
                                            <input type="text" name="pageNumber[]" class="form-control" readonly value="{{ $episode_page_mapping->page_number ?? '' }}">
                                          </div>
                                      </div>
                                      <div class="col-md-5 col-12">
                                        <div class="mb-1">
                                          <label class="form-label" for="itemcost">Start Time</label>
                                          <input type="text" name="starttime[]" class="startTime form-control autioTimer" value="{{ $episode_page_mapping->audio_start ?? '' }}" required>
                                        </div>
                                      </div>
                                      <div class="col-md-5 col-12">
                                        <div class="mb-1">
                                          <label class="form-label" for="itemquantity">End Time</label>
                                          <input type="text" name="endtime[]" class="endtime form-control autioTimer" value="{{ $episode_page_mapping->audio_end ?? '' }}" required>
                                        </div>
                                      </div>
                                  </div>
                              </div>
                            @endforeach;
                        </div>
                  </div>
                </div>
            </div>
            <div class="button-section mt-5">
              <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Update</button>
              <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Basic Floating Label Form section end -->

@endsection

@section('vendor-script')
  <script src="{{asset(mix('vendors/js/extensions/moment.min.js'))}}"></script>
  <script src="{{asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js'))}}"></script>
  <script src="{{asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js'))}}"></script>
  <script src="{{asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js'))}}"></script>
  <script src="{{asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js'))}}"></script>
  <script src="{{asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js'))}}"></script>
  <script src="{{asset(mix('vendors/js/tables/datatable/buttons.bootstrap5.min.js'))}}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
  <script src="{{ URL::asset('js/custom/library/toastr.min.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js'))}}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js'))}}"></script>
@endsection
@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/forms/form-input-mask.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
  <script src="{{ asset('js/custom/episode.js') }}"></script>
<script>

$(document).ready(function(){
  var comicSeriesId = "{{ $getData->comics_series_id }}";
  $(document).on("change", "#comic_pdf", function(){
        $("#progress_upload_folder").show();
        setTimeout(function(){
          var percentComplete = 40;
          $("#progress-bar_upload_folder").width(percentComplete + '%');
        },500)

        var comicTimerMain = "";
        var previewPagesHtml = "";
        var formData = new FormData($("#UpdateEpisodeData").get(0));
        $.ajax({
          url: ajaxUrl + "/admin/upload-comic-pdf",
          type: "POST",
          dataType: "json",
          processData:false,
          contentType:false,
          data: formData,
          success: function (response) {

            var percentComplete = 60;
            $("#progress-bar_upload_folder").width(percentComplete + '%');

            $(".comicTimerSection").empty();
            $("#preview_pages").empty();
            if(response.status)
            {
                $("#total_page_count").val(response.pages);
                for(var l = 1; l <= response.pages; l++)
                {
                    comicTimerMain =`<div class="comicTimerMain">
                                      <div class="row">
                                        <div class="col-md-2 col-12">
                                              <div class="mb-1">
                                                <label class="form-label">Page No</label>
                                                <input type="text" name="pageNumber[]" class="form-control" readonly value="`+l+`">
                                              </div>
                                          </div>
                                          <div class="col-md-5 col-12 ">
                                            <div class="mb-1">
                                              <label class="form-label" for="itemcost">Start Time</label>
                                              <input type="text" name="starttime[]" class="startTime form-control autioTimer" value="00:00:00" required>
                                            </div>
                                          </div>
                                          <div class="col-md-5 col-12">
                                            <div class="mb-1">
                                              <label class="form-label" for="itemquantity">End Time</label>
                                              <input type="text" name="endtime[]" class="endTime form-control autioTimer" required>
                                            </div>
                                          </div>
                                      </div>
                                  </div>`;

                    $(".comicTimerSection").append(comicTimerMain);
                    previewPagesHtml +=`<option value="`+l+`">`+l+`</option>`;
                }
                $("#preview_pages").append(previewPagesHtml);
              }

              var percentComplete = 100;
                $("#progress-bar_upload_folder").width(percentComplete + '%');

              setTimeout(function(){

                $("#progress_upload_folder").hide();
              },1000)

          },
          'error': function (error) {
              console.log(error);
          },
          'complete':function(){

          }
      })
  });


  $("#UpdateEpisodeData").validate({
        rules: {
          'name': {
              required: true,
            },
            'starttime[]': {
              required: true
            },
            'endtime[]': {
              required: true
            },
            'description': {
              required: true
            }
        },
        submitHandler: function (form) {
            var thisForm = $(this);

            $("#UpdateEpisodeData").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#UpdateEpisodeData").get(0));
            let audio_file = $('#audio_file')[0].files[0];
            formData.append('audio_file', audio_file);
            $.ajax({
                url: ajaxUrl + "/admin/update-episode",
                type: "POST",
                dataType: "json",
                processData:false,
                contentType:false,
                data: formData,
                success: function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        setTimeout(function(){
                          window.location.href = "{{ url('admin/view-comics/'.$getData->comics_series_id)}}";
                        }, 500);

                    }
                   else{
                    toastr["error"](response.message, "Error");
                   }

                },
                'error': function (error) {
                    console.log(error);
                },
                'complete':function(){
                    $("#UpdateEpisodeData").find('.importloader').addClass('dNone');
                }
            });
        },
    });

});

  </script>
@endsection
