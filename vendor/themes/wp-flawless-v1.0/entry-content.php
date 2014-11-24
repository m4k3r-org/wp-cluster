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


 if( !isset( $post->show_post_content_type ) || post_password_required() ) {
  $post->show_post_content_type = 'post_excerpt';
 }


 ?>

<div class="entry-content entry-summary clearfix">

  <?php if ( $post->show_post_content_type == 'post_content' ) {
    the_content(); ?>
  <?php } ?>

  <?php if ( $post->show_post_content_type == 'post_excerpt' ) { ?>

    <?php if( $post->show_thumbnail ) { ?>
      <?php flawless_thumbnail(); ?>
    <?php } ?>

    <?php the_excerpt('More Info'); ?>

  <?php } ?>

</div>
