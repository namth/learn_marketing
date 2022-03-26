<?php
get_header();

$current_user = wp_get_current_user();
$this_user = get_queried_object();
$link_avatar = get_avatar_url($this_user->ID);

?>
<div id="content" class="content-area page-wrapper" role="main">
	<div class="row row-main">
		<div class="large-12 col">
			<div class="col-inner">
                <a href="javascript:history.go(-1)" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>
                <div class="user_info">
                    <img src="<?php echo $link_avatar; ?>" alt="">
                    <b class="user_displayname"><?php echo $this_user->display_name; ?></b>
                    <span class="username"><?php echo $this_user->user_login; ?></span>
                    <span class="user_email">(<?php echo $this_user->user_email; ?>)</span>
                </div>
                <div class="courses_list">
                    <h2>Danh sách khoá học</h2>
                    <?php 
                    $list_courses = get_field('list_courses','user_' . $this_user->ID);
                    $icon_first = '<i class="fa-solid fa-book-journal-whills"></i>';
                    
                    if ($list_courses) {
                        echo "<ul class='course_list'>";
                        foreach ($list_courses as $course) {
                            $course_id = $course['course'];
                            $number_lesson = $course['lessons'];
                            $lesson = get_field('lessons', $course_id);
                            
                            $percent_finish_course = number_format(($number_lesson - 1) / count($lesson) * 100);
                            if ($percent_finish_course >= 100) {
                                $icon_last = '<i class="fa-solid fa-check"></i>';
                                $class = 'check_done green';
                            } else {
                                $icon_last = $percent_finish_course . '%';
                                $class = 'check_done percent';
                            }

                            if ($current_user->ID == $this_user->ID) {
                                $link = get_the_permalink($course_id);
                            } else {
                                $link = '#';
                            }
                            
                            echo "<li>";
                            echo '<b><a href="' . $link . '">' . $icon_first . get_the_title($course_id) . '</a></b>';
                            echo '<span class="' . $class . '">' . $icon_last . '</span>';
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "Bạn chưa bắt đầu học khoá học nào, hãy chọn một khoá học để bắt đầu.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
get_footer();
?>