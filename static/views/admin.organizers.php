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
        <?php _e( 'You do not have organizers or they are deprecated? Synchronize then.', $this->get( 'domain' ) ); ?> <a class="button" href="<?php echo admin_url( 'admin.php?page=eventbrite_organizers&sync=true' ); ?>"><?php _e( 'Synchronize', $this->get( 'domain' ) ); ?></a>
      </label>
    </div>
  <?php endif; ?>
  <div class="settings-content">
    <form id="uis_form" action="" method="post" >
      <?php wp_nonce_field( 'organizers_settings' ); ?>
      <table class="wp-list-table widefat fixed pages">
        <thead>
          <tr>
            <th scope="col" class="manage-column column-counter">&nbsp;</th>
            <th scope="col" class="manage-column column-title"><?php _e( 'Organizer', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-overview"><?php _e( 'Information', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-related_users"><?php _e( 'Related Users', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-excerpt"><?php _e( 'Description', $this->get( 'domain' ) ); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th scope="col" class="manage-column column-counter">&nbsp;</th>
            <th scope="col" class="manage-column column-title"><?php _e( 'Organizer', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-overview"><?php _e( 'Information', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-related_users"><?php _e( 'Related Users', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-excerpt"><?php _e( 'Description', $this->get( 'domain' ) ); ?></th>
          </tr>
        </tfoot>
        <tbody id="the-list">
          <?php if( !empty( $organizers ) ) : ?>
            <?php $counter = 1; ?>
            <?php foreach( $organizers as $organizer ) : ?>
              <tr class="type-organizer status-publish">
                <td class="column-counter"><h4><?php echo $counter++; ?>.</h4></td>
                <td class="column-title"><h4><?php echo $organizer->post_title; ?></h4></td>
                <td class="column-overview"><?php  ?></td>
                <td class="column-related_users"><?php  ?>
                  <ul class="">
                    <?php foreach( (array)$organizer->related_users as $user_id ) : ?>
                      <?php $user = get_userdata( $user_id ); ?>
                      <li class="related-user-item">
                        <input type="hidden" class="select2" name="organizers[<?php echo $organizer->ID ?>][related_users][]" data-title="<?php echo $user->display_name; ?>" data-id="<?php echo $user->ID; ?>" data-login="<?php echo $user->user_login; ?>" value="<?php echo $user->ID; ?>"/>
                        <a href="javascript:;" class="add-select2" data-organizer_id="<?php echo $organizer->ID ?>" ><span class="eb-plus-icon"></span></a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </td>
                <td class="column-excerpt"><?php echo wp_trim_words( $organizer->post_content, 30, '...' ); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
          <tr colspan="4" class="type-organizer status-publish alternate">
            <td class="column-title"><?php _e( 'Organizer', $this->get( 'domain' ) ); ?></td>
          </tr>
          <?php endif; ?>
        </tbody>
</table>
      
      
      <?php submit_button( __( 'Submit' ), 'button' ); ?>
    </form>
  </div>
</div>