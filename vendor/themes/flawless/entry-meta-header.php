<?php
/**
 * Entry Footer Meta: Displays author and time.
 *
 * Entry Template Parts:
 * - entry-meta-header
 * - entry-content
 * - entry-meta-footer
 *
 * $post arguments:
 * - show_thumbnail
 * - show_meta
 * - show_entry_utility
 * - show_titles
 * - show_post_excerpt
 * - show_post_content
 *
 * @package Flawless
 * @since Flawless 0.6.0
 */

 global $post, $wp_query;

  //* Show author if Post Type supports it, and author isn't disabled for this post */
  if( post_type_supports( get_queried_object()->post_type, 'author' ) && get_post_meta( get_queried_object()->ID, 'hide_post_author', true ) != 'true' ) {
    $meta_html[] = '<li class="author">By <a class="author vcard" rel="author" href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' .  get_the_author() . '</a></li>';
  }

  if( $flawless[ 'post_types' ][$post->post_type][ 'show_post_meta' ] == 'true' ) {
    $meta_html[] = '<li class="entry-date"><time datetime="' . get_the_date( 'c' ) . '" pubdate>' . get_the_date() . '</time></li>';
  }

  $meta_html = apply_filters( 'flawless_meta_header', $meta_html, array( 'location' => 'header' ) );

  $secondary_entry_title = apply_filters( 'flawless_title', '', array( 'title' => get_the_title(), 'position' => 'secondary-entry-title' ) );

  if( !empty( $secondary_entry_title ) ) {
    echo '<h3 class="secondary-entry-title">' . $secondary_entry_title . '</h3>';
  }

  //* Leave if no HTML generated */
  if( empty( $meta_html ) ) {
    return;
  }

?>

<ul class="entry-meta header">
  <?php echo implode( '', $meta_html ); ?>
</ul>
