<?php
/**
 * The Core Template File
 *
 * Fallback for all other templates.
 * Can be overwritten by: taxonomy.php, category.php, tag.php, author.php, archive-$post_type.php and other more specific templates.
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */
?>

<?php get_template_part( 'templates/header', get_post_type() ); ?>

<section class="container inner-wrapper entry-<?php echo get_post_type(); ?>">
  <div class="row">

    <div class="column <?php echo is_active_sidebar( 'right-sidebar' ) ? 'col-md-9 col-sm-9' : 'col-md-12 col-sm-12'; ?> clearfix">

      <section class="content-container">
    
        <?php if( !have_posts() ) : ?>
          <?php get_template_part( 'templates/aside/alert', get_post_type() ); ?>
        <?php else : ?>

          <?php while( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'templates/article/listing', get_post_type() ); ?>
          <?php endwhile; ?>

        <?php endif; ?>
      
      </section>

    </div>

    <?php if( is_active_sidebar( 'right-sidebar' ) ) : ?>
      <div class="column col-md-3">
        <section class="module-container"><?php dynamic_sidebar( 'right-sidebar' ); ?></section>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php get_template_part( 'templates/footer', get_post_type() ); ?>