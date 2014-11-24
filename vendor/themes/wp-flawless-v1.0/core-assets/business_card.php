<?php
/**
 * Name: Business Card
 * Version: 1.0
 * Description: Widgets for the Flawless theme.
 * Author: Usability Dynamics, Inc.
 * Theme Feature: header-business-card
 *
 */


add_action('flawless_theme_setup', array('flawless_business_card', 'flawless_theme_setup'));
add_filter('flawless::available_theme_features', array('flawless_business_card','available_theme_features'));

class flawless_business_card {

  static function flawless_theme_setup() {
    add_action('flawless::init_lower', array('flawless_business_card', 'init_lower'));
  }


  static function init_lower() {

    if( !current_theme_supports( 'header-business-card' ) ) {
      return;
    }

    //** Add administrative actions */
    add_action('admin_init', array('flawless_business_card', 'admin_init'));

    //** Add business card widget */
    add_action('widgets_init', array('flawless_business_card', 'widgets_init'));

    add_filter('flawless_option_tabs', array('flawless_business_card','flawless_option_tabs'));

    add_filter('flawless_default_settings', array('flawless_business_card','flawless_default_settings'));

    add_filter('flawless_update_settings', array('flawless_business_card','flawless_update_settings'));

  }


  function admin_init() {

    //** Add Business Card to header configuration */
    add_filter('flawless_option_header_elements', array('flawless_business_card','flawless_option_header_elements'));

  }

  /**
   * {}
   *
   */
  static function available_theme_features( $features ) {
    $features[ 'header-business-card' ] = true;
    return $features;
  }


  function flawless_default_settings($flawless) {

    $flawless['business_card']['data']['physical_address'] = array(
      'label' =>  __('Address', 'flawless'),
      'type' => 'geo_map',
      'locked' => 'true',
      'description' => __('A valid address will allow the theme to retreive geo-coordinates using Google Maps and draw a location map.', 'flawless')
    );

    $flawless['business_card']['data']['phone_number'] = array(
      'label' =>  __('Phone Number', 'flawless'),
      'locked' => 'true'
    );

    return $flawless;

  }

  function flawless_update_settings($flawless) {

    //** Set coordinates for Business Card Info */
    $coordinates = Flawless_F::geo_locate_address($flawless['business_card']['data']['physical_address']);

    $flawless['business_card']['system']['latitude'] = $coordinates->longitude;
    $flawless['business_card']['system']['longitude'] = $coordinates->latitude;


    return $flawless;

  }


  function widgets_init() {

    flawless_theme::console_log('P: Widget Registered: Flawless_Widget_Business_Card');

    register_widget("Flawless_Widget_Business_Card");

  }


  function flawless_option_tabs($tabs) {

    $tabs['options_ui_business_card'] = array(
      'label' => __('Business Info','flawless'),
      'id' => 'options_ui_business_card',
      'position' => 30,
      'callback' => array('flawless_business_card','options_ui_business_card')
    );

    return $tabs;

  }


  /**
   * Adds "Business Info" tab to the Header configuration.
   *
   * @since Flawless 1.0
   */
  function flawless_option_header_elements($tabs) {
    global $flawless;

    $tabs['business'] = array(
      'label' => __('Business Info','flawless'),
      'id' => 'header-business-card',
      'name' => 'flawless_settings[disabled_theme_features][header-business-card]',
      'position' => 60,
      'setting' => $flawless['disabled_theme_features']['header-business-card'],
      'toggle_label' => __('Disable the header <b>Caller Card</b> section.', 'flawless'),
      'callback' => array('flawless_business_card','header_business_card_options')
    );

    return $tabs;

  }


