<?php

/**
 * BuddyPress Delete Account
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

          <h3><?php _e( 'Delete Account', 'buddypress' ); ?></h3>

          <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account'; ?>" name="account-delete-form" id="account-delete-form" class="standard-form" method="post">

            <div id="message" class="info">
              <p><?php _e( 'WARNING: Deleting your account will completely remove ALL content associated with it. There is no way back, please be careful with this option.', 'buddypress' ); ?></p>
            </div>

            <input type="checkbox" name="delete-account-understand" id="delete-account-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-account-button').disabled = ''; } else { document.getElementById('delete-account-button').disabled = 'disabled'; }" /> <?php _e( 'I understand the consequences of deleting my account.', 'buddypress' ); ?>

            <?php do_action( 'bp_members_delete_account_before_submit' ); ?>

            <div class="submit">
              <input type="submit" disabled="disabled" class="btn danger" value="<?php _e( 'Delete My Account', 'buddypress' ) ?>" id="delete-account-button" name="delete-account-button" />
            </div>

            <?php do_action( 'bp_members_delete_account_after_submit' ); ?>

            <?php wp_nonce_field( 'delete-account' ); ?>
            
          </form>

          <?php do_action( 'bp_after_member_body' ); ?>

        </div><!-- #item-body -->

        <?php do_action( 'bp_after_member_settings_template' ); ?>
          
      </div><!-- .cfct-module  -->
    </div><!-- .main  -->

	 <?php flawless_widget_area('right_sidebar'); ?>

	</div><!-- #content -->  

<?php get_footer( 'buddypress' ) ?>
