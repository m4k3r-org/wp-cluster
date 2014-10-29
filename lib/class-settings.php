<?php
/**
 * Settings Access Controller
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Settings' ) ) {

    /**
     * Class Settings
     *
     * @module Cluster
     */
    class Settings extends \UsabilityDynamics\Settings {

      /**
       * Register Content Structure
       *
       * @param array|bool|string $data
       *
       * @return \Exception|\Cluster\Exception
       */
      public static function add_content_type( $data = false ) {

        // If a a file path is passed try to load
        if( $data && is_string( $data ) && file_exists( $data ) ) {
          $data = file_get_contents( $data );
        }

        try {

          // Convert into object
          $data = json_decode( $data, true );

          if( !$data ) {
            throw new Exception( 'Unreadable data.' );;
          }

          if( function_exists( 'register_post_type' ) ) {
            // register_post_type( $data );
          }

        } catch( Exception $e ) {
          return $e;
        }

      }

    }

  }

}