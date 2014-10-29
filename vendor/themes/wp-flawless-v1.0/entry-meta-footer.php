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

  if($flawless['post_types'][$post->post_type]['show_post_meta'] == 'true') {

    if(get_the_category_list()) {
      $meta_html[] = '<li class="posted-in">' . __('Categories: ', 'flawless') . get_the_category_list(', ') . '</li>';
    }

  }

  $meta_html = apply_filters('flawless_meta_header', $meta_html, array('location' => 'footer'));

  //* Leave if no HTML generated */
  if(empty($meta_html)) {
    return;
  }
?>
