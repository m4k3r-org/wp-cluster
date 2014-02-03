<?php
/**
 * The template for displaying Category pages.
 *
 * Used to display archive-type pages for posts in a category.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */

get_template_part( 'templates/page/header', get_post_type() );
?>
<section class="container inner-wrapper entry-<?php echo get_post_type(); ?>">
  <div class="row">

    <div class="<?php echo is_active_sidebar( 'right-sidebar' ) ? 'col-md-9 col-sm-9' : 'col-md-12 col-sm-12'; ?> clearfix">
    
      <div class="content-wrapper">
      
        <header class="article-header">
          <h1 class="article-title"><?php single_cat_title(); ?></h1>
        </header>

        <?php if( !have_posts() ) : ?>
        
          <div class="alert alert-warning">
            <?php _e( 'Sorry, no results were found.', wp_festival( 'domain' ) ); ?>
          </div>
        
        <?php else : ?>

          <?php while( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'templates/article/listing', get_post_type() ); ?>
          <?php endwhile; ?>
        
        <?php endif; ?>
        
      </div>

    </div>

    <?php if( is_active_sidebar( 'right-sidebar' ) ) : ?>
      <div class="col-md-3">
        <div class="sidebar">
          <?php dynamic_sidebar('right-sidebar'); ?>
        </div>     
      </div>
    <?php endif; ?>

  </div>
</section>
<?php get_template_part( 'templates/page/footer', get_post_type() ); ?>