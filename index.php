<?php
/**
 * @package Learn Marketing
 * @version 1.0.1
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
