<?php

add_shortcode( "list_khoahoc", "list_khoahoc" );
function list_khoahoc() {
    # danh sách khoá học private
    $current_user = wp_get_current_user();
    $active = get_field('active','user_' . $current_user->ID);
    $own_courses = get_field('own_courses','user_' . $current_user->ID);
    $groups = get_field('groups','user_' . $current_user->ID);

    $courses = array();
    if($groups){
        foreach ($groups as $group) {
            $user_id = $group['group_leader'];
            $group_courses = get_field('own_courses','user_' . $user_id);
            $courses = array_merge($courses, $group_courses);
        }
    }

    if (is_array($own_courses)) {
        $courses = array_unique(array_merge($courses, $own_courses));
    }

    $icon_first = '<i class="fa-solid fa-book-journal-whills"></i>';
    if ($own_courses) {
        echo "<h2>Các khoá học dành riêng cho bạn</h2>";
        echo "<div class='row large-columns-4'>";
    
        foreach ($own_courses as $course) {
            $course_id = $course['own_course'];
            echo "<div class='col post-item'>
                    <div class='box box-normal box-text-bottom'>
                        <div class='box-image'>
                            <div class='image-cover' style='padding-top:56.25%;'>
            ";
            echo get_the_post_thumbnail();
            echo "          </div>
                        </div>
                    </div>
                    <div class='box-text'>";
            echo '      <b><a href="' . get_the_permalink($course_id) . '">' . get_the_title($course_id) . '</a></b>';
            echo "  </div>
                </div>";
        }
        echo "</div>";
    }

    # danh sách khoá học public
    echo "<br>";
    echo "<h2>Danh sách khoá học</h2>";
    echo "<div class='row large-columns-4'>";

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
            echo "<div class='col post-item'>
                    <div class='box box-normal box-text-bottom'>
                        <div class='box-image'>
                            <div class='image-cover' style='padding-top:56.25%;'>
            ";
            echo get_the_post_thumbnail();
            echo "          </div>
                        </div>
                    </div>
                    <div class='box-text'>";
            echo '      <b><a href="' . get_the_permalink($course_id) . '">' . get_the_title($course_id) . '</a></b>';
            echo "  </div>
                </div>";
        }
    }
    echo "</div>";
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