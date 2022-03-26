<?php
if (isset($_GET['course']) && ($_GET['course'] != "")) {
    $url_code = base64_decode($_GET['course']); # data struct ("course_id|lesson|user_id")
    $data_code = explode('|',$url_code);
} else {
    $data_code = array(NULL, NULL);
}
if (is_user_logged_in()) {
    while (have_posts()) {
        the_post();
        
        $current_user = wp_get_current_user();
        $lesson_id = get_the_ID();
        $lesson_title = get_the_title();
        $lesson_type = get_field('lesson_type');
        $questions = get_field('questions');
        
        $dir = dirname( __FILE__ );
        
        $course_type = get_field('course_type', $data_code[1]);
        if ($course_type == 'Private') {
            $permission = false;
            $user_courses = get_user_course($current_user->ID);
            if (in_array($current_course_ID, $user_courses)) {
                $permission = true;
            }
        } else {
            $permission = true;
        }

        if ($permission && ($data_code[2] == $current_user->ID)) {
            if ($lesson_type != "Bài trắc nghiệm") {
                require_once( $dir . '/template/single_posts/normal_lesson.php');
            } else {
                # neu la bai trac nghiem
                require_once( $dir . '/template/single_posts/question_lesson.php');
            }
        }

    }
}