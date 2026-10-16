<?php
/**
 * Plugin Name: Duo Artist Portal
 * Description: Artist registration, login, music/audio and cover submission, and private release status tracking for Duo Music Distribution.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Duo Music Distribution
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) exit;

define('DUO_AP_VERSION', '1.0.0');

function duo_ap_register_post_type() {
    register_post_type('duo_release', array(
        'labels' => array('name' => 'Artist Releases', 'singular_name' => 'Artist Release', 'add_new_item' => 'Add Release'),
        'public' => false, 'show_ui' => true, 'show_in_menu' => true,
        'supports' => array('title', 'editor', 'author'),
        'capability_type' => 'post', 'map_meta_cap' => true,
        'menu_icon' => 'dashicons-album'
    ));
}
add_action('init', 'duo_ap_register_post_type');

function duo_ap_styles() {
    $css = '
    .duo-ap-wrap{max-width:980px;margin:35px auto;padding:0 16px;color:#f4f7f2;font-family:system-ui,sans-serif}
    .duo-ap-box{background:#101612;border:1px solid #263329;border-radius:16px;padding:24px;margin:18px 0}
    .duo-ap-wrap h2,.duo-ap-wrap h3{color:#f4f7f2;margin-top:0}
    .duo-ap-wrap p,.duo-ap-wrap small{color:#b5c1b6}
    .duo-ap-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
    .duo-ap-field{margin-bottom:15px}
    .duo-ap-field label{display:block;color:#e7eee7;margin-bottom:6px;font-weight:650}
    .duo-ap-field input,.duo-ap-field select,.duo-ap-field textarea{display:block;width:100%;padding:12px;background:#080d09;color:#fff;border:1px solid #344336;border-radius:8px;box-sizing:border-box}
    .duo-ap-btn{background:#9cff57;color:#071006;border:0;border-radius:8px;padding:12px 18px;font-weight:800;cursor:pointer}
    .duo-ap-notice{padding:13px 15px;border-radius:8px;background:#172817;border:1px solid #477a37;color:#e1ffd8;margin:14px 0}
    .duo-ap-table{width:100%;border-collapse:collapse}.duo-ap-table th,.duo-ap-table td{text-align:left;padding:10px;border-bottom:1px solid #263329;color:#e9f0e9}
    .duo-ap-badge{display:inline-block;border:1px solid #48633f;border-radius:999px;padding:3px 9px;color:#b9ff91;font-size:12px}
    @media(max-width:640px){.duo-ap-grid{grid-template-columns:1fr}.duo-ap-box{padding:17px}.duo-ap-table{font-size:13px}}
    ';
    wp_register_style('duo-artist-portal-inline', false, array(), DUO_AP_VERSION);
    wp_enqueue_style('duo-artist-portal-inline');
    wp_add_inline_style('duo-artist-portal-inline', $css);
}
add_action('wp_enqueue_scripts', 'duo_ap_styles');

function duo_ap_notice($message) {
    return '<div class="duo-ap-notice">' . esc_html($message) . '</div>';
}

function duo_ap_register_shortcode() {
    if (is_user_logged_in()) return '<div class="duo-ap-wrap"><div class="duo-ap-box">You are already logged in. <a href="' . esc_url(duo_ap_page_url('artist-dashboard')) . '">Open your dashboard</a>.</div></div>';
    $out = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duo_ap_register'])) {
        if (!isset($_POST['duo_ap_register_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['duo_ap_register_nonce'])), 'duo_ap_register')) {
            $out = duo_ap_notice('Security check failed. Reload the page and try again.');
        } else {
            $name = sanitize_text_field(wp_unslash($_POST['duo_artist_name'] ?? ''));
            $email = sanitize_email(wp_unslash($_POST['duo_artist_email'] ?? ''));
            $username = sanitize_user(wp_unslash($_POST['duo_username'] ?? ''));
            $password = (string) ($_POST['duo_password'] ?? '');
            if (!$name || !is_email($email) || !$username || strlen($password) < 12) {
                $out = duo_ap_notice('Enter your artist name, valid email, username, and a password with at least 12 characters.');
            } elseif (username_exists($username) || email_exists($email)) {
                $out = duo_ap_notice('That username or email is already registered. Please log in or use different details.');
            } else {
                $user_id = wp_create_user($username, $password, $email);
                if (is_wp_error($user_id)) {
                    $out = duo_ap_notice('Registration could not be completed. Please try again.');
                } else {
                    wp_update_user(array('ID' => $user_id, 'display_name' => $name, 'first_name' => $name, 'role' => 'subscriber'));
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id, true, is_ssl());
                    wp_mail(get_option('admin_email'), 'New artist registration', "New artist account created.\nArtist: $name\nEmail: $email\nUsername: $username");
                    wp_safe_redirect(duo_ap_page_url('artist-dashboard'));
                    exit;
                }
            }
        }
    }
    ob_start(); ?>
    <div class="duo-ap-wrap"><div class="duo-ap-box"><h2>Create your artist account</h2><p>Register to submit music releases for review. Use a strong, unique password.</p><?php echo wp_kses_post($out); ?>
    <form method="post"><?php wp_nonce_field('duo_ap_register', 'duo_ap_register_nonce'); ?>
      <div class="duo-ap-grid">
       <div class="duo-ap-field"><label for="duo_artist_name">Artist / label name *</label><input id="duo_artist_name" name="duo_artist_name" required maxlength="100"></div>
       <div class="duo-ap-field"><label for="duo_artist_email">Email *</label><input id="duo_artist_email" type="email" name="duo_artist_email" required></div>
       <div class="duo-ap-field"><label for="duo_username">Username *</label><input id="duo_username" name="duo_username" required autocomplete="username"></div>
       <div class="duo-ap-field"><label for="duo_password">Password (12+ characters) *</label><input id="duo_password" type="password" name="duo_password" required minlength="12" autocomplete="new-password"></div>
      </div><p><label><input type="checkbox" required> I confirm that I own or control the rights to the music and artwork I submit, and agree to the site's terms.</label></p>
      <button class="duo-ap-btn" type="submit" name="duo_ap_register" value="1">Create Artist Account</button>
    </form><p>Already registered? <a href="<?php echo esc_url(wp_login_url(duo_ap_page_url('artist-dashboard'))); ?>">Log in here</a>.</p></div></div>
    <?php return ob_get_clean();
}
add_shortcode('duo_artist_register', 'duo_ap_register_shortcode');

function duo_ap_page_url($slug) {
    $p = get_page_by_path($slug);
    return $p ? get_permalink($p->ID) : home_url('/' . trim($slug, '/') . '/');
}

function duo_ap_allowed_upload($file, $kind) {
    if (empty($file['name']) || !empty($file['error'])) return new WP_Error('duo_upload', 'A required file is missing or failed to upload.');
    $max = ($kind === 'audio') ? 100 * 1024 * 1024 : 10 * 1024 * 1024;
    if ((int)$file['size'] > $max) return new WP_Error('duo_size', $kind === 'audio' ? 'Audio file must be 100 MB or smaller.' : 'Cover image must be 10 MB or smaller.');
    $types = $kind === 'audio'
        ? array('mp3' => 'audio/mpeg', 'wav' => 'audio/wav', 'm4a' => 'audio/mp4', 'flac' => 'audio/flac')
        : array('jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png');
    $checked = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $types);
    if (empty($checked['ext']) || empty($checked['type'])) return new WP_Error('duo_type', $kind === 'audio' ? 'Allowed audio formats: MP3, WAV, M4A, FLAC.' : 'Allowed cover formats: JPG or PNG.');
    return true;
}

function duo_ap_release_shortcode() {
    if (!is_user_logged_in()) {
        return '<div class="duo-ap-wrap"><div class="duo-ap-box"><h2>Artist login required</h2><p>Please log in before submitting music.</p><a class="duo-ap-btn" href="' . esc_url(wp_login_url(get_permalink())) . '">Log In</a> <a href="' . esc_url(duo_ap_page_url('artist-register')) . '">Create an artist account</a></div></div>';
    }
    $out = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duo_ap_submit_release'])) {
        if (!isset($_POST['duo_ap_release_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['duo_ap_release_nonce'])), 'duo_ap_release')) {
            $out = duo_ap_notice('Security check failed. Please reload and try again.');
        } else {
            $title = sanitize_text_field(wp_unslash($_POST['duo_release_title'] ?? ''));
            $artist = sanitize_text_field(wp_unslash($_POST['duo_release_artist'] ?? ''));
            $type = sanitize_text_field(wp_unslash($_POST['duo_release_type'] ?? 'Single'));
            $genre = sanitize_text_field(wp_unslash($_POST['duo_release_genre'] ?? ''));
            $date = sanitize_text_field(wp_unslash($_POST['duo_release_date'] ?? ''));
            $rights = isset($_POST['duo_rights_confirmed']);
            if (!$title || !$artist || !$rights) {
                $out = duo_ap_notice('Complete the required fields and confirm you have the necessary rights.');
            } elseif (empty($_FILES['duo_audio_file']['name']) || empty($_FILES['duo_cover_file']['name'])) {
                $out = duo_ap_notice('Please select both an audio file and cover artwork.');
            } else {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $audio_check = duo_ap_allowed_upload($_FILES['duo_audio_file'], 'audio');
                $cover_check = duo_ap_allowed_upload($_FILES['duo_cover_file'], 'cover');
                if (is_wp_error($audio_check)) $out = duo_ap_notice($audio_check->get_error_message());
                elseif (is_wp_error($cover_check)) $out = duo_ap_notice($cover_check->get_error_message());
                else {
                    $audio_id = media_handle_upload('duo_audio_file', 0, array(), array('test_form' => false, 'mimes' => array('mp3'=>'audio/mpeg','wav'=>'audio/wav','m4a'=>'audio/mp4','flac'=>'audio/flac')));
                    if (is_wp_error($audio_id)) {
                        $out = duo_ap_notice('Audio upload failed. Check your WordPress/server upload limits.');
                    } else {
                        $cover_id = media_handle_upload('duo_cover_file', 0, array(), array('test_form' => false, 'mimes' => array('jpg|jpeg|jpe'=>'image/jpeg','png'=>'image/png')));
                        if (is_wp_error($cover_id)) {
                            wp_delete_attachment($audio_id, true);
                            $out = duo_ap_notice('Cover upload failed. Please check the image and try again.');
                        } else {
                            $post_id = wp_insert_post(array(
                                'post_type' => 'duo_release',
                                'post_status' => 'private',
                                'post_author' => get_current_user_id(),
                                'post_title' => $title . ' — ' . $artist,
                                'post_content' => "Artist: $artist\nRelease type: $type\nGenre: $genre\nRelease date: $date\nSubmitted by user ID: " . get_current_user_id(),
                            ), true);
                            if (is_wp_error($post_id)) {
                                wp_delete_attachment($audio_id, true); wp_delete_attachment($cover_id, true);
                                $out = duo_ap_notice('The release could not be saved. Please contact support.');
                            } else {
                                update_post_meta($post_id, '_duo_release_status', 'Pending Review');
                                update_post_meta($post_id, '_duo_audio_attachment_id', (int)$audio_id);
                                update_post_meta($post_id, '_duo_cover_attachment_id', (int)$cover_id);
                                update_post_meta($post_id, '_duo_release_type', $type);
                                update_post_meta($post_id, '_duo_release_genre', $genre);
                                update_post_meta($post_id, '_duo_release_date', $date);
                                $u = wp_get_current_user();
                                wp_mail(get_option('admin_email'), 'New music release submission', "A new release was submitted.\nTitle: $title\nArtist: $artist\nAccount: {$u->user_login}\nRelease ID: $post_id\nReview it in WordPress Admin → Artist Releases.");
                                $out = duo_ap_notice('Release submitted successfully and marked Pending Review. This is not confirmation of delivery to streaming platforms.');
                            }
                        }
                    }
                }
            }
        }
    }
    ob_start(); ?>
    <div class="duo-ap-wrap"><div class="duo-ap-box"><h2>Submit your music</h2><p>Upload your final audio and square cover art. Your release will be sent to the admin for review.</p><?php echo wp_kses_post($out); ?>
    <form method="post" enctype="multipart/form-data"><?php wp_nonce_field('duo_ap_release', 'duo_ap_release_nonce'); ?>
      <div class="duo-ap-grid">
       <div class="duo-ap-field"><label for="duo_release_title">Song / release title *</label><input id="duo_release_title" name="duo_release_title" required maxlength="180"></div>
       <div class="duo-ap-field"><label for="duo_release_artist">Artist name *</label><input id="duo_release_artist" name="duo_release_artist" required maxlength="180"></div>
       <div class="duo-ap-field"><label for="duo_release_type">Release type</label><select id="duo_release_type" name="duo_release_type"><option>Single</option><option>EP</option><option>Album</option></select></div>
       <div class="duo-ap-field"><label for="duo_release_genre">Genre</label><input id="duo_release_genre" name="duo_release_genre" placeholder="Pop, Hip-Hop, Folk..."></div>
       <div class="duo-ap-field"><label for="duo_release_date">Preferred release date</label><input id="duo_release_date" type="date" name="duo_release_date"></div>
       <div class="duo-ap-field"><label for="duo_audio_file">Audio file (MP3/WAV/M4A/FLAC, max 100 MB) *</label><input id="duo_audio_file" type="file" name="duo_audio_file" accept=".mp3,.wav,.m4a,.flac,audio/*" required></div>
       <div class="duo-ap-field"><label for="duo_cover_file">Cover artwork (JPG/PNG, max 10 MB) *</label><input id="duo_cover_file" type="file" name="duo_cover_file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required></div>
      </div>
      <p><label><input type="checkbox" name="duo_rights_confirmed" value="1" required> I confirm I own or have permission to distribute this audio, artwork and all included content. *</label></p>
      <button class="duo-ap-btn" type="submit" name="duo_ap_submit_release" value="1">Upload & Submit for Review</button>
    </form><p><small>Upload limits also depend on your hosting provider's PHP and WordPress settings. Files are stored in the WordPress Media Library; configure private/object storage and access controls before using this for sensitive unreleased music at scale.</small></p></div></div>
    <?php return ob_get_clean();
}
add_shortcode('duo_artist_release_upload', 'duo_ap_release_shortcode');

function duo_ap_dashboard_shortcode() {
    if (!is_user_logged_in()) return '<div class="duo-ap-wrap"><div class="duo-ap-box"><h2>Artist dashboard</h2><p>Please log in to see your releases.</p><a class="duo-ap-btn" href="' . esc_url(wp_login_url(get_permalink())) . '">Log In</a> <a href="' . esc_url(duo_ap_page_url('artist-register')) . '">Register</a></div></div>';
    $user_id = get_current_user_id();
    $releases = get_posts(array('post_type'=>'duo_release','post_status'=>array('private','publish','pending','draft'),'author'=>$user_id,'numberposts'=>50,'orderby'=>'date','order'=>'DESC'));
    ob_start(); ?>
    <div class="duo-ap-wrap"><div class="duo-ap-box"><h2>Artist dashboard</h2><p>Welcome, <?php echo esc_html(wp_get_current_user()->display_name); ?>.</p><a class="duo-ap-btn" href="<?php echo esc_url(duo_ap_page_url('submit-release')); ?>">＋ Submit New Music</a></div>
    <div class="duo-ap-box"><h3>Your release submissions</h3>
    <?php if (!$releases) : ?><p>You haven't submitted any music yet.</p><?php else : ?><div style="overflow-x:auto"><table class="duo-ap-table"><thead><tr><th>Release</th><th>Submitted</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($releases as $r) : $status = get_post_meta($r->ID, '_duo_release_status', true); if (!$status) $status = 'Pending Review'; ?>
    <tr><td><?php echo esc_html($r->post_title); ?></td><td><?php echo esc_html(get_the_date('', $r)); ?></td><td><span class="duo-ap-badge"><?php echo esc_html($status); ?></span></td></tr>
    <?php endforeach; ?></tbody></table></div><?php endif; ?>
    </div><p><small>For safety, artist accounts can see only their own release submissions. Audio playback/download controls and automated royalty reporting are not included.</small></p></div>
    <?php return ob_get_clean();
}
add_shortcode('duo_artist_dashboard', 'duo_ap_dashboard_shortcode');

function duo_ap_create_pages() {
    $pages = array(
        'artist-register' => array('Artist Register', '[duo_artist_register]'),
        'artist-dashboard' => array('Artist Dashboard', '[duo_artist_dashboard]'),
        'submit-release' => array('Submit a Release', '[duo_artist_release_upload]')
    );
    foreach ($pages as $slug => $p) {
        $page = get_page_by_path($slug);
        if (!$page) {
            wp_insert_post(array('post_title'=>$p[0],'post_name'=>$slug,'post_content'=>$p[1],'post_status'=>'publish','post_type'=>'page'));
        } else {
            // Do not overwrite existing page content; users can manually add shortcode if needed.
        }
    }
}
register_activation_hook(__FILE__, 'duo_ap_activate');
function duo_ap_activate() {
    duo_ap_register_post_type();
    duo_ap_create_pages();
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, function(){ flush_rewrite_rules(); });

function duo_ap_admin_columns($columns) {
    $columns['duo_status'] = 'Release Status';
    return $columns;
}
add_filter('manage_duo_release_posts_columns', 'duo_ap_admin_columns');
function duo_ap_admin_column_content($column, $post_id) {
    if ($column === 'duo_status') {
        $status = get_post_meta($post_id, '_duo_release_status', true);
        echo esc_html($status ?: 'Pending Review');
    }
}
add_action('manage_duo_release_posts_custom_column', 'duo_ap_admin_column_content', 10, 2);

function duo_ap_status_metabox() {
    add_meta_box('duo_ap_release_status', 'Distribution Review Status', function($post) {
        wp_nonce_field('duo_ap_save_status', 'duo_ap_status_nonce');
        $status = get_post_meta($post->ID, '_duo_release_status', true) ?: 'Pending Review';
        $options = array('Pending Review','Approved','Rejected','Needs Changes','Delivered');
        echo '<label for="duo_ap_status_select">Status</label><select id="duo_ap_status_select" name="duo_ap_status_select" style="width:100%;margin-top:8px">';
        foreach ($options as $o) echo '<option value="'.esc_attr($o).'" '.selected($status,$o,false).'>'.esc_html($o).'</option>';
        echo '</select><p>Changing status does not send the release to music platforms automatically.</p>';
        $audio = (int)get_post_meta($post->ID, '_duo_audio_attachment_id', true);
        $cover = (int)get_post_meta($post->ID, '_duo_cover_attachment_id', true);
        if ($audio) echo '<p>Audio attachment: '.esc_html(get_the_title($audio)).' (Media ID '.$audio.')</p>';
        if ($cover) echo '<p>Cover attachment: '.esc_html(get_the_title($cover)).' (Media ID '.$cover.')</p>';
    }, 'duo_release', 'side', 'high');
}
add_action('add_meta_boxes', 'duo_ap_status_metabox');
function duo_ap_save_status($post_id) {
    if (get_post_type($post_id) !== 'duo_release') return;
    if (!isset($_POST['duo_ap_status_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['duo_ap_status_nonce'])), 'duo_ap_save_status')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    $status = sanitize_text_field(wp_unslash($_POST['duo_ap_status_select'] ?? 'Pending Review'));
    $allowed = array('Pending Review','Approved','Rejected','Needs Changes','Delivered');
    if (in_array($status, $allowed, true)) {
        update_post_meta($post_id, '_duo_release_status', $status);
        $author = (int)get_post_field('post_author', $post_id);
        $user = get_user_by('id', $author);
        if ($user && is_email($user->user_email)) {
            wp_mail($user->user_email, 'Your Duo release status was updated', "Your release \"" . get_the_title($post_id) . "\" status is now: $status.\nPlease log in to your artist dashboard for details.");
        }
    }
}
add_action('save_post_duo_release', 'duo_ap_save_status');
