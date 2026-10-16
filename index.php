<?php get_header(); ?>
<main class="content-area"><div class="container">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article <?php post_class(); ?>><h1><?php the_title(); ?></h1><?php the_content(); ?></article>
<?php endwhile; else : ?><h1>Nothing found</h1><p class="muted">Please check back later.</p><?php endif; ?>
</div></main>
<?php get_footer(); ?>
