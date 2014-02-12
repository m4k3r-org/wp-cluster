<?php
/**
 * The template for displaying Comments.
 *
 * The comments.php file is expected to be located in root of theme.
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */
?>
<?php if( comments_open() && shortcode_exists( 'fbcomments' ) ) echo do_shortcode( '[fbcomments scheme=dark]' ); ?>


<?php if( comments_open( get_the_ID() ) ) : ?>
  <?php if( have_comments() ) : ?>
    <!-- Comment section -->
    <div id="comments" class="comments" id="comments">
      <div class="title"><h5><?php comments_number( 'No Comments', 'One Comment', '% Comments' ); ?></h5></div>
      <ul class="comment-list">
        <?php wp_list_comments( array( 'style' => 'ul', 'short_ping' => true, 'avatar_size' => 64 ) ); ?>
      </ul>
    </div>
  <?php endif; // have_comments() ?>
  <!-- Comment posting -->
  <div class="respond well">
    <?php comment_form( array( 'comment_notes_after' => '' ) ); ?>
  </div>
<?php endif; ?>