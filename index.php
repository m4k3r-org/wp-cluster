<?php
/**
 * Template for home page which may be static or include latest posts.
 *
 * @module Flawless
 * @submodule Template
 *
 * @version 0.60.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 */

if ( !function_exists( 'get_template_part' ) ) {
  die( 'Direct access prohibited.' );
}
?>

<?php get_template_part( 'templates/header', 'index' ); ?>

<?php get_template_part( 'templates/attention', 'home' ); ?>

  <div class="<?php flawless_wrapper_class(); ?>">

    <?php flawless_widget_area( 'left_sidebar' ); ?>

    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

      <?php get_template_part( 'templates/loop', 'home' ); ?>

      <?php get_template_part( 'templates/content', 'home-bottom' ); ?>

    </div>

    <?php flawless_widget_area( 'right_sidebar' ); ?>

  </div>

<?php get_template_part( 'templates/footer', 'index' ); ?>
