<?php
/**
 * Settings Page Section - Plugins Options
 *
 * @version 1.37.0
 * @package WP-Property
 * @author  team@UD
 * @copyright 2010-2012 Usability Dyanmics, Inc.
 */

//** Required globals */
global $wp_properties;
$parseUrl = parse_url( trim( get_bloginfo( 'url' ) ) );
$this_domain = trim( $parseUrl[ 'host' ] ? $parseUrl[ 'host' ] : array_shift( explode( '/', $parseUrl[ 'path' ], 2 ) ) );
?>

<script type="text/javascript">
  jQuery(document).ready(function(){
    // Check plugin updates
    jQuery( "#wpp_ajax_check_plugin_updates" ).click( function() {
      jQuery( '.plugin_status' ).remove();
      jQuery.post( ajaxurl, {
          action: 'wpp_ajax_check_plugin_updates'
        }, function( data ) {
          var message = "<div class='plugin_status updated fade'><p>" + data + "</p></div>";
          jQuery( message ).insertAfter( "h2" );
        });
    });
  });
</script>
<div class="form-table wpp_option_table wpp_setting_page wpp_premium_overview_ui">
  <div class="ud_table_head">
    <div class="ud_tr">
      <div class="ud_th" colspan="2">
        <h3 class="hidden wpp_section_title" data-bind="">Premium Features</h3>
        <div class="wpp_section_overview">
          <p>In this page you will be able to see the status of all available Premium Features. For purchased and installed features, you will see the installed version and given the ability to disable them.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="ud_tbody">
  <div class="wpp_secondary_section" data-bind="">
    <div class="ud_th"><strong data-bind="">Domain Name</strong></div>
    <div class="ud_td">
      <ul>
        <li class="clearfix">
          <label>
    <?php _e( 'When prompted for your domain name during a premium feature purchase, enter as appears here:','wpp' ); ?>
    <input type="text" readonly="true" value="<?php echo $this_domain; ?>" size="<?php echo strlen( $this_domain ) + 10; ?>" />
    </label>
        </li>
      </ul>
    </div>
  </div>

  <?php foreach ((array)$wp_properties['available_features'] as $plugin_slug => $plugin_data): ?>

    <input type="hidden" name="wpp_settings[available_features][<?php echo $plugin_slug; ?>][title]" value="<?php echo $plugin_data['title']; ?>" />
    <input type="hidden" name="wpp_settings[available_features][<?php echo $plugin_slug; ?>][tagline]" value="<?php echo $plugin_data['tagline']; ?>" />
    <input type="hidden" name="wpp_settings[available_features][<?php echo $plugin_slug; ?>][image]" value="<?php echo $plugin_data['image']; ?>" />
    <input type="hidden" name="wpp_settings[available_features][<?php echo $plugin_slug; ?>][description]" value="<?php echo $plugin_data['description']; ?>" />

    <?php $installed = WPP_F::check_premium($plugin_slug); ?>
    <?php $active = ( @$wp_properties['installed_features'][$plugin_slug]['disabled'] != 'false' ? true : false ); ?>

    <?php if ($installed): ?>
      <?php /* Do this to preserve settings after page save. */ ?>
      <input type="hidden" name="wpp_settings[installed_features][<?php echo $plugin_slug; ?>][disabled]" value="<?php echo $wp_properties['installed_features'][$plugin_slug]['disabled']; ?>" />
      <input type="hidden" name="wpp_settings[installed_features][<?php echo $plugin_slug; ?>][name]" value="<?php echo $wp_properties['installed_features'][$plugin_slug]['name']; ?>" />
      <input type="hidden" name="wpp_settings[installed_features][<?php echo $plugin_slug; ?>][version]" value="<?php echo $wp_properties['installed_features'][$plugin_slug]['version']; ?>" />
      <input type="hidden" name="wpp_settings[installed_features][<?php echo $plugin_slug; ?>][description]" value="<?php echo $wp_properties['installed_features'][$plugin_slug]['description']; ?>" />
    <?php endif; ?>


    <!-- yarr -->
    <div class="wpp_secondary_section" data-bind="visible: true || $root.saas.connected">
    <div class="ud_th">
    <a href="http://usabilitydynamics.com/products/wp-property/"><img src="<?php echo $plugin_data['image']; ?>" /></a>
    </div>
    <div class="ud_td">
      <ul>
        <li class="clearfix">
        <strong><?php echo $plugin_data['title']; ?></strong>: <span><?php echo $plugin_data['tagline']; ?></span>
          <p><?php echo $plugin_data['description']; ?></p>
        </li>
        <li class="clearfix">
          <?php if ($installed) { ?>

              <div class="alignleft">
                <?php
                if ($wp_properties['installed_features'][$plugin_slug]['needs_higher_wpp_version'] == 'true') {
                  printf(__('This feature is disabled because it requires WP-Property %1$s or higher.'), $wp_properties['installed_features'][$plugin_slug]['minimum_wpp_version']);
                } else {
                  echo WPP_F::checkbox("name=wpp_settings[installed_features][$plugin_slug][disabled]&label=" . __('Disable Premium Feature.', 'wpp'), $wp_properties['installed_features'][$plugin_slug]['disabled']);
                  ?>
                </div>
                <div class="alignright"><?php _e('Feature installed, using version', 'wpp') ?> <?php echo $wp_properties['installed_features'][$plugin_slug]['version']; ?>.</div>
    <?php
    }
  } else {
    $pr_link = 'https://usabilitydynamics.com/products/wp-property/premium/';
    echo sprintf(__('Please visit <a href="%s">UsabilityDynamics.com</a> to purchase this feature.', 'wpp'), $pr_link);
  }
  ?>

        </li>
      </ul>
    </div>
  </div>

    <!-- yarr -->
<?php endforeach; ?>
</div>