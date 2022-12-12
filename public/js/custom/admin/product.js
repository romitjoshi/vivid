$(function() {
    var dt_ajax_table = $('.displayData');

    // Ajax Sourced Server-side
    // --------------------------------------------------------------------
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            ajax: {
                url: ajaxUrl + "/admin/get-product",
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
                    data: "featured_Image"
                },
                {
                    data: "product_name"
                },
                {
                    data: "price"
                },
                {
                    data: "special_price"
                },
                {
                    data: "available_qty"
                },
                {
                    data: "sold_qty"
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
                { "bSortable": false, "aTargets": [ 0,1,6,7 ] },
                { "bSearchable": false, "aTargets": [ 0,1,6,7] }
            ],

            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
             buttons: [
          {
          text:
            feather.icons["plus"].toSvg({
              class: "me-50 font-small-4",
            }) + "Add New Product",
          className: "btn btn-primary addProduct",
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
          $("div.head-label").html('<h6 class="mb-0">Total Number of Product</h6>');
    }
    $("#insertForm").validate({
        rules: {
            'product_type': {
                required: true,
            },
            'product_name': {
                required: true,
                minlength: 2,
                maxlength: 60,
            },
            'featured_Image': {
                required: true,
            },

            'description' : {
                required: true,
            },
            'price' : {
                required: true,
            },
            'comic[]' : {
                required: true,
            },
            'available_qty' : {
                required: true,
            },
            'min_comics' :{
                required: true,
            },
            'max_comics':{
                required:true,
            }
        },
        submitHandler: function (form) {
            var thisForm = $(this);

            $("#insertForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#insertForm").get(0));
            $.ajax({
                url: ajaxUrl + "/admin/insert-product",
                type: "POST",
                dataType: "json",
                processData:false,
                contentType:false,
                data: formData,


                success: function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        setTimeout(function(){
                            window.location.href = ajaxUrl + '/admin/product';
                        },1000)
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

   //here we add new link button class when we click
    $(document).on("click", ".addProduct", function (e) {

        window.location.href = ajaxUrl + '/admin/insert-product-view';
    });
     //update here
     $("#updateForm").validate({
        rules: {
            'product_name': {
                required: true,
                minlength: 2,
                maxlength: 60,
            },
            'description' : {
                required: true,
            },
            'price' : {
                required: true,
            },
            // 'special_price' : {
            //     required: true,
            //     greaterThan: "#price",
            // },
            'available_qty' : {
                required: true,
            }

        },
        submitHandler: function (form) {
            var thisForm = $(this);
            $("#updateForm").find('.importloader').removeClass('dNone');
            var formData = new FormData($("#updateForm").get(0));
            $.ajax({
                'url': ajaxUrl + "/admin/update-product",
                'type': "POST",
                'dataType': "json",
                processData:false,
                contentType:false,
                'data': formData,
                'success': function (response) {
                    if(response.status)
                    {
                        toastr["success"](response.message, "Success");
                        setTimeout(function(){
                            window.location.href = ajaxUrl + '/admin/product';
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
                'url': ajaxUrl + "/admin/delete-product",
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



    $.validator.addMethod("greaterThan",function (value, element, param) {
        var $otherElement = $(param);
        return parseInt(value, 10) <= parseInt($otherElement.val(), 10);
  }, 'Special price need to be less than original price');



    $(document).on("click", ".deleteImages", function (e) {
        var thisEle = $(this);
        var thisVal = thisEle.attr("imagename");
        var product_id = thisEle.attr("product_id");

        $.ajax({
            'url': ajaxUrl + "/admin/delete-image",
            'type': "POST",
            'dataType': "json",
            'data': {thisVal:thisVal,product_id:product_id},
            'success': function (response) {
                if(response.status)
                {
                    console.log("here")
                    thisEle.parents(".deleteImageSection").remove();
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

            }
        });
    });


    $('select[name="comic[]"]').select2({
        allowClear: true,
        placeholder: 'Choose comic name..',
        ajax: {
            url: ajaxUrl + "/admin/get-comic-by-name",
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term,
                    selected: true
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: false
        }
    });

    $(document).on("change", "#product_type", function(){
        var thisval = $(this).val();
        //alert(thisval)
        $(".configProductSec").addClass("d-none")
        if(thisval == 2)
        {
            $(".configProductSec").removeClass("d-none")
        }
    })

});

