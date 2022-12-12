@extends('content.front.layouts.master')
@section('title','Comic Details')
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('front/css/publisher.css')}}">
<section class="container publishertop">
   <div class="row">
   <h1 class="searchhead">Comic Details</h1>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('my-comic')}}">My Comic</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Comic</li>

         </ol>
      </nav>
   </div>
</section>
<section class="searchpage bg-dark slidercalss viewcpage">
   <div class="container">
      <div class="row">
        <div class="content-body">
        <section class="app-user-view-account">
            <div class="row">
                <!-- User Sidebar -->
                <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
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
                            <li class="mb-75 mt-3">
                            <span class="fw-bolder me-25">Status:</span>
                            @php
                            if($comicInfo->status == 1)
                            {
                                $status = '<span class="badge bg-success">Active</span>';
                            }
                            else if($comicInfo->status == 0)
                            {
                                $status = '<span class="badge bg-danger">Inactive</span>';
                            }
                            @endphp
                            {!! $status !!}
                            </li>
                            <li class="mb-75 mt-3">
                                <span class="fw-bolder me-25">Rating:</span>
                                <span>{{ $avgSum?? ""}} [{{$countavg ?? ""}}] </span>
                            </li>
                            <li class="mb-75 mt-3">
                                <span class="fw-bolder me-25">Description:</span>
                                <span>{{ $comicInfo->description ?? ""}}</span>
                            </li>



                            </ul>
                            <div class="d-flex justify-content-center pt-2">
                            <a href="javascript:;" class="btn btn-primary me-1" data-bs-toggle="modal" data-bs-target="#modals-slide-in-update">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
                </div>
                <!--/ User Sidebar -->
                <!-- User Content -->
                <div class="col-xl-8 col-lg-7 col-md-7 ordergridtable">
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
                <div class="card tableSection d-none">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 class="card-title mb-50">Episode List</h4>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ url('/publisher/add-episode').'/'.$id}}" class="btn btn-primary waves-effect waves-float waves-light float-end"><i data-feather="plus" class="font-medium-1 me-50"></i>Add</a>
                            </div>
                        </div>

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
                <div class="row emptysection mb-5 d-none">
                    <div class="col-12">
                        <div class="imgwithtag genres"><img class="tagimg tagblankimg" src="{{ asset('front/images/empty-episode.png') }}"></div>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <a href="{{ url('/publisher/add-episode').'/'.$id}}" class="btn btn-primary"><i data-feather="plus" class="font-medium-1 me-50"></i>Create Episode</a>
                    </div>
                </div>
                </div>
            </div>
        </section>
    </div>
      </div>
   </div>
</section>

 <!-- Modal to Update Series -->

 <div class="modal fade modelbutton addFornt" id="modals-slide-in-update">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content">
       <!-- Modal Header -->
       <div class="modal-header text-center">
         <h4 class="modal-title w-100">Update Comic</h4>
         <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>
       </div>
       <!-- Modal body -->
       <div class="modal-body text-center">
            <form class="add-new-record modal-content1 pt-0" id="updateForm">
            <input type="hidden" name="updateid" id="updateid" value="{{ $comicInfo->id ?? ''}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="modal-body flex-grow-1">
                <div class="">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control dt-full-name" name="name" value="{{ $comicInfo->name ?? ''}}" placeholder="Name" />
                </div>


                <div class="mt-2">
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
                <div class="mt-2">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="description" rows="6" placeholder="" name="description">{{ $comicInfo->description ?? ''}}</textarea>
                </div>

                <div class="mt-2">
                    <label class="form-label">Cover Image[ Recommended size 800 * 1200 ]</label>
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
   </div>
 </div>

 <!--   Modal to delete record -->
 <div class="modal fade modelbutton addFornt" id="modals-slide-in-delete">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content">
       <!-- Modal Header -->
       <div class="modal-header text-center">
         <h4 class="modal-title w-100">Delete Episode</h4>
         <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>
       </div>
       <!-- Modal body -->
       <div class="modal-body text-center">
       <form class="modal-content1 pt-0" id="deleteForm">
                        <input type="hidden" value=" {{ csrf_token() }}" name="_token">
          <input type="hidden" name="deleteid" value="" id="deleteid">
            <div class="modal-body text-center">
                <p>Are you sure you want to delete this record?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Delete</button>
                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
      </form>
       </div>
     </div>
   </div>
 </div>



@endsection
@section('scripts')
<script>

$(function() {

var dt_ajax_table = $('.displayData');

    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var comicId = $("#updateid").val();
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/publisher/get-episode",
                method: "get",
                dataType: "json",
                data:{'id':comicId},
                cache: false,
                complete:function(response){
                    if(response.responseJSON.data.length > 0)
                    {
                        $(".tableSection").removeClass("d-none");
                    }
                    else
                    {
                        $(".emptysection").removeClass("d-none");
                    }
                },
            },
            processing: true,
            serverSide: true,
            columns: [
                {
                    data: "id"
                },
                {
                    data: "image"
                },
                {
                    data: "name"
                },
                {
                    data: "impression"
                },
                {
                    data: "description"
                },
                {
                    data: "action"
                },
            ],
            order: [
                [0, "desc"]
            ],
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: [
        {
        init: function (api, node, config) {
            $(node).removeClass("btn-secondary");
        },
        },
    ],
        language: {
            paginate: {
                // remove previous & next text from pagination
                previous: "&nbsp;",
                next: "&nbsp;",
            },
        },
        });
    }

    $("#updateForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
            },
            access_type:{
                required:true,
            },
            description: {
                required: true,
                minlength: 2,
            },

        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#updateForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#updateForm").get(0));
            $.ajax({
                'url': ajaxUrl + "/publisher/update-comics",
                'type': "POST",
                'dataType': "json",
                'processData':false,
                'contentType':false,
                'data': formData,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        dt_ajax.DataTable().ajax.reload();
                        $("#modals-slide-in-update").modal('toggle');
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



    $("#deleteForm").validate({
        rules: {
            deleteid: {
                required: true,
            },
        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#deleteForm").find('.importloader').removeClass('dNone');
            var forms = $("#deleteForm");
            var formData = forms.serialize();
            $.ajax({
                'url': ajaxUrl + "/publisher/delete-episode",
                'type': "POST",
                'dataType': "json",
                'data': formData,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        dt_ajax_table.DataTable().ajax.reload();
                        $("#modals-slide-in-delete").modal('toggle');
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
                    $("#deleteForm").find('.importloader').addClass('dNone');
                }
            });
        },
    });

});



</script>
@endsection