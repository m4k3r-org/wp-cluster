<?php
/**
 * Plugin Name: EDM Vertical
 * Plugin URI: http://wpCloud.io
 * Description: EDM Vertical for wpCloud.io
 * Author: Usability Dynamics, Inc
 * Version: 1.1.3
 * Network: true
 * Vertical: true
 * Author URI: http://wpCloud.io
 * Text Domain: edm-vertical
 * Domain Path: /static/locale/
 *
 * GitHub Plugin URI: wpCloud/wp-vertical-edm
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
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */

if( file_exists( __DIR__ . '/vendor/libraries/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/libraries/autoload.php' );
}
if( class_exists( 'wpCloud\Vertical\EDM\Bootstrap' ) ) {
  new wpCloud\Vertical\EDM\Bootstrap;
}

