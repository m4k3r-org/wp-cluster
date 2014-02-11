<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to twentytwelve_comment() which is
 * located in the functions.php file.
 *
 * @author Usability Dynamics
 * @module wp-escalade  
 * @since wp-escalade 0.1.0
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
  return;

?>
<?php if( comments_open( get_the_ID() ) ) : ?>
  <?php if ( have_comments() ) : ?>
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