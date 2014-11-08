<?php
/**
 * Plugin Name: Cloud CLI
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
		 * Display active themes accross the network including relative path.
		 *
		 * ## OPTIONS
		 *
		 * <stage>
		 * : Which migration stage we want to do, defaults to all
		 *
		 * ## EXAMPLES
		 *
		 *     wp cloud themes
		 *
		 * @synopsis [<stage>]
		 */
		function themes( $args ) {
			global $wpdb, $current_blog;

			//WP_CLI::line( 'DB_NAME: ' . DB_NAME );
			//WP_CLI::line( 'DB_USER: ' . DB_USER );
			//WP_CLI::line( 'DB_HOST: ' . DB_HOST );

			// WP_CLI::line( 'Generating list of sites with themes.' );

			$_results = array();

			foreach( (array) wp_get_sites( array( 'public' => true, 'network_id' => null ) ) as $site ) {

				switch_to_blog( $site[ 'blog_id' ] );

				$_template = wp_get_theme( get_option( 'template' ) );
				$_stylesheet= wp_get_theme( get_option( 'stylesheet' ) );
				$_status = array();

				$_templateActual = get_option( 'stylesheet' ) !== get_option( 'template' ) ? get_option( 'template' ) : null;

				if( $_templateActual && !is_dir( $_template->get_stylesheet_directory() ) ) {
					$_status[] = 'Template missing.';
				}

				if( !is_dir( $_stylesheet->get_stylesheet_directory() ) ) {
					$_status[] = 'Theme missing.';
				}

				$_network = (array) wp_get_network( $site['site_id'] );

				$_results[ $site['domain'] ] = array(
					'id' => $site['blog_id'],
					'network' => $_network['domain'],
					'site' => $site['domain'],
					'theme' => get_option( 'stylesheet' ), // . ' ' . $_stylesheet->get( 'Version' ),
					'template' => $_templateActual,
					'path' => str_replace( getcwd(), '.', $_stylesheet->get_stylesheet_directory() ),
					'status' => join( ' ', $_status )
				);

			}

			\WP_CLI\Utils\format_items( 'table', $_results,  array( 'id', 'network', 'site', 'theme', 'path', 'template', 'status' ) );

		}


		/**
		 * Display active themes accross the network including relative path.
		 *
		 * ## OPTIONS
		 *
		 * <stage>
		 * : Which migration stage we want to do, defaults to all
		 *
		 * ## EXAMPLES
		 *
		 *     wp cloud sites
		 *
		 * @synopsis [<stage>]
		 */
		function sites( $args ) {
			global $wpdb, $current_blog;

			//WP_CLI::line( 'DB_NAME: ' . DB_NAME );
			//WP_CLI::line( 'DB_USER: ' . DB_USER );
			//WP_CLI::line( 'DB_HOST: ' . DB_HOST );

			// WP_CLI::line( 'Generating list of sites with themes.' );

			$_results = array();

			foreach( (array) wp_get_sites( array( 'network_id' => null ) ) as $site ) {

				switch_to_blog( $site[ 'blog_id' ] );

				$_template = wp_get_theme( get_option( 'template' ) );
				$_stylesheet= wp_get_theme( get_option( 'stylesheet' ) );
				$_status = '';

				$_templateActual = get_option( 'stylesheet' ) !== get_option( 'template' ) ? get_option( 'template' ) : null;

				if( $_templateActual && !is_dir( $_template->get_stylesheet_directory() ) ) {
					$_status[] = 'Template missing.';
				}

				if( !is_dir( $_stylesheet->get_stylesheet_directory() ) ) {
					$_status[] = 'Theme missing.';
				}

				$_network = (array) wp_get_network( $site['site_id'] );

				$_status = $site['public'] ? 'Public' : null;

				$_results[ $site['domain'] ] = array(
					'id' => $site['blog_id'],
					'network' => $_network['domain'],
					'domain' => $site['domain'],
					'ip' => gethostbyname( $site['domain'] ),
					'status' => $_status
				);

			}

			\WP_CLI\Utils\format_items( 'table', $_results,  array( 'id','network', 'domain', 'ip', 'status' ) );

			//die( '<pre>' . print_r( $_results, true ) . '</pre>');

		}

		/**
		 * Display active themes accross the network including relative path.
		 *
		 * ## OPTIONS
		 *
		 * <stage>
		 * : Which migration stage we want to do, defaults to all
		 *
		 * ## EXAMPLES
		 *
		 *     wp cloud sites
		 *
		 * @synopsis [<stage>]
		 */
		function media( $args ) {
			global $wpdb, $current_blog;

			// WP_CLI::line( 'Generating list of sites with themes.' );

			$_results = array();

			foreach( (array) wp_get_sites( array( 'network_id' => null ) ) as $site ) {

				switch_to_blog( $site[ 'blog_id' ] );

				$_template = wp_get_theme( get_option( 'template' ) );
				$_stylesheet= wp_get_theme( get_option( 'stylesheet' ) );
				$_status = '';

				$_templateActual = get_option( 'stylesheet' ) !== get_option( 'template' ) ? get_option( 'template' ) : null;

				if( $_templateActual && !is_dir( $_template->get_stylesheet_directory() ) ) {
					$_status[] = 'Template missing.';
				}

				if( !is_dir( $_stylesheet->get_stylesheet_directory() ) ) {
					$_status[] = 'Theme missing.';
				}

				if( defined( 'WP_CLI' )  ) {
					//die( '<pre>' . print_r( get_current_site(), true ) . '</pre>');
					//die( '<pre>' . print_r( wp_upload_dir(), true ) . '</pre>');
				}

				if( class_exists( 'UsabilityDynamics\Veneer\Bootstrap' ) ) {
					//die(WP_VENEER_STORAGE);
					//global $wp_veneer;
					//die( '<pre>' . print_r( $wp_veneer->get(), true ) . '</pre>');
					//die( 'have:' . current_action() );

				}
				//$_wp_upload_dir = wp_upload_dir();
				//die( '<pre>' . print_r( $_wp_upload_dir , true ) . '</pre>');

				$_network = (array) wp_get_network( $site['site_id'] );
				$_path = wp_normalize_path( trailingslashit( WP_CONTENT_DIR ). get_option( 'upload_path' ) );

				$_results[ $site['domain'] ] = array(
					'id' => $site['blog_id'],
					'network' => $_network['domain'],
					'domain' => $site['domain'],
					'path' => $_path,
					'url' => get_option( 'upload_url_path' ),
					'size' => is_dir( $_path ) ? format_size( foldersize( $_path ) ) : '-'
				);

			}

			\WP_CLI\Utils\format_items( 'table', $_results,  array( 'id','network', 'domain', 'path', 'url', 'size' ) );

			//die( '<pre>' . print_r( $_results, true ) . '</pre>');

		}

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
		 *     wp cloud test
		 *     wp cloud test all
		 *
		 * @synopsis [<stage>]
		 */
		function test( $arg, $args ) {
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
		 *     wp cloud migrate
		 *     wp cloud migrate artist
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

	function foldersize($path) {
		$total_size = 0;

		if( !$path ) {
			return;
		}
		$files = scandir($path);
		$cleanPath = rtrim($path, '/'). '/';

		foreach( (array) $files as $t) {
			if ($t<>"." && $t<>"..") {
				$currentFile = $cleanPath . $t;
				if (is_dir($currentFile)) {
					$size = foldersize($currentFile);
					$total_size += $size;
				}
				else {
					$size = filesize($currentFile);
					$total_size += $size;
				}
			}
		}

		return $total_size;
	}

	function format_size($size) {
		global $units;

		$mod = 1024;

		for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
		}

		$endIndex = strpos($size, ".")+3;

		return substr( $size, 0, $endIndex).' '.$units[$i];
	}

	/** Add the commands from above */
	WP_CLI::add_command( 'cloud', 'DDP_Utility_CLI' );

}
