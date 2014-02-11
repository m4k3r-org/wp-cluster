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
<section class="full-width inner-wrapper no-sidebar">
  <div class="row">
    <div class="col-md-12">
      <div class="content-container">
        <?php while( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</section>
<?php get_template_part( 'templates/footer', get_post_type() ); ?>