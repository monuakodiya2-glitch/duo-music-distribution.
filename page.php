<?php get_header(); ?>
<main class="content-area"><div class="container">
<?php while (have_posts()) : the_post(); ?>
<article <?php post_class(); ?>><?php if (!is_front_page()) : ?><h1><?php the_title(); ?></h1><?php endif; ?><?php the_content(); ?></article>
<?php endwhile; ?>
</div></main>
<?php get_footer(); ?>
