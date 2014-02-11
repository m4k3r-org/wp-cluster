<?php
/**
 * Template for home page which may be static or include latest posts.
 *
 *
 *
 * @version 0.60.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */
?>

<?php get_template_part( 'templates/header' ); ?>

<?php get_template_part( 'templates/aside/attention', 'home' ); ?>

<div class="<?php wp_disco()->wrapper_class( ); ?>">

  <?php wp_disco()->widget_area( 'left_sidebar' ); ?>

  <div class="<?php wp_disco()->block_class( 'main cfct-block' ); ?>">
    <?php get_template_part( 'templates/article/loop', 'home' ); ?>
    <?php get_template_part( 'templates/article/content', 'home-bottom' ); ?>
  </div>

  <?php wp_disco()->widget_area( 'right_sidebar' ); ?>

</div>

<?php get_template_part( 'templates/footer' ); ?>
