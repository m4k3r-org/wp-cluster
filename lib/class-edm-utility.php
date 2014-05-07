<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical\EDM {

  class Utility extends \UsabilityDynamics\Utility {

    /**
     * Install Plugin from Repository.
     *
     * @todo Check if already installed before installing.
     * @param $name
     * @return object
     */
    public static function install_plugin( $name = false ) {

      if( !$name ) {
        return (object) array( 'meta' => array(), 'result' => array() );
      }

      include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
      include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
      include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

      $api = plugins_api( 'plugin_information', array(
        'slug' => $name,
        'fields' => array( 'sections' => false )
      ));

      $upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );

      // $upgrader->run( array() );
      $upgrader->install( $api->download_link );

      return (object) array( 'meta' => $api, 'result' => $upgrader->skin->result );

    }

  }

}