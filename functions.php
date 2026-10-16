<?php
if (!defined('ABSPATH')) exit;

function duo_music_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption','style','script'));
    register_nav_menus(array('primary' => __('Primary Menu', 'duo-music-distribution')));
}
add_action('after_setup_theme', 'duo_music_setup');

function duo_music_assets() {
    wp_enqueue_style('duo-music-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_script('duo-music-script', get_template_directory_uri() . '/assets/js/theme.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'duo_music_assets');

function duo_music_page_url($slug) {
    $page = get_page_by_path($slug);
    return $page ? get_permalink($page->ID) : home_url('/' . trim($slug, '/') . '/');
}

function duo_music_install_pages() {
    $pages = array(
        'home' => array('Home', '[duo_home]'),
        'music-distribution' => array('Music Distribution', '[duo_distribution]'),
        'pricing' => array('Pricing', '[duo_pricing]'),
        'artist-dashboard' => array('Artist Dashboard', '[duo_dashboard]'),
        'submit-release' => array('Submit a Release', '[duo_release_form]'),
        'about' => array('About Us', '[duo_about]'),
        'contact' => array('Contact', '[duo_contact_form]')
    );
    foreach ($pages as $slug => $page) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            wp_insert_post(array(
                'post_title' => $page[0],
                'post_name' => $slug,
                'post_content' => $page[1],
                'post_status' => 'publish',
                'post_type' => 'page'
            ));
        }
    }
    $home = get_page_by_path('home');
    if ($home && get_option('show_on_front') !== 'page') {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home->ID);
    }
}
add_action('after_switch_theme', 'duo_music_install_pages');

function duo_music_shortcode_styles() {
    return '';
}

function duo_music_home_shortcode() {
    ob_start(); ?>
    <section class="hero"><div class="container hero-grid">
      <div><div class="eyebrow">Independent artists. Worldwide reach.</div>
      <h1>Your music.<br><span>Your world.</span></h1>
      <p>Release your music with confidence. Duo Music Distribution helps independent artists prepare, manage and promote releases for the digital streaming era.</p>
      <div class="hero-actions"><a class="btn" href="<?php echo esc_url(duo_music_page_url('submit-release')); ?>">Start a Release ↗</a><a class="btn btn-outline" href="<?php echo esc_url(duo_music_page_url('music-distribution')); ?>">Explore Services</a></div>
      <div class="platforms"><span class="platform">Spotify*</span><span class="platform">Apple Music*</span><span class="platform">YouTube Music*</span><span class="platform">Amazon Music*</span></div>
      <p class="small-note">*Platform delivery depends on an active distribution agreement and approval.</p></div>
      <div class="hero-art"><div class="disc"></div><div class="art-copy"><strong>MAKE SOME NOISE.</strong><small>One release at a time.</small></div></div>
    </div></section>
    <section class="section"><div class="container"><div class="section-head"><div class="eyebrow">Why Duo</div><h2>Everything you need to move your music forward.</h2><p>Clear tools, artist-first support and a simple workflow from upload to release planning.</p></div>
      <div class="cards">
       <div class="card"><div class="icon">♫</div><h3>Digital Distribution</h3><p>Prepare metadata and release assets for delivery to supported music services.</p></div>
       <div class="card"><div class="icon">◎</div><h3>Release Management</h3><p>Keep your release details, cover art and launch dates organized in one place.</p></div>
       <div class="card"><div class="icon">↗</div><h3>Artist-First Support</h3><p>Get help with submission requirements, release preparation and next steps.</p></div>
      </div></div></section>
    <section class="section alt"><div class="container"><div class="section-head"><div class="eyebrow">How it works</div><h2>From your studio to your audience.</h2></div><div class="steps">
      <div class="step"><h3>Prepare your release</h3><p>Gather your final audio, cover artwork, artist names, credits and release date.</p></div>
      <div class="step"><h3>Submit for review</h3><p>Send the release information for review. Check that all rights and metadata are accurate.</p></div>
      <div class="step"><h3>Plan your launch</h3><p>After partner approval and delivery, share your links and promote your music.</p></div>
    </div></div></section>
    <section class="section"><div class="container"><div class="cta"><div><div class="eyebrow">Your next release starts here</div><h2>Ready to put your sound out there?</h2><p>Create a release submission and get your music organized for distribution review.</p></div><a class="btn" href="<?php echo esc_url(duo_music_page_url('submit-release')); ?>">Submit Your Music →</a></div></div></section>
    <?php return ob_get_clean();
}
add_shortcode('duo_home', 'duo_music_home_shortcode');

