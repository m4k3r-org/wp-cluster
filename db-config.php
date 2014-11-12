<?php
/**
 * Introduction to HyperDB configuration
 *
 * HyperDB can manage connections to a large number of databases. Queries are
 * distributed to appropriate servers by mapping table names to datasets.
 *
 * A dataset is defined as a group of tables that are located in the same
 * database. There may be similarly-named databases containing different
 * tables on different servers. There may also be many replicas of a database
 * on different servers. The term "dataset" removes any ambiguity. Consider a
 * dataset as a group of tables that can be mirrored on many servers.
 *
 * Configuring HyperDB involves defining databases and datasets. Defining a
 * database involves specifying the server connection details, the dataset it
 * contains, and its capabilities and priorities for reading and writing.
 * Defining a dataset involves specifying its exact table names or registering
 * one or more callback functions that translate table names to datasets.
 */

/** Variable settings **/

/**
 * save_queries (bool)
 * This is useful for debugging. Queries are saved in $wpdb->queries. It is not
 * a constant because you might want to use it momentarily.
 * Default: false
 */
$wpdb->save_queries = false;

/**
 * persistent (bool)
 * This determines whether to use mysql_connect or mysql_pconnect. The effects
 * of this setting may vary and should be carefully tested.
 * Default: false
 */
$wpdb->persistent = true;

/**
 * max_connections (int)
 * This is the number of mysql connections to keep open. Increase if you expect
 * to reuse a lot of connections to different servers. This is ignored if you
 * enable persistent connections.
 * Default: 10
 */
$wpdb->max_connections = 10;

/**
 * check_tcp_responsiveness
 * Enables checking TCP responsiveness by fsockopen prior to mysql_connect or
 * mysql_pconnect. This was added because PHP's mysql functions do not provide
 * a variable timeout setting. Disabling it may improve average performance by
 * a very tiny margin but lose protection against connections failing slowly.
 * Default: true
 */
$wpdb->check_tcp_responsiveness = true;

$wpdb->add_database(array(
	'host'     => 'localhost',
	'user'     => 'root',
	'password' => 'root',
	'name'     => 'global',
	'dataset'  => 'all',
	'read'     => 1,
	'write'    => 0,
	'timeout'  => 0.2
));

$wpdb->add_database(array(
	'host'     => 'cloud.wpcloud.io',
	'user'     => 'root',
	'password' => 'jmnbaoqnwygoeitf',
	'name'     => 'global',
	'dataset'  => 'all',
	'read'     => 2,
	'write'    => 1
));


$wpdb->add_callback(function( $query, $wpdb ) {

	die( 'callback:' . $query );

});
