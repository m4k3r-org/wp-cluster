<?php
/**
 * Template Name: Full Width Page
 *
 * The template for displaying full width pages.
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

get_template_part( 'templates/header', get_post_type() );
?>
<main id="main" class="main full-width" role="main">
  <?php wp_festival()->section( 'above-content' ); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <?php if( have_posts() ) : ?>
          <section class="container-inner">
          <?php while( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
          <?php endwhile; ?>
          <section>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php wp_festival()->section( 'below-content' ); ?>
</main>
<?php get_template_part( 'templates/footer', get_post_type() ); ?>