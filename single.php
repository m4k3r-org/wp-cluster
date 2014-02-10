<?php
/**
 * The Template for displaying all single posts.
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

get_template_part( 'templates/page/header', get_post_type() );
?>
<section class="container inner-wrapper">
  <div class="row">

    <div class="column <?php echo is_active_sidebar( 'single-sidebar' ) ? 'col-md-9 col-sm-9' : 'col-md-12 col-sm-12'; ?>">
      <section class="content-container">
        <?php while( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
        <?php endwhile; ?>
        <?php comments_template( '/templates/aside/comments.php' ); ?>
      </section>
    </div>

    <?php if( is_active_sidebar( 'single-sidebar' ) ) : ?>
      <div class="column col-md-3">
        <section class="module-container"><?php dynamic_sidebar('single-sidebar'); ?></section>
      </div>
    <?php endif; ?>

  </div>
</section>
<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>
