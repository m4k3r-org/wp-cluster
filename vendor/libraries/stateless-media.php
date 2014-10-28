<?php
/**
 * Plugin Name: Various Hacks
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */

add_action( 'template_redirect', function() {
	// die( current_action() . ' - ' . time() );
}, 12 );

add_action( 'init', function() {
	global $wp_veneer;

	// Ignore "develop" and "hotfix" environment-branches.
	if( defined( 'WP_ENV' ) && in_array( WP_ENV, array( 'develop', 'hotfix' ) ) && function_exists( 'newrelic_ignore_transaction' ) ) {
		newrelic_ignore_transaction();
	}

	if( isset( $wp_veneer ) && method_exists ($wp_veneer, 'set' ) ) {
		$wp_veneer->set( 'rewrites.login', false );
		$wp_veneer->set( 'rewrites.manage', false );
		$wp_veneer->set( 'rewrites.api', true );

		$wp_veneer->set( 'static.enabled', false );
		$wp_veneer->set( 'cdn.enabled', false );
		$wp_veneer->set( 'cache.enabled', false );

		$wp_veneer->set( 'media.shard.enabled', false );
		$wp_veneer->set( 'scripts.shard.enabled', false );
		$wp_veneer->set( 'styles.shard.enabled', false );
	}

});

add_filter( 'upgrader_pre_download', function( $false, $package, $this ) {

	return $false;
}, 10, 3 );

add_filter( 'downloading_package', function( $package ) {

	return $package;
});

add_filter( 'automatic_updates_is_vcs_checkout', function( $checkout, $context ) {

	return $checkout;
}, 10, 2);

add_filter( 'auto_update_plugin', function( $update, $item ) {

	return $update;
}, 10, 2);

add_filter( 'auto_update_theme', function( $update, $item ) {

	return $update;
}, 10, 2);

add_filter( 'auto_update_translation', function( $update, $item ) {
	return $update;
}, 10, 2);


if( defined( 'WP_CLI' ) ) {

	//$command = WP_CLI::get_root_command();
	//$command->add_subcommand( $subcommand_name, $subcommand );

	add_action( 'wp', function() {

	});
	//die( '<pre>' . print_r( $command, true ) . '</pre>' );

	/**
	 * DreamHost Migrate Plugin
	 *
	 * @package DH_Migrate_Command
	 * @subpackage commands/community
	 * @maintainer Mike Schroder
	 */
	class TEST_Migrate_Command extends WP_CLI_Command {

		/**
		 * Backup entire WordPress install, including core, plugins and database.
		 *
		 * @subcommand upload
		 *
		 * @alias upload
		 *
		 * @param array $args
		 * @param array $assoc_args
		 */
		function upload( $args, $assoc_args ) {
			WP_CLI::line( 'uploading...' );

			//
			// -L ./log.log
			//$_cmd = '/Users/andy.potanin/devtools/gsutil/gsutil -a cp /Users/andy.potanin/Sites/www.wpcloud.io/wp-content/public/www.wpcloud.io/stereo-cat.jpg gs://media.wpcloud.io';
			$_cmd = '/Users/andy.potanin/devtools/gsutil/gsutil -m cp -a public-read -L gce.log -v /Users/andy.potanin/Sites/www.wpcloud.io/wp-content/public/www.wpcloud.io/stereo-cat.jpg gs://media.wpcloud.io';
			// $_cmd = '/Users/andy.potanin/devtools/gsutil/gsutil -m cp -a public-read -v /Users/andy.potanin/Sites/www.wpcloud.io/wp-content/public/www.wpcloud.io/stereo-cat.jpg gs://media.wpcloud.io';

			$_test = WP_CLI::launch($_cmd, false, true);

			WP_CLI::print_value( $_test->return_code );




		}

		/**
		 * Backup entire WordPress install, including core, plugins and database.
		 *
		 * @subcommand backup
		 *
		 * @alias mv
		 *
		 * @param array $args
		 * @param array $assoc_args
		 * @synopsis [backup_filename] [--no-db] [--db-name=<filename>]
		 */
		function backup( $args, $assoc_args ) {
			$filename         = $dbname = null;
			$backup_directory = '../';

			// If a filename isn't specified, default to "Site's Title.tar.gz".
			if ( empty( $args ) ) {
				$filename = $backup_directory . get_bloginfo() . '.tar.gz';
			} else {
				$filename = $args[ 0 ];
			}

			// If --no-db is specified, don't include the database in backup
			if ( ! isset( $assoc_args[ 'no-db' ] ) ) {
				$dbname = isset( $assoc_args[ 'db-name' ] ) ? $assoc_args[ 'db-name' ] : 'database_backup.sql';

				WP_CLI::run_command( array( 'db', 'export', $backup_directory . $dbname ), array() );
			}

			// Using esc_cmd to automatically escape parameters.
			// We can't use --exclude-vcs, because it's not available on OSX.
			WP_CLI::line( "Backing up to $filename ..." );
			$result = WP_CLI::launch( WP_CLIUtilsesc_cmd( "
			tar
				--exclude '.git'
				--exclude '.svn'
				--exclude '.hg'
				--exclude '.bzr'
				-czf %s . -C %s %s
		", $filename, $backup_directory, $dbname ), false );

			// If we created a database backup, remove the temp file.
			if ( $dbname && ! unlink( $backup_directory . $dbname ) ) {
				WP_CLI::warning( "Couldn't remove temporary database backup, '$dbname'." );
			}

			if ( 0 == $result ) {
				WP_CLI::success( "Backup Complete." );
			} else {
				WP_CLI::error( "Backup Failed." );
			}
		}
	}

	WP_CLI::add_command( 'gcs', 'TEST_Migrate_Command' );

	//die( '<pre>' . print_r( $_test, true ) . '</pre>');

}