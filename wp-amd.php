<?php
/**
 * Plugin Name: WP-AMD
 * Plugin URI: http://UsabilityDynamics.com/plugins/wp-amd/
 * Description: JS and CSS handling.
 * Author: Usability Dynamics, Inc
 * Version: 0.1.0
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
 * @version 0.1.0
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 * @module WP-Preview
 */

// Load bootstrap
require_once( __DIR__ . '/lib/class-bootstrap.php' );

// Intialize Plugin
if( class_exists( 'UsabilityDynamics\AMD\Bootstrap' ) ) {
  new UsabilityDynamics\AMD\Bootstrap();
}