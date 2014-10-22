<?php
/**
 * CLI
 *
 * @author potanin@UD
 * @class UsabilityDynamics\Cluster\CLI
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\CLI' ) || !class_exists( 'WP_CLI_Command' ) ) {

    /**
     * Manager WordPress cluster.
     *
     * @module Cluster
     */
    class CLI extends \WP_CLI_Command{

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
	     *     wp cluster test
	     *     wp cluster test all
	     *
	     * @synopsis [<stage>]
	     */
	    function test( $args ) {
		    $this->_init();
		    $type = false;

		    \WP_CLI::line( 'DB_NAME: '. DB_NAME );
		    \WP_CLI::line( 'DB_USER: '. DB_USER );
		    \WP_CLI::line( 'DB_HOST: '. DB_HOST );

	    }

	    /**
	     * Setup our limits
	     *
	     */
	    private function _init(){
	    }


    }

  }

}