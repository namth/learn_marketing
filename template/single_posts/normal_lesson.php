<?php 
# when press finished lesson
if (isset($_POST['wp_post_nonce_field']) &&
    wp_verify_nonce($_POST['wp_post_nonce_field'], 'wp_post_nonce')) {
    
    # check from list courses of current user
    $updated = false;
    if( have_rows('list_courses', 'user_' . $current_user->ID ) ) {
        while( have_rows('list_courses', 'user_' . $current_user->ID ) ) {
            the_row();
            
            $course_id = get_sub_field('course');
            if ($course_id==$data_code[0]) {
                $lesson_number = get_sub_field('lessons');
                if ($lesson_number == $data_code[1]) {
                    update_sub_field('lessons', $lesson_number + 1 );
                    $updated = true;
                }
            }
        }
    }
    if (!$updated) {
        $data = array(
            'course' => $data_code[0],
            'lessons' => $data_code[1]+1,
        );
        add_row('field_6210b168101b4', $data, 'user_' . $current_user->ID);
        $updated = true;
    }
    wp_redirect( get_permalink($data_code[0]) );
}

if ( isset($_POST['post_nonce_field']) &&
    wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {
    
    $mpdf = new \Mpdf\Mpdf();

    $data = '<h1>' . $lesson_title . '</h1>';

    foreach ($questions as $question) {
        $qid            = $question['question']->ID;
        $content        = wpautop($question['question']->post_content);
        $question_type  = get_field('question_type', $qid);
        $more_answer    = get_field('more_answer', $qid);
        $name_question  = 'question_' . $qid;
        
        $data .= $content;

        if (isset($_POST[$name_question])) {
            if (is_array($_POST[$name_question])) {
                $answer_content = implode("<br>", $_POST[$name_question]);
            } else $answer_content = wpautop($_POST[$name_question]);

            $data .=  "<div class='answer'>";
            switch ($question_type) {
                case 'Trắc nghiệm':
                case 'Câu hỏi lựa chọn':
                    $choices = get_field('choices', $qid);
                    $list_answers = explode(PHP_EOL, $choices);

                    foreach ($list_answers as $answer) {
                        $checked = in_array(trim($answer), $_POST[$name_question]);
                        if (!$checked) {
                            $data .= '<input type="checkbox"> ' . trim($answer) . '<br>';
                        } else {
                            $data .= '<img src="data:image/png;base64,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" width="15"> ' . trim($answer) . '<br>';
                        }
                    }
                    break;
                
                default:
                    $data .= wpautop($_POST[$name_question]);
                    break;
            }
            $data .=  "</div>";

        }
    }

    $mpdf->WriteHTML($data);
    $filename = $lesson_title . '.pdf';
    $upload_dir = wp_upload_dir();
    $attachments = $upload_dir['basedir'] . '/' . $filename;
    $content = $mpdf->Output($attachments, 'F');
    $content = chunk_split(base64_encode($content));

    $email_title = get_field('email_title', 'option');
    $message = get_field('email_content', 'option');
    $to = $_POST['email'];
    $subject = $email_title!=""?$email_title:$lesson_title;

    $headers[] = 'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>';

    $successful = wp_mail( $to, $subject, $message, $headers, $attachments );
    
    $answered = true;

    # send to admin_email
    $admin_email = get_field('admin_email','option');
    $subject = "Bài học: " . $lesson_title . " - từ bạn " . $to;

    if ($admin_email) {
        $send_admin = wp_mail($admin_email, $subject, $message, $headers, $attachments);
    }
}

get_header();
do_action( 'flatsome_before_page' ); 

?>
<div id="content" class="content-area normal_lesson" role="main">
    <div class="gallery row-main">
        <div class="large-3 col" style="background-color: #f7f7f7;">
            <div class="col-inner page-wrapper">
            <?php 

                $lessons = get_field('lessons', $current_course_id);

                echo "<ul class='lessons_list active fixed_div'>";
                $i = 0;
                $list_lesson = array();
                foreach ($lessons as $lesson) {
                    $i++;
                    // print_r($lesson);
                    $chapter_name = $lesson['chapter_name'];
                    if (!$chapter_name) {
                        $lesson_id      = $lesson['lesson']->ID;
                        $list_lesson[]  = $lesson_id;
                        $lesson_title   = $lesson['lesson']->post_title;
                        $lesson_type    = get_field('lesson_type', $lesson_id);
                        $icon_first     = ($lesson_type == "Bài trắc nghiệm")?'<i class="fa-solid fa-circle-question"></i>':'<i class="fa-solid fa-book"></i>';

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
                    } else {
                        echo "<li class='chapter_name'>" . $chapter_name . "</li>";
                    }
                }
                echo "</ul>";
                ?>
            </div>
        </div>
        <div class="large-9 col main_scroll">
            <div class="col-inner">
                <div class="videos">
                <?php 
                    $link_youtube = get_field('youtube');
                    if ($link_youtube) echo apply_filters('the_content', $link_youtube);
                ?>
                </div>
                <div class="action_bar">
                    <?php 
                        if ($current_course_id) {
                            echo '<a href="'. get_permalink($current_course_id) .'" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>';
                        } else {
                            echo '<a href="javascript:history.go(-1)" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>';
                        }
                    ?>
                    <ul>
                        <li class="active">
                            <a href="#" data-tab="#main_content"><i class="fa-solid fa-book-open"></i> <b>Nội dung chính</b></a>
                        </li>
                        <li>
                            <a href="#" data-tab="#question_answer"><i class="fa-solid fa-comments"></i> <b>Hỏi đáp</b></a>
                        </li>
                    </ul>
                </div>
                <article id="main_content">
                    <header class="entry-header alignwide">
                        <h1><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content" id="my-lesson">
                        <?php
                        the_content();

                        echo '<form action="#" method="POST">';

                        foreach ($questions as $question) {
                            $qid = $question['question']->ID;
                            $content = $question['question']->post_content;
                            $question_type = get_field('question_type', $qid);
                            $more_answer = get_field('more_answer', $qid);
                            $name_question = 'question_' . $qid;

                            echo "<div class='question'>";
                            echo apply_filters('the_content', $content);
                            
                            if (isset($_POST[$name_question])) {
                                if (is_array($_POST[$name_question])) {
                                    $answer_content = implode("<br>", $_POST[$name_question]);
                                } else $answer_content = $_POST[$name_question];

                                if ($answer_content){
                                    echo "<div class='answer'>";
                                    echo wpautop($answer_content);
                                    echo "</div>";
                                }
                                if ($more_answer){
                                    echo "<div class='more_answer'>";
                                    echo wpautop($more_answer);
                                    echo "</div>";
                                }
                            } else {
                                echo "<div class='answer'>";
                                switch ($question_type) {
                                    case 'Câu hỏi ngắn':
                                        echo '<input type="text" name="' . $name_question . '">';
                                        break;
                                
                                    case 'Câu hỏi dài':
                                        // echo '<textarea name="' . $name_question . '" id="" cols="30" rows="10"></textarea>';
                                        wp_editor('', $name_question, array('media_buttons'=>false)); 
                                        break;
                                        
                                    default:
                                        $choices = get_field('choices', $qid);
                                        $list_answers = explode(PHP_EOL, trim($choices));
    
                                        echo '<ul class="list_answers">';
                                        foreach ($list_answers as $answer) {
                                            echo '<li>';
                                            echo '<input type="checkbox" name="' . $name_question . '[]" value="' . trim($answer) . '"> ' . trim($answer);
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                        break;
                                }
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                        
                        if (!$answered) {
                            echo "<label>Nhập địa chỉ email của bạn</label>";
                            echo '<input type="text" name="email" class="my_email" value="' . $current_user->user_email . '">';
                            wp_nonce_field('post_nonce', 'post_nonce_field');
                            echo '<input type="submit" class="button button-primary" value="Nộp bài và gửi kết quả">';
                            echo '</form>';
                        } else {
                        ?>
                            <p>Chúng tôi đã gửi email cho bạn, hãy kiểm tra email để download câu trả lời.</p>
                            <a href="javascript:history.go(-1)" class="button">Quay lại</a>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="align_right">
                        <form action="" method="post">
                            <?php 
                                wp_nonce_field('wp_post_nonce', 'wp_post_nonce_field');
                            ?>
                            <input type="submit" value="Hoàn tất bài học">
                        </form>
                    </div>
                </article>
                <article id="question_answer" style="display: none;">
                    <div id="comments" class="comments-area">
                    <?php 
                        if ( comments_open() || '0' != get_comments_number() ){
                            comments_template();
                        }
                    ?>
                    </div>
                </article>
            </div>
        </div>
    </div>
</div>
<?php


do_action( 'flatsome_after_page' );
get_footer();