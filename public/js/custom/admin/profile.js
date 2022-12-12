
      $("#generalForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
            },
            email: {
                required: true,
            },
        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#generalForm").find('.importloader').removeClass('dNone');
            // var forms = $("#generalForm");
            var formData = new FormData($("#generalForm").get(0));

            $.ajax({
                'url': ajaxUrl + "/admin/update-profile",
                'type': "POST",
                'dataType': "json",
                'data': formData,
                'processData':false,
                'contentType':false,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
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
                    $("#generalForm").find('.importloader').addClass('dNone');
                }
            });
        },
    });
    $("#passwordForm").validate({
        rules: {
            oldpassword: {
                required: true,
                minlength: 2,
            },
            newpassword: {
                required: true,
                minlength: 2,
            },
            retypenewpassword: {
                required: true,
                minlength: 2,
            },
        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#passwordForm").find('.importloader').removeClass('dNone');
            var forms = $("#passwordForm");
            var formData = forms.serialize();
            $.ajax({
                'url': ajaxUrl + "/admin/update-password",
                'type': "POST",
                'dataType': "json",
                'data': formData,
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
                    $("#passwordForm").find('.importloader').addClass('dNone');
                }
            });
        },
    });

