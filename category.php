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

get_template_part( 'templates/header', get_post_type() );
?>
<section class="container inner-wrapper entry-<?php echo get_post_type(); ?>">
  <div class="row">

    <div class="column <?php echo is_active_sidebar( 'right-sidebar' ) ? 'col-md-9 col-sm-9' : 'col-md-12 col-sm-12'; ?>">
    
      <section class="content-container">
      
        <header class="article-header">
          <h1 class="article-title"><?php single_cat_title(); ?></h1>
        </header>

        <?php if( !have_posts() ) : ?>

          <div class="alert alert-warning">
            <?php _e( 'Sorry, no results were found.', wp_festival( 'domain' ) ); ?>
          </div>

        <?php else : ?>

          <?php while( have_posts() ) : the_post(); ?>
            <hr/>
            <?php get_template_part( 'templates/article/listing', get_post_type() ); ?>
          <?php endwhile; ?>

        <?php endif; ?>
        
      </section>

    </div>

    <?php if( is_active_sidebar( 'right-sidebar' ) ) : ?>
      <div class="column col-md-3">
        <section class="sidebar"><?php dynamic_sidebar( 'right-sidebar' ); ?></section>
      </div>
    <?php endif; ?>

  </div>
</section>
<?php get_template_part( 'templates/footer', get_post_type() ); ?>