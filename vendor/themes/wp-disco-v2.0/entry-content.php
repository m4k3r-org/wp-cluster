<?php
/**
 * Entry Content: Displays either the excerpt or the full content, depending on request.
 *
 * Entry Template Parts:
 * - entry-meta-header
 * - entry-content
 * - entry-meta-footer
 *
 * $post arguments:
 * - show_title
 * - show_thumbnail
 * - show_meta_header
 * - show_post_excerpt
 * - show_post_content
 * - show_meta_footer
 *
 * @package Flawless
 * @since Flawless 0.6.0
 */

global $post;

/**
 * If 'show_post_content_type' is unknown - set 'show_post_content_type' to 'post_content'
 * If 'show_thumbnail' is unkown - set 'show_thumbnail' to true
 * Set thumb size to 'hd_small'
 *
 * @author korotkov@ud
 * @ticket https://ud-dev.com/projects/projects/discodonniepresentscom-november-2012/tasks/12
 */
if( !isset( $post->show_post_content_type ) || post_password_required() ) {
  $post->show_post_content_type = 'post_content';
}
if( !isset( $post->show_thumbnail ) ) {
  $post->show_thumbnail = true;
}

?>

<div class="entry-content entry-summary clearfix">

  <?php if( $post->show_thumbnail && $post->post_type == 'post' ) { ?>
    <?php flawless_thumbnail( array( 'size' => 'hd_large' ) ); ?>
  <?php } ?>

  <?php if( $post->show_post_content_type == 'post_content' ) {
    the_content(); ?>
  <?php } ?>

  <?php if( $post->show_post_content_type == 'post_excerpt' ) { ?>
    <?php the_excerpt( 'More Info' ); ?>
  <?php } ?>

</div>
