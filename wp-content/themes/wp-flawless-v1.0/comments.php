<?php
/**
 * Renders Comments List and / or form.
 *
 * @package Flawless
 * @since Flawless 0.6.0
 */

//** Check if this post type support comments */
if( !post_type_supports( get_queried_object()->post_type, 'comments' ) || post_password_required() ) {
  return;
}

?>
<div class="row-fluid comments_row">
  <div class="span12">
  <div class="cfct-module">

    <div class="comments_wrapper">

      <div class="wp_list_comments_wrapper">
        <?php if ( have_comments() ) { ?>
        <h3 class="comments-title"><?php printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'flawless' ), number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' ); ?></h3>

        <ol class="commentlist">
        <?php wp_list_comments( array( 'callback' => 'flawless_comment' ) ); ?>
        </ol>
        <?php } ?>
      </div>

      <?php flawless_comment_form(); ?>

    </div>

  </div>
  </div>
</div>
