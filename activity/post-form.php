<?php

/**
 * BuddyPress - Activity Post Form
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<div class="primary-item">
  <form action="<?php bp_activity_post_form_action(); ?>" method="post" class="whats-new-form" name="whats-new-form" role="complementary">

  <?php do_action( 'bp_before_activity_post_form' ); ?>
  
  
    <div class="item-avatar whats-new-avatar">
      <a href="<?php echo bp_loggedin_user_domain(); ?>">
        <?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
      </a>
    </div>

    <div class="item-content whats-new-content">
      
      <h5 class="item-title"><?php if ( bp_is_group() )
          printf( __( "What's new in %s, %s?", 'buddypress' ), bp_get_group_name(), bp_get_user_firstname() );
        else
          printf( __( "What's new, %s?", 'buddypress' ), bp_get_user_firstname() );
      ?></h5>

      
      <div class="control-group whats-new-textarea">
        <div class="controls">
          <textarea name="whats-new" id="whats-new" class="input-xxlarge"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_attr( $_GET['r'] ); ?> <?php endif; ?></textarea>
        </div>
      </div>

      <div class="whats-new-options">

        <?php if ( bp_is_active( 'groups' ) && !bp_is_my_profile() && !bp_is_group() ) : ?>

          <div class="control-group whats-new-post-in-box">

            <label class="control-label"><?php _e( 'Post in', 'buddypress' ) ?>:</label>

            <div class="controls">
              <select id="whats-new-post-in" name="whats-new-post-in">
                <option selected="selected" value="0"><?php _e( 'My Profile', 'buddypress' ); ?></option>

                <?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0' ) ) :
                  while ( bp_groups() ) : bp_the_group(); ?>

                    <option value="<?php bp_group_id(); ?>"><?php bp_group_name(); ?></option>

                  <?php endwhile;
                endif; ?>

              </select>
            </div>
          </div>
          
          <input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />

        <?php elseif ( bp_is_group_home() ) : ?>

          <input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
          <input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />

        <?php endif; ?>
        
        <div class="form-actions whats-new-submit">
          <input type="submit" name="aw-whats-new-submit" class="aw-whats-new-submit" value="<?php _e( 'Post Update', 'buddypress' ); ?>" />
        </div>        

        <?php do_action( 'bp_activity_post_form_options' ); ?>

      </div><!-- #whats-new-options -->
    </div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'bp_after_activity_post_form' ); ?>

  </form><!-- #whats-new-form -->
</div>
