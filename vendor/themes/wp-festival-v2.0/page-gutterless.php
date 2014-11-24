<?php
/**
 * Template Name: Gutterless
 *
 * The template for displaying full width pages with no gutter.
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */ ?>

<?php get_template_part( 'templates/header', get_post_type() ); ?>

<?php get_template_part( 'templates/aside/header-image' ); ?>

<main id="main" class="main full-width" role="main">
  <?php wp_festival2()->section( 'above-content' ); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <?php if( have_posts() ) : ?>
          <section class="container-inner">
          <?php while( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'templates/article/content-page' ); ?>
          <?php endwhile; ?>
          <section>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php wp_festival2()->section( 'below-content' ); ?>
</main>

<?php get_template_part( 'templates/footer', get_post_type() ); ?>