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
 
$cc = is_active_sidebar( 'left-sidebar' ) ? 'col-md-9 col-sm-9' : 'col-md-12 col-sm-12';
 
get_template_part( 'templates/page/header', get_post_type() ); 
?>
<section class="container inner-wrapper">
  <div class="row">

    <?php if( is_active_sidebar( 'left-sidebar' ) ) : ?>
      <div class="col-md-3">
        <div class="sidebar">
          <?php dynamic_sidebar('left-sidebar'); ?>
        </div>     
      </div>
    <?php endif; ?>
  
    <div class="<?php echo $cc; ?>">
      <?php while( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
      <?php endwhile; ?>
    </div>

  </div>
</section>
<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>