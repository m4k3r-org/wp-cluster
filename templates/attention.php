<?php
/**
 * Attention General displays the attenion grabbing element on all standard pages.
 *
 *
 * This can be overridden in child themes with loop.php or
 * attention-template.php, where 'template' is the context
 * requested by a template. For example, attention-blog-home.php would
 * be used if it exists and we ask for the attention with:
 * <code>get_template_part( 'templates/attention', 'blog-home' );</code>
 *
 * @module Flawless
 * @since Flawless 0.0.3
 *
 */

if ( get_post_meta( $post->ID, 'hide_header', true ) == 'true' || !current_theme_supports( 'inner_page_slideshow_area' ) ) {
  return;
}
