<?php
/**
 * UI for Dshboard Page
 *
 * Pending refractoring.
 *
 */
?>
<div class="wrap ud-admin-wrap hdd_manage_options">
  <h2 class="manage_title"><?php _e( 'Site Management', HDDP ); ?>
    <?php if( $_GET[ 'message' ] == 'updated' ) { ?><span class="hddp_update">Saved...</span> <?php } ?>
  </h2>

  <h2 class="ud-tabs nav-tab-wrapper" tab_target=".ud-tab">
    <a href="#update_log" class="nav-tab nav-tab-active">Updates</a><a href="#settings" class="nav-tab">UD Cloud</a><a href="#advanced" class="nav-tab">Advanced</a>
  </h2>

  <div id="update_log" class="changelog point-releases ud-tab">
    <?php foreach( $ud_log as $entry ) { ?>
      <p>
        <strong><?php echo date( get_option( 'time_format' ) . ', ' . get_option( 'date_format' ), $entry[ 'time' ] ); ?></strong> <?php echo $entry[ 'message' ]; ?>
      </p>
    <?php } ?>
  </div>

  <div id="settings" class="changelog point-releases ud-tab hidden">

      <table class="form-table">

        <tr>
          <th style="padding:0;">
            <ul id="cloud_actions" class="ud_json_editor_sidemenu">
              <li data-cloud-document-url="api/v1/settings" data-cloud-document-type="settings">Account Settings</li>
              <li data-cloud-document-url="api/v1/documents/hdp_event" data-cloud-document-type="keys">Events</li>
              <li data-cloud-document-url="account/access-keys" data-cloud-document-type="keys">Keys</li>
            </ul>
          </th>

          <td class="ud_json_container" style="padding: 0;" valign="top">
            <div id="editor_jsoneditor" class="ud_json_editor"></div>
          </td>

        </tr>

      </table>
  </div>

  <div id="advanced" class="changelog point-releases ud-tab hidden">
    <form action="<?php admin_url( 'index.php?page=hddp_manage' ); ?>" method="POST">
      <input type="hidden" name="hddp_options[update]" value="true" />
      <?php wp_nonce_field( 'hddp_save_form', 'hddp_save_form' ); ?>
      <table class="form-table">

        <tr class="hddp_advanced">
          <th>Site UID</th>
          <td>
            <label>
              <input type="text" name="_options[ud::site_uid]" value="<?php echo get_option( 'ud::site_uid' ); ?>" class="regular-text ud_key" />
            </label>
          </td>
        </tr>

        <tr>
          <th>Cloud Account ID</th>
          <td>
            <label>
              <input type="text" name="_options[ud::cloud::account-id]" value="<?php echo get_option( 'ud::cloud::account-id' ); ?>" class="regular-text ud_key ud_cloud_id" autocomplete="off" />
            </label>
          </td>
        </tr>

        <tr>
          <th>Cloud Access Key</th>
          <td>
            <label>
              <input type="text" name="_options[ud::cloud::access-key]" value="<?php echo get_option( 'ud::cloud::access-key' ); ?>" class="regular-text ud_key ud_cloud_key" autocomplete="off" />
            </label>
          </td>
        </tr>

        <tr class="hddp_advanced">
          <th>Customer Key</th>
          <td>
            <input type="text" name="_options[ud::customer_key]" value="<?php echo get_option( 'ud::customer_key' ); ?>" class="regular-text" />
            <p>This is private key that should not be shared with others. This key is necessary if the current site is on a closed network and fails site verification.</p>
          </td>
        </tr>

        <tr class="hddp_advanced">
          <th>Public Key</th>
          <td>
            <input type="text" name="_options[ud::public_key]" value="<?php echo get_option( 'ud::public_key' ); ?>" class="regular-text" />
            <p>This key is automatically provided by the UD API once the site owner has been verified. This key will only work on the IP addresses apporved by the owner.</p>
          </td>
        </tr>

        <tr>
          <th>Administrator Actions</th>
          <td>
            <ul class="ud-options-list">

              <li class="hddp_advanced">
                <a class="button" href="<?php echo admin_url( 'index.php?page=hddp_manage&request=clear_event_log' ); ?>">Clear Event Log</a>
                <span class="description"></span>
              </li>

              <li>
                <a class="button" href="<?php echo admin_url( 'index.php?page=hddp_manage&request=synchronize_with_cloud' ); ?>">Batch Synchronization with UD Cloud API</a>
                <span class="description">Update the following Post Types: <?php echo implode( ', ', (array) $hddp[ 'dynamic_filter_post_types' ] ); ?></span>
              </li>

              <li class="hddp_advanced">
                <a class="button" href="<?php echo admin_url( 'index.php?page=hddp_manage&request=synchronize_config' ); ?>">Update Settings on UD Cloud API</a>
                <span class="description">Update the following Post Types: <?php echo implode( ', ', (array) $hddp[ 'dynamic_filter_post_types' ] ); ?></span>
              </li>

            </ul>
          </td>
        </tr>

        <tr>
          <th>Developer Actions</th>
          <td>
            <ul class="ud-options-list">

              <li class="hddp_advanced">
                <a class="button" href="<?php echo admin_url( 'index.php?page=hddp_manage&request=update_qa_all_tables' ); ?>">Rebuild Quick Access Tables</a>
                <span class="description">Update Quick Access tables for the following Post Types: <?php echo implode( ', ', (array) $hddp[ 'dynamic_filter_post_types' ] ); ?></span>
              </li>

              <li>
                <a class="button" href="<?php echo admin_url( 'index.php?page=hddp_manage&request=update_lat_long' ); ?>">Update Lat/Long</a>
                <span class="description">Update Latitude and Longitude for all Events.</span>
              </li>

              <li class="hddp_advanced">
                <a class="button" href="<?php echo admin_url( 'index.php?page=hddp_manage&request=delete_hddp_options' ); ?>">Delete HDDP Options</a>
                <span class="description">Will delete all HDDP-Theme options. This can not be undone.</span>
              </li>

            </ul>
          </td>
        </tr>

        <tr>
          <th>Debugging</th>
          <td>
            <pre class="ud_pre"><?php print_r( $hddp ); ?></pre>
          </td>
        </tr>

        <tr>
          <td>
            <input type="submit" class="button-primary" value="Save Settings" />
          </td>
          <td></td>
        </tr>

      </table>
    </form>
  </div>

</div>