<?php
/**
 * Template for standard single posts.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */
?>

<?php get_template_part( 'templates/header', get_post_type() ); ?>

<?php get_template_part( 'templates/aside/attention', get_post_type() ); ?>

  <div class="<?php wp_disco()->wrapper_class(); ?>">

  <?php wp_disco()->widget_area( 'left_sidebar' ); ?>

    <div class="<?php wp_disco()->block_class( 'main cfct-block' ); ?>">
    <?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
      <div id="post-<?php the_ID(); ?>" class="<?php wp_disco()->module_class(); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

        <header class="entry-title-wrapper">
        <?php wp_disco()->breadcrumbs(); ?>
        <?php wp_disco()->page_title(); ?>
      </header>

        <?php get_template_part( 'templates/article/entry-meta', 'header' ); ?>

        <div class="entry-content clearfix">
      <?php the_content( 'More Info' ); ?>
      <?php comments_template(); ?>
      </div>

        <?php get_template_part( 'templates/article/entry-meta', 'footer' ); ?>

    </div>
    <?php endwhile; endif; ?>
  </div>

    <?php wp_disco()->widget_area( 'right_sidebar' ); ?>

</div>

<?php get_template_part( 'templates/footer', get_post_type() ); ?>