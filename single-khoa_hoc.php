<?php

while (have_posts()) {
    the_post();
    
    $current_user = wp_get_current_user();
    $active = get_field('active','user_' . $current_user->ID);
    $list_courses = get_field('list_courses','user_' . $current_user->ID);
    
    $current_course_ID = get_the_ID();
    $current_lesson = 1;
    foreach ($list_courses as $course) {
        if ($course['course'] == $current_course_ID) {
            $current_lesson = $course['lessons'];
        }
    }

    $lesson_title = get_the_title();
    $lessons = get_field('lessons');
    $course_type = get_field('course_type');
    
    get_header();
    do_action( 'flatsome_before_page' ); 

    $back_link = get_bloginfo('url') . '/user/';
?>
<div id="content" class="content-area page-wrapper" role="main">
    <div class="row row-main">
        <div class="large-12 col">
            <div class="col-inner">
                <!-- Check if course is private and current user is not login or haven't permission in this course -->
                <?php 
                if (is_user_logged_in()) {
                    # check if course is private?
                    if ($course_type == 'Private') {
                        $permission = false;
                        $user_courses = get_user_course($current_user->ID);
                        if (in_array($current_course_ID, $user_courses)) {
                            $permission = true;
                        }
                    } else {
                        $permission = true;
                    }
                    if ($permission) {
                ?>
                <a href="<?php echo $back_link; ?>" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>
                <article>
                    <header class="entry-header alignwide">
                        <h1><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content" id="my-lesson">
                        <?php
                        the_content();

                        // print_r($current_lesson);
                        if ($active) {
                            $course_class = 'active';
                        } else {
                            $course_class = 'not_active';
                            echo "<span class='warning'><i class='fa-solid fa-circle-exclamation'></i> Bạn chưa kích hoạt tài khoản, hãy <a href='". get_bloginfo('url') ."/email-active'>bấm vào đây</a> để kích hoạt trước khi tham gia khoá học</span>";
                        }

                        echo "<ul class='lessons_list " . $course_class . "'>";
                        $i = 0;
                        foreach ($lessons as $lesson) {
                            $chapter_name = $lesson['chapter_name'];
                            if (!$chapter_name) {
                                $i++;
                                $lesson_id = $lesson['lesson']->ID;
                                $lesson_title = $lesson['lesson']->post_title;
                                $lesson_type = get_field('lesson_type', $lesson_id);
                                $icon_first = ($lesson_type == "Bài trắc nghiệm")?'<i class="fa-solid fa-circle-question"></i>':'<i class="fa-solid fa-book"></i>';
    
                                if ($active) {
                                    $url_code = '?course=' . base64_encode($current_course_ID . '|' . $current_lesson . '|' . $current_user->ID . '|' . $lesson_id );
                                    if ($current_lesson >= $i) {
                                        if ($current_lesson == $i) {
                                            $next_lesson = $lesson_id;
                                            $icon_last = '';
                                            echo "<li class='current_lesson'>";
                                        } else{
                                            $icon_last = '<i class="fa-solid fa-check"></i>';
                                            echo "<li>";
                                        }
                                        echo '<b><a href="' . get_the_permalink($lesson_id) . $url_code . '">' . $icon_first . '</i>' . $lesson_title . '</a></b><span class="check_done">' . $icon_last . '</span>';
                                    } else{
                                        echo "<li class='lesson_lock'>";
                                        echo '<b>' . $icon_first . '</i>' . $lesson_title . '</b><span class="locked"><i class="fa-solid fa-lock"></i></span>';
                                    }
                                } else {
                                    echo "<li class='lesson_lock'>";
                                    echo '<b>' . $icon_first . '</i>' . $lesson_title . '</b><span class="locked"><i class="fa-solid fa-lock"></i></span>';
                                    echo "</li>";
                                }
                                
                                echo "</li>";
                            } else {
                                echo "<li class='chapter_name'>" . $chapter_name . "</li>";
                            }
                        }
                        echo "</ul>";
                            
                        ?>
                    </div>
                    <div class="align_right">
                    <?php 
                        if ($next_lesson) {
                    ?>
                        <a href="<?php echo get_the_permalink($next_lesson) . $url_code; ?>" class="button align_right">Học tiếp</a>
                    <?php 
                        }
                    ?>
                    </div>
                </article>
                <?php 
                    } else {
                        echo '<div class="entry-content" id="my-lesson">Bạn không có quyền truy cập tính năng này. Hãy kiểm tra lại.</div>';
                    }
                } else {
                    echo '<div class="entry-content" id="my-lesson">Bạn chưa đăng nhập. Hãy đăng nhập để sử dụng tính năng này.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();

}