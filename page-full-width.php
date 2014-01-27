<?php
/**
 * Template Name: Full Width Page
 * 
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
get_template_part( 'templates/page/header', get_post_type() ); 
?>
<section class="inner-wrapper no-sidebar no-default-container">
  <?php while( have_posts() ) : the_post(); ?>
    <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
  <?php endwhile; ?>
</section>
<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>