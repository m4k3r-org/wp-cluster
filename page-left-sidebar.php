<?php
/**
 * Template Name: Left Sidebar page
 *
 * The template for displaying pages with Left Sidebar.
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
<section class="container inner-wrapper">
  <div class="row">

    <?php if( is_active_sidebar( 'left-sidebar' ) ) : ?>
      <div class="column col-md-3">
        <section class="module-container"><?php dynamic_sidebar('left-sidebar'); ?></section>
      </div>
    <?php endif; ?>
  
    <div class="column <?php echo is_active_sidebar( 'left-sidebar' ) ? 'col-md-9 col-sm-9' : 'col-md-12 col-sm-12'; ?>">
      <section class="content-container">
        <?php while( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
        <?php endwhile; ?>
      </section>
    </div>

  </div>
</section>
<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>