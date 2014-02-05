<?php
/**
 * Template Name: Splash
 * 
 * Page does not contain any sidebars and a minimal header and footer.
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
get_template_part( 'templates/page/header', 'splash' );
?>
<section class="no-sidebar no-default-container">
  <?php while( have_posts() ) : the_post(); ?>
    <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
  <?php endwhile; ?>
</section>
<?php get_template_part( 'templates/page/footer', 'splash' ); ?>