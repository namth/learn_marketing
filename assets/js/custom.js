jQuery(document).ready(function ($) {
    $("#add_user").click(function () {
        $(this).hide();
        $(".add_new_user form").show(200);
    });

    $('.closed_button').click(function () {
        $(".add_new_user form").hide();
        $("#add_user").show(100);
    });
});
