<?php
/**
 * Home page
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */
get_template_part( 'templates/page/header', get_post_type() );

?>

<div class="container block">
  <?php get_template_part( 'templates/aside/carousel', get_post_type() ); ?>
</div>

<div class="parallax block">
  <?php get_template_part( 'templates/aside/parallax-block', get_post_type() ); ?>
</div>

<div class="container block the-artist">
  <?php get_template_part( 'templates/aside/the-artist', get_post_type() ); ?>
</div>

<div class="block artist-alphabetical">
  <?php get_template_part( 'templates/aside/artist-alphabetical', get_post_type() ); ?>
</div>

<div class="container block local-support-artist">
  <?php get_template_part( 'templates/aside/local-support', get_post_type() ); ?>
</div>

<?php get_template_part( 'templates/aside/infinite-scroll', get_post_type() ); ?>

<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>