<?php

add_shortcode( "company_list_courses", "company_list_courses" );
function company_list_courses() {
    $current_user = wp_get_current_user();
    $active = get_field('active','user_' . $current_user->ID);
    $own_courses = get_field('own_courses','user_' . $current_user->ID);

    // print_r($own_courses);
    $icon_first = '<i class="fa-solid fa-book-journal-whills"></i>';
    echo "<ul class='course_list'>";

    foreach ($own_courses as $course) {
        $course_id = $course['own_course'];
        echo "<li>";
        echo '<b><a href="' . get_the_permalink($course_id) . '">' . $icon_first . get_the_title($course_id) . '</a></b>';
        echo "</li>";
    }
    echo "</ul>";
}