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
    var height = $('.fixed_div').outerHeight() + 2;
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

    // console.log($(window).scrollTop());

    jQuery.extend(jQuery.fn, {
        /*
         * check if field value lenth more than 3 symbols ( for name and comment ) 
         */
        validate: function () {
            if (jQuery(this).val().length < 3) {jQuery(this).addClass('error');return false} else {jQuery(this).removeClass('error');return true}
        },
        /*
         * check if email is correct
         * add to your CSS the styles of .error field, for example border-color:red;
         */
        validateEmail: function () {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
                emailToValidate = jQuery(this).val();
            if (!emailReg.test( emailToValidate ) || emailToValidate == "") {
                jQuery(this).addClass('error');return false
            } else {
                jQuery(this).removeClass('error');return true
            }
        },
    });
     
    /*
     * On comment form submit
     */
    $( '#commentform' ).submit(function(){
    
        // define some vars
        var button = $('#submit'), // submit button
            respond = $('#respond'), // comment form container
            commentlist = $('.comment-list'), // comment list container
            cancelreplylink = $('#cancel-comment-reply-link');
            
        // if user is logged in, do not validate author and email fields
        if( $( '#author' ).length )
            $( '#author' ).validate();
        
        if( $( '#email' ).length )
            $( '#email' ).validateEmail();
            
        // validate comment in any case
        $( '#comment' ).validate();
        
        // if comment form isn't in process, submit it
        if ( !button.hasClass( 'loadingform' ) && !$( '#author' ).hasClass( 'error' ) && !$( '#email' ).hasClass( 'error' ) && !$( '#comment' ).hasClass( 'error' ) ){
            
            // ajax request
            $.ajax({
                type : 'POST',
                url : misha_ajax_comment_params.ajaxurl, // admin-ajax.php URL
                data: $(this).serialize() + '&action=ajaxcomments', // send form data + action parameter
                beforeSend: function(xhr){
                    // what to do just after the form has been submitted
                    button.addClass('loadingform').val('Loading...');
                },
                error: function (request, status, error) {
                    if( status == 500 ){
                        alert( 'Error while adding comment' );
                    } else if( status == 'timeout' ){
                        alert('Error: Server doesn\'t respond.');
                    } else {
                        // process WordPress errors
                        var wpErrorHtml = request.responseText.split("<p>"),
                            wpErrorStr = wpErrorHtml[1].split("</p>");
                            
                        alert( wpErrorStr[0] );
                    }
                },
                success: function ( addedCommentHTML ) {
                
                    // if this post already has comments
                    if( commentlist.length > 0 ){
                    
                        // if in reply to another comment
                        if( respond.parent().hasClass( 'comment' ) ){
                        
                            // if the other replies exist
                            if( respond.parent().children( '.children' ).length ){	
                                respond.parent().children( '.children' ).append( addedCommentHTML );
                            } else {
                                // if no replies, add <ol class="children">
                                addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
                                respond.parent().append( addedCommentHTML );
                            }
                            // close respond form
                            cancelreplylink.trigger("click");
                        } else {
                            // simple comment
                            commentlist.append( addedCommentHTML );
                        }
                    }else{
                        // if no comments yet
                        addedCommentHTML = '<ol class="comment-list">' + addedCommentHTML + '</ol>';
                        respond.before( $(addedCommentHTML) );
                    }
                    // clear textarea field
                    $('#comment').val('');
                },
                complete: function(){
                    // what to do after a comment has been added
                    button.removeClass( 'loadingform' ).val( 'Post Comment' );
                }
            });
        }
        return false;
    });

    /* 
     * Select tabs on the lesson
     */
    $('.action_bar ul li a').click(function(){
        var tabs = $(this).data('tab');
        $('.action_bar ul li').removeClass('active');
        $(this).parent().addClass('active');
        $('.main_scroll > div > article').hide();
        $(tabs).show(300);

        return false;
    });
});


