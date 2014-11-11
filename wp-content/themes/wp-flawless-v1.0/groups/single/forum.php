<?php
/**
 * Group Forum - Overview, Edit or single Topic Loader.
 *
 *
 *
 * @version 0.3.4
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

global $bp;

do_action( 'bp_before_group_forum_content' );

if ( bp_is_group_forum_topic_edit() ) {
	locate_template( array( 'groups/single/forum/edit.php' ), true );

} elseif ( bp_is_group_forum_topic() )  {
	locate_template( array( 'groups/single/forum/topic.php' ), true );

} else { ?>

<script type="text/javascript">
  jQuery(document).ready(function() {

    var forum_filter = jQuery('<div class="filter cfct-module single-widget-area"></div>');

    if( jQuery( '.sidebar.cfct-block').length ) {
      jQuery( '.sidebar.cfct-block').prepend( forum_filter );
    }

    jQuery('#forums-dir-list').dynamic_filter({
      settings: {
        per_page: 50
      },
      ajax: {
        args: {
          action: 'bp_get_topics',
          forum_id: '<?php echo groups_get_groupmeta( $bp->groups->current_group->id, 'forum_id' ); ?>'
        },
        url: '<?php echo admin_url('admin-ajax.php'); ?>'
      },
      classes: {
        inputs_list_wrapper: 'inputs_list_wrapper'
      },
      callbacks: {
        result_format: function( data ) {
          return data.results;
        }
      },
      ux: {
        filter: forum_filter,
        results_wrapper: jQuery('<div class="results_wrapper cfct-block"></div>'),
        results: jQuery('<div class="results cfct-module"></div>'),
        status: jQuery('<div class="alert warning"></a>')
      },
      attributes: {
        topic_title: {
          label: 'Topic Title',
          sort_order: 10,
          display: true,
          render_callback: function( default_value, args ) {
            return '<a href="' + args.result_row.topic_link + '">' + default_value + '</a>'
          }
        },
        post_text: {
          label: 'Content',
          sort_order: 20,
          display: true,
          render_callback: function( default_value, args ) {
            return default_value;
          }
        },
        freshness: {
          label: 'Freshness',
          sort_order: 100,
          display: true,
          render_callback: function( default_value, args ) {
            return 'Freshness ' + default_value;
          }
        },
        topic_posts: {
          label: 'Posts',
          sort_order: 50,
          display: false,
          filter: false,
          render_callback: function( default_value, args ) {
            return default_value +  ' Posts';
          }
        },
        topic_type: {
          label: 'Topic Types',
          do_not_collapse: true,
          display: false,
          filter: true,
        }
      }
    });

  });
</script>

<div id="forums-dir-list" class="forums single-forum dir-list dynamic_filter cf"></div>

<?php } ?>

<?php do_action( 'bp_after_group_forum_content' ) ?>

<?php if ( !bp_is_group_forum_topic_edit() && !bp_is_group_forum_topic() ) : ?>

	<?php if ( !bp_group_is_user_banned() && ( ( is_user_logged_in() && 'public' == bp_get_group_status() ) || bp_group_is_member() ) ) : ?>

    <form action="" method="post" id="forum-topic-form" class="form-horizontal">

        <?php do_action( 'bp_before_group_forum_post_new' ) ?>

        <?php if ( bp_groups_auto_join() && !bp_group_is_member() ) : ?>
        	<p><?php _e( 'You will auto join this group when you start a new topic.', 'buddypress' ) ?></p>
        <?php endif; ?>

        <fieldset>
          <legend><?php _e( 'Post a New Topic:', 'buddypress' ) ?></legend>

        <div class="control-group">
          <label><?php _e( 'Title:', 'buddypress' ) ?></label>
          <div class="controls">
            <input type="text" name="topic_title" class="topic_title span4" value="" />
          </div>
        </div>

        <div class="control-group">
          <label><?php _e( 'Content:', 'buddypress' ) ?></label>
          <div class="controls">
            <textarea name="topic_text" class="topic_text span4"></textarea>
          </div>
        </div>

        <div class="control-group">
          <label><?php _e( 'Tags:', 'flawless' ) ?></label>
          <div class="controls">
            <input type="text" name="topic_tags" class="topic_tags span4" value="" />
            <p class="help-block"><?php _e( 'Use commas to separate tags related to your topic.', 'flawless' ) ?></p>
          </div>
        </div>

        <?php do_action( 'bp_after_group_forum_post_new' ) ?>

        <div class="form-actions">
        	<input type="submit" name="submit_topic" class="btn btn-primary submit" value="<?php _e( 'Post Topic', 'buddypress' ) ?>" />
        </div>

        <?php wp_nonce_field( 'bp_forums_new_topic' ) ?>
        </fieldset>

    </form><!-- #forum-topic-form -->

	<?php endif; ?>

<?php endif; ?>

