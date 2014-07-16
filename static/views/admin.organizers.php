<?php
/**
 * Settings page main template
 */
?>
<div class="wrap">
  <h2><?php _e( 'WP-Eventbrite Organizers', $this->get( 'domain' ) ); ?></h2>
  <?php if( $this->instance->client->ping() && !isset( $_REQUEST[ 'sync' ] ) ) : ?>
    <div class="updated info fade">
      <label>
        <?php _e( 'You do not have Organizers or they are deprecated? Synchronize then.', $this->get( 'domain' ) ); ?> <a class="button" href="<?php echo admin_url( 'admin.php?page=eventbrite_organizers&sync=true' ); ?>"><?php _e( 'Synchronize', $this->get( 'domain' ) ); ?></a>
      </label>
    </div>
  <?php endif; ?>
  <div class="settings-content">
    <form id="uis_form" action="" method="post" >
      <?php wp_nonce_field( 'ui_settings' ); ?>
      
      <?php submit_button( __( 'Submit' ), 'button' ); ?>
    </form>
  </div>
</div>