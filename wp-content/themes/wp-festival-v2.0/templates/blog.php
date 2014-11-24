<?php
/**
 *  Blog Loop
 */
?>

<?php get_template_part( 'templates/header' ); ?>

<?php get_template_part( 'templates/aside/header-image' ); ?>

  <main id="main" class="main" role="main">

    <section id="blog-loop">

      <?php
      // Doesn`t work this:
      // echo wp_festival2()->nav( 'blog', 1 );

      // Instead of this we list the categories with the wp function.
      $args = array(
        'parent' => 0,
        'orderby' => 'term_id'
      );
      $categories = get_categories( $args );

      $selected_text = null;
      
      ?>

      <nav class="category">
        <a <?php if( get_permalink( get_option('page_for_posts' ) ) == home_url( add_query_arg( array() ) ) ) { echo 'class="selected" '; $selected_text = 'All'; } ?> href="<?php echo get_permalink( get_option('page_for_posts' ) ) ?>"><?php _e('All', wp_festival2( 'domain' )); ?></a>
        <?php foreach( $categories as $category ): ?>
          <a <?php if( get_category_link( $category->term_id ) == home_url( add_query_arg( array() ) ) ) { echo 'class="selected" '; $selected_text = $category->name; } ?> href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name; ?></a>
        <?php endforeach; ?>
      </nav>

      <div class="mobile-nav">
        <a href="#" class="selected-category nav-closed"><?php echo $selected_text; ?>
          <span class="icon-triangle-down"></span></a>
        <?php if( $selected_text != 'All' ): ?>
          <a href="<?php echo get_permalink( get_option('page_for_posts' ) ) ?>">All</a>
        <?php endif; ?>
        <?php
        foreach( $categories as $category ):
          if( $selected_text != $category->name ): ?>
            <a href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name; ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>


      <div class="posts-list-container container" id="blog">

        <?php if( have_posts() ) : $i = 0; ?>
          <?php while( have_posts() ) : the_post();
            $i++; ?>
            <?php get_template_part( 'templates/article/content', wp_festival2()->get_query_template() ); ?>
            <?php if( $i % 3 == 0 ): ?>
              <div class="clearfix hidden-sm hidden-xs hidden-md"></div>
            <?php endif; ?>
          <?php endwhile; ?>
        <?php endif; ?>

      </div>

      <div class="clearfix"></div>

    </section>

    <section class="blog-pagination">
      <?php //wp_festival2()->page_navigation(); ?>
    </section>
  </main>
<?php get_template_part( 'templates/footer' ); ?>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/static/scripts/src/components/dotdotdot/src/js/jquery.dotdotdot.min.js"></script>

<script type='text/javascript'>
  (function( $ ){
    $( document.body ).on( 'post-load', function(){

      // New posts have been added to the page.
      // Run dotdotdot on new posts.

      // @todo - move this to app.js, currently doesn`t want to work from there
      if( $( '.post .flip-container h3' ).length )
        $( '.post .flip-container h3' ).dotdotdot( {
          height: 110
        } );
    } );

  })( jQuery );
</script>