  /**
   * Render contents of the "Business Info" settings in Header
   *
   * @since Flawless 1.0
   */
  function header_business_card_options( $flawless ) {

    $header_options = (is_array($flawless['business_card']['header']) ? $flawless['business_card']['header'] : array());
    $main_options = (is_array($flawless['business_card']['data']) ? $flawless['business_card']['data'] : array());

    $social_icons = flawless_footer_follow($flawless, array('return_raw' => true));

    if(count($social_icons) > 0) {
      $main_options['social_icons']['label'] = __('Social Media Icons', 'flawless');
    }

    //** Cycle through saved attributes, to maintain order, and remove any that are no longer in main list */
    foreach($header_options as $slug => $selected) {
      if(!in_array($slug, array_keys($main_options))) {
        unset($header_options[$slug]);
      } else {
        $header_options[$slug] = $main_options[$slug];

        if($selected['enable'] == 'true') {
          $header_options[$slug]['enable']  = 'true';
        }
      }
    }

    //** Cycle through main options, and add any to array that are not already there */
    foreach($main_options as $slug => $data) {
      if(!in_array($slug, array_keys($header_options))) {
        $header_options[$slug] = $data;
      }
    }


    ?>

    <p><?php _e('Fields to Display:', 'flawless'); ?></p>
    <div class="flawless_sortable_wrapper flawless_bc_options wp-tab-panel">
    <ul class="flawless_sortable_attributes">
    <?php foreach($header_options as $slug => $data) { ?>
    <li>
      <input type="hidden" name="flawless_settings[business_card][header][<?php echo $slug; ?>][enable]" value="false" />
      <input class="checkbox" type="checkbox" <?php checked($header_options[$slug]['enable'], 'true') ?> id="header_business_card_<?php echo $slug; ?>" name="flawless_settings[business_card][header][<?php echo $slug; ?>][enable]" value="true" />
      <label for="header_business_card_<?php echo $slug; ?>"><?php echo $data['label']; ?></label>
    </li>
    <?php } ?>
    </ul>
    </div>

    <?php

  }

