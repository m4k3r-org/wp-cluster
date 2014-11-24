<?php /* Template Name: Inner Wrapper */ ?>
<?php get_header(); ?>
<div id="content">
  <h2 class="page-title"><span><?php the_tile(); ?></span></h2>
  <div class="inner">
    <?php if(have_posts()) : while (have_posts()) : the_post(); ?>
      <?php the_content(); ?>
    <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>