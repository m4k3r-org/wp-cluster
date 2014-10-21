
<div class="row">

  <?php
  if ( comments_open( get_the_ID() ) ):

    $comments = get_comments(array(
      'status' => 'approve',
      'post_id' => get_the_ID()
    ));

    ?>
    <div class="col-xs-12 col-md-7">
      <div class="comment-list">
        <h4><?php comments_number('No Comments', '1 Comment', '% Comments'); ?></h4>

        <?php for ( $i = 0, $mi = count($comments); $i < $mi; $i++ ): ?>

          <div class="comment-item clearfix">
            <div class="comment-avatar">
              <?php echo get_avatar( $comments[$i]->comment_author_email); ?>
            </div>
            <div class="comment-content-container">
              <div class="comment-content">
                <h5><?php echo $comments[$i]->comment_author; ?></h5>
                <time><?php echo $comments[$i]->comment_date; ?></time>

                <p><?php echo $comments[$i]->comment_content; ?></p>
              </div>
            </div>
          </div>

        <?php endfor; ?>

      </div>
    </div>
  <?php endif; ?>

  <div class="col-xs-12 col-md-5">
    <div class="comment-form">
      <h4>Add Comment</h4>
      <?php

      $commenter = wp_get_current_commenter();
      $req = get_option( 'require_name_email' );
      $aria_req = ( $req ? " aria-required='true'" : '' );

      comment_form(array(


        'label_submit' => __('Submit'),
        'title_reply' => '',
        'title_reply_to' => '',
        'cancel_reply_link' => '',
        'logged_in_as' => '',
        'comment_notes_before' => '',
        'comment_notes_after' => '',
        'comment_field' => '<p><textarea placeholder="Comment" id="comment" name="comment" ' .$aria_req .'></textarea></p>',

        'fields' => array(
          'author' => '<p><input id="author" name="author" type="text" placeholder="Full Name" value="' . esc_attr( $commenter['comment_author'] ) .'" ' .$aria_req .'></p>',
          'email' => '<p><input id="email" name="email" type="text" placeholder="E-mail Address" value="' . esc_attr(  $commenter['comment_author_email'] ) .'" ' .$aria_req .'></p>'
        )
      ));

      ?>
    </div>
  </div>

</div>

