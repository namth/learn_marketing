<?php
/**
 * @package Learn Marketing
 * @version 2.0.1
 */
/*
Plugin Name: Learn Marketing 
Plugin URI: http://9outfit.com
Description: Plugin học marketing cho website wordpress.
Author: Nam
Version: 1.0.1
Author URI: http://9outfit.com
*/

$dir = dirname( __FILE__ );
require_once( $dir . '/inc/custom_post.php');
require_once( $dir . '/inc/custom_field.php');
require_once( $dir . '/template/index.php');

/* Filter the single_template with our custom function*/
add_filter('single_template', 'my_custom_template');

function my_custom_template($single) {

    global $post;

    /*
    * This is a 'bai_hoc' post
    * AND a 'single movie template' is not found on
    * theme or child theme directories, so load it
    * from our plugin directory.
    */
    if ( 'bai_hoc' === $post->post_type ) {
        return plugin_dir_path( __FILE__ ) . 'single-bai_hoc.php';
    }

    if ( 'khoa_hoc' === $post->post_type ) {
        return plugin_dir_path( __FILE__ ) . 'single-khoa_hoc.php';
    }

    if ( 'cau_hoi' === $post->post_type ) {
        return plugin_dir_path( __FILE__ ) . 'single-cau_hoi.php';
    }

    return $single;

}

/* Filter the author page template with our function */
add_filter( 'template_include', 'wpa_155871_template_author' );

function wpa_155871_template_author( $template ) {

    $file = '';

    if ( is_author() ) {
        $file   = 'author.php'; // the name of your custom template
        $find[] = $file;
        $find[] = 'learn_marketing/' . $file; // name of folder it could be in, in user's theme
    } 

    if ( $file ) {
        $template       = locate_template( array_unique( $find ) );
        if ( ! $template ) { 
            // if not found in theme, will use your plugin version
            $template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' . $file;
        }
    }

    return $template;
}

add_action( 'wp_enqueue_scripts', 'learn_mkt_init' );
function learn_mkt_init() {
    wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ), array(), '1.0' );

    wp_enqueue_script( 'fontawesome', 'https://kit.fontawesome.com/dfe5b27416.js', array( 'jquery' ), '5.0', true );
    wp_enqueue_script( 'es6-promise', plugins_url( 'assets/js/es6-promise.auto.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'jspdf', plugins_url( 'assets/js/jspdf.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'html2canvas', plugins_url( 'assets/js/html2canvas.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'html2pdf', plugins_url( 'assets/js/html2pdf.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'custom', plugins_url( 'assets/js/custom.js', __FILE__ ), array( 'jquery' ), '1.0', true );
    wp_localize_script( 'custom', 'misha_ajax_comment_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php'
	) );
}

// Add custom Theme Functions here
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'    => 'Cấu hình học marketing', // Title hiển thị khi truy cập vào Options page
        'menu_title'    => 'Tùy biến chung', // Tên menu hiển thị ở khu vực admin
        'menu_slug'     => 'theme-settings', // Url hiển thị trên đường dẫn của options page
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

function wpse27856_set_content_type()
{
    return "text/html";
}
add_filter('wp_mail_content_type', 'wpse27856_set_content_type');

add_action( 'wp_ajax_ajaxcomments', 'misha_submit_ajax_comment' ); // wp_ajax_{action} for registered user
add_action( 'wp_ajax_nopriv_ajaxcomments', 'misha_submit_ajax_comment' ); // wp_ajax_nopriv_{action} for not registered users

