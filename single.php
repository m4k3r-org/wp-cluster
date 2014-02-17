<?php
/**
 * The Template for displaying all single posts.
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

get_template_part( 'templates/header', get_post_type() );
?>
<main id="main" class="main single " role="main">
  <?php wp_festival()->section( 'above-content' ); ?>

  <?php get_template_part( 'templates/main' ); ?>

  <?php wp_festival()->section( 'below-content' ); ?>
</main>
<?php get_template_part( 'templates/footer', get_post_type() ); ?>
