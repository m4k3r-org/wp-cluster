<?php
/**
 * Template for home page which may be static or include latest posts.
 *
 *
 *
 * @version 0.60.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }
?>

<?php get_header() ?>

<?php get_template_part('attention','home'); ?>
    
<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>
  
  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    
    <?php get_template_part('loop', 'home'); ?>

    <?php get_template_part('content','home-bottom'); ?>

  </div>

  <?php flawless_widget_area('right_sidebar'); ?>

</div>

<?php get_footer() ?>
