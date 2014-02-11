<?php
/**
 * Template for standard pages.
 *
 *
 * @version 0.60.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */
?>

<?php get_template_part( 'templates/header', 'page' ); ?>

  <section id="body-content" class="frame">

    <?php get_template_part( 'templates/aside/attention', 'page' ); ?>

    <?php wp_disco()->widget_area( 'left_sidebar' ); ?>

    <?php if( have_posts() ) : ?>
      <section class="posts" data-query="">

    <?php while( have_posts() ) : the_post(); ?>
      <?php get_template_part( 'templates/article/single', get_post_type() ); ?>
    <?php endwhile; ?>

    </section>
    <?php endif; ?>

    <?php wp_disco()->widget_area( 'right_sidebar' ); ?>

  </section>

<?php get_template_part( 'templates/footer', get_post_type() ); ?>