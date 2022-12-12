$(function() {
    var dt_ajax_table = $('.displayData');

    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var pendingval = $("#pendingcomic").val();

        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-comics",
                method: "get",
                dataType: "json",
                cache: false,
                data:{pagename:pendingval},
            },
            processing: true,
            serverSide: true,
            columns: [
                {
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
                    data: "status"
                },
                {
                    data: "is_featured"
                },
                {
                    data: "access_type"
                },
                {
                    data: "action"
                },
            ],
            order: [
                [0, "desc"]
            ],
            "aoColumnDefs": [
                { "bVisible": false, "aTargets": [0] },
                { "bSortable": false, "aTargets": [ 0,1,4,5,6] },
                { "bSearchable": false, "aTargets": [ 0,1,4,5,6] }
            ],

            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
             buttons: [
               // {
            //     extend: "collection",
            //     className: "btn btn-outline-secondary dropdown-toggle me-2",
            //     text: feather.icons["share"].toSvg({
            //         class: "font-small-4 me-50",
            //     }) + "Export",
            //     buttons: [{
            //             extend: "csv",
            //             text: feather.icons["file-text"].toSvg({
            //                 class: "font-small-4 me-50",
            //             }) + "Csv",
            //             className: "dropdown-item",
            //             exportOptions: {
            //                 columns: [ 1, 2, 3, 4],
            //             },
            //             "action": newexportaction,
            //         },
            //         {
            //             extend: "excel",
            //             text: feather.icons["file"].toSvg({
            //                 class: "font-small-4 me-50",
            //             }) + "Excel",
            //             className: "dropdown-item",
            //             exportOptions: {
            //                 columns: [ 1, 2, 3, 4],
            //             },
            //             "action": newexportaction,
            //         },
            //     ],
            //     init: function(api, node, config) {
            //         $(node).removeClass("btn-secondary");
            //         $(node).parent().removeClass("btn-group");
            //         setTimeout(function() {
            //             $(node)
            //                 .closest(".dt-buttons")
            //                 .removeClass("btn-group")
            //                 .addClass("d-inline-flex");
            //         }, 50);
            //     },
            // },
          {
          text:
            feather.icons["plus"].toSvg({
              class: "me-50 font-small-4",
            }) + "Add New Comic",
          className: "create-new btn btn-primary dateSetBtn profileOk",
          attr: {
            "data-bs-toggle": "modal",
            "data-bs-target": "#modals-slide-create",
          },
          init: function (api, node, config) {
            $(node).removeClass("btn-secondary");
          },
        }, ],
            dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    // remove previous & next text from pagination
                    previous: "&nbsp;",
                    next: "&nbsp;",
                },
            },
          });
          $("div.head-label").html('<h6 class="mb-0">Total Number of Comics</h6>');
    }


    var dt_ajax_table = $('.displayPendingComic');
    if (dt_ajax_table.length) {


            var dt_ajax = dt_ajax_table.dataTable({
                ajax: {
                    url: ajaxUrl + "/admin/get-pendingcomics",
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
                        data: "featured_image"
                    },
                    {
                        data: "name"
                    },
                    {
                        data: "pub_name"
                    },
                    {
                        data: "description"
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
                "aoColumnDefs": [
                    { "bVisible": false, "aTargets": [0] },
                    { "bSortable": false, "aTargets": [ 0,1,5,6] },
                    { "bSearchable": false, "aTargets": [ 0,1,5,6] }
                ],

                displayLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
                buttons: [
                // {
                //     extend: "collection",
                //     className: "btn btn-outline-secondary dropdown-toggle me-2",
                //     text: feather.icons["share"].toSvg({
                //         class: "font-small-4 me-50",
                //     }) + "Export",
                //     buttons: [{
                //             extend: "csv",
                //             text: feather.icons["file-text"].toSvg({
                //                 class: "font-small-4 me-50",
                //             }) + "Csv",
                //             className: "dropdown-item",
                //             exportOptions: {
                //                 columns: [ 1, 2, 3, 4],
                //             },
                //             "action": newexportaction,
                //         },
                //         {
                //             extend: "excel",
                //             text: feather.icons["file"].toSvg({
                //                 class: "font-small-4 me-50",
                //             }) + "Excel",
                //             className: "dropdown-item",
                //             exportOptions: {
                //                 columns: [ 1, 2, 3, 4],
                //             },
                //             "action": newexportaction,
                //         },
                //     ],
                //     init: function(api, node, config) {
                //         $(node).removeClass("btn-secondary");
                //         $(node).parent().removeClass("btn-group");
                //         setTimeout(function() {
                //             $(node)
                //                 .closest(".dt-buttons")
                //                 .removeClass("btn-group")
                //                 .addClass("d-inline-flex");
                //         }, 50);
                //     },
                // },
            {
            text:
                feather.icons["plus"].toSvg({
                class: "me-50 font-small-4 d-none",
                }) + "Add New Comic",
            className: "create-new btn btn-primary dateSetBtn profileOk",
            attr: {
                "data-bs-toggle": "modal",
                "data-bs-target": "#modals-slide-create",
            },
            init: function (api, node, config) {
                $(node).removeClass("btn-secondary");
            },
            }, ],
                dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    paginate: {
                        // remove previous & next text from pagination
                        previous: "&nbsp;",
                        next: "&nbsp;",
                    },
                },
            });
            $("div.head-label").html('<h6 class="mb-0">Total Number of Comics</h6>');
    }


    var dt_ajax_table = $('.displayPendingEpisode');
    if (dt_ajax_table.length) {
            var dt_ajax = dt_ajax_table.dataTable({
                ajax: {
                    url: ajaxUrl + "/admin/getpendingEpisode",
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
                        data: "image"
                    },
                    {
                        data: "name"

                    },
                    {
                        data: "comic_name"
                    },
                    {
                        data: "approve"
                    },
                    {
                        data: "action"
                    },
                ],
                order: [
                    [0, "desc"]
                ],
                "aoColumnDefs": [
                    { "bVisible": false, "aTargets": [0] },
                    { "bSortable": false, "aTargets": [ 0,1] },
                    { "bSearchable": false, "aTargets": [ 0,1] }
                ],

                displayLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
                buttons: [
                    // {
                    //     extend: "collection",
                    //     className: "btn btn-outline-secondary dropdown-toggle me-2",
                    //     text: feather.icons["share"].toSvg({
                    //         class: "font-small-4 me-50",
                    //     }) + "Export",
                    //     buttons: [{
                    //             extend: "csv",
                    //             text: feather.icons["file-text"].toSvg({
                    //                 class: "font-small-4 me-50",
                    //             }) + "Csv",
                    //             className: "dropdown-item",
                    //             exportOptions: {
                    //                 columns: [ 1, 2, 3, 4],
                    //             },
                    //             "action": newexportaction,
                    //         },
                    //         {
                    //             extend: "excel",
                    //             text: feather.icons["file"].toSvg({
                    //                 class: "font-small-4 me-50",
                    //             }) + "Excel",
                    //             className: "dropdown-item",
                    //             exportOptions: {
                    //                 columns: [ 1, 2, 3, 4],
                    //             },
                    //             "action": newexportaction,
                    //         },
                    //     ],
                    //     init: function(api, node, config) {
                    //         $(node).removeClass("btn-secondary");
                    //         $(node).parent().removeClass("btn-group");
                    //         setTimeout(function() {
                    //             $(node)
                    //                 .closest(".dt-buttons")
                    //                 .removeClass("btn-group")
                    //                 .addClass("d-inline-flex");
                    //         }, 50);
                    //     },
                    // },
                {
                text:
                    feather.icons["plus"].toSvg({
                    class: "me-50 font-small-4 d-none",
                    }) + "Add New Comic",
                className: "create-new btn btn-primary dateSetBtn profileOk",
                attr: {
                    "data-bs-toggle": "modal",
                    "data-bs-target": "#modals-slide-create",
                },
                init: function (api, node, config) {
                    $(node).removeClass("btn-secondary");
                },
                }, ],
                    dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    language: {
                        paginate: {
                            // remove previous & next text from pagination
                            previous: "&nbsp;",
                            next: "&nbsp;",
                        },
                    },
                });
            $("div.head-label").html('<h6 class="mb-0">Total Number of Comics</h6>');
    }


    $(document).on("click", ".confirm-text", function(){
        var statusId= jQuery(this).attr("updateid");
        var side= jQuery(this).attr("side");

            Swal.fire({
            title: 'Are you sure?',
            text: "You want to change the status",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
            }).then(function (result) {
            if (result.isConfirmed)
            {
                $.ajax({
                    'url': ajaxUrl + "/admin/comicstatuschange",
                    'type': "POST",
                    'dataType': "json",
                    'data':{"statusId":statusId,"side":side},
                    'success': function (response) {
                        if(response.status)
                        {
                            dt_ajax.DataTable().ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Status!',
                                text: 'Comic Status Change Successfully..',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            })
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
            }
            })
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
                'url': ajaxUrl + "/admin/delete-comics",
                'type': "POST",
                'dataType': "json",
                'data': formData,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        dt_ajax.DataTable().ajax.reload();
                        $(".btn-close").trigger('click');
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

    $("#updateForm").validate({
        rules: {
            'name': {
                required: true,
                minlength: 2,
            },
            'access_type':{
                required:true,
            },
            'description': {
                required: true,
                minlength: 2,
            },
            'category[]' : {
                required: true,
            },
            'charge_coin_free_user':{
                required: true,
            },
            'charge_coin_paid_user':{
                required: true,
            },

        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#updateForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#updateForm").get(0));
            $.ajax({
                'url': ajaxUrl + "/admin/update-comics",
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
                        $(".btn-close").trigger('click');

                        location.reload();
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
            'access_type':{
                required:true,
            },
            'is_featured': {
                required: true,
            },
            'description': {
                required: true,
            },
            'category[]' : {
                required: true,
            }
        },
        submitHandler: function (form) {
            var thisForm = $(this);

            $("#insertForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#insertForm").get(0));
            $.ajax({
                url: ajaxUrl + "/admin/insert-comics",
                type: "POST",
                dataType: "json",
                processData:false,
                contentType:false,
                data: formData,
                success: function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        dt_ajax.DataTable().ajax.reload();
                        $(".btn-close").trigger('click');

                    }
                   else{
                    toastr["error"](response.message, "Error");
                   }

                },
                'error': function (error) {
                    console.log(error);
                },
                'complete':function(){
                    $("#insertForm").find('.importloader').addClass('dNone');
                }
            });
        },
    });


    $(document).on("click", ".item-edit", function (e) {
        $("#name").val(
            jQuery(this).parents("tr").find(".dataField").attr("name")
        );
        $("#description").val(
            jQuery(this).parents("tr").find(".dataField").attr("description")
        );
        $("#is_featured").val(
            jQuery(this).parents("tr").find(".dataField").attr("is_featured")
        );
        $("#status").val(
            jQuery(this).parents("tr").find(".dataField").attr("status")
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
        if(comicCatid.length > 0)
        {
            var appendData = "";
            for(var j = 0; j < comicCatid.length; j++)
            {
                appendData +=`<option value="`+comicCatid[j]+`" selected>`+getCatName[j]+`</option>`;
            }
            $("#comicCategory").append(appendData);
        }

        //image preview
        var imagePath = jQuery(this).parents("tr").find(".dataField").attr("imagePath");
        $("#previewComicImg").attr("src",imagePath);


    });
});