  /**
   *
   * @param $flawless
   */
  function options_ui_business_card($flawless) {
    /** Determine if business_card option doesn't exist some way we set default settings */
    if(empty($flawless['business_card'])) {
      $flawless = self::flawless_default_settings($flawless);
    }
    ?>

    <div class="tab_description"><?php _e( '', 'flawless' ); ?></div>

    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th><?php _e('Business Info', 'flawless'); ?></th>
          <td>
            <table class="widefat wpp_something_advanced_wrapper ud_ui_dynamic_table" sortable_table="true" allow_random_slug="false">
              <tbody>
              <?php foreach((array)$flawless['business_card']['data'] as $slug => $data) { ?>
                <tr class="flawless_dynamic_table_row <?php echo ($data['locked'] == 'true' ? 'flawless_locked_row' : ''); ?>" slug="<?php echo $slug; ?>" new_row="false" lock_row="<?php echo ($data['locked'] == 'true' ? 'true' : 'false'); ?>">
                  <th>
                    <div class="delete_icon flawless_delete_row" verify_action="true"></div>
                    <ul class="flawless_options_wrapper">
                      <li>
                        <input type="text" id="flawless_card_<?php echo $slug;?>" class="slug_setter" name="flawless_settings[business_card][data][<?php echo $slug; ?>][label]" value="<?php echo $data['label']; ?>" />
                      </li>
                    </ul>
                    <input type="hidden" class="slug_setter" name="flawless_settings[business_card][data][<?php echo $slug; ?>][type]" value="<?php echo $data['type']; ?>" />
                    <input type="hidden" do_not_clone="true" class="slug_setter" name="flawless_settings[business_card][data][<?php echo $slug; ?>][locked]" value="<?php echo $data['locked']; ?>" />
                    <input type="hidden" do_not_clone="true" class="slug_setter" name="flawless_settings[business_card][data][<?php echo $slug; ?>][description]" value="<?php echo $data['description']; ?>" />
                  </th>
                  <td class="draggable_col">
                    <input type="text" id="flawless_card_<?php echo $slug;?>" class="regular-text" name="flawless_settings[business_card][data][<?php echo $slug; ?>][value]" value="<?php echo $data['value']; ?>" />
                    <?php echo ($data['description'] ? '<div class="description">' . $data['description'] . '</div>' : ''); ?>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan='2'><input type="button" class="flawless_add_row button-secondary" value="<?php _e('Add Row','flawless') ?>" /></td>
                </tr>
              </tfoot>
            </table>
          </td>
        </tr>

        <tr>
          <th><?php _e('Social Media Links'); ?></th>
          <td>
            <ul class="social_media_icons">
              <?php foreach(flawless_footer_follow($flawless, array('return_raw' => true)) as $social_key => $social_row) { ?>
                <li>
                  <label for="<?php echo $social_key; ?>">
                    <img class="social_media_icon_thumb" src="<?php echo $social_row['thumb_url']; ?>" />
                  </label>
                  <input id="<?php echo $social_key; ?>" type="text"  class="flawless_force_http_prefix" name="flawless_settings[social_icons][<?php echo $social_row['option']; ?>]" value="<?php echo $social_row['url']; ?>" />
                  <span class="label"><?php echo $social_row['label']; ?></span>
                </li>
              <?php } ?>
            </ul>

          </td>
          </tr>

      </tbody>
    </table>


    <?php
  }


}


/**
 * Business Card widget.
 *
 * @since 3.0.0
 */
class Flawless_Widget_Business_Card extends WP_Widget {

/**
   * Initialize the widget.
   *
   * @since 3.0.0
   */
  function __construct() {

    parent::__construct(
      'business_card',
      __('Business Card', 'flawless'),
      array(
        'classname' => 'business_card',
        'description' => __('Display business information in a flexible format.', 'flawless')
      ),
      array(
        'width' => 300
      )
    );

  }

/**
   * Renders the widget on the front-end.
   *
   * @since 3.0.0
   */
  function widget($args, $instance) {
    global $flawless;

    extract($args);

    if(empty($instance)) {
      return;
    }

    $html[] = $before_widget;

    if ( $instance['widget_title'] ) {
      $html[] = $before_title . $instance['widget_title'] . $after_title;
    }

    $main_data = (is_array($flawless['business_card']['data']) ? $flawless['business_card']['data'] : array());

    foreach($instance as $slug => $option) {

      if($option != 'true') {
        continue;
      }

      $classes = array($slug);

      $data = $main_data[$slug];

      $value = $flawless['business_card']['data'][$slug]['value'];
      $label = $flawless['business_card']['data'][$slug]['label'];

      if($slug == 'social_icons') {
        $value = flawless_footer_follow();
      }

      if(empty($value)) {
        continue;
      }

      if(Flawless_F::is_url($value)) {
        $value = '<a class="business_info_link" href="'. $value .'">' . $label. '</a>';
        $classes[] = 'has_link';
      }

      $attributes[] = '<p class="' . implode(' ', (array) $classes) . '">' . nl2br(do_shortcode($value)) . '</p>';
    }

    if(!empty($attributes)) {
      $html['attributes'] = '<address class="flawless_business_card_items">' . implode('', $attributes) . '</address>';
    }

    $html[] = $after_widget;

    if(!empty($html['attributes'])) {
      echo implode('', $html);
    } else {
      return false;
    }

  }


/**
   * Handles any special functions when the widget is being updated.
   *
   * @since 3.0.0
   */
  function update($new_instance, $old_instance) {
    return $new_instance;
  }


/**
   * Renders widget UI in control panel.
   *
   *
   * @todo Needs to make use of sortable attributes.
   * @uses $current_screen global variable
   * @since 3.0.0
   */
  function form($instance = false) {
    global $flawless;

    if($this) {
      $this_here = $this;
    }

    $widget_options = (is_array($instance) ? $instance : array());
    $main_options = (is_array($flawless['business_card']['data']) ? $flawless['business_card']['data'] : array());

    //** We don't want to mix in the title into our array */
    unset($widget_options['widget_title']);

    //** Cycle through saved attributes, to maintain order, and remove any that are no longer in main list */
    foreach($widget_options as $slug => $selected) {
      if(!in_array($slug, array_keys($main_options))) {
        unset($widget_options[$slug]);
      } else {
        $widget_options[$slug] = $main_options[$slug];
      }
    }

    //** Cycle through main options, and add any to array that are not already there */
    foreach($main_options as $slug => $data) {
      if(!in_array($slug, array_keys($widget_options))) {
        $widget_options[$slug] = $data;
      }
    }

    $social_icons = flawless_footer_follow($flawless, array('return_raw' => true));

    if(count($social_icons) > 0) {
      $widget_options['social_icons']['label'] = __('Social Media Icons', 'flawless');
    }

    ?>

    <script type="text/javascript">
      jQuery(document).ready(function() {
        if(typeof jQuery.fn.sortable == 'function') {
          jQuery(".flawless_sortable_wrapper").each(function() {
            jQuery(".flawless_sortable_attributes", this).sortable();
          });
        }
      });
    </script>

    <p>
      <label for="<?php echo $this_here->get_field_id('widget_title'); ?>"><?php _e('Title:'); ?>
      <input class="widefat" id="<?php echo $this_here->get_field_id('widget_title'); ?>" name="<?php echo $this_here->get_field_name('widget_title'); ?>" type="text" value="<?php echo esc_attr($instance['widget_title']); ?>" /></label>
    </p>

    <p><?php _e('Fields to Display:', 'flawless'); ?></p>
    <div class="flawless_sortable_wrapper flawless_bc_options wp-tab-panel">
    <ul class="flawless_sortable_attributes">
    <?php foreach($widget_options as $slug => $data) { ?>
    <li>
      <input type="hidden" name="<?php echo $this_here->get_field_name($slug); ?>" value="false" />
      <input class="checkbox" type="checkbox" <?php checked($instance[$slug], 'true') ?> id="<?php echo $this_here->get_field_id($slug); ?>" name="<?php echo $this_here->get_field_name($slug); ?>" value="true" />
      <label for="<?php echo $this_here->get_field_id($slug); ?>"><?php echo $data['label']; ?></label>
    </li>
    <?php } ?>
    </ul>
    </div>

    <?php

  }

}


/**
 * Display Folow icons in the footer if required data is inputed
 *
 *
 */
if ( ! function_exists( 'flawless_footer_follow' ) ) {
  function flawless_footer_follow($flawless = false, $args = false) {

    if(!$flawless) {
      global $flawless;
    }

    $defaults = array(
      'return_raw' => false,
      'return_array' => false,
      'echo' => false
    );

    $args = wp_parse_args( $args, $defaults );

    $template_dir = get_bloginfo('template_url');

    $icons['twitter']['icon'] = '/img/follow_t.png';
    $icons['twitter']['url'] = $flawless['social_icons']['twitter'];
    $icons['twitter']['option'] = 'twitter';
    $icons['twitter']['label'] = 'Twitter';

    $icons['facebook']['icon'] = '/img/follow_f.png';
    $icons['facebook']['url'] = $flawless['social_icons']['facebook'];
    $icons['facebook']['option'] = 'facebook';
    $icons['facebook']['label'] = 'Facebook';

    $icons['linkedin']['icon'] = '/img/follow_in.png';
    $icons['linkedin']['url'] = $flawless['social_icons']['linkedin'];
    $icons['linkedin']['option'] = 'linkedin';
    $icons['linkedin']['label'] = 'LinkedIn';

    $icons['rss']['icon'] = '/img/follow_rss.png';
    $icons['rss']['url'] = $flawless['social_icons']['rss'];
    $icons['rss']['option'] = 'rss';
    $icons['rss']['label'] = 'RSS';

    $icons['youtube']['icon'] = '/img/follow_y.png';
    $icons['youtube']['url'] = $flawless['social_icons']['youtube'];
    $icons['youtube']['option'] = 'youtube';
    $icons['youtube']['label'] = 'YouTube';

    $icons = apply_filters('flawless_social_icons', $icons);

    foreach($icons as $network => $data) {

      $thumb_url = $template_dir . $data['icon'];

      $icons[$network]['thumb_url'] = $thumb_url;

      if(!empty($data['url'])) {
        $html[] = '<a href="'. $data['url'] . '"><img class="flawless_social_link" src="'. $thumb_url .'" /></a>';
      }

    }

    if($args['return_raw']) {
      return $icons;
    }

    if(!is_array($icons)) {
      return array();
    }


    if(!is_array($html)) {
      return array();
    }

    $html = implode('', $html);

    if($args['echo']) {
      echo $html;
      return;
    }

    if($args['return_array']) {
      return $icons;
    }

    return $html;

  }
}



if(!function_exists('flawless_have_business_card')) {
  function flawless_have_business_card($scope = false) {
    global $flawless;

    if(!$scope || !is_array($flawless['business_card'][$scope])) {
      return;
    }

    $options = $flawless['business_card'][$scope];
    $main_data = (is_array($flawless['business_card']['data']) ? $flawless['business_card']['data'] : array());

    foreach($options as $slug => $data) {

      $classes = array($slug);

      if($data['enable'] != 'true') {
        continue;
      }

      $data = $main_data[$slug];

      $value = $flawless['business_card']['data'][$slug]['value'];
      $label = $flawless['business_card']['data'][$slug]['label'];

      if($slug == 'social_icons') {
        $value = flawless_footer_follow();
      }

      if(empty($value)) {
        continue;
      }

      //** Convert into URL, if a URL is used */
      if(Flawless_F::is_url($value)) {
        $value = '<a class="business_info_link" href="'. $value .'">' . $label. '</a>';
        $classes[] = 'has_link';
      }

      $attributes[] = '<li class="' . implode(' ', (array) $classes) . '">' . nl2br(do_shortcode($value)) . '</li>';
    }

    if(!empty($attributes)) {
      $html['attributes'] = '<ul class="flawless_business_card_items '. $scope .'">' . implode('', $attributes) . '</ul>';
    }

    if(!empty($html['attributes'])) {
      return implode('', $html);
    } else {
      return false;
    }


  }
}
