<?php
/**
 * Template for 404 pages.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

  if( $flawless['404_page'] ) {
    $wp_query->post_count = 1;
    $wp_query->posts[0] = get_post( $flawless['404_page'] );
  }

?>

<?php get_header( '404' ); ?>

<?php get_template_part( 'attention', '404' ); ?>

<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area( 'left_sidebar' ); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class(); ?>">

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <?php flawless_page_title(); ?>
      </header>

      <div class="entry-content clearfix">
        <?php the_content( 'More Info' ); ?>
      </div>

    </div>
    <?php endwhile; else: ?>

    <div id="post-0"  class="<?php flawless_module_class( 'post error404 not-found' ); ?>">

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'flawless' ); ?></h1>
      </header>

    	<div class="entry-content clearfix">

        <?php get_template_part( 'content', '404' ); ?>

      </div>
    </div>

    <?php endif; ?>

  </div>

  <?php flawless_widget_area( 'right_sidebar' ); ?>

</div>

<?php get_footer(); ?>
