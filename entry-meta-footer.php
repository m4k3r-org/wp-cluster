<?php
/**
 * Entry Footer: Displays associated taxonomies and comments informaiton.
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

if( !isset( $post->show_meta_footer ) ) {
  $post->show_meta_footer = true;
}

if( is_single() ) {
  return;
}

if( $flawless[ 'post_types' ][ $post->post_type ][ 'show_post_meta' ] == 'true' ) {

  $meta_html[ ] = '<li class="posted-ago"><i class="icon-dd icon-time-ago"></i>' . get_the_date() . '</li>';

  // do not show categories here
  /*if( get_the_category_list() ) {
    $meta_html[ ] = '<li class="posted-in"><i class="icon-dd icon-posted-in"></i>' . __( 'Posted under ', 'flawless' ) . get_the_category_list( ', ' ) . '</li>';
  }*/

  if( get_comments_number() ) {
    $meta_html[ ] = '<li class="comments-count"><i class="icon-dd icon-comments-count"></i>' . sprintf( __( '%1s comments', 'flawless' ), get_comments_number() ) . '</li>';
  }

  $meta_html[ ] = '<li class="permalink"><a href="' . get_permalink() . '"><i class="icon-big-dd icon-permalink"></i></a></li>';

}

//* Leave if no HTML generated */
if( empty( $meta_html ) ) {
  return;
}

echo '<ul class="entry-meta gray_bar footer">' . implode( '', (array) $meta_html ) . '</ul>';
