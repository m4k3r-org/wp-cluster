<?php
/**
 * Plugin Name: WP-Eventbrite
 * Text Domain: wp-eventbrite
 * Description: Eventbrite Attendees Notifications
 * Author: Usability Dynamics, Inc
 * Version: 0.0.1
 * Author URI: http://UsabilityDynamics.com
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
   * Returns WP_Evenbrite object
   *
   * @author peshkov@UD
   */
  function get_wp_eventbrite( $key = false, $default = null ) {
    if( class_exists( '\DiscoDonniePresents\Eventbrite\Bootstrap' ) ) {
      $instance = \DiscoDonniePresents\Eventbrite\Bootstrap::get_instance();
      return $key ? $instance->get( $key, $default ) : $instance;
    }
    return false;
  }

}

// Initialize.
get_wp_eventbrite();


