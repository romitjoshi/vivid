@extends('content.front.layouts.master')
@section('title','My Comic')
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('front/css/publisher.css')}}">
<style>
    textarea.form-control.valid:focus {
        background-color: #262626;
    }

    .form-control:focus {
        background-color: #262626 !important;
    }
</style>

<section class="searchpage bg-dark slidercalss ">
    <div class="container mb-5">
        <div class="row p-10">
            <div class="row tableSection d-none">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <h3>My Comic</h3>

                        <div class="rightSection text-end ">
                            <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modals-slide-create">Add New</a>
                        </div>

                    </div>
                    <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('my-dashboard') }}">Dashboard</a></li>
                                <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
                                    <li class="breadcrumb-item active" aria-current="page">My Comic</li>

                                </ol>
                            </nav>
                </div>
                <div class="col-12">
                    <div class="table-section mb-5 comic-table">
                        <table class="datatables-comic table displayData">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Rating</th>
                                    <th>Approval</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row emptysection mb-5 d-none">
            <div class="col-12">
                <div class="imgwithtag genres"><img class="tagimg tagblankimg" src="{{ asset('front/images/empty-comic.png') }}"></div>
            </div>
            <div class="col-12 text-center mt-4">
                <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modals-slide-create">Create Comic</a>
            </div>
        </div>
    </div>
</section>

<!-- Modal to add new record -->
<div class="modal fade modelbutton addFornt" id="modals-slide-create">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                @if($user->getUserDetails->is_approve_docs ?? '' == 1)
                <h4 class="modal-title w-100">Add New Comic</h4>
                @else
                <h4 class="modal-title w-100">Account Pending</h4>
                @endif
                <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
            </div>
            <!-- Modal body -->
            <div class="modal-body text-center">
                @if($user->getUserDetails->is_approve_docs ?? '' == 1)
                <form class="modal-content1 pt-0" id="insertForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="modal-body flex-grow-1">
                        <div class="mt-2">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control dt-full-name" name="name" placeholder="Name" />
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Cover image [ Recommended size 800 * 1200 ]</label>
                            <input type="file" class="form-control dt-full-name" name="featured_image" placeholder="featured_image" accept="image/png, image/gif, image/jpeg, image/jpg" />
                        </div>
                        <!-- <div class="mt-2">
                <label class="form-label">Is Featured</label>
                <select class="form-control"  name="is_featured" required>
                        <option value=""selected disabled>Select</option>
                        <option value="2" >Yes</option>
                        <option value="1" >No</option>
                    </select>
                </div>

                <div class="mt-2">
                        <label class="form-label">Access Type</label>
                        <select class="form-select" name="access_type">
                        <option value="" disabled selected>Select</option>
                        <option value="1">Free</option>
                        <option value="2">Premium</option>
                        </select>
                    </div> -->

                        <div class="mt-2">
                            <label class="form-label">Category [Search Category name]</label>
                            <select class="form-control select2 form-select" name="category[]" multiple>

                            </select>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="6" placeholder="" name="description" aria-invalid="Description"></textarea>
                        </div>
                        <div class="button-section mt-5">
                            <button type="submit" class="btn btn-primary data-submit me-1">
                                <div class="spinner-border text-light importloader dNone" role="status"></div>Submit
                            </button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
                @else
                @if(empty($user->getUserDetails->tax_doc) && empty($user->getUserDetails->lic_doc))
                <p class="text-white">Please upload the Licensing agreement and Tax Agreement document to start creating your comics. To do so Please go to Publisher Portal Page to upload the document.</p>
                @else
                <p class="text-white">Your documents are currently pending (Licensing Agreement and W-9) please allow 2-3 business days for processing.</p>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal to Update record -->
