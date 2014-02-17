<?php
/**
 * The Core Template File
 *
 * Fallback for all other templates.
 * Can be overwritten by: taxonomy.php, category.php, tag.php, author.php, archive-$post_type.php and other more specific templates.
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
