<?php
/**
 * Plugin Name: CLI
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */

/** If we have wp-cli, load the file for our migration */
if( defined( 'WP_CLI' ) && class_exists( 'WP_CLI_Command' ) && !class_exists( 'DDP_Utility_CLI' ) ) {

	/**
	 * Our WP-CLI command to migrate their site
	 */
	class DDP_Utility_CLI extends WP_CLI_Command {

		/**
		 * Test stuff.
		 *
		 * ## OPTIONS
		 *
		 * <stage>
		 * : Which migration stage we want to do, defaults to all
		 *
		 * ## EXAMPLES
		 *
		 *     wp utility themes
		 *
		 * @synopsis [<stage>]
		 */
		function themes( $args ) {
			global $wpdb;

			WP_CLI::line( 'Generating list of sites with themes.' );

			foreach( (array) wp_get_sites() as $site ) {
				die( '<pre>' . print_r( $site, true ) . '</pre>');
			}


		}

	}

	/** Add the commands from above */
	WP_CLI::add_command( 'utility', 'DDP_Utility_CLI' );

}
