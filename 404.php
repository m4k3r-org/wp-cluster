<?php
/**
 * Template for 404 pages.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */

if( isset( $flawless[ '404_page' ] ) && $flawless[ '404_page' ] ) {
  $wp_query->post_count = 1;
  $wp_query->posts[ 0 ] = get_post( $flawless[ '404_page' ] );
}

?>

<?php get_template_part( 'templates/header', '404' ); ?>

<?php get_template_part( 'templates/aside/attention', '404' ); ?>

  <div class="<?php wp_disco()->wrapper_class( ); ?>">

  <?php wp_disco()->widget_area( 'left_sidebar' ); ?>

    <div class="<?php wp_disco()->block_class( 'main cfct-block' ); ?>">
    <?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
      <div id="post-<?php the_ID(); ?>" class="<?php wp_disco()->module_class(); ?>">

      <header class="entry-title-wrapper">
        <?php wp_disco()->breadcrumbs(); ?>
        <?php wp_disco()->page_title(); ?>
      </header>

      <div class="entry-content clearfix">
        <?php the_content( 'More Info' ); ?>
      </div>

    </div>
    <?php endwhile;
    else: ?>

      <div id="post-0" class="<?php wp_disco()->module_class( 'post error404 not-found' ); ?>">

      <header class="entry-title-wrapper">
        <?php wp_disco()->breadcrumbs(); ?>
        <h1 class="entry-title"><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'flawless' ); ?></h1>
      </header>

    	<div class="entry-content clearfix">

        <?php get_template_part( 'templates/article/content', '404' ); ?>

      </div>
    </div>

    <?php endif; ?>

  </div>

    <?php wp_disco()->widget_area( 'right_sidebar' ); ?>

</div>

<?php get_template_part( 'templates/header', '404' ); ?>