<?php

add_shortcode( "active_account", "active_account" );
function active_account() {
    echo '<div id="active_account">';
    if (isset($_GET['active'])) {
        $data_user = explode("|", base64_decode($_GET['active']));
        $check_user = get_user_by('ID', $data_user[0]);

        $active = get_field('active', 'user_' . $check_user->ID);
        if (!$active) {
            # active tài khoản
            update_field('field_6210b12e101b3', true, 'user_' . $check_user->ID );
            echo "<p>Tài khoản của bạn đã được kích hoạt thành công. Mời bạn tiếp tục sử dụng các tính năng khác.</p>";
            echo '<a href="' . get_bloginfo('url') . '" class="button button-primary">Về trang chủ</a>';
        } else {
            echo "<p>Tài khoản này đã được kích hoạt. Mời bạn tiếp tục sử dụng các tính năng khác.</p>";
            echo '<a href="' . get_bloginfo('url') . '" class="button button-primary">Về trang chủ</a>';
        }
        return false;
    }
    if (is_user_logged_in()) {
        if (isset($_POST['post_nonce_field']) &&
            wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {
            $current_user = wp_get_current_user();
            $code_active = base64_encode($current_user->ID . "|" . $current_user->user_email);
            $link_active = get_permalink() . "?active=" . $code_active;

            // print_r($link_active);
            # Gửi email kích hoạt 
            $headers[] = 'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>';
            $to = $current_user->user_email;
            $email_title = 'Kích hoạt tài khoản học marketing';
            $email_content = 'Bạn hãy bấm vào link dưới đây để kích hoạt tài khoản: ' . $link_active;
            wp_mail($to, $email_title, $email_content, $headers);
        }
    ?>
        
            <h1>Kích hoạt tài khoản</h1>
            <p>Chúng tôi sẽ gửi cho bạn một email để kích hoạt tài khoản. Bấm vào nút bên dưới để nhận email kích hoạt.</p>
            <form action="" method="post">
                <?php 
                    wp_nonce_field('post_nonce', 'post_nonce_field');
                ?>
                <input type="submit" value="Gửi email kích hoạt">
            </form>
            
    <?php 
    } else {
        echo "<p>Bạn chưa đăng nhập. Xin vui lòng đăng nhập để sử dụng các tính năng.</p>";
    }
    echo '</div>';
}

add_shortcode( "register_account", "register_account" );
function register_account() {
    if (!is_user_logged_in()) {
        if (isset($_GET['register'])) {
            $data_user = explode("|", base64_decode($_GET['register']));
            $check_user = $data_user[0];
        }

        if (
            isset($_POST['post_nonce_field']) &&
            wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
        ) {
            $user_login     = strip_tags($_POST['username']);
            $display_name   = strip_tags($_POST['display_name']);
            $user_email     = strip_tags($_POST['email']);
            $password       = strip_tags($_POST['password']);
            $confirm_password   = strip_tags($_POST['confirm_password']);
            $error = false;
            $active = false;

            if (($password != $confirm_password) || ($password=="")) {
                $error = true;
                $thongbao = "Mật khẩu không trùng khớp hoặc không hợp lệ.";
            }

            if (!$user_login) {
                $error = true;
                $thongbao = "Tên đăng nhập không được bỏ trống.";
            } else {
                if (username_exists($user_login)){
                    $error = true;
                    $thongbao = "Tên đăng nhập đã tồn tại, hãy chọn lại tên đăng nhập mới.";
                }
            }

            if (!$user_email) {
                $error = true;
                $thongbao = "Email không được bỏ trống.";
            } else {
                if (email_exists($user_email)){
                    $error = true;
                    $thongbao = "Email đã tồn tại, hãy chọn lại email mới.";
                }
            }

            if (isset($_POST['active']) && ($_POST['active'] == '1')) {
                $active = true;
            }

            if (!$error) {
                $args = array(
                    'user_login'    => $user_login,
                    'user_email'    => $user_email,
                    'user_pass'     => $password,
                    'display_name'  => $display_name,
                );
                $new_partner = wp_insert_user($args);

                if ($active) {
                    update_field('field_6210b12e101b3', true, 'user_' . $new_partner );

                    #add to group
                    if ($check_user) {
                        add_to_group($new_partner, $check_user);
                    }
                }

                # redirect to thank you page
                wp_redirect( get_bloginfo('url') . '/thank-you/');
                exit;
            } else {
                echo "<p class='warning'><i class='fa-solid fa-circle-exclamation'></i> " . $thongbao . "</p>";
            }
        }
        ?>
        <form action="#" method="POST" class="register_account">
            <p>Email <span class="red">*</span></p>
            <?php 
                if(isset($data_user[1])){
                    echo '<input class="disable" type="email" disabled value="' . $data_user[1] . '">';
                    echo '<input type="hidden" name="active" value="1">';
                    echo '<input type="hidden" name="email" value="' . $data_user[1] . '">';
                } else {
                    $email = $user_email?$user_email:"";
                    echo '<input type="email" name="email" id="" value="'. $email .'">';
                }
            ?>
            
            <p>Họ và tên</p>
            <input type="text" name="display_name" id="" value="<?php if (isset($display_name)) echo $display_name; ?>">
            <p>Tên đăng nhập <span class="red">*</span></p>
            <input type="text" name="username" id="" value="<?php if (isset($user_login)) echo $user_login; ?>">
            <p>Mật khẩu <span class="red">*</span></p>
            <input type="password" name="password" id="">
            <p>Xác nhận mật khẩu <span class="red">*</span></p>
            <input type="password" name="confirm_password" id="">
            <?php 
            wp_nonce_field('post_nonce', 'post_nonce_field');
            ?>
            <input type="submit" class="button button-primary" value="Đăng ký">
        </form>
        <?php
    } else {
        echo "Bạn đang đăng nhập.";
    }
}


function add_to_group($user_id, $leader_id) {
    if ($user_id == $leader_id) {
        return false;
    }

    $own_member = get_field('own_member','user_' . $leader_id);
    if (!user_in_list($user_id, $own_member, 'member') || !$own_member) {
        # add user to group
        $data = array(
            'member' => $user_id
        );

        if ($own_member) {
            array_push($own_member, $data);
            update_field('field_622094e83f62c', $own_member, 'user_' . $leader_id);
        } else add_row('field_622094e83f62c', $data, 'user_' . $leader_id);
        
        $own_member = get_field('own_member','user_' . $leader_id);
    } else {
        return false;
    }

    # add leader to user account
    $groups = get_field('groups','user_' . $user_id);
    if (!user_in_list($leader_id, $groups, 'group_leader') || !$groups) {
        $data = array(
            'group_leader' => $leader_id
        );
        if ($groups) {
            array_push($groups, $data);
            update_field('field_6210f79ee5b05', $groups, 'user_' . $user_id);
        } else add_row('field_6210f79ee5b05', $data, 'user_' . $user_id);
    } else {
        return false;
    }

    return true;
}

function del_to_group($user_id, $leader_id) {
    $success = false;
    # delete member from leader id
    if( have_rows('own_member', 'user_' . $leader_id) ):
        while( have_rows('own_member', 'user_' . $leader_id) ) : the_row();
            $member = get_sub_field('member');
    
            if($member == $user_id) {
                $row = get_row_index();
                delete_row('field_622094e83f62c', $row, 'user_' . $leader_id);
                $success = true;
            }
        endwhile;
    endif;

    # delete leader id from user id
    if( have_rows('groups', 'user_' . $user_id) ):
        while( have_rows('groups', 'user_' . $user_id) ) : the_row();
            $group_leader = get_sub_field('group_leader');
    
            if($group_leader == $leader_id) {
                $row = get_row_index();
                delete_row('field_6210f79ee5b05', $row, 'user_' . $user_id);
            }
        endwhile;
    endif;

    return $success;
}

function user_in_list($user_id, $user_arr, $field_name) {
    if (is_array($user_arr)) {
        foreach ($user_arr as $user) {
            if ($user_id == $user[$field_name]) {
                return true;
            }
        }
    }
    return false;
}

add_shortcode( "my_account", "my_account" );
function my_account() {
    $this_user = wp_get_current_user();
    $link_avatar = get_avatar_url($this_user->ID);
    $business_account = get_field('business_account','user_' . $this_user->ID);
    ?>
        <a href="javascript:history.go(-1)" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="user_info">
            <img src="<?php echo $link_avatar; ?>" alt="">
            <b class="user_displayname"><?php echo $this_user->display_name; ?></b>
            <span class="username"><?php echo $this_user->user_login; ?></span>
            <span class="user_email">(<?php echo $this_user->user_email; ?>)</span>
            <ul class="list_function">
                <?php
                $user_slug = '/danh-sach-thanh-vien-cong-ty/';
                $course_slug = '/danh-sach-khoa-hoc-cua-cong-ty/';
                $edit_user_slug = '/account/';

                if ($business_account) {
                    echo '<li><a href="' . get_bloginfo('url') . $course_slug . '"><i class="fa-solid fa-book-journal-whills"></i></a></li>';
                    echo '<li><a href="' . get_bloginfo('url') . $user_slug . '"><i class="fa-solid fa-users"></i></a></li>';
                }
                echo '<li><a href="' . get_bloginfo('url') . $edit_user_slug . '"><i class="fa-solid fa-user-pen"></i></a></li>';
                echo '<li><a href="' . wp_logout_url() . '"><i class="fa-solid fa-right-from-bracket"></i></a></li>';
                ?>
            </ul>
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
                    
                    echo "<li>";
                    echo '<b><a href="' . get_the_permalink($course_id) . '">' . $icon_first . get_the_title($course_id) . '</a></b>';
                    echo '<span class="' . $class . '">' . $icon_last . '</span>';
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "Bạn chưa bắt đầu học khoá học nào, hãy chọn một khoá học để bắt đầu.";
            }
            ?>
        </div>
    <?php
}

function get_user_course($userID){
    $own_courses = get_field('own_courses','user_' . $userID);
    $groups = get_field('groups','user_' . $userID);

    $courses = array();
    if($groups){
        foreach ($groups as $group) {
            $user_id = $group['group_leader'];
            $group_courses = get_field('own_courses','user_' . $user_id);
            $courses = array_merge($courses, $group_courses);
        }
    }

    if (is_array($own_courses)) {
        $courses = array_merge($courses, $own_courses);
    }

    $result = array();
    foreach ($courses as $value) {
        if (!in_array($value['own_course'], $result)) {
            $result[] = $value['own_course'];
        }
    }
    return $result;
}