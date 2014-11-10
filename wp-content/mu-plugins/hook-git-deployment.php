<?php
/**
 * Plugin Name: Git Post Deployment Hook
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Configures things after a GitHub branch is updated.
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.0
 * Author URI: http://usabilitydynamics.com
 *
 * curl http://drop.ud-dev.com/wp-content/mu-plugins/hook-git-deployment.php
 * php
 */
namespace EDM\Application\Hooks {

	function setConstants() {
		/** Display errors */
		error_reporting( E_ALL );
		ini_set( 'display_errors', 1 );
		ini_set( 'display_startup_errors', 1 );

		/** Define our GitHub hook secret */
		define( 'GITHUB_HOOK_SECRET', 'WJn2jTwtTXRB7uWJn2jTwtTXRB7u' );

		/** The prefix we're using for commands in the commit message */
		define( 'COMMIT_COMMAND_PREFIX', 'drop' );

		/** Define our source DB Details */
		define( 'SOURCE_DB_HOST', 'rds.wpcloud.io' );
		define( 'SOURCE_DB_USER', 'edm_production' );
		define( 'SOURCE_DB_PASSWORD', 'psxcazdhbpoxbkfl' );
		define( 'SOURCE_DB_NAME', 'edm_production' );

		/** Define our DB details */
		define( 'DB_HOST', 'rds.uds.io' );
		define( 'DB_USER', 'dud_edm_cluster' );
		define( 'DB_PASSWORD', 'asdF9UjhgimzV4' );
		define( 'DB_NAME', 'dud_edm_cluster' );
		define( 'DB_PREFIX', 'edm_' );
		define( 'DB_DUMP', '/home/dud/db/edm_cluster.sql' );

		/** Define our local environment variables */
		define( 'GIT_ROOT', '/home/dud/public_html' );
		define( 'GIT_BRANCH', 'develop' );

		/** Define our new domain suffix */
		define( 'DOMAIN_SCHEME', 'http://' );
		define( 'DOMAIN_SUFFIX', '.drop.ud-dev.com' );

	}

	/**
	 * WebHook
	 *
	 */
	function fefreshCode() {

		/** Start our try/catch block */
		try {

			header( 'Cache-Control:private' );
			header( 'Pragma:no-cache' );

			/** Ok, first get the payload */
			$raw_payload = file_get_contents( 'php://input' );
			$payload     = json_decode( $raw_payload, true );

			if ( ! isset( $_SERVER[ 'HTTP_X_GITHUB_EVENT' ] ) ) {
				header( "HTTP/1.0 401 Unauthorized" );
				die( 'Expected GitHub web hook not detected.' );
			}

			/** Make sure we're a PUSH event */
			if ( $_SERVER[ 'HTTP_X_GITHUB_EVENT' ] != 'push' ) {
				die( 'done' );
			}

			/** Ok, we have the push event, make sure that we have a valid GitHub key */
			$hash = 'sha1=' . hash_hmac( 'sha1', $raw_payload, GITHUB_HOOK_SECRET );
			if ( $hash != $_SERVER[ 'HTTP_X_HUB_SIGNATURE' ] ) {
				throw new Exception( 'Invalid key.' );
			}

			/** Make sure we're on the correct branch */
			if ( $payload[ 'ref' ] != 'refs/heads/' . GIT_BRANCH ) {
				echo "not the correct branch\n";
				die( 'done' );
			}

			/** We're going to look through the commits, and determine if we have to update the database */
			$commands = array(
				'refreshdb'
			);

			$to_run   = array();

			foreach ( $payload[ 'commits' ] as $commit ) {
				foreach ( $commands as $command ) {
					if ( stripos( $commit[ 'message' ], '[' . COMMIT_COMMAND_PREFIX . ' ' . $command . ']' ) !== false ) {
						if ( ! in_array( $command, $to_run ) ) {
							$to_run[ ] = $command;
						}
					}
				}
			}

			sleep( 5 );

			/** First thing we're going to do is pull from github */
			echo "Running: " . "git -C " . GIT_ROOT . " fetch" . "\n";
			exec( "git -C " . GIT_ROOT . " fetch" );

			echo "Running: " . "git -C " . GIT_ROOT . " reset --hard origin/" . GIT_BRANCH . "\n";
			exec( "git -C " . GIT_ROOT . " reset --hard origin/" . GIT_BRANCH );

			echo "Running: " . "git -C " . GIT_ROOT . " pull" . "\n";
			exec( "git -C " . GIT_ROOT . " pull" );

			/** Ok, we're going to go through the commands, and run them */
			foreach ( $to_run as $command ) {

				if ( ! method_exists( 'EDM\Application\Hooks\Commands', $command ) ) {
					echo "Not running command " . $command . " because the file doesn't exist\n";
				} else {
					echo "Running command: " . "nohup hhvm " . __FILE__ . " " . $command . " 2>&1 &" . ".\n";
					exec( "nohup hhvm " . __FILE__ . " " . $command . " 2>&1 &" );
				}
			}

			/** If we didn't run any commands, print it out */
			if ( ! count( $to_run ) ) {
				echo "No extra commands were run.\n";
			}

			die( 'Hook completed successfully.' );

		} catch( Exception $e ) {

			/** If we get here, we're just going to display an error message */
			header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 500 Internal Server Error', true, 500 );
			die();

		}

	}

