<?php
/**
 * Plugin Name: WP-Cluster Database
 * Version: 0
 * Description: Thin class to handles database for WP-Cluster
 * Author: Usability Dynamics
 * Domain Path: WIP
 * Network: True
 * @uses UsabilityDynamics\Cluster\Database
 * @note Everything after "muplugins_loaded" action will have access to network-specific database.
 * @note Constants are set at this point.
 */
use UsabilityDynamics\Cluster as UDC;

/** Init our class for the database handling */
try{
  UDC\Utility::add_global_object_attribute( 'database', UDC\Database::init()->__construct() );
} catch( Exception $e ){
  nocache_headers();
  wp_die( '<h1>WordPress DB Error</h1><p>There has been a problem with the database: ' . $e->getMessage() . '</p>' );
}