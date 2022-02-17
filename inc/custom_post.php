<?php 

function cptui_register_my_cpts() {

/**
 * Post Type: Câu hỏi.
 */

$labels = [
    "name" => __( "Câu hỏi", "twentytwentyone" ),
    "singular_name" => __( "Câu hỏi", "twentytwentyone" ),
];

$args = [
    "label" => __( "Câu hỏi", "twentytwentyone" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => [ "slug" => "cau_hoi", "with_front" => true ],
    "query_var" => true,
    "supports" => [ "title", "editor", "thumbnail" ],
    "show_in_graphql" => false,
];

register_post_type( "cau_hoi", $args );

/**
 * Post Type: Bài học.
 */

$labels = [
    "name" => __( "Bài học", "twentytwentyone" ),
    "singular_name" => __( "Bài học", "twentytwentyone" ),
];

$args = [
    "label" => __( "Bài học", "twentytwentyone" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "delete_with_user" => false,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => [ "slug" => "bai_hoc", "with_front" => true ],
    "query_var" => true,
    "supports" => [ "title", "editor", "thumbnail" ],
    "show_in_graphql" => false,
];

register_post_type( "bai_hoc", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

function cptui_register_my_cpts_khoa_hoc() {

	/**
	 * Post Type: Khoá học.
	 */

	$labels = [
		"name" => __( "Khoá học", "custom-post-type-ui" ),
		"singular_name" => __( "Khoá học", "custom-post-type-ui" ),
	];

	$args = [
		"label" => __( "Khoá học", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "khoa_hoc", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "post_tag", "phan_loai" ],
		"show_in_graphql" => false,
	];

	register_post_type( "khoa_hoc", $args );
}

add_action( 'init', 'cptui_register_my_cpts_khoa_hoc' );

function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Phân loại.
	 */

	$labels = [
		"name" => __( "Phân loại", "flatsome" ),
		"singular_name" => __( "Phân loại", "flatsome" ),
	];

	
	$args = [
		"label" => __( "Phân loại", "flatsome" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'phan_loai', 'with_front' => true, ],
		"show_admin_column" => true,
		"show_in_rest" => true,
		"rest_base" => "phan_loai",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "phan_loai", [ "cau_hoi", "bai_hoc", "khoa_hoc" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes' );

