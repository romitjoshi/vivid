toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: 500,
    hideDuration: 1000,
    timeOut: 5000,
    extendedTimeOut: 1000,
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
};

$(document).on('draw.dt ajaxComplete',function(){
	feather.replace();
    var delimiterMask = $('.autioTimer');
    if (delimiterMask.length) {
        delimiterMask.each(function(){
            new Cleave($(this), {
              delimiter: ':',
              numeralPositiveOnly: true,
              blocks: [2, 2, 2],
              uppercase: true
            });
        });
    }
});

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).on("input",".preventCharacters",function(){
    $(this).val($(this).val().replace(/[^0-9.]/g,''));
});

$(document).on("input",".preventCharactersAndDot",function(){
    $(this).val($(this).val().replace(/[^0-9]/g,''));
});

$(document).on("click", ".item-delete", function(){
    $("#deleteid").val(
        jQuery(this).parents("tr").find(".dataField").attr("id")
    );
});


function newexportaction(e, dt, button, config) {
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one('preXhr', function(e, s, data) {
        // Just this once, load all data from the server...
        data.start = 0;
        data.length = 2147483647;
        dt.one('preDraw', function(e, settings) {
            // Call the original action function
            if (button[0].className.indexOf('buttons-copy') >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one('preXhr', function(e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
}
$(document).on('click', '.create-new', function(){
    $('#insertForm')[0].reset();
});

$('select[name="category[]"]').select2({
    allowClear: true,
    placeholder: 'Type Category name..',
    ajax: {
        url: ajaxUrl + "/admin/get-category-by-name",
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

