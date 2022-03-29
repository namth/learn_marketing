<?php
if (isset($_GET['course']) && ($_GET['course'] != "")) {
    $url_code = base64_decode($_GET['course']); # data struct ("course_id|lesson|user_id|lesson_id")
    $data_code = explode('|',$url_code);
    $current_course_id = $data_code[0];
    $current_lesson = $data_code[1];
    $access_user_id = $data_code[2];
    $access_lesson_id = $data_code[3];
} else {
    $data_code = array(NULL, NULL);
}
if (is_user_logged_in()) {
    while (have_posts()) {
        the_post();
        
        require_once __DIR__ . '/vendor/autoload.php';
        
        $current_user = wp_get_current_user();
        $active = get_field('active','user_' . $current_user->ID);
        $list_courses = get_field('list_courses','user_' . $current_user->ID);
        $current_lesson = 1;
        foreach ($list_courses as $course) {
            if ($course['course'] == $current_course_id) {
                $current_lesson = $course['lessons'];
            }
        }

        $lesson_id = get_the_ID();
        $lesson_title = get_the_title();
        $lesson_type = get_field('lesson_type');
        $questions = get_field('questions');
        
        $dir = dirname( __FILE__ );
        
        $course_type = get_field('course_type', $current_course_id);
        if ($course_type == 'Private') {
            $permission = false;
            $user_courses = get_user_course($current_user->ID);
            if (in_array($current_course_id, $user_courses)) {
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