function duo_music_distribution_shortcode() {
    ob_start(); ?>
    <section class="page-hero"><div class="container"><div class="eyebrow">Our services</div><h1>Music distribution, made clearer.</h1><p class="muted">Build a release-ready package and understand the path to digital stores.</p></div></section>
    <section class="section"><div class="container cards">
      <div class="card"><div class="icon">♫</div><h3>Single & Album Releases</h3><p>Organize audio, artwork, credits, release dates and contributor details.</p></div>
      <div class="card"><div class="icon">▣</div><h3>Metadata Preparation</h3><p>Keep titles, language, genre, explicit-content flags and songwriter credits consistent.</p></div>
      <div class="card"><div class="icon">◉</div><h3>Release Planning</h3><p>Plan lead time for review, corrections, pitching and marketing activity.</p></div>
    </div></section>
    <section class="section alt"><div class="container"><h2>Supported destinations</h2><p class="muted">Potential destinations include Spotify, Apple Music, YouTube Music, Amazon Music, Deezer and other services, subject to your distribution partner agreement. This demo theme does not deliver audio to stores by itself.</p><div class="platforms"><span class="platform">Spotify</span><span class="platform">Apple Music</span><span class="platform">YouTube Music</span><span class="platform">Amazon Music</span><span class="platform">Deezer</span><span class="platform">More services</span></div><div class="hero-actions"><a class="btn" href="<?php echo esc_url(duo_music_page_url('submit-release')); ?>">Submit a Release</a></div></div></section>
    <?php return ob_get_clean();
}
add_shortcode('duo_distribution', 'duo_music_distribution_shortcode');

function duo_music_pricing_shortcode() {
    ob_start(); ?>
    <section class="page-hero"><div class="container"><div class="eyebrow">Transparent plans</div><h1>Choose your release path.</h1><p class="muted">Example pricing cards — edit these amounts and terms to match your real business before publishing.</p></div></section>
    <section class="section"><div class="container cards">
      <div class="card price-card"><h3>Starter</h3><div class="price">₹499 <small>/ release*</small></div><p class="muted">For your next single.</p><ul><li>Single release submission</li><li>Metadata checklist</li><li>Email support</li><li>Review before delivery</li></ul><a class="btn btn-outline" href="<?php echo esc_url(duo_music_page_url('submit-release')); ?>">Choose Starter</a></div>
      <div class="card price-card featured"><span class="featured-tag">POPULAR</span><h3>Artist</h3><div class="price">₹1,499 <small>/ release*</small></div><p class="muted">For active independent artists.</p><ul><li>EP or album submission</li><li>Release planning checklist</li><li>Priority support queue</li><li>Metadata review</li></ul><a class="btn" href="<?php echo esc_url(duo_music_page_url('submit-release')); ?>">Choose Artist</a></div>
      <div class="card price-card"><h3>Label</h3><div class="price">Custom</div><p class="muted">For teams managing multiple artists.</p><ul><li>Multi-release workflow</li><li>Label onboarding</li><li>Team process setup</li><li>Custom terms</li></ul><a class="btn btn-outline" href="<?php echo esc_url(duo_music_page_url('contact')); ?>">Contact Us</a></div>
    </div><p class="small-note container">*Demo prices only. Confirm final pricing, taxes, renewal rules, rights, royalties and refund terms before accepting payments.</p></section>
    <?php return ob_get_clean();
}
add_shortcode('duo_pricing', 'duo_music_pricing_shortcode');

