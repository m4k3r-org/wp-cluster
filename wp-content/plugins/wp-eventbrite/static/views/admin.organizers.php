<?php
/**
 * Settings page main template
 */
?>
<div class="wrap eventbrite-wrap">
  <h2><?php _e( 'Eventbrite Organizers', $this->get( 'domain' ) ); ?> <?php if( $this->instance->client->ping() && !isset( $_REQUEST[ 'sync' ] ) ) : ?><a class="button" href="<?php echo admin_url( 'admin.php?page=eventbrite_organizers&sync=true' ); ?>"><?php _e( 'Synchronize', $this->get( 'domain' ) ); ?></a><?php endif; ?></h2>
  <div class="settings-content">
    <form id="uis_form" action="<?php echo admin_url( 'admin.php?page=eventbrite_organizers' ); ?>" method="post" >
      <?php wp_nonce_field( 'organizers_settings' ); ?>
      <div class="submit-wrapper"><?php submit_button( __( 'Submit' ), 'button' ); ?></div>
      <table class="wp-list-table widefat fixed pages">
        <thead>
          <tr>
            <th scope="col" class="manage-column column-counter">&nbsp;</th>
            <th scope="col" class="manage-column column-title"><?php _e( 'Organizer', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-related_users"><?php _e( 'Related Users', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-overview"><?php _e( 'Information', $this->get( 'domain' ) ); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th scope="col" class="manage-column column-counter">&nbsp;</th>
            <th scope="col" class="manage-column column-title"><?php _e( 'Organizer', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-related_users"><?php _e( 'Related Users', $this->get( 'domain' ) ); ?></th>
            <th scope="col" class="manage-column column-overview"><?php _e( 'Information', $this->get( 'domain' ) ); ?></th>
          </tr>
        </tfoot>
        <tbody id="the-list">
          <?php if( !empty( $organizers ) ) : ?>
            <?php $counter = 1; ?>
            <?php foreach( $organizers as $organizer ) : ?>
              <tr class="type-organizer status-publish">
                <td class="column-counter"><h4><?php echo $counter++; ?>.</h4></td>
                <td class="column-title"><h4><?php echo $organizer->post_title; ?></h4></td>
                <td class="column-related_users"><?php  ?>
                  <ul class="">
                    <?php $is_first = true; ?>
                    <?php foreach( (array)$organizer->related_users as $user_id ) : ?>
                      <li class="related-user-item">
                        <?php if( $user = get_userdata( $user_id ) ) : ?>
                          <input type="hidden" class="select2" name="organizers[<?php echo $organizer->ID ?>][related_users][]" data-title="<?php echo $user->display_name; ?>" data-id="<?php echo $user->ID; ?>" data-login="<?php echo $user->user_login; ?>" value="<?php echo $user->ID ? $user->ID : ''; ?>"/><a href="javascript:;" class="action <?php echo $is_first ? 'add-select2' : 'remove-select2' ?>" data-organizer_id="<?php echo $organizer->ID ?>" ><span class="eb-icon <?php echo $is_first ? 'eb-plus-icon' : 'eb-minus-icon' ?> "></span></a>
                          </li>
                        <?php else : ?>
                          <input type="hidden" class="select2" name="organizers[<?php echo $organizer->ID ?>][related_users][]" data-title="" data-id="" data-login="" value="" /><a href="javascript:;" class="action <?php echo $is_first ? 'add-select2' : 'remove-select2' ?>" data-organizer_id="<?php echo $organizer->ID ?>" ><span class="eb-icon <?php echo $is_first ? 'eb-plus-icon' : 'eb-minus-icon' ?> "></span></a>
                        <?php endif; ?>
                      </li>
                      <?php $is_first = false; ?>
                    <?php endforeach; ?>
                  </ul>
                </td>
                <td class="column-overview">
                  <ul>
                    <li><label><?php _e( 'URL:', $this->get( 'domain' ) ); ?></label><a target="_blank" href="<?php echo $organizer->eventbrite_url; ?>"><?php echo $organizer->eventbrite_url; ?></a></li>
                    <li><label><?php _e( 'Eventbrite ID:', $this->get( 'domain' ) ); ?></label><?php echo $organizer->eventbrite_id; ?></li>
                    <li><label><?php _e( 'Description:', $this->get( 'domain' ) ); ?></label><?php echo wp_trim_words( $organizer->post_content, 30, '...' ); ?></li>
                  </ul>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
          <tr class="type-organizer status-publish alternate">
            <td colspan="4" class="column-title no-data">
              <p><?php _e( 'There are no organizers.', $this->get( 'domain' ) ); ?>
              <?php if( $this->instance->client->ping() ) : ?>
                <?php printf( __( 'Did not synchronize your organizers with Eventbrite yet? <a href="%s">Synchronize</a> them now.', $this->get( 'domain' ) ), admin_url( 'admin.php?page=eventbrite_organizers&sync=true' ) ); ?>
              <?php else : ?>
                <?php printf( __( 'You need to synchronize your organizers with Eventbrite. But before, you have to setup your Eventbrite API credentials on <a href="%s">Settings</a> page.', $this->get( 'domain' ) ), admin_url( 'admin.php?page=eventbrite_settings' ) ); ?>
              <?php endif; ?></p>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="submit-wrapper"><?php submit_button( __( 'Submit' ), 'button' ); ?></div>
    </form>
  </div>
</div>