<?php
/**
 * Template for standard single posts.
 *
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */

if( get_comments_number() ) {
  $meta_html[ ] = '<li class="comments-count"><i class="icon-dd icon-comments-count"></i>' . sprintf( __( '%1s comments', 'flawless' ), get_comments_number() ) . '</li>';
}

$side_meta[ ] = '<li class="posted-ago"><i class="icon-gray icon-time-ago"></i>' . get_the_date() . '</li>';

if( get_the_category_list() ) {
  $side_meta[ ] = '<li class="posted-in"><i class="icon-gray icon-posted-in"></i>' . __( 'Posted under ', 'flawless' ) . get_the_category_list( ', ' ) . '</li>';
}

$side_meta[ ] = '<li class="permalink"><a href="' . get_permalink() . '"><i class="icon-gray icon-permalink"></i> Permalink</a></li>';

?>

<?php get_header(); ?>

<?php get_template_part( 'attention', 'post' ); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>">

  <div class="cfct-block sidebar-left span4 first">
      <div class="cfct-module" style="padding: 0; margin: 0;">
      <ul class="dd_side_panel_nav">
        <li class="visible-desktop link first ui-tabs-selected"><a href="#section_event_details"><i class="icon-events icon-dd"></i> Post</a></li>
        <li class="visible-desktop link"><a href="#section_comments"><i class="icon-comments icon-dd"></i> Comments <?php echo get_comments_number() ? '<span class="comment_count">' . get_comments_number() . '</span>' : ''; ?></a> </li>
      </ul>

        <?php echo '<ul class="entry-meta dd_side_panel_nav">' . implode( '', (array) $side_meta ) . '</ul>'; ?>

    </div>
  </div>


  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">
    <?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
      <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class(); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

        <div id="section_event_details" class="inner">

        <?php get_template_part( 'entry-meta', 'header' ); ?>

          <?php flawless_thumbnail( array( 'size' => 'hd_large', 'link' => false ) ); ?>

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