	/**
	 * CLI Command
	 *
	 * @param null $command
	 */
	function runCommand( $command = null ) {
		Commands::$command();
	}

	class Commands {

		static function refreshdb() {

			/** Setup various vars */
			$db_prefix = DB_PREFIX;

			/** Backup the latest DB */
			echo "Backing up latest MySQL dump from " . SOURCE_DB_NAME . " on " . SOURCE_DB_HOST . ".\n";
			exec( "mysqldump -u " . SOURCE_DB_USER . " -p'" . addcslashes( SOURCE_DB_PASSWORD, "'" ) . "' -h " . SOURCE_DB_HOST . " " . SOURCE_DB_NAME . " > " . DB_DUMP );

			/** So, the first thing we're going to do is run a native mysql import to restore the db */
			echo "Restoring latest MySQL dump to " . DB_NAME . " on " . DB_HOST . ".\n";
			exec( "mysql -u " . DB_USER . " -p'" . addcslashes( DB_PASSWORD, "'" ) . "' -h " . DB_HOST . " " . DB_NAME . " < " . DB_DUMP );

			/** Connect to the DB */
			$db = new \mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

			if( $db->connect_errno ){
				die( "Failed to connect to the database: " . $db->connect_error );
			}

			/** Changing all passwords - depreciating for now - williams@ud
			echo "updating all user passwords\n";
			$db->query( "UPDATE {$db_prefix}users SET user_pass = MD5( 'password' )" ); */

			/** Empty global transients */
			echo "Emptying global transients.\n";
			$db->query( "DELETE FROM {$db_prefix}options WHERE option_name LIKE '%_transient_%'" );
			$db->query( "DELETE FROM {$db_prefix}sitemeta WHERE meta_key LIKE '%_transient_%'" );

			/** Go ahead and get all the blogs */
			echo "Updating all blogs...\n";
			$res = $db->query( "SELECT * FROM {$db_prefix}blogs" );

			while( $blog = $res->fetch_assoc() ) {

				/** Setup some vars */
				$blog_id = $blog[ 'blog_id' ];
				$blog_prefix = $db_prefix . $blog_id . '_';
				$new_domain = str_ireplace( '.', '-', $blog[ 'domain' ] ) . DOMAIN_SUFFIX;
				$new_domain_url = DOMAIN_SCHEME . $new_domain . '/';

				echo "- Setting domain for $blog_id to $new_domain and url to $new_domain_url. \n";

				/** First, update the blog name and blog options */
				$db->query( "UPDATE {$db_prefix}blogs SET domain = '{$new_domain}' WHERE blog_id = {$blog_id}" );
				$db->query( "UPDATE {$blog_prefix}options SET option_value = '{$new_domain_url}' WHERE option_name = 'siteurl' OR option_name = 'home'" );

				/** Remove blog transients */
				$db->query( "DELETE FROM {$blog_prefix}options WHERE option_name LIKE '%_transient_%'" );

			}

			/** We're done */
			echo "Database refresh done.\n";

		}

	}

	// Handle WebHook
	if( isset( $_SERVER[ 'REQUEST_URI' ] ) && $_SERVER[ 'REQUEST_URI' ] === '/wp-content/mu-plugins/hook-git-deployment.php' ) {
		setConstants();
		fefreshCode();
	}

	// Handle CLI
	if( isset( $_SERVER[ 'HHVM_LIB_PATH' ] ) && $_SERVER[ 'SCRIPT_FILENAME' ] === '/home/dud/public_html/.develop/wp-content/mu-plugins/hook-git-deployment.php' ) {
		setConstants();
		runCommand( $_SERVER[ 'argv' ][1] );
	}

}