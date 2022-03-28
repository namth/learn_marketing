<?php 
if ( isset($_POST['post_nonce_field']) &&
wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {

    $report = array();
    $good_question = 0;
    $total_question = count($questions);
    foreach ($questions as $question) {
        $qid = $question['question']->ID;
        $name_question = 'question_' . $qid;
        $answer = get_field('answer', $qid);
        $answer_arr = array_filter(array_map('trim', explode(PHP_EOL, $answer)));
        $your_answers = array_map('trim', $_POST[$name_question]);

        /* if ($your_answer == $answer_arr) {
            $good_question++;
        } */
        $not_ok = false;
        foreach ($answer_arr as $_answer) {
            if (!in_array(trim($_answer), $your_answers)) {
                $not_ok = true;
                $report['wrong'][$qid] = $_answer;
            }
        }

        if (!$not_ok) {
            $good_question++;
        } else {
            $report['text'] .= 'Wrong answer for question number: ' . $qid . '<br>';
        }
    }
    $mark = round($good_question/$total_question*100);
    $answered = true;

    if ($mark>=80) {
        # check from list courses of current user
        if( have_rows('list_courses', 'user_' . $current_user->ID ) ) {
            while( have_rows('list_courses', 'user_' . $current_user->ID ) ) {
                the_row();
                
                $course_id = get_sub_field('course');
                if ($course_id == $current_course_id) {
                    $lesson_number = get_sub_field('lessons');
                    if ($lesson_number == $data_code[1]) {
                        update_sub_field('lessons', $lesson_number + 1 );
                    }
                }
            }
        }

        $thongbao = "<h3>Chúc mừng bạn đã đạt yêu cầu.</h3>";
        $color = 'limegreen';
    } else {
        $thongbao = "<h3>Chỉ còn một xíu nữa là đạt yêu cầu.</h3>";
        $thongbao2 = "<h4>Hãy thử lại nhé!</h4>";
        $color = 'crimson';
    }
}

get_header();
do_action( 'flatsome_before_page' ); 

?>
<div id="content" class="content-area" role="main">
<div class="gallery row-main">
    <div class="large-3 col">
        <div class="col-inner page-wrapper">
        <?php 
            if ($current_course_id) {
                echo '<a href="'. get_permalink($current_course_id) .'" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>';
            } else {
                echo '<a href="javascript:history.go(-1)" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>';
            }

            $lessons = get_field('lessons', $current_course_id);

            echo "<ul class='lessons_list active fixed_div'>";
            $i = 0;
            foreach ($lessons as $lesson) {
                $i++;
                // print_r($lesson);
                $lesson_id = $lesson['lesson']->ID;
                $lesson_title = $lesson['lesson']->post_title;
                $lesson_type = get_field('lesson_type', $lesson_id);
                $icon_first = ($lesson_type == "Bài trắc nghiệm")?'<i class="fa-solid fa-circle-question"></i>':'<i class="fa-solid fa-book"></i>';

                if ($active) {
                    $url_code = '?course=' . base64_encode($current_course_id . '|' . $current_lesson . '|' . $current_user->ID . '|' . $lesson_id );
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
                    echo '<b>' . $icon_first . '</i>' . $lesson_title . '</b><span class="locked"><i class="fa-solid fa-lock"></i></span>';
                }
                
                echo "</li>";
            }
            echo "</ul>";
            ?>
        </div>
    </div>
    <div class="large-9 col main_scroll">
        <div class="col-inner page-wrapper">
        <?php 
            $link_youtube = get_field('youtube');
            if ($link_youtube) echo apply_filters('the_content', $link_youtube);
        ?>
            <article>
                <header class="entry-header alignwide">
                    <h1><?php the_title(); ?></h1>
                </header>

                <div class="entry-content" id="my-lesson">
                    <?php
                    the_content();

                    if (!$answered) {
                        echo '<form action="#" method="POST">';

                        foreach ($questions as $question) {
                            $qid = $question['question']->ID;
                            $content = $question['question']->post_content;
                            $question_type = get_field('question_type', $qid);
                            $name_question = 'question_' . $qid;

                            echo "<div class='question'>";
                            echo apply_filters('the_content', $content);
                            
                            if (isset($_POST[$name_question])) {
                                if (is_array($_POST[$name_question])) {
                                    $answer_content = implode("<br>", $_POST[$name_question]);
                                } else $answer_content = $_POST[$name_question];

                                echo "<div class='answer'>";
                                echo wpautop($answer_content);
                                echo "</div>";
                            } else {
                                echo "<div class='answer'>";
                                $choices = get_field('choices', $qid);
                                $list_answers = explode(PHP_EOL, trim($choices));

                                echo '<ul class="list_answers">';
                                foreach ($list_answers as $answer) {
                                    echo '<li>';
                                    echo '<input type="checkbox" name="' . $name_question . '[]" value="' . trim($answer) . '"> ' . trim($answer);
                                    echo '</li>';
                                }
                                echo '</ul>';
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                        
                        wp_nonce_field('post_nonce', 'post_nonce_field');
                        echo '<input type="submit" class="button button-primary" value="Gửi kết quả">';
                        echo '</form>';
                    } else {
                    ?>
                    <div style="text-align: center;">
                        <?php if($thongbao) echo $thongbao; ?>
                        <h4>Bạn trả lời đúng được <span class='mark' style="color:<?php echo $color; ?>; font-size:x-large;"><?php echo $mark; ?>%</span> trong bài kiểm tra.</h4>
                        <?php if($thongbao2) echo $thongbao2; ?>
                        <a href="<?php echo get_permalink($current_course_id); ?>" class="button">Quay lại</a>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </article>
        </div>
    </div>
</div>
</div>
<?php
get_footer();