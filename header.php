<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header"><div class="container nav-wrap">
<a class="brand" href="<?php echo esc_url(home_url('/')); ?>">DUO<span>.</span> MUSIC</a>
<button class="menu-toggle" aria-expanded="false" aria-controls="primary-navigation">Menu ☰</button>
<nav id="primary-navigation" class="main-nav" aria-label="Primary navigation">
<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
<a href="<?php echo esc_url(duo_music_page_url('music-distribution')); ?>">Distribution</a>
<a href="<?php echo esc_url(duo_music_page_url('pricing')); ?>">Pricing</a>
<a href="<?php echo esc_url(duo_music_page_url('about')); ?>">About</a>
<a href="<?php echo esc_url(duo_music_page_url('contact')); ?>">Contact</a>
</nav>
<a class="btn" href="<?php echo esc_url(duo_music_page_url('submit-release')); ?>">Submit Music ↗</a>
</div></header>
