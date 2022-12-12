$(function() {
      var dt_ajax_table = $('.displayData');
      // Ajax Sourced Server-side
      // --------------------------------------------------------------------
      if (dt_ajax_table.length) {
          var dt_ajax = dt_ajax_table.dataTable({
              ajax: {
                  url: ajaxUrl + "/admin/get-category",
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
                      data: "category_name"
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
                { "bSortable": false, "aTargets": [ 0, 3] },
                { "bSearchable": false, "aTargets": [ 0, 3] }
            ],


              displayLength: 10,
              lengthMenu: [10, 25, 50, 75, 100],
              buttons: [
            {
            text:
              feather.icons["plus"].toSvg({
                class: "me-50 font-small-4",
              }) + "Add New Category",
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
            $("div.head-label").html('<h6 class="mb-0">Total Number of Category</h6>');
      }

      $("#insertForm").validate({
          rules: {
              category_name: {
                  required: true,
                  minlength: 2,
              },
          },
          submitHandler: function (form) {
              var thisForm = $(this);
              $("#insertForm").find('.importloader').removeClass('dNone');
              var forms = $("#insertForm");
              var formData = forms.serialize();
              $.ajax({
                  'url': ajaxUrl + "/admin/insert-category",
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
                    //   console.log(error);
                  },
                  'complete':function(){
                      $("#insertForm").find('.importloader').addClass('dNone');
                  }
              });
          },
      });

      $("#updateForm").validate({
        rules: {
            category_name: {
                required: true,
                minlength: 2,
            },
            status: {
                required: true,
            },
        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#updateForm").find('.importloader').removeClass('dNone');
            var forms = $("#updateForm");
            var formData = forms.serialize();
            $.ajax({
                'url': ajaxUrl + "/admin/update-category",
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
                'url': ajaxUrl + "/admin/delete-category",
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
        $("#category_name").val(
            jQuery(this).parents("tr").find(".dataField").attr("category_name")
        );
        $("#status").val(
            jQuery(this).parents("tr").find(".dataField").attr("status")
        );
        $("#updateid").val(
            jQuery(this).parents("tr").find(".dataField").attr("id")
        );
      });

});