$(function() {

    var dt_ajax_table = $('.displayData');

    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var comicId = $("#updateid").val();
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-episode",
                method: "get",
                dataType: "json",
                data:{'id':comicId},
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
                    data: "impression"
                },
                {
                    data: "description"
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
        },
       ],
            language: {
                paginate: {
                    // remove previous & next text from pagination
                    previous: "&nbsp;",
                    next: "&nbsp;",
                },
            },
          });
    }



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
                'url': ajaxUrl + "/admin/delete-episode",
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
            name: {
                required: true,
                minlength: 2,
            },
            description: {
                required: true,
                minlength: 2,
            },
        },
        submitHandler: function (form) {
            //alert('sdfsadf');
            var thisForm = $(this);
            $("#updateForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#updateForm").get(0));
            // var forms = $("#updateForm");
            // var formData = forms.serialize();
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




});

// var images_uploaded = $('.image_container').length;
// function upload_image(file,parent,name){
// if(file.type.includes('image/') || (file.type.includes('jpg') || file.type.includes('png') || file.type.includes('jpeg'))){
//   images_uploaded++;
//   var this_imagenum = images_uploaded;
//   var reader = new FileReader();
//   reader.onload = function(e){
//     parent.find('.no-data').remove();
//     parent.append('<div id="upload-image-'+this_imagenum+'" class="image_container d-flex justify-content-center position-relative"><a href="javascript:void(0)" class="remove-image">&times;</a><img src="'+e.target.result+'"></div>');
//     var fd = new FormData();
//     fd.append('image',file);
//     fd.append('_token',csrf_token());
//     $.ajax({
//       'url':url("admin/upload_image"),
//       'type':'post',
//       'dataType':'json',
//       'processData':false,
//       'contentType':false,
//       'data':fd,
//       'beforeSend':function(){
//         $('#upload-image-'+this_imagenum).append('<div class="loading-div pos-acc w-100 h-100 custom-bg-16"><div class="pos-acc fnt-30p custom-text-7">'+loader_icon+'</div></div>');
//       },
//       'success':function(data){
//         $('#upload-image-'+this_imagenum+' .loading-div').remove();
//         if(data.success){
//           $('#upload-image-'+this_imagenum+' img').attr('src',data.imageurl);
//           $('#upload-image-'+this_imagenum).append('<input type="hidden" name="images[]" value="'+data.imagename+'">');
//         }
//       },
//       'error':function(request_error){
//         $('#upload-image-'+this_imagenum+' .loading-div').remove();
//         $('#upload-image-'+this_imagenum).append('<div class="error-div pos-acc w-100 h-100 custom-bg-16"><div class="pos-acc fnt-30p custom-text-17"><i class="fa fa-exclamation-triangle"></i></div></div>');
//         setTimeout(function(){faderemove($('#upload-image-'+this_imagenum));},4000);
//       }
//     });
//   };
//   reader.readAsDataURL(file);
// }
// }
$(document).on('click','.remove-image',function(){
$(this).closest('.image_container').remove();
});

