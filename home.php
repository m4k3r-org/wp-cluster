<?php
/**
 * Home page
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */
get_template_part( 'templates/header', get_post_type() );

?>

<div class="container block">
  <?php get_template_part( 'templates/aside/carousel', get_post_type() ); ?>
</div>

<div class="parallax block">
  <?php get_template_part( 'templates/aside/parallax-block', get_post_type() ); ?>
</div>

<?php get_template_part( 'templates/aside/infinite-scroll', get_post_type() ); ?>

<?php get_template_part( 'templates/footer', get_post_type() ); ?>