function duo_music_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        $login = wp_login_url(get_permalink());
        ob_start(); ?>
        <section class="page-hero"><div class="container"><div class="eyebrow">Artist area</div><h1>Your artist dashboard.</h1><p class="muted">Log in to WordPress to access this demo dashboard.</p><a class="btn" href="<?php echo esc_url($login); ?>">Log In</a> <a class="btn btn-outline" href="<?php echo esc_url(wp_registration_url()); ?>">Register</a><p class="small-note">User registration must be enabled in Settings → General if you want public sign-ups.</p></div></section>
        <?php return ob_get_clean();
    }
    ob_start(); ?>
    <section class="page-hero"><div class="container"><div class="eyebrow">Artist area</div><h1>Welcome, <?php echo esc_html(wp_get_current_user()->display_name); ?>.</h1><p class="muted">This is a starter dashboard layout. Release statuses and royalty data need a connected backend.</p></div></section>
    <section class="section"><div class="container"><div class="dashboard-grid"><div class="stat"><strong>0</strong><span>Submitted releases</span></div><div class="stat"><strong>—</strong><span>Streams / royalties</span></div><div class="stat"><strong>Ready?</strong><span>Start your next release</span></div></div><a class="btn" href="<?php echo esc_url(duo_music_page_url('submit-release')); ?>">＋ Submit a Release</a></div></section>
    <?php return ob_get_clean();
}
add_shortcode('duo_dashboard', 'duo_music_dashboard_shortcode');

