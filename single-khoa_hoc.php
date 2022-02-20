<?php

$current_user = wp_get_current_user();
$active = get_field('active','user_' . $current_user->ID);
$list_courses = get_field('list_courses','user_' . $current_user->ID);

while (have_posts()) {
    the_post();
    
    $lesson_title = get_the_title();
    $lessons = get_field('lessons');
    $course_type = get_field('course_type');
    
    get_header();
    do_action( 'flatsome_before_page' ); 

?>
<div id="content" class="content-area page-wrapper" role="main">
    <div class="row row-main">
        <div class="large-12 col">
            <div class="col-inner">

    <article>
        <header class="entry-header alignwide">
            <h1><?php the_title(); ?></h1>
        </header>

        <div class="entry-content" id="my-lesson">
            <?php
            the_content();
            if ($active) {
                $course_class = 'active';
            } else {
                $course_class = 'not_active';
                echo "<span class='warning'><i class='fa-solid fa-circle-exclamation'></i> Bạn chưa kích hoạt tài khoản, hãy <a href='#'>bấm vào đây</a> để kích hoạt trước khi tham gia khoá học</span>";
            }

            echo "<ul class='lessons_list " . $course_class . "'>";
            foreach ($lessons as $lesson) {
                // print_r($lesson);
                $lesson_id = $lesson['lesson']->ID;
                $lesson_title = $lesson['lesson']->post_title;

                echo "<li>";
                if ($active) {
                    echo '<b><a href="' . get_the_permalink($lesson_id) . '"><i class="fa-solid fa-book"></i>' . $lesson_title . '</a></b>';
                } else {
                    echo '<b><i class="fa-solid fa-book"></i>' . $lesson_title . '</b><span class="locked"><i class="fa-solid fa-lock"></i></span>';
                }
                
                echo "</li>";
            }
            echo "</ul>";
                
            ?>
        </div>
        
    </article>


            </div>
        </div>
    </div>
</div>
<?php
get_footer();

}