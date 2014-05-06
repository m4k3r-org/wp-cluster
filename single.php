<?php
/**
 * Template for standard single posts.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/
?>

<?php get_header(); ?>

<?php get_template_part( 'attention', 'post' ); ?>

<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class(); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <?php flawless_page_title(); ?>
      </header>

      <?php get_template_part( 'entry-meta', 'header' ); ?>

      <div class="entry-content clearfix">
      <?php the_content('More Info'); ?>
      <?php comments_template(); ?>
      </div>

      <?php get_template_part( 'entry-meta', 'footer' ); ?>

    </div>
    <?php endwhile; endif; ?>
  </div>

  <?php flawless_widget_area('right_sidebar'); ?>

</div>

<?php get_footer() ?>