function duo_music_handle_release() {
    if (!isset($_POST['duo_release_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['duo_release_nonce'])), 'duo_release_submit')) {
        return '<div class="notice">Security check failed. Please reload the page and try again.</div>';
    }
    $artist = sanitize_text_field(wp_unslash($_POST['artist_name'] ?? ''));
    $title = sanitize_text_field(wp_unslash($_POST['release_title'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['artist_email'] ?? ''));
    $type = sanitize_text_field(wp_unslash($_POST['release_type'] ?? 'Single'));
    if (!$artist || !$title || !is_email($email)) return '<div class="notice">Please enter a release title, artist name and valid email address.</div>';
    $post_id = wp_insert_post(array(
        'post_type' => 'duo_release',
        'post_status' => 'private',
        'post_title' => $title . ' — ' . $artist,
        'post_content' => "Artist: $artist\nEmail: $email\nRelease type: $type\nSubmitted: " . current_time('mysql'),
    ), true);
    if (is_wp_error($post_id)) return '<div class="notice">Could not save your submission. Please contact the site administrator.</div>';
    update_post_meta($post_id, 'duo_artist_email', $email);
    update_post_meta($post_id, 'duo_release_type', $type);
    $admin_email = get_option('admin_email');
    wp_mail($admin_email, 'New Duo release submission', "Artist: $artist\nTitle: $title\nEmail: $email\nType: $type\nSubmission ID: $post_id");
    return '<div class="notice">Thanks! Your release details were submitted for review. This confirms receipt only; it does not mean your music has been delivered to stores.</div>';
}
function duo_music_release_form_shortcode() {
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duo_release_submit'])) $message = duo_music_handle_release();
    ob_start(); ?>
    <section class="page-hero"><div class="container"><div class="eyebrow">Release submission</div><h1>Tell us about your music.</h1><p class="muted">Submit basic release details. This form stores a private WordPress record and emails the site admin; it does not upload or deliver audio files.</p></div></section>
    <section class="section"><div class="container"><div class="form-panel release-form"><?php echo wp_kses_post($message); ?>
      <form method="post">
        <?php wp_nonce_field('duo_release_submit', 'duo_release_nonce'); ?>
        <div class="field"><label for="release_title">Release title *</label><input id="release_title" name="release_title" required maxlength="180"></div>
        <div class="field"><label for="artist_name">Artist / band name *</label><input id="artist_name" name="artist_name" required maxlength="180"></div>
        <div class="field"><label for="artist_email">Contact email *</label><input id="artist_email" name="artist_email" type="email" required></div>
        <div class="field"><label for="release_type">Release type</label><select id="release_type" name="release_type"><option>Single</option><option>EP</option><option>Album</option><option>Music video</option></select></div>
        <p class="small-note">Do not submit copyrighted material unless you own or control the required rights. Audio and artwork upload fields should be added only after secure upload storage and file validation are configured.</p>
        <button class="btn" type="submit" name="duo_release_submit" value="1">Submit Release Details →</button>
      </form></div></div></section>
    <?php return ob_get_clean();
}
add_shortcode('duo_release_form', 'duo_music_release_form_shortcode');

function duo_music_about_shortcode() {
    return '<section class="page-hero"><div class="container"><div class="eyebrow">About Duo</div><h1>Built for independent music.</h1><p class="muted">Duo Music Distribution is a music-focused service concept designed to help artists prepare releases, organize metadata and understand the digital distribution process.</p></div></section><section class="section"><div class="container"><div class="card"><h2>Our mission</h2><p>Give independent artists a clear, professional place to start their release journey. Add your company history, team details, legal business information and verified partner relationships here before launch.</p></div></div></section>';
}
add_shortcode('duo_about', 'duo_music_about_shortcode');

function duo_music_contact_shortcode() {
    $notice = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duo_contact_submit'])) {
        if (!isset($_POST['duo_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['duo_contact_nonce'])), 'duo_contact')) {
            $notice = '<div class="notice">Security check failed. Please reload and try again.</div>';
        } else {
            $name = sanitize_text_field(wp_unslash($_POST['contact_name'] ?? ''));
            $email = sanitize_email(wp_unslash($_POST['contact_email'] ?? ''));
            $message = sanitize_textarea_field(wp_unslash($_POST['contact_message'] ?? ''));
            if ($name && is_email($email) && $message) {
                $sent = wp_mail(get_option('admin_email'), 'Duo website contact from ' . $name, "Name: $name\nEmail: $email\n\n$message", array('Reply-To: ' . $name . ' <' . $email . '>'));
                $notice = $sent ? '<div class="notice">Your message has been submitted. We will get back to you if email delivery is working.</div>' : '<div class="notice">Your message could not be emailed. Please check the site email configuration.</div>';
            } else $notice = '<div class="notice">Please fill every field with valid information.</div>';
        }
    }
    ob_start(); ?>
    <section class="page-hero"><div class="container"><div class="eyebrow">Contact</div><h1>Let's talk music.</h1><p class="muted">Questions about a release, partnership or label account? Send us a message.</p></div></section>
    <section class="section"><div class="container contact-grid"><div><h2>Get in touch</h2><p class="muted">Replace the admin email in WordPress Settings → General with your business email. Configure SMTP for reliable delivery.</p><p><strong>Email:</strong> <?php echo esc_html(get_option('admin_email')); ?></p></div><div class="form-panel"><?php echo wp_kses_post($notice); ?><form method="post"><?php wp_nonce_field('duo_contact', 'duo_contact_nonce'); ?><div class="field"><label for="contact_name">Your name *</label><input id="contact_name" name="contact_name" required></div><div class="field"><label for="contact_email">Email address *</label><input id="contact_email" type="email" name="contact_email" required></div><div class="field"><label for="contact_message">Message *</label><textarea id="contact_message" name="contact_message" rows="5" required></textarea></div><button class="btn" type="submit" name="duo_contact_submit" value="1">Send Message →</button></form></div></div></section>
    <?php return ob_get_clean();
}
add_shortcode('duo_contact_form', 'duo_music_contact_shortcode');

function duo_music_register_release_type() {
    register_post_type('duo_release', array(
        'labels' => array('name' => 'Release Submissions', 'singular_name' => 'Release Submission'),
        'public' => false, 'show_ui' => true, 'show_in_menu' => true,
        'supports' => array('title', 'editor'), 'capability_type' => 'post',
        'menu_icon' => 'dashicons-album'
    ));
}
add_action('init', 'duo_music_register_release_type');
