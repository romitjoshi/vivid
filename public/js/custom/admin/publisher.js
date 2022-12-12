$(function() {
    var dt_ajax_table = $('.displayData');

    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-publisher",
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
                    data: "name"
                },
                {
                    data: "email"
                },
                {
                    data: "document_uploded"
                },
                {
                    data: "document_approval"
                },
                {
                    data: "status"
                },
                {
                    data: "created_at"
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
              { "bSortable": false, "aTargets": [ 0, 3,4 ] },
              { "bSearchable": false, "aTargets": [ 0,2,3] }
          ],

            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
             buttons: [
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
    //   $("div.head-label").html('<h6 class="mb-0">Total Number of Publisher</h6>');
    }

    //here pending publisher
    var dt_ajax_table = $('.displayPendingPublisher');
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-pending-publisher",
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
                    data: "name"
                },
                {
                    data: "email"
                },
                {
                    data: "created_at"
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
              { "bSortable": false, "aTargets": [ 0, 2,3,4 ] },
              { "bSearchable": false, "aTargets": [ 0,2,3,4] }
          ],

            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
             buttons: [
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
    //   $("div.head-label").html('<h6 class="mb-0">Total Number of Publisher</h6>');
    }


    $(document).on("click", ".confirm-text", function(){
        var statusId= jQuery(this).attr("updateid");
        var action= jQuery(this).attr("action");
          Swal.fire({
            title: 'Are you sure?',
            text: "You want to change this status",
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
                    'url': ajaxUrl + "/admin/publisherstatuschange",
                    'type': "POST",
                    'dataType': "json",
                    'data':{"statusId":statusId,"action":action},
                    'success': function (response) {
                        if(response.status)
                        {
                          dt_ajax.DataTable().ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Status!',
                                text: 'Publisher Status Change Successfully..',
                                customClass: {
                                  confirmButton: 'btn btn-success'
                                }
                            }).then(function (result)
                            {
                                location.reload();
                            });
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

    var userid  = $("#userid").val();
    $(document).on("click", ".docStatus", function(){
        var doctype= jQuery(this).attr("doctype");
          Swal.fire({
            title: 'Are you sure?',
            text: "You want to update document",
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
                    'url': ajaxUrl + "/admin/docs-status",
                    'type': "POST",
                    'dataType': "json",
                    'data':{"doctype":doctype, "userid":userid},
                    'success': function (response) {
                        if(response.status)
                        {
                            toastr["success"](response.message, "Success");
                            setTimeout(function(){
                                location.reload();
                             }, 500)
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

    $("#rejectlicDocsForm").validate({
        rules: {
            notes: {
                required: true,
            },
        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#rejectlicDocsForm").find('.importloader').removeClass('dNone');
            var forms = $("#rejectlicDocsForm");
            var formData = forms.serialize();
            $.ajax({
                'url': ajaxUrl + "/admin/add-lic-docs-notes",
                'type': "POST",
                'dataType': "json",
                'data': formData,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        setTimeout(function(){
                            location.reload();
                        }, 1000)
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
                    $("#rejectlicDocsForm").find('.importloader').addClass('dNone');
                }
            });
        },
    });

    $("#rejecttaxDocsForm").validate({
        rules: {
            notes: {
                required: true,
            },
        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#rejecttaxDocsForm").find('.importloader').removeClass('dNone');
            var forms = $("#rejecttaxDocsForm");
            var formData = forms.serialize();
            $.ajax({
                'url': ajaxUrl + "/admin/add-tax-docs-notes",
                'type': "POST",
                'dataType': "json",
                'data': formData,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        setTimeout(function(){
                            location.reload();
                        }, 1000)
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
                    $("#rejecttaxDocsForm").find('.importloader').addClass('dNone');
                }
            });
        },
    });


    var displayDataComic = $('.displayDataComic');
   
    if (displayDataComic.length) {
        var pendingval = $("#pendingcomic").val();

        var dt_ajax = displayDataComic.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-publisher-comics",
                method: "get",
                dataType: "json",
                cache: false,
                data:{pagename:pendingval,userid:userid},
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
                { "bVisible": false, "aTargets": [0,4] },
                { "bSortable": false, "aTargets": [ 0,1,4,5,6] },
                { "bSearchable": false, "aTargets": [ 0,1,4,5,6] }
            ],

            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
             buttons: [
            {
            text:
                feather.icons["plus"].toSvg({
                class: "me-50 font-small-4",
                }) + "Add New Comic",
            className: "create-new btn btn-primary dateSetBtn profileOk d-none",
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
    }

});

