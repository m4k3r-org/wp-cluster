<?php
/**
 * Our custom mult-tenant database driver
 *
 * @author Reid Williams
 * @class UsabilityDynamics\Cluster\Database
 */
namespace UsabilityDynamics\Cluster {

	if ( ! class_exists( 'UsabilityDynamics\Cluster\WPDB' ) ) {
		/**
		 * We're extending the base wpdb class so that
		 * we can override certain functions
		 */
		class WPDB extends \wpdb {

			/**
			 * Whether we've managed to successfully connect at some point
			 *
			 * @since 3.9.0
			 * @access private
			 * @var bool
			 */
			private $has_connected = false;

			/**
			 * @param bool $allow_bail
			 *        // mysqli::ssl_set
			 *
			 * @return bool
			 */
			public function db_connect( $allow_bail = true ) {

				if ( function_exists( 'mysql_pconnect' ) ) {
					$this->dbh = mysql_pconnect( $this->dbhost, $this->dbuser, $this->dbpassword );
				}

				$this->use_mysqli = false;

				if ( ! $this->dbh && $allow_bail ) {
					wp_die( 'Unable to connect to DB.' );
				}

				// $db_selected = mysql_select_db( $this->dbname, $this->dbh );

				$this->has_connected = true;
				$this->set_charset( $this->dbh );
				$this->set_sql_mode();
				$this->ready = true;
				$this->select( $this->dbname, $this->dbh );

				return false;

			}

			/**
			 * We need to change our blog prefix so that
			 * our own capabilities will work properly
			 */
			function get_blog_prefix( $blog_id = null ) {
				/** If we don't have CLUSTER_PREFIX defined, we can just use the parent function */
				if ( ! defined( 'CLUSTER_PREFIX' ) ) {
					return parent::get_blog_prefix( $blog_id );
				}

				return $this->base_prefix;
			}

			/**
			 * Sets the table prefix for the WordPress tables.
			 *
			 * @modified williams@ud
			 * We need to ensure that the proper prefixes are getting applied to these tables
			 *
			 * @since 2.5.0
			 *
			 * @param string $prefix Alphanumeric name for the new prefix.
			 * @param bool $set_table_names Optional. Whether the table names, e.g. wpdb::$posts, should be updated or not.
			 *
			 * @return string|WP_Error Old prefix or WP_Error on error
			 */
			function set_prefix( $prefix, $set_table_names = true ) {
				global $wp_cluster;
				/** If we don't have CLUSTER_PREFIX defined, we can just use the parent function */
				if ( ! defined( 'CLUSTER_PREFIX' ) ) {
					return parent::set_prefix( $prefix, $set_table_names );
				}
				if ( preg_match( '|[^a-z0-9_]|i', $prefix ) ) {
					return new WP_Error( 'invalid_db_prefix', 'Invalid database prefix' );
				}
				$old_prefix = is_multisite() ? '' : $prefix;
				if ( isset( $this->base_prefix ) ) {
					$old_prefix = $this->base_prefix;
				}
				$this->base_prefix = $prefix;
				if ( $set_table_names ) {
					if ( is_object( $wp_cluster ) && isset( $wp_cluster->database ) ) {
						/** Make sure that the global tables have the proper prefix */
						$_global_tables = array_unique( array_merge( array_keys( $this->tables( 'global' ) ), $wp_cluster->database->get( 'wpc_global_tables' ) ) );
						foreach ( $_global_tables as $table ) {
							$this->{$table} = CLUSTER_PREFIX . $table;
						}
					} else {
						foreach ( $this->tables( 'global' ) as $table => $prefixed_table ) {
							$this->$table = $prefixed_table;
						}
					}
					if ( is_multisite() && empty( $this->blogid ) ) {
						return $old_prefix;
					}
					$this->prefix = $this->get_blog_prefix();
					foreach ( $this->tables( 'blog' ) as $table => $prefixed_table ) {
						$this->$table = $prefixed_table;
					}
					foreach ( $this->tables( 'old' ) as $table => $prefixed_table ) {
						$this->$table = $prefixed_table;
					}
				}

				return $old_prefix;
			}
		}
	}

