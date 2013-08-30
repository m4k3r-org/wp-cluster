<?php


if(!W3TC) {
  return;
}

/**
  * Name: W3 Total Cache
  * Description: Extra functionality for W3 Total Cache
  * Author: Usability Dynamics, Inc.
  * Version: 1.0
  *
  */

//add_action('flawless::init_lower', array('Flawless_W3TC', 'flawless_init'));
//add_filter('flawless_minified_arg', array('Flawless_W3TC', 'flawless_minified_arg'), 10, 2);

class Flawless_W3TC {


  function flawless_init() {
  
    add_action('wp_ajax_flawless_delete_option_clearcache', array('flawless_theme', 'delete_option_clearcache'));    

    //** Add 'Clear W3 Total Cache' notice */
    add_action('admin_notices', array('flawless_theme', 'show_clear_W3_total_cache_notice'));    
    
  }
  

  /**
   * Add minified=true argument to jQuery to avoid W3TC from combining / minifying it
   *
   * @since Flawless 0.2.3
   */
  function flawless_minified_arg($default, $args) {

    if($args['handle'] == 'jquery') {
      return true;
    }

    return $default;

  }
  
  

  /*
   * Adds option for showing notice on Theme Settings updating
   * And shows Notice 'Clear W3 Cache' if W3 Total Cache plugin is used
   *
   */
  static function show_clear_W3_total_cache_notice() {

      if(class_exists('W3_Plugin_TotalCache')) {

          // Checks Flawless Settings Request and Add option
          if(wp_verify_nonce($_REQUEST['_wpnonce'], 'flawless_settings')) {
              add_option('flawless_theme_clear_cache_notice', 'true');
          }

          $clear_notice = get_option('flawless_theme_clear_cache_notice');
          if(!empty($clear_notice)) {
              $note = '';
              ob_start();
              ?>
              <p><?php _e('Looks like Flawless theme Settings were updated. But W3 Total Cache plugin is used. Please, clear cache to be sure that the changes are involved '); ?>
              <input type="button" value="Clear Page Cache" onclick="flawless_delete_clearcache_option();document.location.href = 'admin.php?page=w3tc_general&amp;flush_pgcache';" class="button " />
              <?php _e('or') ?>
              <input type="button" value="Hide Notice" onclick="flawless_delete_clearcache_option();flawless_hide_notice();" class="button " />
              </p>
              <script type="text/javascript">
                 function flawless_delete_clearcache_option() {
                      jQuery.ajax({
                          url: ajaxurl,
                          async: false,
                          type: 'POST',
                          data: 'action=flawless_delete_option_clearcache'
                      });
                  }

                 function flawless_hide_notice() {
                      jQuery('#flawless_w3_total_cache_notice').slideToggle('slow', function(){
                          jQuery(this).remove();
                      });

                  }
              </script>
              <?php
              $note .= ob_get_contents();
              ob_end_clean();

              // Print notice
              echo sprintf('<div id="flawless_w3_total_cache_notice" class="updated fade">%s</div>', $note);
          }
      } else {
          // Try to delete option
          delete_option('flawless_theme_clear_cache_notice');
      }
  }
  

    /*
   * Ajax function. Deletes 'flawless_theme_clear_cache_notice' option,
   * which is used for showing notice to clear W3 Cache if W3 Total Cache plugin is used.
   */
 static function delete_option_clearcache () {
    delete_option('flawless_theme_clear_cache_notice');
    echo json_encode(array('status'=>'success'));
    exit();
  }  

}