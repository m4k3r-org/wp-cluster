<?php

/**
 * BuddyPress - Create Group
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php get_header( 'buddypress' ); ?>

	<div class="<?php flawless_wrapper_class(); ?>">
    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

    <form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="form-horizontal" enctype="multipart/form-data">
    	<h1 class="entry-title"><?php _e( 'Create a Group', 'buddypress' ); ?> &nbsp;<a class="btn" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php _e( 'Groups Directory', 'buddypress' ); ?></a></h1>

    	<?php do_action( 'bp_before_create_group' ); ?>

    	<div class="item-list-tabs no-ajax" id="group-create-tabs" role="navigation">
        <ul class="nav nav-pills">
        	<?php bp_group_creation_tabs(); ?>
        </ul>
    	</div>

    	<div class="item-body" id="group-create-body">

        <?php /* Group creation step 1: Basic group details */ ?>
        <?php if ( bp_is_group_creation_step( 'group-details' ) ) : ?>

        	<?php do_action( 'bp_before_group_details_creation_step' ); ?>

          <div class="control-group">
            <label class="control-label" for="group-name"><?php _e( 'Group Name', 'buddypress' ); ?></label>
            <div class="controls">
              <input type="text" required name="group-name" id="group-name" value="<?php bp_new_group_name(); ?>" class="input-xlarge focused" />
                <span class="help-inline conditional-help">
   <span class="tip active">Enter your e-mail address.</span>
   <span class="invalid">Your e-mail does not seem to be valid.</span>
   <span class="blank">You must enter an e-mail address.</span>
   <span class="good">We will e-mail you a confirmation.</span>
  </span>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="group-desc"><?php _e( 'Group Description', 'buddypress' ) ?></label>
            <div class="controls">
              <textarea required name="group-desc" id="group-desc"  class="span7 xxlarge"><?php bp_new_group_description(); ?></textarea>
              <p class="help-block"><?php _e( 'Group description will help to identify the group.', 'buddypress' ) ?></p>
            </div>
          </div>

        	<?php
        	do_action( 'bp_after_group_details_creation_step' );
        	do_action( 'groups_custom_group_fields_editable' ); // @Deprecated

        	wp_nonce_field( 'groups_create_save_group-details' ); ?>

        <?php endif; ?>

        <?php /* Group creation step 2: Group settings */ ?>
        <?php if ( bp_is_group_creation_step( 'group-settings' ) ) : ?>

        	<?php do_action( 'bp_before_group_settings_creation_step' ); ?>

        	<?php if ( bp_is_active( 'forums' ) ) : ?>
            <?php if ( bp_forums_is_installed_correctly() ) : ?>

            <div class="clearfix input checkbox">
              <ul class="inputs-list">
                <li class="clearfix">
                  <label for="group-show-forum">
                    <input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php checked( bp_get_new_group_enable_forum(), true, true ); ?> />
                    <?php _e( 'Enable discussion forum', 'buddypress' ); ?>
                  </label>
                </li>
              </ul>
            </div>

            <?php else : ?>
            	<?php if ( is_super_admin() ) : ?>

              <div class="alert-message block-message warning">
                <?php printf( __( '<strong>Attention Site Admin:</strong> Group forums require the <a href="%s">correct setup and configuration</a> of a bbPress installation.', 'buddypress' ), bp_get_root_domain() . '/wp-admin/admin.php?page=bb-forums-setup' ); ?>
                <input type="hidden" disabled="disabled" name="disabled" id="disabled" value="0" />
              </div>

            	<?php endif; ?>
            <?php endif; ?>
        	<?php endif; ?>

        	<hr />

          <div class="clearfix">
            <label><?php _e( 'Privacy Options', 'buddypress' ); ?></label>
            <div class="input radio">
              <ul class="inputs-list">
                <li class="clearfix">
                <label>
                  <input type="radio" name="group-status" value="public"<?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
                  <span><?php _e( 'This is a public group', 'buddypress' ); ?></span>
                </label>

                <ul class="help-block">
                  <li><?php _e( 'Any site member can join this group.', 'buddypress' ); ?></li>
                  <li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
                  <li><?php _e( 'Group content and activity will be visible to any site member.', 'buddypress' ); ?></li>
                </ul>
                </li>

                <li class="clearfix">
                  <label>
                    <input type="radio" name="group-status" value="private"<?php if ( 'private' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
                    <span><?php _e( 'This is a private group', 'buddypress' ); ?></span>
                  </label>

                  <ul class="help-block">
                    <li><?php _e( 'Only users who request membership and are accepted can join the group.', 'buddypress' ); ?></li>
                    <li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
                    <li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
                  </ul>
              </li>

                <li class="clearfix">
                  <label>
                    <input type="radio" name="group-status" value="hidden"<?php if ( 'hidden' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
                    <span><?php _e('This is a hidden group', 'buddypress'); ?></span>
                  </label>

                  <ul class="help-block">
                    <li><?php _e( 'Only users who are invited can join the group.', 'buddypress' ); ?></li>
                    <li><?php _e( 'This group will not be listed in the groups directory or search results.', 'buddypress' ); ?></li>
                    <li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
                  </ul>
              </li>

              </ul>
            </div>
          </div>

        	<hr />

          <div class="clearfix">
            <label><?php _e( 'Group Invitations', 'buddypress' ); ?></label>
            <div class="input radio">
              <ul class="inputs-list">
                <li>
                  <label>
                    <input type="radio" name="group-invite-status" value="members"<?php bp_group_show_invite_status_setting( 'members' ) ?> />
                    <span><?php _e( 'All group members', 'buddypress' ) ?></span>
                  </label>
                </li>
                <li>
                  <label>
                    <input type="radio" name="group-invite-status" value="mods"<?php bp_group_show_invite_status_setting( 'mods' ) ?> />
                    <span><?php _e( 'Group admins and mods only', 'buddypress' ) ?></span>
                  </label>
                </li>
                <li>
                  <label>
                    <input type="radio" name="group-invite-status" value="admins"<?php bp_group_show_invite_status_setting( 'admins' ) ?> />
                    <span><?php _e( 'Group admins only', 'buddypress' ) ?></span>
                  </label>
                </li>
              </ul>
              <span class="help-block"><?php _e( 'Select which members of this group are allowed to invite others.', 'buddypress' ) ?></span>
            </div>

        	</div>

        	<hr />

        	<?php do_action( 'bp_after_group_settings_creation_step' ); ?>

        	<?php wp_nonce_field( 'groups_create_save_group-settings' ); ?>

        <?php endif; ?>

        <?php /* Group creation step 3: Avatar Uploads */ ?>
        <?php if ( bp_is_group_creation_step( 'group-avatar' ) ) : ?>

        	<?php do_action( 'bp_before_group_avatar_creation_step' ); ?>

        	<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>

            <div class="left-menu">

            	<?php bp_new_group_avatar(); ?>

            </div><!-- .left-menu -->

            <div class="main-column">
            	<p><?php _e( "Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.", 'buddypress' ); ?></p>

                <div class="control-group">
                  <label class="control-label" for="fileInput"><?php _e( 'Upload Image', 'buddypress' ) ?></label>
                  <div class="controls">
                    <input type="file" name="file" id="file" />
                  </div>
                </div>

                <div class="form-actions">
                  <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                  <input type="submit" class="btn btn-primary" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
                </div>


            	<p><?php _e( 'To skip the avatar upload process, hit the "Next Step" button.', 'buddypress' ); ?></p>
            </div><!-- .main-column -->

        	<?php endif; ?>

        	<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

            <h3><?php _e( 'Crop Group Avatar', 'buddypress' ); ?></h3>

            <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ); ?>" />

            <div id="avatar-crop-pane" class="avatar-crop-pane">
            	<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ); ?>" />
            </div>

            <div class="form-actions">
              <input type="submit" name="avatar-crop-submit" class="avatar-crop-submit btn btn-primary" value="<?php _e( 'Crop Image', 'buddypress' ) ?>" />
            </div>

            <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
            <input type="hidden" name="upload" id="upload" />
            <input type="hidden" id="x" name="x" />
            <input type="hidden" id="y" name="y" />
            <input type="hidden" id="w" name="w" />
            <input type="hidden" id="h" name="h" />

        	<?php endif; ?>

        	<?php do_action( 'bp_after_group_avatar_creation_step' ); ?>

        	<?php wp_nonce_field( 'groups_create_save_group-avatar' ); ?>

        <?php endif; ?>

        <?php /* Group creation step 4: Invite friends to group */ ?>
        <?php if ( bp_is_group_creation_step( 'group-invites' ) ) : ?>

        	<?php do_action( 'bp_before_group_invites_creation_step' ); ?>

        	<?php if ( bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

            <div class="left-menu">

            	<div id="invite-list">
                <ul>
                	<?php bp_new_group_invite_friend_list(); ?>
                </ul>

                <?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ); ?>
            	</div>

            </div><!-- .left-menu -->

            <div class="main-column">

            	<div class="alert-message notice">
                <a class="close" href="#">×</a>
                <p><?php _e('Select people to invite from your friends list.', 'buddypress'); ?></p>
            	</div>

            	<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
            	<ul id="friend-list" class="item-list" role="main">

            	<?php if ( bp_group_has_invites() ) : ?>

                <?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

                	<li id="<?php bp_group_invite_item_id(); ?>">

                    <?php bp_group_invite_user_avatar(); ?>

                    <h4><?php bp_group_invite_user_link(); ?></h4>
                    <span class="activity"><?php bp_group_invite_user_last_active(); ?></span>

                    <div class="action">
                    	<a class="remove" href="<?php bp_group_invite_user_remove_invite_url(); ?>" id="<?php bp_group_invite_item_id(); ?>"><?php _e( 'Remove Invite', 'buddypress' ); ?></a>
                    </div>
                	</li>

                <?php endwhile; ?>

                <?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ); ?>

            	<?php endif; ?>

            	</ul>

            </div><!-- .main-column -->

        	<?php else : ?>

            <div class="alert-message info">
              <a class="close" href="#">×</a>
            	<p><?php _e( 'Once you have built up friend connections you will be able to invite others to your group. You can send invites any time in the future by selecting the "Send Invites" option when viewing your new group.', 'buddypress' ); ?></p>
            </div>

        	<?php endif; ?>

        	<?php wp_nonce_field( 'groups_create_save_group-invites' ); ?>

        	<?php do_action( 'bp_after_group_invites_creation_step' ); ?>

        <?php endif; ?>

        <?php do_action( 'groups_custom_create_steps' ); // Allow plugins to add custom group creation steps ?>

        <?php do_action( 'bp_before_group_creation_step_buttons' ); ?>

        <?php if ( 'crop-image' != bp_get_avatar_admin_step() ) : ?>

        	<div class="form-actions" id="previous-next">

            <?php /* Previous Button */ ?>
            <?php if ( !bp_is_first_group_creation_step() ) : ?>

            	<input type="button" value="<?php _e( 'Back to Previous Step', 'buddypress' ); ?>" id="group-creation-previous" name="previous" onclick="location.href='<?php bp_group_creation_previous_link(); ?>'" class="btn" />

            <?php endif; ?>


            <?php /* Next Button */ ?>
            <?php if ( !bp_is_last_group_creation_step() && !bp_is_first_group_creation_step() ) : ?>

            	<input type="submit" value="<?php _e( 'Next Step', 'buddypress' ); ?>" id="group-creation-next" name="save" class="btn btn-primary" />

            <?php endif;?>

            <?php /* Create Button */ ?>
            <?php if ( bp_is_first_group_creation_step() ) : ?>

            	<input type="submit" value="<?php _e( 'Create Group and Continue', 'buddypress' ); ?>" id="group-creation-create" class="btn btn-primary" name="save" />
            <?php endif; ?>

            <?php /* Finish Button */ ?>
            <?php if ( bp_is_last_group_creation_step() ) : ?>

            	<input type="submit" value="<?php _e( 'Finish', 'buddypress' ); ?>" id="group-creation-finish" name="save" class="btn btn-primary" />

            <?php endif; ?>
        	</div>

        <?php endif;?>

        <?php do_action( 'bp_after_group_creation_step_buttons' ); ?>

        <?php /* Don't leave out this hidden field */ ?>
        <input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id(); ?>" />

        <?php do_action( 'bp_directory_groups_content' ); ?>

    	</div><!-- .item-body -->

    	<?php do_action( 'bp_after_create_group' ); ?>

    </form>

    </div><!-- .main  -->

	 <?php flawless_widget_area( 'buddypress' ) ?>

	</div><!-- #content -->


<?php get_footer( 'buddypress' ); ?>
