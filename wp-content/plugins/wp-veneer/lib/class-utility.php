<?php
/**
 * Utility Class
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Utility' ) ) {

    /**
     * Class Utility
     *
     * @module Veneer
     */
    class Utility extends \UsabilityDynamics\Utility {

	    /**
	     *
	     * @source wp-admin/includes/misc.php
	     * @return mixed
	     */
	    function get_home_path( $extraPath = null ) {
		    $home    = set_url_scheme( get_option( 'home' ), 'http' );
		    $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
		    if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			    $wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			    $pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
			    $home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
			    $home_path = trailingslashit( $home_path );
		    } else {
			    $home_path = ABSPATH;
		    }

		    $basePath = str_replace( '\\', '/', $home_path );


		    if( $extraPath ) {
			    return wp_normalize_path( $basePath . '/' . $extraPath );
		    }

		    return $basePath;

	    }


    }

  }

}