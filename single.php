<?php
/**
 * Template for standard single posts.
 *
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */
?>

<?php get_header(); ?>

<?php get_template_part( 'attention', 'post' ); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>">

  <div class="cfct-block sidebar-left span4 first">
      <div class="cfct-module" style="padding: 0; margin: 0;">
      <ul class="dd_side_panel_nav">
        <li class="visible-desktop link first ui-tabs-selected"><a href="#section_event_details"><i class="icon-events icon-dd"></i> Post</a></li>
        <li class="visible-desktop link"><a href="#section_comments"><i class="icon-comments-gray icon-dd"></i> Comments <?php echo get_comments_number() ? '<span class="comment_count">' . get_comments_number() . '</span>' : ''; ?></a> </li>
      </ul>

    </div>
  </div>


  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">
    <?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>

      <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class(); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

        <div id="section_event_details" class="inner">

        <?php get_template_part( 'entry-meta', 'header' ); ?>

          <?php echo get_the_post_thumbnail( $null, 'hd_large', array( 'data-media-id' => get_post_thumbnail_id() ) ); ?>

          <header class="entry-title-wrapper page-title-wrapper">
          <?php flawless_breadcrumbs(); ?>
          <?php flawless_page_title(); ?>
        </header>

        <div class="entry-content clearfix">
          <?php the_content( 'More Info' ); ?>
        </div>

      </div>

      <div id="section_comments" class="inner">

        <header class="entry-title-wrapper">
          <?php flawless_breadcrumbs(); ?>
          <?php flawless_page_title(); ?>
        </header>

        <?php comments_template(); ?>
      </div>

        <?php get_template_part( 'entry-meta', 'footer' ); ?>

    </div>
    <?php endwhile; endif; ?>
  </div>

</div>

<?php get_footer() ?>
