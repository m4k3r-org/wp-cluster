<?php
/**
 * Plugin Name: WP-Eventbrite
 * Text Domain: wp-eventbrite
 * Description: Eventbrite Attendees Notifications
 * Author: Usability Dynamics, Inc
 * Version: 0.1.4
 * Author URI: http://UsabilityDynamics.com
 * GitHub Plugin URI: DiscoDonniePresents/wp-eventbrite
 *
 * Copyright 2011-2014  Usability Dynamics, Inc.   (email : info@UsabilityDynamics.com)
 *
 * Created by Usability Dynamics, Inc
 * (website: UsabilityDynamics.com       email : info@UsabilityDynamics.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

if( !function_exists( 'get_wp_eventbrite' ) ) {

  if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once ( __DIR__ . '/vendor/autoload.php' );
  }

  define( 'WP_EVENTBRITE_DIR', plugin_dir_path( __FILE__ ) );
  define( 'WP_EVENTBRITE_URL', plugin_dir_url( __FILE__ ) );

  /**
   * Determines if Composer autoloader is included and modules classes are uptodate
   *
   * @author peshkov@UD
   */
  function wp_eventbrite_check_autoload() {
    global $_wp_eventbrite_errors;
    $_wp_eventbrite_errors = $_wp_eventbrite_errors === NULL ? array() : $_wp_eventbrite_errors;
    if( !class_exists( '\DiscoDonniePresents\Eventbrite\Bootstrap' ) || !class_exists( '\DiscoDonniePresents\Eventbrite\Utility' ) ) {
      $_wp_eventbrite_errors[] = __( 'Composer Autoloader does not exist or have to be updated to the latest version or there is no vendor directory.' );
      return false;
    }
    $dependencies = \DiscoDonniePresents\Eventbrite\Utility::get_schema( 'schema.dependency' );
    if( !empty( $dependencies ) && is_array( $dependencies ) ) {
      foreach( $dependencies as $module => $classes ) {
        if( !empty( $classes ) && is_array( $classes ) ) {
          foreach( $classes as $class => $v ) {
            if( !class_exists( $class ) ) {
              $_wp_eventbrite_errors[] = sprintf( __( 'Module <b>%s</b> is not installed or the version is old, class <b>%s</b> does not exist.' ), $module, $class );
            }
            if ( '*' != trim( $v ) && ( !property_exists( $class, 'version' ) || $class::$version < $v ) ) {
              $_wp_eventbrite_errors[] = sprintf( __( 'Module <b>%s</b> should be updated to the latest version, class <b>%s</b> must have version <b>%s</b> or higher.' ), $module, $class, $v );
            }
          }
        }
      }
    }
    if( !empty( $_wp_eventbrite_errors ) ) {
      return false;
    }
    return true;
  }
  
  /**
   * Renders admin notes in case there are errors on plugin init
   *
   * @author peshkov@UD
   */
  function wp_eventbrite_admin_notices() {
    global $_wp_eventbrite_errors;
    if( !empty( $_wp_eventbrite_errors ) && is_array( $_wp_eventbrite_errors ) ) {
      $errors = '<ul style="list-style:disc inside;"><li>' . implode( '</li><li>', $_wp_eventbrite_errors ) . '</li></ul>';
      $message = sprintf( __( '<p><b>WP-Eventbrite</b> is active but can not be initialized due to following errors:</p> %s' ), $errors );
      echo '<div class="error fade" style="padding:11px;">' . $message . '</div>';
    }
  }
  
  /**
   * Returns WP_Evenbrite object
   *
   * @author peshkov@UD
   */
  function get_wp_eventbrite( $key = false, $default = null ) {
    $instance = \DiscoDonniePresents\Eventbrite\Bootstrap::get_instance();
    return $key ? $instance->get( $key, $default ) : $instance;
  }
  
  //** Initialize. */
  add_action( 'admin_notices', 'wp_eventbrite_admin_notices' );
  if( wp_eventbrite_check_autoload() ) {
    get_wp_eventbrite();
  }

}





