<?php
/**
 * WP-Property Settings
 *
 */
//** WP Properties Global */
global $wp_properties;

$wpp_nav = (array) apply_filters('wpp_settings_nav', array());
foreach ($wpp_nav as $k => $v) {
  if ($v['feature'] && !WPP_F::site_has_license($v['slug'])) {
    unset($wpp_nav[$k]);
  }
}

foreach ((array) $wp_properties['available_features'] as $plugin) {
  if (isset($plugin['status']) && $plugin['status'] === 'disabled') {
    unset($wpp_nav[$plugin]);
  }
}

$wrapper_classes = array('wpp_settings_page');
$wp_messages = WPP_F::check_premium_folder_permissions();

if (isset($_REQUEST['message'])) {
  switch ($_REQUEST['message']) {
    case 'settings_updated': $wp_messages['notice'][] = __('Settings updated.', 'wpp');
      break;
    case 'backup_restored': $wp_messages['notice'][] = __("WP-Property configuration restored from backup.", 'wpp');
      break;
    case 'ajax_save_fail': $wp_messages['notice'][] = __('Settings updated, but there seems to be a JavaScript conflict which could cause other issues.', 'wpp');
      break;
  }
}

//** We have to update Rewrite rules here. peshkov@UD */
flush_rewrite_rules(false);

//** Additional wrapper classes */
$wrapper_classes[] = ( get_option('permalink_structure') == '' ) ? 'no_permalinks' : 'have_permalinks';
?>

<div id="wpp_settings_ui" class="wrap wpp-ui-wrap <?php echo implode(' ', $wrapper_classes); ?>">


  <form id="wpp_settings" class="wpp_settings" method="post" action="<?php echo admin_url('edit.php?post_type=property&page=property_settings'); ?>&message=ajax_save_fail" enctype="multipart/form-data">

    <div class="wpp-ui-header-wrap">
      <div class="wpp-ui-header">
        <div class="alignleft wpp_title_wrap">
          <span class="wpp_screen_icon"><?php screen_icon(); ?></span>
          <h1 class="wpp-title"><?php _e('Settings', 'wpp'); ?><span class="hidden wpp_section_title_wrap"><span class="divider">:</span><span class="wpp_section_title"></span></span></h1>
        </div>
        <div class="alignleft wpp_fb_like">
        <!--[if (gt IE 7)|!(IE)]><!-->
          <div class="fb-like" data-href="https://www.facebook.com/wpproperty" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false"></div>
        <!--<![endif]-->
        </div>
        <div class="alignright">
          <div class="wpp_save_wrapper wpp_actions_bar">
            <input type="submit" class="wpp_button wpp_red wpp_save_settings" value="<?php _e('Save Settings', 'wpp'); ?>" data-bind="" />
            <div class="wpp-ui wpp_sidebar_response"></div>
          </div>
        </div>

        <div class="clear"></div>
      </div>
    </div>

    <h2 class="wpp_fake"></h2>

    <div class="wpp_ui_wrapper wpp_settings_page">

      <?php wp_nonce_field('wpp_setting_save'); ?>

      <div class="wpp_settings_wrapper">
        <div class="wpp_content_wrapper"><div class="wpp_content_inner">
            <div class="wpp_core_notice"></div>
            <!-- ko if: $root.load_progress -->
            <span data-bind="$text: load_progress()"></span>
            <!-- /ko -->
            <div class="wpp-ajax-container"></div>
          </div></div>

        <div class="hidden wpp_ui_sidebar">

          <div class="wpp_sidebar_wrapper">

            <div class="wpp_sidebar_options">
              <ul class="wpp_settings">
                <li class="heading"><?php _e('Core Features', 'wpp'); ?></li>
                <li><a href="#main" class="wpp_link" data-ui="core.main"><?php _e('Basic Settings', 'wpp'); ?></a></li>
                <li><a href="#images" class="wpp_link" data-ui="core.images"><?php _e('Image Settings', 'wpp'); ?></a></li>
                <li><a href="#maps" class="wpp_link" data-ui="core.maps"><?php _e('Map Settings', 'wpp'); ?></a></li>
                <?php foreach ((array) $wpp_nav as $url_slug => $nav) : ?>
                  <?php if ( $nav['feature'] || !isset( $nav['interface'] ) ) continue; ?>
                  <li><a href="#<?php echo $url_slug; ?>" class="wpp_link" data-ui="core.<?php echo $nav['interface']; ?>" data-wpp_toggle_ui="settings_page" data-wpp_section_class="<?php echo $nav['slug']; ?>"><?php echo $nav['title']; ?></a></li>
                <?php endforeach; ?>
                <li><a href="#advanced" class="wpp_link" data-ui="core.advanced"><?php _e('Advanced', 'wpp'); ?></a></li>
                <?php if ($wp_properties['configuration']['show_ud_log']) { ?>
                  <li><a href="#log" class="wpp_link" data-ui="core.log"><?php _e('Log', 'wpp'); ?></a></li>
                <?php } ?>
              </ul>
              <ul class="wpp_settings">
                <li class="heading"><?php _e('Premium Features', 'wpp'); ?></li>
                <li><a href="#premium_features" class="wpp_link" data-ui="core.features"><?php _e('Feature Overview', 'wpp'); ?></a></li>
                <!-- Add an if here in case there's no premium features installed with the text "you don't have any premium features installed. (Visit our website - link) to see what you're missing. -->
                <?php foreach ((array) $wpp_nav as $url_slug => $nav) : ?>
                  <?php if (!$nav['feature'] || !isset( $nav['interface'] ) ) continue; ?>
                  <li><a href="#<?php echo $url_slug; ?>" class="wpp_link" data-ui="<?php echo $nav['slug'] . '.' . $nav['interface'] ?>"><?php echo $nav['title']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>

          </div>
        </div>
      </div>

      <input type="hidden" name="current_section" value="" />

    </div>

  </form>

</div>

<!--fb-->
<div id="fb-root"></div>
<script type="text/javascript">(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=373515126019844"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));</script>
