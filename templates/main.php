<?php
/**
 *
 * The right sidebar has a "col-md-push-4" class.
 * "main" elemnet
 */
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="container">
        <div class="row">
          <?php wp_festival()->section( 'left-sidebar' ); ?>
          <?php wp_festival()->section( 'right-sidebar' ); ?>

          <?php if( !have_posts() ) : ?>
          <?php endif; ?>

          <?php while( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
            <?php get_template_part( 'templates/article/author', get_post_type() ); ?>
            <?php comments_template(); ?>
          <?php endwhile; ?>

      </div>
    </div>
  </div>
</div>

<?php //get_template_part( 'templates/article/listing' ); ?>
<?php //get_template_part( 'templates/section/articles' ); ?>
<?php //get_template_part( 'templates/section/grid-artist' ); ?>