	if ( ! class_exists( 'UsabilityDynamics\Cluster\Database' ) ) {
		/**
		 * This is our WP-Cluster database driver that is directly
		 * initialized and handled in the db.php dropin
		 */
		class Database {

			/**
			 * This variable holds the global tables, that should be written/read to
			 * on the cluster database
			 *
			 * NOTE:
			 * We're including the BuddyPress tables for possible future use, they'll
			 * be commented out - but don't erase them.
			 */
			private $wpc_global_tables = array(
				'blogs',
				'blog_versions',
				'registration_log',
				'signups',
				'site',
				'sitecategories',
				'sitemeta',
				'usermeta',
				'users'
				/**
				 * 'bp_activity_sitewide',
				 * 'bp_activity_user_activity',
				 * 'bp_activity_user_activity_cached',
				 * 'bp_friends',
				 * 'bp_groups',
				 * 'bp_groups_groupmeta',
				 * 'bp_groups_members',
				 * 'bp_groups_wire',
				 * 'bp_messages_messages',
				 * 'bp_messages_notices',
				 * 'bp_messages_notices',
				 * 'bp_messages_recipients',
				 * 'bp_messages_threads',
				 * 'bp_messages_threads',
				 * 'bp_notifications',
				 * 'bp_user_blogs',
				 * 'bp_user_blogs_blogmeta',
				 * 'bp_user_blogs_comments',
				 * 'bp_user_blogs_posts',
				 * 'bp_xprofile_data',
				 * 'bp_xprofile_fields',
				 * 'bp_xprofile_groups',
				 * 'bp_xprofile_wire',
				 * 'bp_activity',
				 * 'bp_activity_meta' */
			);

			/**
			 * Holds the db by reference
			 */
			private $db;

			/**
			 * Our current credentials
			 */
			private $creds = array(
				'DB_NAME'     => false,
				'DB_PREFIX'   => false,
				'DB_USER'     => false,
				'DB_PASSWORD' => false,
				'DB_HOST'     => false
			);

			/**
			 * Ok, this variable stores if we're actually a cluster'd database
			 */
			private $config = array(
				'filter_queries' => false,
				'multi_db'       => false
			);

			/**
			 * Getter
			 */
			function get( $name ) {
				if ( isset( $this->{$name} ) ) {
					return $this->{$name};
				}

				return false;
			}

			/**
			 * Connects to a DB, possibly overriding the $wpdb object
			 *
			 * @todo I believe the same "Invalid credentials" error is thrown even when credentials are valid but the database is simply empty. Verify. - potanin@UD
			 *
			 * @param array $creds The DB info we're connecting with
			 *
			 * @returns boolean Whether or not it was successful
			 * @throws \Exception Throws error if it can't connect
			 */
			function _connect_to_db( $creds ) {
				global $wpdb, $table_prefix;
				/** Backup those objects */
				$backups = array(
					'wpdb'         => $wpdb,
					'table_prefix' => $table_prefix,
					'creds'        => $this->creds
				);

				/** Make sure we have all the creds we need */
				foreach ( array_keys( $this->creds ) as $key ) {
					if ( ! ( isset( $creds[ $key ] ) && ! empty( $creds[ $key ] ) ) && $key !== 'DB_PASSWORD' ) {
						throw new \Exception( 'Not enough credentials to connect to DB' );
					}
				}

				/** Overwrite the globals */
				$table_prefix = $creds[ 'DB_PREFIX' ];

				/** Create the DB */
				$wpdb = new WPDB( $creds[ 'DB_USER' ], $creds[ 'DB_PASSWORD' ], $creds[ 'DB_NAME' ], $creds[ 'DB_HOST' ] );

				/** Make sure we have a connection */
				if ( ! ( $wpdb && is_object( $wpdb ) && $wpdb->ready ) ) {
					throw new \Exception( 'Invalid credentials, cannot connect to DB.' );
				}

				/** Ok, we made it, lets run the func to generate the table names */
				wp_set_wpdb_vars();
				/** Ok, we made it - bail out the backups */
				foreach ( $backups as $key => $value ) {
					unset( $backups[ $key ] );
				}
				/** Set the blog ID to null */
				$wpdb->set_blog_id( null, null );
				/** Set the 'this' variable as well */
				$this->db    =& $wpdb;
				$this->creds = $creds;

				/** Return true */

				return true;
			}

			/**
			 * Sanitizes select query's table names by adding database prefixes.
			 *
			 * This function solves the issue when we have JOINs in a select query,
			 * which connects global tables from global database. For instance:
			 *
			 * SELECT * FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.post_author WHERE p.ID = 2;
			 *
			 * @author WPMU Plugins, Multi-DB Plugin
			 * @since 3.1.2
			 * @filter query
			 *
			 * @access public
			 * @global array $global_tables The array of globals tables.
			 *
			 * @param string $query The initial query.
			 *
			 * @return string Sanitized query.
			 */
			public function _filter_query( $query ) {
				global $blog_id;
				//echo "\r\n\r\n::" . $query . "==";
				$global_tables = $this->wpc_global_tables;
				/** Trim it up */
				$query = trim( $query );
				/** Here, I'm going to strip out the "_##_ pattern where that number represents the blog ID */
				if ( stripos( $query, '_' . $blog_id . '_' ) ) {
					$query = preg_replace( '/' . DB_PREFIX . $blog_id . '_([^ ]*)/i', DB_PREFIX . '$1', $query );
				}
				/** Look through all global tables and add global database prefix if it has been found */
				foreach ( $global_tables as $table ) {
					//echo ';;;;' . $table . ';;;;';
					$query = preg_replace( '/`' . CLUSTER_PREFIX . $table . '`/i', '`' . CLUSTER_NAME . '`.`' . CLUSTER_PREFIX . $table . '`', $query );
					$query = preg_replace( '/ ' . CLUSTER_PREFIX . $table . ' /i', ' ' . CLUSTER_NAME . '.' . CLUSTER_PREFIX . $table . ' ', $query );
				}
				//echo "==" . $query . "::\r\n\r\n";
				/** Return it! */

				return $query;
			}

			/**
			 * Returns all tables available in the database.
			 *
			 * @filter _filter_tables_to_repair
			 * @todo Do this, not sure when this is called!
			 * @access public
			 *
			 * @param array $tables The initial array of tables.
			 *
			 * @return array The array of database tables.
			 */
			public function _filter_tables_to_repair( $tables ) {

				/** Haven't changed anything below this line yet - williams@ud */
				$blogs_ids = $this->get_col( "SELECT blog_id FROM {$this->base_prefix}blogs WHERE deleted = 0 AND spam = 0 AND archived = '0'" );
				foreach ( $blogs_ids as $blog_id ) {
					$new_tables = $this->get_col( "SHOW TABLES LIKE '{$this->base_prefix}{$blog_id}_%';" );
					if ( $new_tables && is_array( $new_tables ) && count( $new_tables ) > 0 ) {
						$tables = array_merge( $tables, $new_tables );
					}
				}

				return $tables;
			}

			/**
			 * Runs after sunrise.php is loaded, and may try to connect to a specific blog's database
			 * @todo Hook this up for the wpcloud environment!
			 */
			function _maybe_connect_to_blog_db() {
				global $wp_veneer, $current_blog;

				// Added this because below the method assumes there's a public get_config(). This logic is flawed since the files are mixed up between veneer and cluster.
				if( !method_exists( $wp_veneer->config, 'get_config' ) ) {
					return false;
				}

				/** @todo We should really be getting this from the DB */
				if ( ! ( $blog_db_options = $wp_veneer->config->get_config( 'options/sites/' . $current_blog->domain, 'db' ) ) ) {
					/** Make sure we have a connection */
					throw new \Exception( 'Could not retreive the blog\'s options - it may not be configured properly.' );
				}

				/** For each of the options, check to see if one of them is a constant */
				foreach ( $blog_db_options as $key => $value ) {
					if ( defined( $value ) ) {
						$blog_db_options[ $key ] = constant( $value );
					}
				}

				/** If we have our blog options, lets go ahead and set them up */
				if ( $blog_db_options ) {
					/** Loop through them and define the vars */
					foreach ( $blog_db_options as $key => $value ) {
						if ( ! defined( $key ) ) {
							define( $key, $value );
						}
					}
				}

				/** Ok, we should have our DB info setup now */
				$creds = array(
					'DB_NAME'     => ( defined( 'DB_NAME' ) ? DB_NAME : false ),
					'DB_PREFIX'   => ( defined( 'DB_PREFIX' ) ? DB_PREFIX : false ),
					'DB_USER'     => ( defined( 'DB_USER' ) ? DB_USER : false ),
					'DB_PASSWORD' => ( defined( 'DB_PASSWORD' ) ? DB_PASSWORD : false ),
					'DB_HOST'     => ( defined( 'DB_HOST' ) ? DB_HOST : false )
				);

				/** Attempt to connect with these creds */
				$this->_connect_to_db( $creds );

				/** Well, the only thing left to do is create those actions */
				//add_filter( 'tables_to_repair', array( $this, '_filter_tables_to_repair' ) );

				add_filter( 'query', array( $this, '_filter_query' ) );

				$this->config[ 'filter_queries' ] = true;
			}

			/**
			 * Initializes the databases, actions, and filters for this to properly operate
			 *
			 * @param bool $do_stuff Whether we should actually do initialization( needed for 'init' )
			 *
			 * @throws \Exception
			 */
			function __construct( $do_stuff = true ) {
				global $wp_cluster;

				if ( ! ( is_bool( $do_stuff ) && $do_stuff ) ) {
					return;
				}

				/**
				 * Let's set our initial creds, looking for CLUSTER_ before DB_
				 */
				$creds = array(
					'DB_NAME'     => ( defined( 'CLUSTER_NAME' ) ? CLUSTER_NAME : defined( 'DB_NAME' ) ? DB_NAME : false ),
					'DB_PREFIX'   => ( defined( 'CLUSTER_PREFIX' ) ? CLUSTER_PREFIX : defined( 'DB_PREFIX' ) ? DB_PREFIX : false ),
					'DB_USER'     => ( defined( 'CLUSTER_USER' ) ? CLUSTER_USER : defined( 'DB_USER' ) ? DB_USER : false ),
					'DB_PASSWORD' => ( defined( 'CLUSTER_PASSWORD' ) ? CLUSTER_PASSWORD : defined( 'DB_PASSWORD' ) ? DB_PASSWORD : false ),
					'DB_HOST'     => ( defined( 'CLUSTER_HOST' ) ? CLUSTER_HOST : defined( 'DB_HOST' ) ? DB_HOST : false )
				);

				/** Attempt to connect with these creds */
				$this->_connect_to_db( $creds );
				/** If we have a CLUSTER_USER, we should set the multi_db config */
				if ( defined( 'CLUSTER_USER' ) ) {
					$this->config[ 'multi_db' ] = true;
				}

				/** Ok, now we're going to return, as we should be good for now */

				return $this;
			}

			/**
			 * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
			 */
			static public function init() {
				return new self( false );
			}

		}

		/**
		 * If we don't have a wp_cluster object, we should make one, but it should be a child of wp_veneer
		 */
		global $wp_veneer;
		if ( ! is_object( $wp_veneer ) ) {
			$wp_veneer = new \stdClass();
		}
		/** Add to our object, if we don't have the cluster object */
		if ( ! isset( $wp_veneer->cluster ) ) {
			$wp_veneer->cluster = new \stdClass();
		}
		/** Now, add on our DB object, finally */
		if ( ! isset( $wp_veneer->cluster->db ) ) {
			$wp_veneer->cluster->db = Database::init()->__construct();
		}

	}

}