<?php 

if ( isset($_POST['post_nonce_field']) &&
wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce') ) {

    $good_question = 0;
    $total_question = count($questions);
    foreach ($questions as $question) {
        $qid = $question['question']->ID;
        $name_question = 'question_' . $qid;
        $answer = trim(get_field('answer', $qid));
        $answer_arr = explode(PHP_EOL, $answer);

        if ($_POST[$name_question] == $answer_arr) {
            $good_question++;
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
                if ($course_id==$data_code[0]) {
                    $lesson_number = get_sub_field('lessons');
                    if ($lesson_number == $data_code[1]) {
                        update_sub_field('lessons', $lesson_number + 1 );
                    }
                }
            }
        }
    }
}

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

        if (!$answered) {
            echo '<form action="#" method="POST">';

            foreach ($questions as $question) {
                $qid = $question['question']->ID;
                $content = $question['question']->post_content;
                $question_type = get_field('question_type', $qid);
                $name_question = 'question_' . $qid;

                echo "<div class='question'>";
                echo '<h4>' . $content . '</h4>';
                
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
            <p>Bạn đạt được <span><?php echo $mark; ?></span> điểm</p>
            <a href="<?php echo get_permalink($data_code[0]); ?>" class="button">Quay lại</a>
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