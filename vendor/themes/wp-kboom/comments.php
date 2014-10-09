<?php
/*
The comments page for Site5 Framework
*/

// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) { ?>
<div class="help">
    <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','site5framework'); ?></p>
</div>
<?php
    return;
}
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>

<h4 id="comments"><?php comments_number('<span>No</span> Comments', '<span>One</span> Comment', '<span>%</span> Comments' );?></h4>

<nav class="comment-nav">
    <ul>
        <li><?php previous_comments_link() ?></li>
        <li><?php next_comments_link() ?></li>
    </ul>
</nav>

<ol class="commentlist">
    <?php wp_list_comments('type=comment&callback=site5framework_comments'); ?>
</ol>

<nav class="comment-nav">
    <ul>
        <li><?php previous_comments_link() ?></li>
        <li><?php next_comments_link() ?></li>
    </ul>
</nav>

<?php else : // this is displayed if there are no comments so far ?>

<?php if ( comments_open() ) : ?>
    <!-- If comments are open, but there are no comments. -->

    <?php else : // comments are closed ?>
    <!-- If comments are closed. -->
    <p class="nocomments"><?php _e('Comments are closed.','site5framework'); ?></p>

    <?php endif; ?>

<?php endif; ?>

<!-- Comment Form -->
    <?php if ( comments_open() )  ?>
    <h4 id="comments-respond"><?php _e('Leave A Reply','site5framework') ?></h4>
    <?php
        global $aria_req, $am_validate; $am_validate = true;
        $commenter = wp_get_current_commenter();
        $comment_args = array( 'fields' => apply_filters( 'comment_form_default_fields', array(

            'author' => '<p class="one-fourth">' .
                '<label for="author">' . __( 'Your name:', 'site5framework' ) . '</label> ' .

                '<input id="author" name="author" type="text" value="' .
                esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' class="required" />' .
                '</p>',
            'email'  => '<p class="one-fourth">' .
                '<label for="email">' . __( 'Your email:', 'site5framework' ) . '</label> ' .

                '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' class="required" />' .
                '</p>',
            'url'    => '<p class="one-fourth last">' .
                '<label for="url">' . __( 'Your website:', 'site5framework' ) . '</label> ' .
                '<input id="url" name="url" type="text" value="' . esc_attr(  $commenter['comment_author_url'] ) . '" size="30"' . $aria_req . ' />' .
                '</p>' ) ),
            'comment_field' => '<p class="comment-form-comment">' .
                '<label for="comment">' . __( 'Comment:', 'site5framework' ) . '</label>' .
                '<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" class="required"></textarea>' .
                '</p>'.
                '<p class=comment-form-submit>'.
                '<button type="submit" class="button small steel_blue round" style="float: left;"><span>'. __( 'Post Comment', 'site5framework' ) .'</span></button>',
            'comment_notes_after' => '',
            'comment_notes_before' => ''
        );

    comment_form($comment_args, $post->ID);
?>
