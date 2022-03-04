<?php
if ($_GET['course'] != "") {
    $url_code = base64_decode($_GET['course']); # data struct ("course_id|lesson")
    $data_code = explode('|',$url_code);
}

while (have_posts()) {
    the_post();
    
    $current_user = wp_get_current_user();
    $lesson_id = get_the_ID();
    $lesson_title = get_the_title();
    $lesson_type = get_field('lesson_type');
    $questions = get_field('questions');
    
    $dir = dirname( __FILE__ );
    if ($lesson_type != "Bài trắc nghiệm") {
        require_once( $dir . '/template/single_posts/normal_lesson.php');
    } else {
        # neu la bai trac nghiem
        require_once( $dir . '/template/single_posts/question_lesson.php');
    }

}