<div class="modal fade modelbutton addFornt" id="modals-slide-in-update">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <h4 class="modal-title w-100">Update Comic</h4>
                <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25" />
            </div>
            <!-- Modal body -->
            <div class="modal-body text-center">
                <form class="add-new-record modal-content1 pt-0" id="updateForm">
                    <input type="hidden" name="updateid" id="updateid" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="modal-body flex-grow-1">
                        <div class="mt-2">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control dt-full-name" name="name" id="name" placeholder="Name" />
                        </div>
                        <!-- <div class="mt-2">
                <label class="form-label">Is Featured</label>
                <select class="form-control" id="is_featured" name="is_featured" required>
                <option value=""selected disabled>Select</option>
                        <option value="1" >No</option>
                        <option value="2" selected>Yes</option>
                    </select>
                </div>
                <div class="mt-2">
                <label class="form-label">Access Type</label>
                    <select class="form-select" name="access_type" id="access_type">
                    <option value="" selected disabled>Select</option>
                    <option value="1">Free</option>
                    <option value="2">Premium</option>
                    </select>
                </div> -->
                        <div class="mt-2">
                            <label class="form-label">Category [Search Category name]</label>
                            <select class="form-control select2 form-select" name="category[]" id="comicCategory" multiple>

                            </select>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="6" placeholder="" name="description" aria-invalid="Description"></textarea>
                        </div>

                        <div class="mt-2">
                            <label class="form-label">Cover Image[ Recommended size 800 * 1200 ]</label>
                            <input type="file" class="form-control dt-full-name" id="featured_image" name="featured_image" placeholder="featured_image" accept="image/png, image/gif, image/jpeg, image/jpg" />
                            <div class="previewImageSection">
                                <img src="" class="img-fluid rounded" id="previewComicImg" alt="avatar img">
                            </div>
                        </div>
                        <div class="button-section mt-5">
                            <button type="submit" class="btn btn-primary data-submit me-1">
                                <div class="spinner-border text-light importloader dNone" role="status"></div>Update
                            </button>
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
        <form class="modal-content pt-0" id="deleteForm">
            <input type="hidden" name="deleteid" value="" id="deleteid">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title w-100" id="myModalLabel120">Delete Series</h5>
                    <img class="cursor-pointer" data-bs-dismiss="modal" src="{{asset('front/images/close.svg')}}" width="25"/>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this record?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary data-submit me-1">
                        <div class="spinner-border text-light importloader dNone" role="status"></div>Delete
                    </button>
                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')

