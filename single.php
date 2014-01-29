<?php
/**
 * The Template for displaying all single posts.
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */

$cc = is_active_sidebar( 'single-sidebar' ) ? 'col-md-9 col-sm-9' : 'col-md-12 col-sm-12';
 
get_template_part( 'templates/page/header', get_post_type() ); 
?>
<section class="container inner-wrapper">
  <div class="row">

    <div class="<?php echo $cc; ?>">

      <?php while( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
      <?php endwhile; ?>

      <?php comments_template( '/templates/aside/comments.php' ); ?>

    </div>

    <?php if( is_active_sidebar( 'single-sidebar' ) ) : ?>
      <div class="col-md-3">
        <div class="sidebar">
          <?php dynamic_sidebar('single-sidebar'); ?>
        </div>     
      </div>
    <?php endif; ?>

  </div>
</section>
<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>
