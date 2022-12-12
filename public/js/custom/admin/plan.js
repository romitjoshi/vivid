$(function() {
    var dt_ajax_table = $('.displayData');
    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-plan",
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
                    data: "price"
                },
                {
                    data: "type"
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
          text:
            feather.icons["plus"].toSvg({
              class: "me-50 font-small-4 " , 
            })  ,
          className: "create-new btn btn-primary dateSetBtn profileOk d-none ",
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
          $("div.head-label").html('<h6 class="mb-0">Total Number of Plan</h6>');
    }
    
   
    $("#updateForm").validate({
      rules: {
          price: {
              required: true,
          },
          
      },
      submitHandler: function (form) {
          var thisForm = $(this);
          $("#updateForm").find('.importloader').removeClass('dNone');
          var forms = $("#updateForm");
          var formData = forms.serialize();
          $.ajax({
              'url': ajaxUrl + "/admin/update-plan",
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
                  $("#updateForm").find('.importloader').addClass('dNone');
              }
          });
      },
    });


    // $("#deleteForm").validate({
    //   rules: {
    //       deleteid: {
    //           required: true,
    //       },
    //   },
    //   submitHandler: function (form) {
    //       var thisForm = $(this);
    //       $("#deleteForm").find('.importloader').removeClass('dNone');
    //       var forms = $("#deleteForm");
    //       var formData = forms.serialize();
    //       $.ajax({
    //           'url': ajaxUrl + "/admin/delete-plan",
    //           'type': "POST",
    //           'dataType': "json",
    //           'data': formData,
    //           'success': function (response) {
    //               if(response.status)
    //               {
    //                   toastr["success"](response.message, "Success");
    //                   dt_ajax.DataTable().ajax.reload();
    //                   $(".btn-close").trigger('click');
    //               }
    //               else
    //               {
    //                   toastr["error"](response.message, "Error");
    //               }
    //           },
    //           'error': function (error) {
    //               console.log(error);
    //           },
    //           'complete':function(){
    //               $("#deleteForm").find('.importloader').addClass('dNone');
    //           }
    //       });
    //   },
    // });
  
    $(document).on("click", ".item-edit", function (e) {
         
      $("#price").val(
          jQuery(this).parents("tr").find(".dataField").attr("price")
          
      );
      $("#type").val(
          jQuery(this).parents("tr").find(".dataField").attr("type")
      );
      $("#updateid").val(
          jQuery(this).parents("tr").find(".dataField").attr("id")
      );
    });    

});