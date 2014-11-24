<?php
/**
 * Plugin Name: Stateless Media
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */
namespace wpCloud\Stateless {

	if( defined( 'WP_CLI' ) && class_exists( 'WP_CLI_Command' ) && class_exists( 'WP_CLI' ) && !class_exists( 'wpCloud\Stateless\Stateless_Media_Command' ) ) {


		/**
		 * DreamHost Migrate Plugin
		 *
		 * @package DH_Migrate_Command
		 * @subpackage commands/community
		 * @maintainer Mike Schroder
		 */
		class Stateless_Media_Command extends \WP_CLI_Command {

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

				$_test = \WP_CLI::launch($_cmd, false, true);

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
				$result = \WP_CLI::launch( \WP_CLIUtilsesc_cmd( "
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

		\WP_CLI::add_command( 'stateless media', 'wpCloud\Stateless\Stateless_Media_Command' );

	//die( '<pre>' . print_r( $_test, true ) . '</pre>');

	}
}