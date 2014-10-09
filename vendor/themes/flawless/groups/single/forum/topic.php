<?php
/**
 * Group - Single Topic
 *
 * If this view has a right sidebar, the "Meta" column is loaded into the sidebar as a widget.
 * Traditional pagination is disabled - pagination elements removed.
 * Admin links removed and relocated to Navbar.
 * Topic tags can be inserted via Sidebar.
 *
 * @version 0.3.4
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */

  global $bp;

  //** Check if this view has a right sidebar.  If it does, we load the Actions into a widget.
  $have_sidebars = flawless_theme::get_current_sidebars( 'right_sidebar' );

  if( $have_sidebars ) {
    $classes[ 'main_column' ][] = '';
    $classes[ 'meta_column' ][] = 'cfct-module';

  } else {
    $classes[ 'main_column' ][] = 'block-75 cfct-block';
    $classes[ 'meta_column' ][] = 'block-25 cfct-block';
  }


?>

<?php do_action( 'bp_before_group_forum_topic' ); ?>

<?php if ( bp_has_forum_topic_posts() ) : ?>

    <h1><?php bp_the_topic_title() ?></h1>

    <div class="<?php echo implode( ' ', (array) $classes[ 'main_column' ] ); ?> cf">

      <?php do_action( 'bp_before_group_forum_topic_posts' ) ?>

      <ul id="topic-post-list" class="item-list cf" role="main">
        <?php while ( bp_forum_topic_posts() ) : bp_the_forum_topic_post(); ?>

          <li id="post-<?php bp_the_topic_post_id() ?>" class="<?php bp_the_topic_post_css_class( ) ?> single_post cf">

            <div class="post-content">
              <?php bp_the_topic_post_content() ?>
            </div>

            <div class="poster-meta">

              <a href="<?php bp_the_topic_post_poster_link() ?>"><?php bp_the_topic_post_poster_avatar( 'width=45&height=45' ) ?></a>
              <span class="time_since"><a href="#post-<?php bp_the_topic_post_id() ?>" title="<?php _e( 'Permanent link to this post', 'buddypress' ) ?>"><?php echo bp_get_the_topic_post_time_since(); ?></a></span>
              <span class="poster_name"><?php echo bp_get_the_topic_post_poster_name(); ?></span>

            </div>

            <div class="admin-links">
              <?php if ( bp_group_is_admin() || bp_group_is_mod() || bp_get_the_topic_post_is_mine() ) : ?>
                <?php bp_the_topic_post_admin_links() ?>
              <?php endif; ?>
              <?php do_action( 'bp_group_forum_post_meta' ); ?>
            </div>
          </li>

        <?php endwhile; ?>
      </ul><!-- #topic-post-list -->

      <?php do_action( 'bp_after_group_forum_topic_posts' ) ?>

      <?php if ( ( is_user_logged_in() && 'public' == bp_get_group_status() ) || bp_group_is_member() ) : ?>

          <?php if ( bp_get_the_topic_is_topic_open() && !bp_group_is_user_banned() ) : ?>

          	<form action="<?php bp_forum_topic_action() ?>" method="post" class="forum-topic-form standard-form cf">

            <div id="post-topic-reply">

              <?php do_action( 'groups_forum_new_reply_before' ) ?>

            <?php if( groups_get_groupmeta( $bp->groups->current_group->id, 'new_topic_entry_intro' ) != '' ) { ?>
              <div class="bb-new-topic-entry-intro"><?php echo nl2br( stripslashes( groups_get_groupmeta( $bp->groups->current_group->id, 'new_topic_entry_intro' ) ) ); ?></div>
            <?php } ?>
                  
              <div class="control-group">
                <div class="controls">
                  <textarea required name="reply_text" id="reply_text" class="input-xxlarge" placeholder="<?php _e( 'Add a reply...', 'flawless' ) ?>"></textarea>

                  <?php if( groups_get_groupmeta( $bp->groups->current_group->id, 'new_topic_entry_help' ) != '' ) { ?>
                  <p class="help-block"><?php echo nl2br( stripslashes( groups_get_groupmeta( $bp->groups->current_group->id, 'new_topic_entry_help' ) ) ); ?></p>
                  <?php } ?>

                </div>
              </div>

              <div class="form-actions submit">
                <input type="submit" name="submit_reply" id="submit" class="btn btn-primary" value="<?php _e( 'Post Reply', 'buddypress' ) ?>" />
              </div>

              <?php do_action( 'groups_forum_new_reply_after' ) ?>

              <?php wp_nonce_field( 'bp_forums_new_reply' ) ?>
            </div>

          </form><!-- .forum-topic-form -->

          <?php elseif ( !bp_group_is_user_banned() ) : ?>

          <div id="message" class="info">
            <p><?php _e( 'This topic is closed, replies are no longer accepted.', 'buddypress' ) ?></p>
          </div>

          <?php endif; ?>

      <?php endif; ?>

    </div>



<?php else: ?>

	<div id="message" class="info">
    <p><?php _e( 'There are no posts for this topic.', 'buddypress' ) ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_group_forum_topic' ) ?>
