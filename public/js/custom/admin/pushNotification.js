


  $(document).ready(function(){

      $(".flatpickr-date-time-new").flatpickr({
          enableTime: true,
          dateFormat: "Y-m-d H:i",
          time_24hr: true,
          minDate: "today",
           autoclose: true,
        });
  });


  $(function() {
       var couponDataTable = $('.notificationDataTable');

      // Ajax Sourced Server-side
      // --------------------------------------------------------------------

      if (couponDataTable.length) {
          var dt_ajax = couponDataTable.dataTable({
              ajax: {
                  url: ajaxUrl + "/admin/get-notification",
                  method: "get",
                  dataType: "json",
                  cache: false,
              },
              processing: true,
              serverSide: true,
              columns: [{
                      data: "id"
                  },{
                      data: "push_title"
                  },
                  {
                      data: "push_description"
                  },
                  {
                      data: "user"
                  },
                  {
                      data: "status"
                  },
                  {
                      data: "updated_at"
                  },
                  {
                      data: "action"
                  }
              ],
              order: [
                  [0, "desc"]
              ],
              "aoColumnDefs": [
                { "bVisible": false, "aTargets": [0] },
                { "bSortable": false, "aTargets": [ 0,6 ] },
                { "bSearchable": false, "aTargets": [ 0,6] }
            ],

              displayLength: 10,
              lengthMenu: [10, 25, 50, 75, 100],
              buttons: [
              {
              text:
                  feather.icons["plus"].toSvg({
                  class: "me-50 font-small-4",
                  }) + "Add New Notifications",
              className: "create-new btn btn-primary",
              attr: {
                  "data-bs-toggle": "modal",
                  "data-bs-target": "#modals-slide-in",
              },
              init: function (api, node, config) {
                  $(node).removeClass("btn-secondary");
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
      }
  });

  //Coupon view code End


  $(document).on("change", ".user", function(){
      var thisVal = $(this).val();
      $(".selectUserType").addClass("d-none")
      if(thisVal == 2)
      $(".selectUserType").removeClass("d-none")
  });

  $(document).on("change", ".pushType", function(){
      var thisVal = $(this).val();
      $(".pushDateTime").addClass("d-none")
      if(thisVal == 2)
      $(".pushDateTime").removeClass("d-none")
  });


  $("#insertForm").validate({
    rules: {
      pushType: {
        required: true,
      },
      pushDateTime: {
        required: true,
      },
      push_title: {
        required: true,
      },
      push_description: {
        required: true,
      },
      user: {
        required: true,
      },
      inactiveDays: {
        required: true,
      },
    },
    submitHandler: function (form) {
      // $("#overlay").fadeIn(300);
      $("#insertForm").find('.importloader').removeClass('dNone');
      $("#insertForm button").attr('disabled', true);

      var forms = $("#insertForm");
      var formDatas = forms.serialize();
      $.ajax({
        url: ajaxUrl + "/admin/insert-notification",
        type: "POST",
        dataType: "json",
        data: formDatas,
        success: function (response) {
              if(response.status){
                   toastr["success"](response.message, "Success");
                    setTimeout(function () {
                       location.reload();
                  }, 2000);
              }
              else{
                  toastr["error"](response.message, "Error");
              }
              // $("#overlay").fadeOut(300);
        },
              error: function (error) {
                //  console.log(error);
              },
              'complete':function(){
                $("#insertForm").find('.importloader').addClass('dNone');
            }
      });
    },
  });


  $("#updateForm").validate({
    rules: {
      pushType: {
        required: true,
      },
      pushDateTime: {
        required: true,
      },
      push_title: {
        required: true,
      },
      push_description: {
        required: true,
      },
      user: {
        required: true,
      },
      inactiveDays: {
        required: true,
      },
    },
    submitHandler: function (form) {
      $("#overlay").fadeIn(300);
      var forms = $("#updateForm");
      var formDatas = forms.serialize();
      $.ajax({
        url: ajaxUrl + "/admin/update-notification",
        type: "POST",
        dataType: "json",
        data: formDatas,
        success: function (response) {
              if(response.status){
                   toastr["success"](response.message, "Success");
                  setTimeout(function () {
                      location.reload();
                  }, 500);
              }
              else{
                  toastr["error"](response.message, "Error");
              }
               $("#overlay").fadeOut(300);
        },
        error: function (error) {
          console.log(error);
        },
      });
    },
  });


  //update view
  $(document).on("click", ".pushView", function(){

      var thisAction = $(this).attr("action");
      $("#btnSection").removeClass("d-none");
        $("#pushHeading").text("Update Notification");
      if(thisAction == "view")
      {
        $("#btnSection").addClass("d-none");
        $("#pushHeading").text("View Notifications");
      }

      $("#updatedId").val(jQuery(this).parents("tr").find(".pushDetails").attr("id"));

      $("#push_title").val(jQuery(this).parents("tr").find(".pushDetails").attr("push_title"));
      $("#push_description").val(jQuery(this).parents("tr").find(".pushDetails").attr("push_description"));
      $("#inactiveDays").val(jQuery(this).parents("tr").find(".pushDetails").attr("inactiveDays"));

      var sendPush = jQuery(this).parents("tr").find(".pushDetails").attr("send_type");
      $("#send_type").val(sendPush);
      $(".pushDateTime").addClass("d-none")
      if(sendPush == 2)
      $(".pushDateTime").removeClass("d-none")


      var user = jQuery(this).parents("tr").find(".pushDetails").attr("user");
      $("#user").val(user);
      $(".selectUserType").addClass("d-none")
      if(user == 2)
      $(".selectUserType").removeClass("d-none")



      var send_datetime = jQuery(this).parents("tr").find(".pushDetails").attr("send_datetime");
      if(send_datetime)
      {
        $(".flatpickr-date-time-new").flatpickr({
          defaultDate: send_datetime,
          enableTime: true,
          dateFormat: "Y-m-d H:i",
          time_24hr: true,
          minDate: "today",
           autoclose: true,
        });
      }

  });

  $(document).on("input",".preventCharacters",function(){
     $(this).val($(this).val().replace(/[^0-9.]/g,''));
  })

  $(document).on("input",".preventCharactersAndDot",function(){
     $(this).val($(this).val().replace(/[^0-9]/g,''));
  })