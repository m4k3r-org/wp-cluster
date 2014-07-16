<?php
/**
 * Settings page main template
 */
?>
<div class="wrap">
  <h2><?php _e( 'WP-Eventbrite Organizers', $this->get( 'domain' ) ); ?></h2>
  <div class="settings-content">
    <form id="uis_form" action="" method="post" >
      <?php wp_nonce_field( 'ui_settings' ); ?>
      
      <?php submit_button( __( 'Submit' ), 'button' ); ?>
    </form>
  </div>
</div>