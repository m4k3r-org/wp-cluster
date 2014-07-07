<?php
/**
 * Plugin Name: WP-Site Bootstrap
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1
 * Author URI: http://usabilitydynamics.com
 */

/** Init the application */
if( class_exists( 'EDM\Application\Bootstrap' ) ) {
  new EDM\Application\Bootstrap;
}

/** Ok, this action is hackish so we can load this functionality only on DDP! */
add_action( 'init', function(){
  if( defined( 'DOMAIN_CURRENT_SITE' ) && DOMAIN_CURRENT_SITE == 'discodonniepresents.com' ){
    define( 'WP_ELASTIC_SCHEMAS_DIR', WP_LIBRARY_DIR . '/wpcloud/wp-vertical-edm/static/schemas' );
    require_once( WP_LIBRARY_DIR . '/wpcloud/wp-vertical-edm/vertical-edm.php' );
    require_once( WP_PLUGIN_DIR . '/wp-elastic/wp-elastic.php' );
  }
} );

/**
 * DDP Migration API endpoint
 *
 * @url http://discodonniepresents.com/api/v1/migrate-ddp
 */
UsabilityDynamics\API::define( '/v1/migrate-ddp', array(
  'scopes' => array( 'manage_options' ),
  'handler' => function(){
      /** Create the object */
      $migration = new \EDM\Application\Migration();
      /** Bail */
      if( !$migration->dump_logs ){
        UsabilityDynamics\API::send( array(
          'ok' => true,
          'message' => 'Upgrade ran.',
          'data' => $migration->logs
        ) );
      }
    }
) );

/** If we have wp-cli, load the file for our migration */
if( defined( 'WP_CLI' ) && WP_CLI ) {


  /**
   * Our WP-CLI command to migrate their site
   */
  class DDP_CLI extends WP_CLI_Command {

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

      if( isset( $args ) && is_array( $args ) && count( $args ) ){
        $type = array_shift( $args );
      }

      /** All we're going to do is call the import command */
      $migration = new \EDM\Application\Migration( $type );
    }

    /**
     * Setup our limits
     */
    private function _init(){
      set_time_limit( 0 );
      ini_set( 'memory_limit', '2G' );
    }

  }

  /** Add the commands from above */
  WP_CLI::add_command( 'ddp', 'DDP_CLI' );

}
