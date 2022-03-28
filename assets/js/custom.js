jQuery(document).ready(function ($) {
    $("#add_user").click(function () {
        $(this).hide();
        $(".add_new_user form").show(200);
    });

    $('.closed_button').click(function () {
        $(".add_new_user form").hide();
        $("#add_user").show(100);
    });

    var width = $('.fixed_div').width();
    var height = $('.fixed_div').height() + 2;
    if(height > ($(window).height() - 117) ) {
        height = $(window).height() - 137;
    }
    var scroll_height = $('.main_scroll').outerHeight();
    $(window).scroll(function fix_element() {
        var top_pos = scroll_height - ($(window).scrollTop() + height);
        $('.fixed_div').css(
        ($(window).scrollTop() > 100 && (top_pos > 0) )
            ? { 'position': 'fixed', 'top': '117px', 'width': width, 'height': height }
            : { 'position': 'relative', 'top': 'auto' }
        );
        if (($(window).scrollTop() > 100 && (top_pos < 0) )) {
            $('.fixed_div').css(
                { 'position': 'fixed', 'top': (117 + top_pos) }
            );
        }
        return fix_element;
    }());

    console.log($(window).scrollTop());
});


