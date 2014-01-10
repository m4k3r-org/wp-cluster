<?php
/**
 * Capatibilities Settings page
 *
 * @version 2.0
 * @package WP-Property
 * @subpackage Power Tools
 * @author  team@UD
 * @copyright 2010-2012 Usability Dyanmics, Inc.
 */

global $wp_roles, $wpp_capabilities;

?>
<div class="form-table wpp_option_table">
  <div class="ud_table_head">
    <div class="ud_tr">
      <div class="ud_th">
        <h3 class="hidden wpp_section_title"><?php _e( 'Capabilities', 'wpp' ); ?></h3>
        <div class="wpp_section_overview">
          <p><?php _e( 'In this page, you can easily control the system capabilities for each user role. Be very careful, as these settings can greatly change the user experience. We suggest that you only change capabilities for custom user roles, unless you really know what you are doing. For detailed information about Roles and Capabilities please visit <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">WordPress Codex</a>', 'wpp' ); ?></p>
        </div>
      </div>
    </div>
  </div>
  <div class="ud_tbody">
    <?php $i=0; foreach ($wp_roles->roles as $r_slug => $role) : ?>
    <div class="wpp_secondary_section<?php echo $i?'':' wpp_first'; ?>">
      <div class="ud_th">
        <strong><?php _e($role['name'],'wpp') ?></strong>
        <?php
          $description = "";
          switch ($r_slug) {
            case "administrator":
              $description = __('The administrator has all the possible capabilities. For security reasons, they cannot be changed.','wpp');
              break;
          }
        ?>
        <div class="description"><p><?php echo apply_filters('wpp_role_description_'. $r_slug, $description); ?></p></div>
      </div>
      <div class="ud_td">
        <ul class="wp-tab-panel wpp_hidden_property_attributes">
          <?php foreach((array)$wpp_capabilities as $cap => $value): ?>
          <?php $checked = (array_key_exists($cap , $role['capabilities']) ? "checked=\"checked\"" : ""); ?>
          <?php $disabled = ( ($r_slug == "administrator") ? "disabled=\"disabled\"" : ""); ?>
          <li>
            <input id="wpp_<?php echo $r_slug;?>_<?php echo $cap;?>_capability" <?php echo $checked; ?> <?php echo $disabled; ?> type="checkbox" name="wpp_settings[capabilities][<?php echo $r_slug; ?>][]" value="<?php echo $cap; ?>" />
            <label for="wpp_<?php echo $r_slug;?>_<?php echo $cap;?>_capability">
            <?php _e($value, 'wpp');?>
            </label>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php $i++; endforeach; ?>
  </div>
</div>