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
if ( defined( 'WP_CLI' ) && class_exists( 'WP_CLI_Command' ) && ! class_exists( 'DDP_CLI' ) ) {

	/**
	 * Our WP-CLI command to migrate their site
	 */
	class DDP_CLI extends WP_CLI_Command {

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
		 *     wp ddp test
		 *     wp ddp test all
		 *
		 * @synopsis [<stage>]
		 */
		function test( $args ) {
			$this->_init();
			$type = false;

			WP_CLI::line( 'DB_NAME: ' . DB_NAME );
			WP_CLI::line( 'DB_USER: ' . DB_USER );
			WP_CLI::line( 'DB_HOST: ' . DB_HOST );

		}

		/**
		 * Attempts the migration
		 *
		 * ## OPTIONS
		 *
		 * <stage>
		 * : Which migration stage we want to do, defaults to all
		 *
		 * ## EXAMPLES
		 *
		 *     wp ddp migrate
		 *     wp ddp migrate artist
		 *
		 * @synopsis [<stage>]
		 */
		function migrate( $args ) {
			$this->_init();
			$type = false;

			if ( isset( $args ) && is_array( $args ) && count( $args ) ) {
				$type = array_shift( $args );
			}

			/** All we're going to do is call the import command */
			$migration = new \EDM\Application\Migration( $type );
		}

		/**
		 * Setup our limits
		 */
		private function _init() {
			set_time_limit( 0 );
			ini_set( 'memory_limit', '2G' );
		}

	}

	/** Add the commands from above */
	WP_CLI::add_command( 'ddp', 'DDP_CLI' );

}
