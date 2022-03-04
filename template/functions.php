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

/* add_shortcode( "register_account", "register_account" );
function register_account() {

} */