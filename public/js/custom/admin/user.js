$(function() {
    var dt_ajax_table = $('.displayData');

    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-user",
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
                  data: "user_type"
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
              { "bSortable": false, "aTargets": [ 0, 3,4,5 ] },
              { "bSearchable": false, "aTargets": [ 0, 3,4,5] }
          ],

            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
             buttons: [
          {
            text:
            feather.icons["plus"].toSvg({
            class: "me-50 font-small-4 d-none",
            }) + "Add New user",
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
      $("div.head-label").html('<h6 class="mb-0">Total Number of User</h6>');
    }

    $(document).on("click", ".confirm-text", function(){
    var statusId= jQuery(this).attr("updateid");
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
                'url': ajaxUrl + "/admin/statuschange",
                'type': "POST",
                'dataType': "json",
                'data':{"statusId":statusId},
                'success': function (response) {
                    if(response.status)
                    {
                      dt_ajax.DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Status!',
                            text: 'User Status Change Successfully..',
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

//here user js

    $(document).on('click', '.tabSectionBusiness', function(){
      $('.tabSectionBusiness').removeClass('active');
      $(this).addClass('active');

      //sectionHide
      var sectionVal = $(this).attr('sectionVal');
      $('.tabSectionHideBusiness').addClass('d-none');

      if(sectionVal == 'first'){
           $('#firstSection').removeClass('d-none');
      }else if(sectionVal == 'second'){
           $('#secondSection').removeClass('d-none');
      }
      else if(sectionVal == 'third'){
        $('#thirdSection').removeClass('d-none');
      }
      else if(sectionVal == 'fourth'){
        $('#fourthSection').removeClass('d-none');
      }
      else {
          $('#fifthSection').removeClass('d-none');
      }
  });

  //review show here  	 displayDataLibrary
  var displayDataReview = $('.displayDataReview');
  var userid  = $("#userid").val();
  if (displayDataReview.length) {
    var dt_ajax = displayDataReview.dataTable({
        ajax: {
            url: ajaxUrl + "/admin/get-user-ratings",
            method: "get",
            dataType: "json",
            cache: false,
            data:{'userid':userid},
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
                data: "ratings"
            },
            {
                data: "created_at"
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
          class: "me-50 font-small-4 d-none",
        }) ,
      className: "btn btn-primary  d-none",
      attr: {
        "data-bs-toggle": "modal",
        "data-bs-target": "#modals-slide-create",
      },
      init: function (api, node, config) {
        $(node).removeClass("btn-secondary");
      },
    }, ],

        language: {
            paginate: {
                previous: "&nbsp;",
                next: "&nbsp;",
            },
        },
    });
  }

  var displayDataLibrary = $('.displayDataLibrary');
  var userid  = $("#userid").val();
  if (displayDataLibrary.length) {
    var dt_ajax = displayDataLibrary.dataTable({
        ajax: {
            url: ajaxUrl + "/admin/get-user-library",
            method: "get",
            dataType: "json",
            cache: false,
            data:{'userid':userid},
         },
        processing: true,
        serverSide: true,
        columns: [
            {
                data: "id"
            },
            {
              data: "order_request_id"
            },
            {
              data:"amount"
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
          class: "me-50 font-small-4 d-none",
        }) ,
      className: "btn btn-primary  d-none",
      attr: {
        "data-bs-toggle": "modal",
        "data-bs-target": "#modals-slide-create",
      },
      init: function (api, node, config) {
        $(node).removeClass("btn-secondary");
      },
    }, ],

        language: {
            paginate: {
                previous: "&nbsp;",
                next: "&nbsp;",
            },
        },
    });
  }

  var displayCoin = $('.displayCoin');
  var userid =$("#userid").val();
  if (displayCoin.length) {
    var dt_ajax_displayCoin = displayCoin.dataTable({
        ajax: {
            url: ajaxUrl + "/admin/get-user-coin",
            method: "get",
            dataType: "json",
            cache: false,
            data:{'userid':userid},
         },
        processing: true,
        serverSide: true,
        columns: [
            {
                data: "id"
            },
            {
              data: "created_at"
            },

            {
              data:"coins"
            },
            {
              data: "description"
            },
            {
              data: "transation_id"
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
          class: "me-50 font-small-4 d-none",
        }) ,
      className: "btn btn-primary  d-none",
      attr: {
        "data-bs-toggle": "modal",
        "data-bs-target": "#modals-slide-create",
      },
      init: function (api, node, config) {
        $(node).removeClass("btn-secondary");
      },
    }, ],

        language: {
            paginate: {
                previous: "&nbsp;",
                next: "&nbsp;",
            },
        },
    });
  }

  var displayDataComic = $('.displayDataComic');
  var userid  = $("#userid").val();
  if (displayDataComic.length) {
    var dt_ajax = displayDataComic.dataTable({
        ajax: {
            url: ajaxUrl + "/admin/get-user-comic",
            method: "get",
            dataType: "json",
            cache: false,
            data:{'userid':userid},
        },
        processing: true,
        serverSide: true,
        columns: [
            {
                data: "id"
            },
            {
              data: "comic_name"
            },
            {
              data: "episode_name"
            },
            {
              data: "page_number"
            },
            {
              data: "action"
            },

        ],
        order: [
            [0, "desc"]
        ],
        "aoColumnDefs": [{ "bVisible": false, "aTargets": [0, 4] }],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
         buttons: [
      {
      text:
        feather.icons["plus"].toSvg({
          class: "me-50 font-small-4 d-none",
        }) ,
      className: "btn btn-primary  d-none",
      attr: {
        "data-bs-toggle": "modal",
        "data-bs-target": "#modals-slide-create",
      },
      init: function (api, node, config) {
        $(node).removeClass("btn-secondary");
      },
    }, ],

        language: {
            paginate: {
                previous: "&nbsp;",
                next: "&nbsp;",
            },
        },
    });
  }

  $("#addCoinsFirst").validate({
    rules: {
      coins: {
            required: true,
            min: 1,
            max: 1000,
        },
    },
    submitHandler: function (form) {
        var thisForm = $(this);
        $("#addCoinsFirst").find('.importloader').removeClass('dNone');
        var forms = $("#addCoinsFirst");
        var formData = forms.serialize();
        $.ajax({
            'url': ajaxUrl + "/admin/add-coins",
            'type': "POST",
            'dataType': "json",
            'data': formData,
            'success': function (response) {
                if(response.status)
                {
                    toastr["success"](response.message, "Success");
                    dt_ajax_displayCoin.DataTable().ajax.reload();
                    $(".btn-close").trigger('click');
                }
                else
                {
                    toastr["error"](response.message, "Error");
                }
                $('#addCoinsFirst')[0].reset();
            },
            'error': function (error) {
              //   console.log(error);
            },
            'complete':function(){
                $("#addCoinsFirst").find('.importloader').addClass('dNone');
            }
        });
    },
});

});

