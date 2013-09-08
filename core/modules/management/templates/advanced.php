<?php
/**
 * Settings Page Section - Help
 *
 * @version 2.0
 * @package WP-Property
 * @author  team@UD
 * @copyright 2010-2012 Usability Dyanmics, Inc.
 */

//** Required globals */
global $wp_properties, $wpdb;
$_backups = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE  'wpp_settings::backup%'" );

?>
<div class="form-table wpp_option_table wpp_setting_interface">
  <div class="ud_table_head">
    <div class="ud_tr">
      <div class="ud_th">
        <h3 class="hidden wpp_section_title"><?php _e( 'Advanced Settings and Developer Tools','wpp'); ?></h3>
        <div class="wpp_section_overview">
          <p><?php _e('In this page, you will be able to change some advanced settings for your installation. Developers will find some helpful tools for troubleshooting and theme development. If you cannot understand the concepts described here, please do not change anything.','wpp'); ?></p>
        </div>
      </div>
    </div>
  </div>
  <div class="ud_tbody">
    <div class="wpp_primary_section">
      <div class="ud_th">
        <strong><?php _e( 'Advanced Settings', 'wpp' ); ?></strong>
        <div class="description"><?php _e( 'Here are some core advanced settings, including settings backup and restore, address revalidation and cache cleaning.', 'wpp' ); ?></div>
      </div>
    </div>
    <div class="wpp_secondary_section">
      <div class="ud_th">
        <strong><?php _e( 'Backup and Restore', 'wpp' ); ?></strong>
      </div>
      <div class="ud_td">
        <ul>
          <li>
            <label><?php _e( 'Upload a backup of WP-Property configuration', 'wpp'); ?>:</label>
            <input name="wpp_settings[settings_from_backup]" type="file"/>
          </li>
          <li>
            <label><?php _e( 'Download current WP-Property configuration', 'wpp');?> (<i><?php echo sanitize_key( 'wpp-' . get_bloginfo( 'name' ) ) . '-' . date( 'y-m-d' ) . '.json';  ?></i>):</label>
              <a class="button" href="<?php echo wp_nonce_url( "edit.php?post_type=property&page=property_settings&wpp_action=download-wpp-backup", 'download-wpp-backup' ); ?>" target="_blank"><?php _e('Download', 'wpp');?></a>
          </li>
          <?php if( $_backups ) : ?>
            <li class="wpp_backups">
              <span><?php _e( 'Restore configuration to an automatically created backup', 'wpp' ); ?>:</span>
              <ul class="ud_button_list clearfix">
              <?php foreach( (array) $_backups as $backup ) : ?>
                <li class="wpp_left">
                  <div class="wpp_button wpp_action wpp-backup-button">
                    <span class="wpp_restore wpp_label" data-href="<?php echo wp_nonce_url( "edit.php?post_type=property&page=property_settings&wpp_action=restore-wpp-backup&backup=" . $backup, 'restore-wpp-backup' ); ?>"><?php echo date( get_option( 'date_format' ), str_replace( 'wpp_settings::backup::', '', $backup ) ); ?></span>
                    <span class="wpp_del wpp_independent wpp_icon wpp_icon_56" data-backup="<?php echo $backup; ?>"></span>
                  </div>
                </li>
              <?php endforeach; ?>
              </ul>
              <div class="clear" ></div>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="wpp_secondary_section">
      <div class="ud_th">
        <strong><?php _e( 'Advanced Actions', 'wpp' ); ?></strong>
      </div>
      <div class="ud_td">
        <ul>
          <li>
            <?php $google_map_localizations = WPP_F::draw_localization_dropdown( 'return_array=true' ); ?>
            <label>
              <?php _e( 'Revalidate all addresses using', 'wpp' ); ?> <b><?php echo $google_map_localizations[$wp_properties[ 'configuration' ][ 'google_maps_localization' ]]; ?></b> <?php _e( 'localization', 'wpp' ); ?>.
            </label>
            <input type="button" value="<?php _e('Revalidate','wpp');?>" id="wpp_ajax_revalidate_all_addresses" class="button wpp_input_button" />
            <?php if (!WPP_F::available_address_validation()): ?>
              <span class="address_validation_unavailable"><?php _e( 'Be aware, Google\'s Geocoding Service right now is unavailable because query limit was exceeded. Try again later.', 'wpp' ); ?></span>
            <?php endif; ?>
          </li>
          <li>
            <label>
              <?php _e( 'Clear WP-Property Cache.','wpp' ) ?>
            </label>
            <input type="button" value="<?php _e( 'Clear Cache','wpp' ) ?>" id="wpp_clear_cache" class="button wpp_input_button" />
            <span class="wpp_help wpp_button alignnone">
              <span class="wpp_icon wpp_icon_106"></span>
              <div class="wpp_description"><?php _e( 'Some shortcodes and widgets use cache, so the good practice is clear it after widget, shortcode changes.', 'wpp');?></div>
            </span>
          </li>
        </ul>
      </div>
    </div>
    <div class="wpp_primary_section">
      <div class="ud_th">
        <strong><?php _e( 'Developer Tools', 'wpp' ); ?></strong>
        <div class="description"><?php _e( 'Here are some tools that developers will appreciate when troubleshooting.','wpp' ) ?></div>
      </div>
    </div>
    <div class="wpp_secondary_section">
      <div class="ud_th">
        <strong></strong>
      </div>
      <div class="ud_td">
        <ul>
          <li>
            <input type="checkbox" data-bind="checked: $root.wpp_model.developer_mode, attr: { name: 'wpp_settings[configuration][developer_mode]' }" value='true' id='wpp_settings[configuration][developer_mode]' />
            <label for="wpp_settings[configuration][developer_mode]" data-bind="text: $root.strings.developer_mode_label"></label>
            <span class="wpp_help wpp_button alignnone">
              <span class="wpp_icon wpp_icon_106"></span>
              <div class="wpp_description"><?php _e( 'Enables certain advanced functionality. If you are using Google Chrome or have Firefox Firebug, you will see debugging information in the browser console log.','wpp' ); ?></div>
            </span>
          </li>
        </ul>
      </div>
    </div>
    <div class="wpp_secondary_section">

      <div class="ud_th">
        <strong><?php _e( 'Developer Settings', 'wpp' ); ?></strong>
        <div class="description"><p></p></div>
      </div>
      <div class="ud_td">
        <ul>


        <li data-bind="visible: $root.wpp_model.developer_mode()">
          <input type="checkbox" data-bind="checked: !$root.wpp_model.developer_mode() ? false : $root.wpp_model.show_ud_log, attr: { name: 'wpp_settings[configuration][show_ud_log]' }" value='true' id='wpp_settings[configuration][show_ud_log]' />
          <label for="wpp_settings[configuration][show_ud_log]" data-bind="text: $root.strings.show_ud_log_label"></label>
          <span class="wpp_help wpp_button alignnone">
            <span class="wpp_icon wpp_icon_106"></span>
            <div class="wpp_description"><?php _e( 'The log is always active, but the UI is hidden.  If enabled, it will be visible in the admin sidebar.','wpp' ); ?></div>
          </span>
        </li>

        <li data-bind="visible: $root.wpp_model.developer_mode()">
          <input type="checkbox" data-bind="checked: !$root.wpp_model.developer_mode() ? false : $root.wpp_model.disable_automatic_feature_update, attr: { name: 'wpp_settings[configuration][disable_automatic_feature_update]' }" value='true' id='wpp_settings[configuration][disable_automatic_feature_update]' />
          <label for="wpp_settings[configuration][disable_automatic_feature_update]" data-bind="text: $root.strings.disable_automatic_feature_update_label"></label>
          <span class="wpp_help wpp_button alignnone">
            <span class="wpp_icon wpp_icon_106"></span>
            <div class="wpp_description"><?php _e( 'If disabled, feature updates will not be downloaded automatically.','wpp' ); ?></div>
          </span>
        </li>

        <li>
          <input type="checkbox" data-bind="checked: $root.wpp_model.build_mode, attr: { name: 'wpp_settings[configuration][build_mode]' }" value='true' id='wpp_settings[configuration][build_mode]' />
          <label for="wpp_settings[configuration][build_mode]" data-bind="text: $root.strings.enable_build_mode"></label>
          <span class="wpp_help wpp_button alignnone">
            <span class="wpp_icon wpp_icon_106"></span>
            <div class="wpp_description"><?php _e( 'When enabled, all specific assets (css, js) will be recompiled every time when data changes. Also it turns off transient usage in different places.','wpp' ); ?></div>
          </span>
        </li>

        <li>
          <input type="checkbox" data-bind="checked: $root.wpp_model.disable_wordpress_postmeta_cache, attr: { name: 'wpp_settings[configuration][disable_wordpress_postmeta_cache]' }" value='true' id='wpp_settings[configuration][disable_wordpress_postmeta_cache]' />
          <label for="wpp_settings[configuration][disable_wordpress_postmeta_cache]" data-bind="text: $root.strings.disable_wordpress_postmeta_cache_label"></label>
          <span class="wpp_help wpp_button alignnone">
            <span class="wpp_icon wpp_icon_106"></span>
            <div class="wpp_description"><?php _e( 'This may solve "out of memory" issues if you have a lot of listings.','wpp' ); ?></div>
          </span>
        </li>

        <li>
          <input type="checkbox" data-bind="checked: $root.wpp_model.disable_legacy_detailed, attr: { name: 'wpp_settings[configuration][disable_legacy_detailed]' }" value='true' id='wpp_settings[configuration][disable_legacy_detailed]' />
          <label for="wpp_settings[configuration][disable_legacy_detailed]" data-bind="text: $root.strings.disable_legacy_detailed_label"></label>
          <span class="wpp_help wpp_button alignnone">
            <span class="wpp_icon wpp_icon_106"></span>
            <div class="wpp_description"><?php printf( __( 'If not checked then it copies %1$s Meta attributes into $wp_properties[\'property_meta\'] for theme support, if needed.', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ); ?></div>
          </span>
        </li>

        <li data-bind="visible: $root.wpp_model.developer_mode()">
          <input type="checkbox" data-bind="checked: !$root.wpp_model.developer_mode() ? false : $root.wpp_model.do_not_automatically_regenerate_thumbnails, attr: { name: 'wpp_settings[configuration][do_not_automatically_regenerate_thumbnails]' }" value='true' id='wpp_settings[configuration][do_not_automatically_regenerate_thumbnails]' />
          <label for="wpp_settings[configuration][do_not_automatically_regenerate_thumbnails]" data-bind="text: $root.strings.do_not_automatically_regenerate_thumbnails_label"></label>
        </li>

        <li data-bind="visible: $root.wpp_model.developer_mode()">
          <input type="checkbox" data-bind="checked: !$root.wpp_model.developer_mode() ? false : $root.wpp_model.load_scripts_everywhere, attr: { name: 'wpp_settings[configuration][load_scripts_everywhere]' }" value='true' id='wpp_settings[configuration][load_scripts_everywhere]' />
          <label for="wpp_settings[configuration][load_scripts_everywhere]" data-bind="text: $root.strings.load_scripts_everywhere_label"></label>
        </li>

        <?php if( WPP_F::has_theme_specific_stylesheet() ) : ?>
        <li data-bind="visible: $root.wpp_model.developer_mode()">
          <input type="checkbox" data-bind="checked: !$root.wpp_model.developer_mode() ? false : $root.wpp_model.do_not_load_theme_specific_css, attr: { name: 'wpp_settings[configuration][do_not_load_theme_specific_css]' }" value='true' id='wpp_settings[configuration][do_not_load_theme_specific_css]' />
          <label for="wpp_settings[configuration][do_not_load_theme_specific_css]" data-bind="text: $root.strings.do_not_load_theme_specific_css_label"></label>
          <span class="wpp_help wpp_button alignnone">
            <span class="wpp_icon wpp_icon_106"></span>
            <div class="wpp_description"><?php _e( 'This version of WP-Property has a stylesheet made specifically for the theme you are using.', 'wpp' ); ?></div>
          </span>
        </li>
        <?php endif; ?>


      </ul>

    </div>
  </div>

  <div class="wpp_secondary_section" data-bind="visible: true || $root.saas.connected">
    <div class="ud_th">
      <strong><?php _e( 'Developer Keys', 'wpp' ); ?></strong>
      <div class="description"><?php _e( 'Keys for advanced functionality with the Usability Dynamics API', 'wpp' ); ?></div>
    </div>
    <div class="ud_td">
      <ul class="clearfix">

        <li class="clearfix">
          <span class="wpp_label" data-bind="text: $root.strings.api_key"></span>
          <div class="wpp_input_wrapper">
            <input class="wpp_major wpp_api_key" type="text" name="wp_options[ud::api_key]" data-bind="value: $root.wpp_model.api_key" value="<?php echo esc_attr( get_option( 'ud::api_key' ) ); ?>" maxlength="20" readonly="true"/>
            <span class="wpp_input_button" data-bind="click: $root.generate.api_key"><?php _e( 'Generate' ); ?></span>
          </div>
          <span data-bind="help: $root.strings.help_api_key" class="alignnone" style="margin-left:5px; margin-bottom:5px;" />
        </li>
        <li class="clearfix">
          <span class="wpp_label" data-bind="text: $root.strings.site_uid"></span>
          <div class="wpp_input_wrapper">
            <input class="wpp_major wpp_api_key" type="text" name="wp_options[ud::site_uid]" data-bind="value: $root.wpp_model.site_uid" />
            <span class="wpp_input_button" data-bind="click: $root.generate.site_uid"><?php _e( 'Generate' ); ?></span>
          </div>
          <span data-bind="help: $root.strings.help_site_uid" style="margin-left:5px; margin-bottom:5px;" />
        </li>
        <li class="clearfix">
          <span class="wpp_label" data-bind="text: $root.strings.public_key"></span>
          <div class="wpp_input_wrapper">
            <input class="wpp_major wpp_api_key" type="text" name="wp_options[ud::public_key]" data-bind="value: $root.wpp_model.public_key" />
            <span class="wpp_input_button" data-bind="click: $root.generate.public_key"><?php _e( 'Generate' ); ?></span>
          </div>
          <span data-bind="help: $root.strings.help_site_uid" style="margin-left:5px; margin-bottom:5px;" />
        </li>
      </ul>
    </div>
  </div>

  </div>
</div>

<?php if( ( is_multisite() && is_super_admin() ) || ( !is_multisite() && is_admin() ) ) : ?>
<div class="wpp_inner_tab">

  <div class="wpp_settings_block hidden"><?php echo sprintf(__( 'Get %1$s image data.','wpp' ), WPP_F::property_label('singular')); ?>
    <label for="wpp_image_id"><?php echo sprintf(__( '%1$s ID:','wpp' ), ucfirst(WPP_F::property_label('singular'))); ?></label>
    <input type="text" id="wpp_image_id" />
    <input type="button" value="<?php _e( 'Lookup','wpp' ) ?>" id="wpp_ajax_image_query" class="button wpp_input_button" />
    <span id="wpp_ajax_image_query_cancel" class="wpp_link hidden"><?php _e( 'Cancel','wpp' ) ?></span>
    <pre id="wpp_ajax_image_result" class="wpp_class_pre hidden"></pre>
  </div>

  <?php if( $wp_properties[ '_api_routes' ][ '_api_path' ] ) { /* Should almost never happen except for in development environments */ ?>
  <div class="wpp_settings_block">
    <?php _e( 'To view WP-Property API functionality', 'wpp' ) ?>
    <a class="button" href="<?php echo trailingslashit( $wp_properties[ '_api_routes' ][ '_api_path' ] ). 'explorer/'; ?>" target="_blank"><?php _e( 'Open API Explorer' ); ?></a>
  </div>
  <?php } ?>

  <?php do_action( 'wpp_settings_help_tab' ); ?>
</div>
<?php endif; ?>
