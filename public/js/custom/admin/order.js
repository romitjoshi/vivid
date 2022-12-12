$(function() {
    var dt_ajax_table = $('.displayData');
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-orders",
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
                    data: "price"
                },
                {
                    data: "created_at"
                },
                {
                    data: "order_status"
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
                { "bSortable": false, "aTargets": [ 0,5 ] },
                { "bSearchable": false, "aTargets": [ 0,5] }
            ],

            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
             buttons: [
                 {
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
                            columns: [ 1, 2, 3, 4],
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
                            columns: [ 1, 2, 3, 4],
                        },
                        "action": newexportaction,
                    },
                ],
                init: function(api, node, config) {
                    $(node).removeClass("btn-secondary");
                    $(node).parent().removeClass("btn-group");
                    setTimeout(function() {
                        $(node)
                            .closest(".dt-buttons")
                            .removeClass("btn-group")
                            .addClass("d-inline-flex");
                    }, 50);
                },
               },
        //   {
            //   text:
            //     feather.icons["plus"].toSvg({
            //       class: "me-50 font-small-4",
            //     }) + "Add New Order",
            //   className: "create-new btn btn-primary dateSetBtn profileOk",
            //   attr: {
            //     "data-bs-toggle": "modal",
            //     "data-bs-target": "#modals-slide-create",
            //   },
            //   init: function (api, node, config) {
            //     $(node).removeClass("btn-secondary");
            //   },
        // },
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
          $("div.head-label").html('<h6 class="mb-0">Total Number of Order</h6>');
    }

    var dt_ajax_table = $('.displayNotesData');
      var orderId  = $("#orderId").val();
     if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-orders-notes",
                method: "get",
                dataType: "json",
                data:{'id':orderId},
                cache: false,
            },bFilter: false ,
            processing: true,
            serverSide: true,
            columns: [
                {
                    data: "id"
                },
                {
                    data: "created_at"
                },
            ],
            order: [
                [0, "desc"]
            ],
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [0] }],
            displayLength: 3,
            lengthMenu: [2, 4, 6, 100],
             buttons: [
                 {
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
                            columns: [ 1, 2, 3, 4],
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
                            columns: [ 1, 2, 3, 4],
                        },
                        "action": newexportaction,
                    },
                ],
                init: function(api, node, config) {
                    $(node).removeClass("btn-secondary");
                    $(node).parent().removeClass("btn-group");
                    setTimeout(function() {
                        $(node)
                            .closest(".dt-buttons")
                            .removeClass("btn-group")
                            .addClass("d-inline-flex");
                    }, 50);
                },
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
          $("div.head-label").html('<h6 class="mb-0">Total Number of Notes</h6>');
    }

    $(document).on("click", "#statusUpdateBtn", function (e) {
        $("#statusUpdateBtn").find('.importloader').removeClass('dNone');
     var order_status  = $("#order_status").val();
     //alert(order_status);
     var orderId  = $("#orderId").val();

     $.ajax({
        'url': ajaxUrl + "/admin/order-status",
        'type': "POST",
        'dataType': "json",
        'data': {"order_status":order_status,"orderId":orderId},
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
            $("#statusUpdateBtn").find('.importloader').addClass('dNone');
        }

     });
    });


    $("#insertForm").validate({
        rules: {
            'note': {
                required: true,
                minlength: 2,
            },
        },
        submitHandler: function (form) {
            var thisForm = $(this);

            $("#insertForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#insertForm").get(0));

            $.ajax({
                url: ajaxUrl + "/admin/insert-order-note",
                type: "POST",
                dataType: "json",
                processData:false,
                contentType:false,
                data: formData,
                success: function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        // setTimeout(function(){
                        //     window.location.href = ajaxUrl + '/admin/order';
                        // },1000)
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
        $("#note").val(
            jQuery(this).attr("note")
        );

        var notify_customer = jQuery(this).attr("notify_customer");
        if(notify_customer == 1)
        {
            $("#notify_customer").attr('checked', false);
        }
        else
        {
            $("#notify_customer").attr('checked', true);
        }
      });

});