<script>
    $(document).ready(function() {
        var dt_ajax_table = $('.displayData');
        if (dt_ajax_table.length) {
            var dt_ajax = dt_ajax_table.dataTable({
                ajax: {
                    url: ajaxUrl + "/publisher/get-comics",
                    method: "get",
                    dataType: "json",
                    cache: false,
                    complete: function(response) {
                        if (response.responseJSON.data.length > 0) {
                            $(".tableSection").removeClass("d-none");
                        } else {
                            $(".emptysection").removeClass("d-none");
                        }
                    },
                },
                processing: true,
                serverSide: true,

                columns: [{
                        data: "id"
                    },
                    {
                        data: "featured_image"
                    },
                    {
                        data: "name"
                    },
                    {
                        data: "avg"
                    },
                    {
                        data: "approve"
                    },
                    {
                        data: "status"
                    },
                    {
                        data: "action"
                    },
                ],
                order: [
                    [0, "desc"]
                ],
                "aoColumnDefs": [{
                        "bVisible": false,
                        "aTargets": [0]
                    },
                    {
                        "bSortable": false,
                        "aTargets": [0, 1, 4, 5, 6]
                    },
                    {
                        "bSearchable": false,
                        "aTargets": [0, 1, 4, 5, 6]
                    }
                ],

                displayLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
                dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    paginate: {
                        // remove previous & next text from pagination
                        previous: "&nbsp;",
                        next: "&nbsp;",
                    },
                },
            });
            $("div.head-label").html('<h6 class="mb-0">Total Number of Comic</h6>');
        }

        $("#insertForm").validate({
            rules: {
                'name': {
                    required: true,
                    minlength: 2,
                    maxlength: 60,
                },
                'featured_image': {
                    required: true,
                },
                'access_type': {
                    required: true,
                },
                'is_featured': {
                    required: true,
                },
                'description': {
                    required: true,
                },
                'category[]': {
                    required: true,
                }
            },
            errorPlacement: function(error, element) {
                element.parent().after(error)
            },
            submitHandler: function(form) {
                var thisForm = $(this);

                $("#insertForm").find('.importloader').removeClass('dNone');
                var formData = new FormData($("#insertForm").get(0));
                $.ajax({
                    url: ajaxUrl + "/publisher/insert-comics",
                    type: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            toastr["success"](response.message, "Success");
                            //dt_ajax_table.DataTable().ajax.reload();
                            //$("#modals-slide-create").modal('toggle');
                            setTimeout(function() {
                                window.location.href = "{{ url('publisher/view-comics/')}}/" + response.comic_id;
                            }, 500);

                        } else {
                            toastr["error"](response.message, "Error");
                        }

                    },
                    'error': function(error) {
                        console.log(error);
                    },
                    'complete': function() {
                        $("#insertForm").find('.importloader').addClass('dNone');
                    }
                });
            },
        });

        $('select[name="category[]"]').select2({
            allowClear: true,
            placeholder: 'Type Category name..',
            ajax: {
                url: ajaxUrl + "/publisher/get-category-by-name",
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        selected: true
                    };
                },
                processResults: function(response) {
                    console.log(response)
                    return {
                        results: response
                    };
                },
                cache: false
            }
        });

        $(document).on("click", ".item-edit", function(e) {
            $("#name").val(
                jQuery(this).parents("tr").find(".dataField").attr("name")
            );
            $("#description").val(
                jQuery(this).parents("tr").find(".dataField").attr("description")
            );
            $("#is_featured").val(
                jQuery(this).parents("tr").find(".dataField").attr("is_featured")
            );
            $("#access_type").val(
                jQuery(this).parents("tr").find(".dataField").attr("access_type")
            );
            $("#updateid").val(
                jQuery(this).parents("tr").find(".dataField").attr("id")
            );

            //auto select
            var comicCatid = jQuery(this).parents("tr").find(".dataField").attr("comicCatid");
            var getCatName = jQuery(this).parents("tr").find(".dataField").attr("getCatName");
            var comicCatid = JSON.parse(comicCatid);
            var getCatName = JSON.parse(getCatName);

            $("#comicCategory").empty();
            if (comicCatid.length > 0) {
                var appendData = "";
                for (var j = 0; j < comicCatid.length; j++) {
                    appendData += `<option value="` + comicCatid[j] + `" selected>` + getCatName[j] + `</option>`;
                }
                $("#comicCategory").append(appendData);
            }

            //image preview
            var imagePath = jQuery(this).parents("tr").find(".dataField").attr("imagePath");
            $("#previewComicImg").attr("src", imagePath);


        });

        $("#deleteForm").validate({
            rules: {
                deleteid: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                var thisForm = $(this);
                $("#deleteForm").find('.importloader').removeClass('dNone');
                var forms = $("#deleteForm");
                var formData = forms.serialize();
                $.ajax({
                    'url': ajaxUrl + "/publisher/delete-comics",
                    'type': "POST",
                    'dataType': "json",
                    'data': formData,
                    'success': function(response) {
                        if (response.status) {
                            toastr["success"](response.message, "Success");
                            dt_ajax.DataTable().ajax.reload();
                            $(".btn-close").trigger('click');
                        } else {
                            toastr["error"](response.message, "Error");
                        }
                    },
                    'error': function(error) {
                        console.log(error);
                    },
                    'complete': function() {
                        $("#deleteForm").find('.importloader').addClass('dNone');
                    }
                });
            },
        });

        $("#updateForm").validate({
            rules: {
                'name': {
                    required: true,
                    minlength: 2,
                },
                'access_type': {
                    required: true,
                },
                'description': {
                    required: true,
                    minlength: 2,
                },
                'category[]': {
                    required: true,
                }

            },
            errorPlacement: function(error, element) {
                element.parent().after(error)
            },
            submitHandler: function(form) {
                var thisForm = $(this);
                $("#updateForm").find('.importloader').removeClass('dNone');
                var formData = new FormData($("#updateForm").get(0));
                $.ajax({
                    'url': ajaxUrl + "/publisher/update-comics",
                    'type': "POST",
                    'dataType': "json",
                    'processData': false,
                    'contentType': false,
                    'data': formData,
                    'success': function(response) {
                        if (response.status) {
                            toastr["success"](response.message, "Success");
                            dt_ajax.DataTable().ajax.reload();
                            $("#modals-slide-in-update").modal('toggle');
                        } else {
                            toastr["error"](response.message, "Error");
                        }
                    },
                    'error': function(error) {
                        console.log(error);
                    },
                    'complete': function() {
                        $("#updateForm").find('.importloader').addClass('dNone');
                    }
                });
            },
        });

    });
</script>
@endsection