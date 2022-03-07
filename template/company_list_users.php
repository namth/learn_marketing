<?php

add_shortcode( "company_list_users", "company_list_users" );
function company_list_users() {
    $current_user = wp_get_current_user();
    $active = get_field('active','user_' . $current_user->ID);
    $business_account = get_field('business_account','user_' . $current_user->ID);
    $icon_first = '<i class="fa-solid fa-user-tie"></i>';
    
    // echo '<a href="javascript:history.go(-1)" class="back_button"><i class="fa-solid fa-arrow-left"></i></a>';

    if (isset($_POST['wp_post_nonce_field']) &&
        wp_verify_nonce($_POST['wp_post_nonce_field'], 'wp_post_nonce')) {

        $user_email = $_POST['user_email'];
        
        if ($user_email) {
            $user_obj = get_user_by('email', $user_email);
            if (!$user_obj) {
                # send email register
                $link_register = get_bloginfo('url') . "/registration/?register=" . base64_encode($current_user->ID . "|" . $user_email);
                $headers[] = 'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>';
                $to = $user_email;
                $email_title = 'Thư mời đăng ký khoá học marketing';
                $email_content = 'Bạn hãy click vào link dưới đây để đăng ký tài khoản: ' . $link_register;
                wp_mail($to, $email_title, $email_content, $headers);
    
                echo "<span class='warning green'>Đã gửi lời mời đăng ký tới địa chỉ email: " . $user_email . "</span>";
                // echo $link_register;
            } else {
                # if not in own_member, add to group
                $success = add_to_group($user_obj->ID, $current_user->ID);
                
                if ($success) {
                    echo "<span class='warning green'><i class='fa-solid fa-clipboard-check'></i> Đã thêm thành công tài khoản <b>" . $user_obj->display_name . "</b> vào nhóm.</span>";
                } else {
                    echo "<span class='warning'>Tài khoản đã có trong danh sách.</span>";
                }
            }
        }
    } else {
        if (isset($_GET['delete'])) {
            $del_data = explode("|", base64_decode($_GET['delete']));
            $verify = wp_verify_nonce($del_data[1], 'delete_' . $del_data[0]);
            if ($verify) {
                $success = del_to_group($del_data[0], $current_user->ID);
                if ($success) {
                    echo "<span class='warning green'><i class='fa-solid fa-clipboard-check'></i> Đã xoá thành công tài khoản khỏi nhóm.</span>";
                } else {
                    echo "<span class='warning'>Chưa xoá được do có lỗi gì đó, xin vui lòng thử lại.</span>";
                }
            }
        }    
    }

    echo "<h2>Danh sách thành viên </h2>";
    echo "<div class='member_list'>";

    $own_member = get_field('own_member','user_' . $current_user->ID);
    if ($active) {
        if ($business_account) {
            echo "<ul class='lessons_list active'>";
            echo "<li><a href='" . get_author_posts_url($current_user->ID) . "'><b>" . $icon_first . $current_user->display_name . "</b> (" . $current_user->user_email . ")</a></li>";
            
            if ($own_member) {
                $icon_first = '<i class="fa-solid fa-user"></i>';
                foreach ($own_member as $member) {
                    $memobj = get_user_by('ID', $member['member']);
                    if ($memobj) {
                        $nonce = wp_create_nonce('delete_' . $memobj->ID);
                        $code = base64_encode($memobj->ID . "|" . $nonce);
                        echo "<li><a href='" . get_author_posts_url($memobj->ID) . "'><b>" . $icon_first . $memobj->display_name . "</b> (" . $memobj->user_email . ")</a>";
                        echo "<a href='?delete=" . $code . "' class='delete_user' onclick='return confirm(\"Bạn có chắc chắn muốn xoá user này?\")'><i class='fa-solid fa-trash-can'></i></a></li>";
                    }
                }
            }
            echo "</ul>";

            echo '<div class="add_new_user align_right">';
            ?>
            <button class="button" id="add_user">Thêm thành viên</button>
            <form action="" method="post" class="">
                <label for="">Email thành viên</label>
                <input type="text" class="form-control" name="user_email" value="">
                <?php 
                    wp_nonce_field('wp_post_nonce', 'wp_post_nonce_field');
                ?>
                <input type="submit" value="Thêm">
                <button class="closed_button">X</button>
            </form>
            <?php
            echo '</div>';
        }
    }
}