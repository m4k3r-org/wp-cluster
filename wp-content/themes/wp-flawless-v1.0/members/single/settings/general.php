<?php

/**
 * BuddyPress Member Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */ 
?>

<?php get_header( 'buddypress' ); ?>

	<div class="<?php flawless_wrapper_class(); ?>">
  
    <?php flawless_widget_area('left_sidebar'); ?>
  
    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    
      <div class="<?php flawless_module_class( '' ); ?>">

        <?php do_action( 'bp_before_member_settings_template' ); ?>

        <div id="item-header" class="item-header">

          <?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

        </div><!-- #item-header -->

        <?php Flawless_BuddyPress::render_navigation(); ?>

        <div class="item-body clearfix">

          <?php do_action( 'bp_before_member_body' ); ?>

          <h3><?php _e( 'General Settings', 'buddypress' ); ?></h3>

          <?php do_action( 'bp_template_content' ) ?>

          <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">

            <label for="pwd"><?php _e( 'Current Password <span>(required to update email or change current password)</span>', 'buddypress' ); ?></label>
            <input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" /> &nbsp;<a href="<?php echo site_url( add_query_arg( array( 'action' => 'lostpassword' ), 'wp-login.php' ), 'login' ); ?>" title="<?php _e( 'Password Lost and Found', 'buddypress' ); ?>"><?php _e( 'Lost your password?', 'buddypress' ); ?></a>

            <label for="email"><?php _e( 'Account Email', 'buddypress' ); ?></label>
            <input type="text" name="email" id="email" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" />

            <label for="pass1"><?php _e( 'Change Password <span>(leave blank for no change)</span>', 'buddypress' ); ?></label>
            <input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small" /> &nbsp;<?php _e( 'New Password', 'buddypress' ); ?><br />
            <input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small" /> &nbsp;<?php _e( 'Repeat New Password', 'buddypress' ); ?>

            <?php do_action( 'bp_core_general_settings_before_submit' ); ?>

            <div class="submit">
              <input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
            </div>

            <?php do_action( 'bp_core_general_settings_after_submit' ); ?>

            <?php wp_nonce_field( 'bp_settings_general' ); ?>

          </form>

          <?php do_action( 'bp_after_member_body' ); ?>

        </div><!-- #item-body -->

        <?php do_action( 'bp_after_member_settings_template' ); ?>
        
      </div><!-- .cfct-module  -->
    </div><!-- .main  -->

	 <?php flawless_widget_area('right_sidebar'); ?>

	</div><!-- #content -->
  

<?php get_footer( 'buddypress' ) ?>
