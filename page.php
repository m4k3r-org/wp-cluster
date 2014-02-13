<?php
/**
 * The template for displaying all pages.
 * Page does not contain sidebars
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */
?>

<?php get_template_part( 'templates/header' ); ?>

<main id="main" class="main" role="main">
  <?php wp_festival()->section( 'above-content' ); ?>

  <?php get_template_part( 'templates/main' ); ?>

  <?php wp_festival()->section( 'below-content' ); ?>
</main>

<?php get_template_part( 'templates/footer' ); ?>
