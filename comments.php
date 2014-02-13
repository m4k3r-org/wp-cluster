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
<<<<<<< HEAD:comments.php
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
=======
  <article class="comments">
    <?php if ( have_comments() ) : ?>
      <!-- Comment section -->
      <section class="comments-list">
        <div class="title"><h5><?php comments_number( 'No Comments', 'One Comment', '% Comments' ); ?></h5></div>
        <ul>
          <?php wp_list_comments( array( 'style' => 'ul', 'short_ping' => true, 'avatar_size' => 64 ) ); ?>
        </ul>
      </section>
    <?php endif; // have_comments() ?>
    <!-- Comment posting -->
    <section class="respond well">
      <?php comment_form( array( 'comment_notes_after' => '' ) ); ?>
    </section>
  </article>
>>>>>>> 0be1abb53ddb6aad334724579097e76f1ac8892e:templates/article/comments.php
<?php endif; ?>