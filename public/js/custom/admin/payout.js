$(function() {
    var dt_ajax_table = $('.displayData');

    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-payout",
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
                    data: "amount"
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
                { "bSortable": false, "aTargets": [0,1,4] },
                { "bSearchable": false, "aTargets": [0,1,4] }
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
              class: "me-50 font-small-4  d-none",
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
        //   $("div.head-label").html('<h6 class="mb-0">Total Number of Comic</h6>');
    }

    $("#updateForm").validate({
        rules: {
            amount: {
                required: true,

            },
            note:{
                required:true,
            },
            status: {
                required: true,

            },

        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#updateForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#updateForm").get(0));
            $.ajax({
                'url': ajaxUrl + "/admin/update-payout",
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
                'url': ajaxUrl + "/admin/delete-payout",
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

    $(document).on("click", ".item-edit", function (e) {
        $("#amount").val(
            jQuery(this).parents("tr").find(".dataField").attr("amount")
        );
        $("#note").val(
            jQuery(this).parents("tr").find(".dataField").attr("admin_note")
        );
        $("#status").val(
            jQuery(this).parents("tr").find(".dataField").attr("status")
        );
        $("#updateid").val(
            jQuery(this).parents("tr").find(".dataField").attr("id")
            );

    });

});


