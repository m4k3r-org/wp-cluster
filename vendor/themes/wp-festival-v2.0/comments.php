<?php
/**
 * The template for displaying Comments.
 *
 * The comments.php file is expected to be located in root of theme.
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */
?>
<?php if( comments_open() && shortcode_exists( 'fbcomments' ) ) echo do_shortcode( '[fbcomments scheme=dark]' ); ?>

<?php if( comments_open( get_the_ID() ) ) : ?>
  <?php if ( have_comments() ) : ?>
  <article class="comments">
    <section class="comments-list">
      <div class="title"><h5><?php comments_number( 'No Comments', 'One Comment', '% Comments' ); ?></h5></div>
      <div class="divider"></div>
      <ul><?php wp_list_comments( array( 'style' => 'ul', 'short_ping' => true, 'avatar_size' => 60 ) ); ?></ul>
    </section>
  </article>
  <?php endif; ?>

  <section class="respond">
    <?php comment_form( array( 'label_submit' => __('Add Comment'), 'title_reply' => '', 'logged_in_as' => '', 'comment_notes_before' => '', 'comment_notes_after' => '', 'comment_field' => '<textarea placeholder="Comment" id="comment" name="comment" aria-required="true"></textarea>', 
        'fields' => array(
          'author' => '<p class="comment-form-author">' .
                      '<input id="author" placeholder="Your Name" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
          'email'  => '<p class="comment-form-email">' .
                      '<input id="email" placeholder="Your Email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>'
      ) ) ); 
    ?>
  </section>
<?php endif; ?>