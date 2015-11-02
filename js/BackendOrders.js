$(function () {
    $('.filter-wrap > .heading').on('click', function () {
        var cnt = $(this).closest('.filter-wrap').find('.filter-list');

        if ($(this).hasClass('active')) {
            $('.heading i').removeClass('darr').addClass('rarr');
            $(this).removeClass('active');
        } else {
            $('.heading i').removeClass('rarr').addClass('darr');
            $(this).addClass('active');
        }

        cnt.toggle();
    });

    $('ul.filter-list input').on('change', function () {
        if($(this).attr('name') == 'price_from') {
            if($(this).val() != $( "#filterinorder_slider-range" ).slider( "values", 0 )) {
                if($(this).val() <= $( "#filterinorder_slider-range" ).slider( "option", "min" )) {
                    $(this).val($( "#filterinorder_slider-range" ).slider( "option", "min" ));
                    $( "#filterinorder_slider-range" ).slider( "values", 0 , $( "#filterinorder_slider-range" ).slider( "option", "min" ));
                } else if($(this).val() >= $( "#filterinorder_slider-range" ).slider( "values", 1 )) {
                    $(this).val($( "#filterinorder_slider-range" ).slider( "values", 1 ));
                    $( "#filterinorder_slider-range" ).slider( "values", 0 , $( "#filterinorder_slider-range" ).slider( "values", 1 ));
                } else {
                    $( "#filterinorder_slider-range" ).slider( "values", 0 , $(this).val());
                }
            }
        } else if ($(this).attr('name') == 'price_to') {
            if($(this).val() != $( "#filterinorder_slider-range" ).slider( "values", 1 )) {
                if($(this).val() <= $( "#filterinorder_slider-range" ).slider( "values", 0 )) {
                    $(this).val($( "#filterinorder_slider-range" ).slider( "values", 0 ));
                    $( "#filterinorder_slider-range" ).slider( "values", 1 , $( "#filterinorder_slider-range" ).slider( "values", 0 ));
                } else if($(this).val() >= $( "#filterinorder_slider-range" ).slider( "option", "max" )) {
                    $(this).val($( "#filterinorder_slider-range" ).slider( "option", "max" ));
                    $( "#filterinorder_slider-range" ).slider( "values", 1 , $( "#filterinorder_slider-range" ).slider( "option", "max" ));
                } else {
                    $( "#filterinorder_slider-range" ).slider( "values", 1 , $(this).val());
                }
            }
        } 
        
        var form = $(this).closest('form');
        var href = form.serialize();
        var view = $('#s-orders-views li.selected').data('view');
        form.find('.filters_href').attr('href', '#/orders/hash=' + encodeURIComponent(href) + '&view=' + view + '/');
        window.location = form.find('.filters_href').attr('href');
    });

    $('.filter-items .filter-head').on('click', function () {
        $(this).siblings('.filter-params').slideToggle();
        var arr = $(this).children('i');
        if ($(this).hasClass('active')) {
            arr.removeClass('darr').addClass('rarr');
            $(this).removeClass('active');
        } else {
            arr.removeClass('rarr').addClass('darr');
            $(this).addClass('active');
        }
    });

    $("#filterinorder_from").datepicker({
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        numberOfMonths: 1,
        maxDate: new Date(),
        onClose: function (selectedDate) {
            $("#filterinorder_to").datepicker("option", "minDate", selectedDate);
        }
    });

    $("#filterinorder_to").datepicker({
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        numberOfMonths: 1,
        maxDate: new Date(),
        onClose: function (selectedDate) {
            $("#filterinorder_from").datepicker("option", "maxDate", selectedDate);
        }
    });
});

function str_replace(haystack, needle, replacement) {
    var temp = haystack.split(needle);
    return temp.join(replacement);
}