function misha_submit_ajax_comment(){
	/*
	 * Wow, this cool function appeared in WordPress 4.4.0, before that my code was muuuuch mooore longer
	 *
	 * @since 4.4.0
	 */
	$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
	if ( is_wp_error( $comment ) ) {
		$error_data = intval( $comment->get_error_data() );
		if ( ! empty( $error_data ) ) {
			wp_die( '<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure' ), array( 'response' => $error_data, 'back_link' => true ) );
		} else {
			wp_die( 'Unknown error' );
		}
	}
 
	/*
	 * Set Cookies
	 */
	$user = wp_get_current_user();
	do_action('set_comment_cookies', $comment, $user);
 
	/*
	 * If you do not like this loop, pass the comment depth from JavaScript code
	 */
	$comment_depth = 1;
	$comment_parent = $comment->comment_parent;
	while( $comment_parent ){
		$comment_depth++;
		$parent_comment = get_comment( $comment_parent );
		$comment_parent = $parent_comment->comment_parent;
	}
 
 	/*
 	 * Set the globals, so our comment functions below will work correctly
 	 */
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $comment_depth;
	
	/*
	 * Here is the comment template, you can configure it for your website
	 * or you can try to find a ready function in your theme files
	 */
	$comment_html = '<li ' . comment_class('', null, null, false ) . ' id="li-comment-' . get_comment_ID() . '">
		<article class="comment-inner" id="comment-' . get_comment_ID() . '">
            <div class="flex-row align-top">
                <div class="flex-col">
                    <div class="comment-author mr-half">
					' . get_avatar( $comment, 48 ) . '
                    </div>
                </div>
    
                <div class="flex-col flex-grow"><cite class="strong fn">' . get_comment_author_link() . '</cite>
				   ';
					
					// if( $edit_link = get_edit_comment_link() )
					// 	$comment_html .= '<span class="edit-link"><a class="comment-edit-link" href="' . $edit_link . '">Edit</a></span>';
					
				if ( $comment->comment_approved == '0' )
					$comment_html .= '<em>'._e( 'Your comment is awaiting moderation.', 'flatsome' ).'</em>';

			$comment_html .= '<br />
    			<div class="comment-content">
                    ' . apply_filters( 'comment_text', get_comment_text( $comment ), $comment ) . '
                    
                    <div class="comment_source">';
            if ($comment_depth==1) {
                $comment_html .=  "<span class='comment_lable'>Từ bài học: </span>
                        <a>";
                $comment_html .=     get_the_title($comment->comment_post_ID); 
                $comment_html .=  "</a>";
            }
            $comment_html .= '</div>
		</article>
	</li>';
	echo $comment_html;

	die();
	
}

if ( ! function_exists( 'learning_comment' ) ) :
    /**
     * Template for comments and pingbacks.
     *
     * Used as a callback by wp_list_comments() for displaying the comments.
     */
    function learning_comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        switch ( $comment->comment_type ) :
            case 'pingback' :
            case 'trackback' :
        ?>
        <li class="post pingback">
            <p><?php _e( 'Pingback:', 'flatsome' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'flatsome' ), '<span class="edit-link">', '<span>' ); ?></p>
        <?php
                break;
            default :
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <article id="comment-<?php comment_ID(); ?>" class="comment-inner">
    
                <div class="flex-row align-top">
                    <div class="flex-col">
                        <div class="comment-author mr-half">
                            <?php echo get_avatar( $comment, 48 ); ?>
                        </div>
                    </div>
    
                    <div class="flex-col flex-grow">
                        <?php printf( sprintf( '<cite class="strong fn">%s</cite>', get_comment_author_link() ) ); ?>
                        <?php if ( $comment->comment_approved == '0' ) : ?>
                        <em><?php _e( 'Your comment is awaiting moderation.', 'flatsome' ); ?></em>
                         <br />
                        <?php endif; ?>
    
                       <div class="comment-content">
                            <?php comment_text(); ?>
    
                            <div class="comment_source"><?php 
                                if ($depth==1) {
                                    echo "<span class='comment_lable'>Từ bài học: </span>
                                          <a>";
                                    echo    get_the_title($comment->comment_post_ID); 
                                    echo "</a>";
                                }
                            ?></div>
                        </div>
    
                     <div class="comment-meta commentmetadata uppercase is-xsmall clear">
                        <?php //edit_comment_link( __( 'Edit', 'flatsome' ), '<span class="edit-link ml-half strong">', '<span>' ); ?>
    
                            <div class="reply pull-right">
                                <?php
                                    comment_reply_link( array_merge( $args,array(
                                        'depth'     => $depth,
                                        'max_depth' => $args['max_depth'],
                                    ) ) );
                                ?>
                            </div>
                    </div>
    
                    </div>
                </div>
            </article>
        <?php
                break;
        endswitch;
    }
endif; // ends check for flatsome_comment()

# setup video youtube default size 
add_filter( 'embed_defaults', 'wpse150029_change_embed_defaults' );
function wpse150029_change_embed_defaults() {
    return array(
        'width'  => 800, 
        'height' => 400
    );
}

# add avatar to login form
add_action( 'comment_form_logged_in_after', 'psot_comment_form_avatar' );
add_action( 'comment_form_after_fields', 'psot_comment_form_avatar' );
function psot_comment_form_avatar()
{
  ?>
   <div class="comment-avatar">
     <?php 
     $current_user = wp_get_current_user();
     if ( ($current_user instanceof WP_User) ) {
        echo get_avatar( $current_user->user_email, 48 );
     }
     ?>
   </div>
<?php
}