@extends('content.front.layouts.master')
@section('title','My Comic')
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('front/css/publisher.css')}}">
<style>
textarea.form-control.valid:focus {
  background-color: #161616;
}
.form-control:focus {
    background-color: #161616 !important;
}
</style>
<section class="searchpage bg-dark slidercalss ">
   <div class="container">
        <div class="row mb-5 p-10">
            <h3>Add New Episode</h3>
            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{route('my-comic')}}">My Comic</a></li>
                                <li class="breadcrumb-item"><a href="{{url('publisher/view-comics')}}/{{ $id }}">View Comic</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Episode</li>

                                </ol>
                            </nav>
                <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <form class="form" id="addEpisodeData">
                        <input type="hidden" name="total_page_count" id="total_page_count" value="1">
                        <input type="hidden" name="comics_series_id" value="{{ $id }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="row">
                                    <div class="mt-3">
                                    <label class="form-label"> Name</label>
                                    <input type="text" class="form-control" placeholder="Name" name="name" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mt-3">
                                    <label class="form-label"> Image [ Recommended size 800 * 1200 ]</label>
                                    <input type="file" class="form-control" name="image" accept="image/png, image/gif, image/jpeg"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mt-3">
                                    <label class="form-label"> Comic Pdf</label>
                                    <input type="file" class="form-control" name="comic_pdf" id="comic_pdf" accept="application/pdf, application/vnd.ms-excel" />
                                        <div class="progress progress-bar-primary mt-2" id="progress_upload_folder" style="display:none">
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


                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="row">
                                    <div class="mt-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="5" placeholder="Description"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="mt-3">
                                        <input class="form-label form-check-input uploadAudioCheck" type="checkbox" value="1" />
                                        <label class="form-check-label">Do you want to upload audio file</label>
                                    </div>
                                </div>
                                <div class="row comicAudioSection d-none">
                                    <div class="mt-3">
                                    <label class="form-label"> Audio File</label>
                                    <input type="file" class="form-control" name="audio_file" id="audio_file" accept="audio/mp3,audio/*;capture=microphone"/>
                                    </div>
                                </div>

                                <div class="previewSection d-none">
                                    <div class="row mt-1">
                                        <div class="mt-3">
                                            <input class="form-label form-check-input uploadPreviewCheck" type="checkbox" name="unable_preview" />
                                            <label class="form-check-label">Do you want to enable preview?</label>
                                        </div>
                                    </div>
                                    <div class="row previewPagesSection d-none">
                                        <div class="col-md-2 col-12">
                                            <div class="mt-3">
                                            <label class="form-label">From</label>
                                            <input type="text" class="form-control" readonly value="1" >
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <div class="mt-3">
                                            <label class="form-label">To</label>
                                            <select class="form-select" name="preview_pages" id="preview_pages">

                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="comicTimerSection d-none">

                                    </div>
                                </div>
                              </div>
                        </div>
                        <div class="button-section mt-5 mb-5">
                            <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('scripts')

<script src="{{ asset('js/custom/episode.js') }}"></script>
<script>
var comicSeriesId = "{{ $id }}";




$(document).on("change", "#comic_pdf", function(){

      $("#progress_upload_folder").show();
      setTimeout(function(){
        var percentComplete = 40;
        $("#progress-bar_upload_folder").width(percentComplete + '%');
        //$("#progress-bar_upload_folder").html(percentComplete+'%');
      },500)

      var comicTimerMain = "";
      var previewPagesHtml = "";
      var formData = new FormData($("#addEpisodeData").get(0));
      $.ajax({
        url: ajaxUrl + "/publisher/upload-comic-pdf",
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
                                            <div class="mt-3">
                                              <label class="form-label">Page No</label>
                                              <input type="text" name="pageNumber[]" class="form-control" readonly value="`+l+`">
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-12 ">
                                          <div class="mt-3">
                                            <label class="form-label" for="itemcost">Start Time</label>
                                            <input type="text" name="starttime[]" class="startTime form-control autioTimer" value="00:00:00"/ required>
                                          </div>
                                        </div>
                                        <div class="col-md-5 col-12">
                                          <div class="mt-3">
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


$("#addEpisodeData").validate({
      rules: {
          'name': {
              required: true,
          },
          'image': {
              required: true,
          },
          'comic_pdf': {
            required: true
          },
          'access_type': {
            required: true
          },
          'description': {
            required: true
          },
          'audio_file': {
            required: true
          },
          'endtime[]': {
            required: true
          },
          'preview_pages': {
            required: true
          }
      },
      submitHandler: function (form) {
          var thisForm = $(this);

          $("#addEpisodeData").find('.importloader').removeClass('dNone');
          var formData = new FormData($("#addEpisodeData").get(0));
          let audio_file = $('#audio_file')[0].files[0];
          formData.append('audio_file', audio_file);
          $.ajax({
              url: ajaxUrl + "/publisher/insert-episode",
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
                        window.location.href = "{{ url('publisher/view-comics/'.$id)}}";
                      },500);
                  }
                 else{
                  toastr["error"](response.message, "Error");
                 }

              },
              'error': function (error) {
                  console.log(error);
              },
              'complete':function(){
                  $("#addEpisodeData").find('.importloader').addClass('dNone');
              }
          });
      },
});




</script>
@endsection