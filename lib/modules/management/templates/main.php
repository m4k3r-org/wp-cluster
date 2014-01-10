<?php
/**
 * Settings Page Section - Main Options
 *
 * @version 2.0
 * @package WP-Property
 * @author  team@UD
 * @copyright 2010-2012 Usability Dynamics, Inc.
 */

//** Required globals */
global $wp_properties;

?>
<div class="form-table wpp_option_table wpp_setting_interface">

  <div class="ud_table_head">
    <div class="ud_tr" data-bind="with: $root.wpp_model">
      <div class="ud_th">
         <h3 class="hidden wpp_section_title"><?php _e( 'Main', 'wpp' ); ?></h3>
         <div class="wpp_section_overview"><p><?php _e( 'In this page there are the core settings to start building your website with WP-Property. If you have purchased the Power Tools premium feature, White Label functionality will appear on the bottom of this page.', 'wpp' ); ?></p></div>
        <div class="wpp_section_overview" data-bind="visible: $data.splash"><div class="wpxi_contextual wpp_large_guide" data-bind="html: $data.splash"></div></div>
      </div>
    </div>
  </div>

  <div class="ud_tbody">

  <?php if( WPP_F::is_saas_cap_available() ) : ?>
  <div class="wpp_primary_section" data-bind="visible: $root.saas.connected">
    <div class="ud_th">
      <strong><?php _e( 'Client License Settings','wpp' ) ?></strong>
      <div class="description"><?php _e( 'To have access to your Premium Features you need to set your customer key below.','wpp' ) ?></div>
    </div>
  </div>

  <div class="wpp_secondary_section" data-bind="visible: $root.saas.connected && $root.wpp_model.customer_key != ''">
    <div class="ud_th"><strong><?php _e( 'Customer Name','wpp' ); ?></strong></div>
    <div class="ud_td">
      <ul>
        <li data-bind="visible: $root.wpp_model.customer_name_error" ><em data-bind="text: $root.wpp_model.customer_name_error" /></li>
        <li data-bind="visible: $root.wpp_model.customer_name" ><em class="wpp_updated" data-bind="text: $root.wpp_model.customer_name" /></li>
      </ul>
    </div>
  </div>

  <div class="wpp_secondary_section" data-bind="visible: true || $root.saas.connected">
    <div class="ud_th"><strong data-bind="text: $root.strings.customer_key"></strong></div>
    <div class="ud_td">
      <ul>
        <li class="clearfix">
          <input class="wpp_api_key regular-text" type="text" name="wp_options[ud::customer_key]" data-bind="value: $root.wpp_model.customer_key, valueUpdate: 'keyup' " value="<?php echo esc_attr( get_option( 'ud::customer_key' ) ); ?>" maxlength="20" />
          <span data-bind="help: $root.strings.help_customer_key" style="margin-left:5px; margin-bottom:5px;" />
        </li>
      </ul>
    </div>
  </div>
  <?php endif; ?>

  <div class="wpp_primary_section">
    <div class="ud_th">
      <strong><?php _e( 'General Options','wpp' ) ?></strong>
      <div class="description"><?php _e( 'In this section you will be able to set the most basic WP-Property settings that apply to almost everything.','wpp' ) ?></div>
    </div>
  </div>

  <div class="wpp_secondary_section">
    <div class="ud_th">
      <strong><?php _e( 'Basic Settings', 'wpp' ); ?></strong>
      <div class="description"></div>
    </div>
    <div class="ud_td">
      <ul>
        <li><?php echo WPP_F::checkbox( "name=wpp_settings[configuration][include_in_regular_search_results]&label=" . sprintf( __( 'Include %1s in regular search results.', 'wpp' ), strtolower( WPP_F::property_label( 'plural' ) ) ) , $wp_properties[ 'configuration' ][ 'include_in_regular_search_results' ] ); ?></li>
        <li><?php echo WPP_UD_UI::checkbox( "name=wpp_settings[configuration][enable_post_excerpt]&label=" . sprintf( __( 'Enable excerpts for %1s.', 'wpp' ), strtolower( WPP_F::property_label( 'plural' ) ) ), $wp_properties[ 'configuration' ][ 'enable_post_excerpt' ] ); ?></li>
        <?php if ( get_template() != 'denali' ) : ?>
        <li data-bind=""><?php echo WPP_F::checkbox( "name=wpp_settings[configuration][autoload_css]&label=" . __( 'Load CSS from the wp-properties.css file.','wpp' ), $wp_properties[ 'configuration' ][ 'autoload_css' ] ); ?></li>
        <?php endif; ?>
        <li><?php echo WPP_F::checkbox( "name=wpp_settings[configuration][completely_hide_hidden_attributes_in_admin_ui]&label=" . sprintf(__( 'Completely hide hidden attributes when editing %1$s.', 'wpp' ), ucfirst(WPP_F::property_label('plural'))), $wp_properties[ 'configuration' ][ 'completely_hide_hidden_attributes_in_admin_ui' ] ); ?></li>
        <li>
          <label><?php _e( 'Default Phone Number','wpp' ); ?>:</label>
          <?php echo WPP_F::input( "name=phone_number&group=wpp_settings[configuration]&style=width: 200px;", $wp_properties[ 'configuration' ][ 'phone_number' ] ); ?>
          <span data-bind="help: $root.strings.help_phone_number" class="alignnone" />
        </li>

      </ul>
    </div>
  </div>

  <div class="wpp_secondary_section wpp_something_advanced_wrapper" data-wpp-feature="physical_locations">
    <div class="ud_th"><strong><?php _e( 'Geolocation','wpp' ); ?></strong></div>
    <div class="ud_td">
      <ul>
        <li>
          <?php printf(__( 'Physical addresses is stored in the %1$s attribute. ','wpp' ), WPP_F::draw_attribute_dropdown( array( 'name' => 'wpp_settings[configuration][address_attribute]', 'selected' => $wp_properties[ 'configuration' ][ 'address_attribute' ], 'classification'=>'location' ) )); ?>
          <span data-bind="help: $root.strings.help_physical_address" />
        </li>
        <li>
          <label><?php _e( 'After geolocation, apply the following format:','wpp' )?></label>
          <input type="text" name="wpp_settings[configuration][display_address_format]" style="width: 200px;" value="<?php echo $wp_properties[ 'configuration' ][ 'display_address_format' ]; ?>"/>
          <span data-bind="help: $root.strings.help_location_tags" />
        </li>
      </ul>
    </div>
  </div>

  <div class="wpp_secondary_section wpp_something_advanced_wrapper" data-wpp-feature="localization">
    <div class="ud_th">
      <strong><?php _e('Localization', 'wpp'); ?></strong>
      <div class="description"><p><?php /* _e( '','wpp' ); */ ?></p></div>
    </div>
    <div class="ud_td">
      <ul>
        <li data-wpp-setting="area_unit_type">
          <label>
            <?php _e('All areas are saved using squared', 'wpp'); ?>
            <select name="wpp_settings[configuration][area_unit_type]">
              <option value=""> - </option>
              <option value="square_foot" <?php selected($wp_properties['configuration']['area_unit_type'], 'square_foot'); ?>><?php _e('feet', 'wpp'); ?>&nbsp;(<?php echo WPP_Formatting::get_area_unit('square_foot')?>)</option>
              <option value="square_meter" <?php selected($wp_properties['configuration']['area_unit_type'], 'square_meter'); ?>><?php _e('meters', 'wpp'); ?>&nbsp;(<?php echo WPP_Formatting::get_area_unit('square_meter')?>)</option>
              <option value="square_kilometer" <?php selected($wp_properties['configuration']['area_unit_type'], 'square_kilometer'); ?>><?php _e('kilometers', 'wpp'); ?>&nbsp;(<?php echo WPP_Formatting::get_area_unit('square_kilometer')?>)</option>
              <option value="square_mile" <?php selected($wp_properties['configuration']['area_unit_type'], 'square_mile'); ?>><?php _e('miles', 'wpp'); ?>&nbsp;(<?php echo WPP_Formatting::get_area_unit('square_mile')?>)</option>
            </select>
          </label>
        </li>
        <li data-wpp-setting="currency_symbol">
          <label><?php echo __('All prices are stored using the', 'wpp') . WPP_F::input("name=currency_symbol&label=" . __('currency symbol.', 'wpp') . "&class=currency&group=wpp_settings[configuration]", $wp_properties['configuration']['currency_symbol']); ?></label>
        </li>
        <li data-wpp-setting="thousands_sep">
          <label>
            <?php _e('Thousands separator symbol:', 'wpp'); ?>
            <select name="wpp_settings[configuration][thousands_sep]">
              <option value=""> - </option>
              <option value="." <?php selected($wp_properties['configuration']['thousands_sep'], '.'); ?>><?php _e('. (period)', 'wpp'); ?></option>
              <option value="," <?php selected($wp_properties['configuration']['thousands_sep'], ','); ?>><?php _e(', (comma)', 'wpp'); ?></option>
            </select>
          </label>
          <span data-bind="help: $root.strings.help_separator_symbol" class="wpp_right" />
        </li>
        <li data-wpp-setting="currency_symbol_placement">
          <label>
            <?php _e('Currency symbol placement:', 'wpp'); ?>
            <select name="wpp_settings[configuration][currency_symbol_placement]">
              <option value=""> - </option>
              <option value="before" <?php selected($wp_properties['configuration']['currency_symbol_placement'], 'before'); ?>><?php _e('Before number', 'wpp'); ?></option>
              <option value="after" <?php selected($wp_properties['configuration']['currency_symbol_placement'], 'after'); ?>><?php _e('After number', 'wpp'); ?></option>
            </select>
          </label>
          <span data-bind="help: $root.strings.help_currency_symbol" class="wpp_right" />
        </li>
        <li data-wpp-setting="google_maps_localization">
          <label>
            <?php _e('Localize and display geolocated addresses in', 'wpp'); ?> <?php echo WPP_F::draw_localization_dropdown("name=wpp_settings[configuration][google_maps_localization]&selected={$wp_properties['configuration']['google_maps_localization']}"); ?>
          </label>
        </li>
      </ul>
    </div>
  </div>

  <div class="wpp_primary_section">
    <div class="ud_th">
        <strong><?php _e( 'Page Elements','wpp' ) ?></strong>
        <div class="description"><?php _e( 'In this section you will be able to set which elements and attributes appear on dedicated page layouts for WP-Property, such as the Property Overview page, Search Results page and Single Property page.','wpp' ) ?></div>
      </div>
  </div>

  <div class="wpp_secondary_section">
    <div class="ud_th">
      <strong><?php printf( __( '%1s Overview', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ); ?></strong>
      <div class="description"><p><?php printf( __( 'Settings related to the visualization of the %1s Overview shortcode and page.', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ); ?></p></div>
    </div>
    <div class="ud_td">
      <ul>
        <li><?php echo WPP_F::checkbox( 'name=wpp_settings[configuration][property_overview][fancybox_preview]&label=' . __( 'Use Fancybox to enlarge thumbnails to their full size when clicked.','wpp' ) , $wp_properties[ 'configuration' ][ 'property_overview' ][ 'fancybox_preview' ] ); ?></li>
        <li><?php echo WPP_F::checkbox( "name=wpp_settings[configuration][bottom_insert_pagenation]&label=" . __( 'Show pagination on bottom of results.','wpp' ), $wp_properties[ 'configuration' ][ 'bottom_insert_pagenation' ] ); ?></li>
        <li><?php echo WPP_F::checkbox( 'name=wpp_settings[configuration][property_overview][show_children]&label=' . sprintf(__( 'Show children %1$s.','wpp' ), WPP_F::property_label('plural')), $wp_properties[ 'configuration' ][ 'property_overview' ][ 'show_children' ] ); ?></li>
        <li><?php _e( 'Default Sorter Type:','wpp' ) ?> <?php WPP_F::render_dropdown( array( 'buttons' => __( 'Buttons', 'wpp' ), 'dropdown' => __( 'Dropdown', 'wpp' ) ), $wp_properties[ 'configuration' ][ 'property_overview' ][ 'sorter_type' ], array( 'name' => 'wpp_settings[configuration][property_overview][sorter_type]' ) ); ?></li>
        <li class="must_have_permalinks">
          <label>
            <?php printf( __( 'Root %1s Page:', 'wpp' ), WPP_F::property_label( 'singular' ) ); ?>
            <select name="wpp_settings[configuration][base_slug]" id="wpp_settings_base_slug">
              <option <?php selected( $wp_properties[ 'configuration' ][ 'base_slug' ], 'property' ); ?> value="property"><?php echo sprintf(__( '%1$s (Default)','wpp' ), ucfirst(WPP_F::property_label('singular'))); ?></option>
              <?php foreach( get_pages() as $page ): ?>
                <option <?php selected( $wp_properties[ 'configuration' ][ 'base_slug' ],$page->post_name ); ?> value="<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <span data-bind="help: $root.strings.help_root_property_page" class="alignnone" />
        </li>
        <li>
          <input data-bind="attr:{name:'wpp_settings[configuration][property_overview][show_true_as_image]'}, checked: $root.global.show_true_as_image" id='show_true_as_image' type="checkbox" value="true" />
          <label data-bind="text: $root.strings.at_show_true_as_image_label" for="show_true_as_image" />
        </li>
        <?php do_action( 'wpp_settings_overview_bottom', $wp_properties ); ?>
      </ul>
      <ul class="wpp_non_property_page_settings hidden">
        <li><?php echo WPP_F::checkbox( 'name=wpp_settings[configuration][automatically_insert_overview]&label='. __( 'Automatically overwrite this page\'s content with [property_overview].','wpp' ), $wp_properties[ 'configuration' ][ 'automatically_insert_overview' ] ); ?></li>
        <li class="wpp_wpp_settings_configuration_do_not_override_search_result_page_row <?php if( $wp_properties[ 'configuration' ][ 'automatically_insert_overview' ] == 'true' ) echo " hidden ";?>">
          <?php echo WPP_F::checkbox( "name=wpp_settings[configuration][do_not_override_search_result_page]&label=" . __( 'When showing property search results, don\'t override the page content with [property_overview].', 'wpp' ), $wp_properties[ 'configuration' ][ 'do_not_override_search_result_page' ] ); ?>
          <div class="description"><?php echo sprintf(__( 'If checked, be sure to include [property_overview] somewhere in the content, or no %1$s will be displayed.','wpp' ), ucfirst(WPP_F::property_label('plural'))); ?></div>
        </li>
      </ul>
    </div>
  </div>

  <div class="wpp_secondary_section">
    <div class="ud_th">
      <strong><?php printf( __( 'Single %1s Page', 'wpp' ),WPP_F::property_label( 'singular' ) );  ?></strong>
      <div class="description"><p><?php printf( __('Display settings for the single %1s page.', 'wpp' ), strtolower( WPP_F::property_label( 'singular' ) ) ); ?></p></div>
    </div>
    <div class="ud_td">
      <ul>
        <li><?php echo WPP_F::checkbox( "name=wpp_settings[configuration][do_not_register_sidebars]&label=" .__( 'Disable WP-Property Widget Areas','wpp' ), $wp_properties[ 'configuration' ][ 'do_not_register_sidebars' ] ); ?></li>
        <li><?php echo WPP_F::checkbox( "name=wpp_settings[configuration][property_overview][sort_stats_by_groups]&label=" .__( 'Display attributes organized by their groups.','wpp' ), $wp_properties[ 'configuration' ][ 'property_overview' ][ 'sort_stats_by_groups' ] ); ?></li>
        <li><?php echo WPP_F::checkbox( "name=wpp_settings[configuration][hide_infobox_on_single_property]&label=" .__( 'Hide Map Infobox if map is used for Single Property','wpp' ), $wp_properties[ 'configuration' ][ 'hide_infobox_on_single_property' ] ); ?></li>
        <?php do_action( 'wpp_settings_page_property_page' );?>
        <li>
          <label>
            <?php printf( __( 'Single Listing Template', 'wpp' ), WPP_F::property_label( 'singular' ) ); ?>
          </label>
          <select name="wpp_settings[configuration][single_listing_template]">
            <option> - </option>
            <?php foreach( (array)WPP_F::get_available_theme_templates() as $file_name => $_detail ) { ?>
              <option <?php selected( $wp_properties[ 'configuration' ][ 'single_listing_template' ], $file_name ); ?> value="<?php echo $file_name; ?>"><?php echo $_detail[ 'name' ]; ?> (<?php echo $file_name; ?>)</option>
            <?php } ?>
          </select>
          <span data-bind="help: $root.strings.help_single_page" />
        </li>
      </ul>
    </div>
  </div>

  <?php do_action( 'wpp::settings::main::bottom' ); ?>

  </div>
</div>
