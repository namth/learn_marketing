<?php

add_shortcode( "list_khoahoc", "list_khoahoc" );
function list_khoahoc() {
    # danh sách khoá học private
    $current_user = wp_get_current_user();
    $active = get_field('active','user_' . $current_user->ID);
    $own_courses = get_field('own_courses','user_' . $current_user->ID);
    $groups = get_field('groups','user_' . $current_user->ID);

    $courses = array();
    foreach ($groups as $group) {
        $user_id = $group['group_leader'];
        $group_courses = get_field('own_courses','user_' . $user_id);
        $courses = array_merge($courses, $group_courses);
    }

    $courses = array_unique(array_merge($courses, $own_courses));

    $icon_first = '<i class="fa-solid fa-book-journal-whills"></i>';
    echo "<h2>Các khoá học dành riêng cho bạn</h2>";
    echo "<ul class='course_list'>";

    foreach ($own_courses as $course) {
        $course_id = $course['own_course'];
        echo "<li>";
        echo '<b><a href="' . get_the_permalink($course_id) . '">' . $icon_first . get_the_title($course_id) . '</a></b>';
        echo "</li>";
    }
    echo "</ul>";

    # danh sách khoá học public
    echo "<br>";
    echo "<h2>Danh sách khoá học</h2>";
    echo "<ul class='course_list'>";

    $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

    $args   = array(
        'post_type'     => 'khoa_hoc',
        'paged'         => $paged,
        'posts_per_page'=> 20,
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => 'course_type',
                'compare'   => '=',
                'value'     => 'Public',
            ),
        ),
    );

    $query = new WP_Query( $args );

    if( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            $course_id = get_the_ID();
            echo "<li>";
            echo '<b><a href="' . get_the_permalink($course_id) . '">' . $icon_first . get_the_title($course_id) . '</a></b>';
            echo "</li>";
        }
    }
    echo "</ul>";
    echo '<div class="pagination justify-content-center">';
    
    $big = 999999999; // need an unlikely integer
    echo paginate_links( array(
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => '?paged=%#%',
        'current'   => max( 1, get_query_var('paged') ),
        'total'     => $query->max_num_pages,
        'type'      => 'list',
    ) );

    echo '</div>';
}