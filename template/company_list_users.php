<?php

add_shortcode( "company_list_users", "company_list_users" );
function company_list_users() {
    $current_user = wp_get_current_user();
    $active = get_field('active','user_' . $current_user->ID);
    $business_account = get_field('business_account','user_' . $current_user->ID);
    $own_member = get_field('own_member','user_' . $current_user->ID);
    $icon_first = '<i class="fa-solid fa-user-tie"></i>';

    if (isset($_POST['wp_post_nonce_field']) &&
        wp_verify_nonce($_POST['wp_post_nonce_field'], 'wp_post_nonce')) {

        $user_email = $_POST['user_email'];
        $user_obj = get_user_by('email', $user_email);

        if (!$user_obj) {
            # send email register
        } else {
            # if not in own_member, add to group
            if (!in_array($user_obj->ID, $own_member)) {
                # add user to group
                $data = array(
                    'member' => $user_obj->ID
                );

                if ($own_member) {
                    array_push($own_member, $data);
                    update_field('field_622094e83f62c', $own_member, 'user_' . $current_user->ID);
                } else add_row('field_622094e83f62c', $data, 'user_' . $current_user->ID);
                
                $own_member = get_field('own_member','user_' . $current_user->ID);
            }
            # add leader to user account
            $groups = get_field('groups','user_' . $user_obj->ID);
            if (!in_array($current_user->ID, $groups)) {
                $data = array(
                    'group_leader' => $current_user->ID
                );
                if ($groups) {
                    array_push($groups, $data);
                    update_field('field_6210f79ee5b05', $groups, 'user_' . $user_obj->ID);
                } else add_row('field_6210f79ee5b05', $data, 'user_' . $user_obj->ID);
            }

            echo "Đã thêm thành công tài khoản <b>" . $user_obj->display_name . "</b> vào nhóm.";
        }
    }

    echo "<div class='member_list'>";
    if ($active) {
        if ($business_account) {
            echo "<ul class='lessons_list'>";
            echo "<li><a href='" . get_author_posts_url($current_user->ID) . "'>" . $icon_first . $current_user->display_name . "</a></li>";
            
            if ($own_member) {
                $icon_first = '<i class="fa-solid fa-user"></i>';
                foreach ($own_member as $member) {
                    echo "<li><a href='" . get_author_posts_url($member['member']["ID"]) . "'>" . $icon_first . $member['member']["display_name"] . "</a></li>";
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