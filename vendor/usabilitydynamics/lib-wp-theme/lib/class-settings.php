<?php
/**
 * Theme Settings
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Theme
 * @since 2.0.0
 */
namespace UsabilityDynamics\Theme {

  if( !class_exists( 'UsabilityDynamics\Theme\Settings' ) ) {

    class Settings extends \UsabilityDynamics\Settings {

      /**
       * Create Settings Instance
       *
       * @since 2.0.0
       */
      static function define( $args = false, $data = false ) {

        // Instantiate Settings object
        $_instance = new Settings( Utility::parse_args( $args, array(
          "store" => "options",
          "key"   => 'theme::' . ( wp_get_theme()->get( 'Name' ) ),
        )));

        // Prepare default data which is used for storing in DB.
        if( !$_instance->get( 'configuration' ) ) {
          $_instance->set( $_instance->_get_system_settings() );
        }

        //** Always load structure to keep it updated */
        $_instance->set( 'structure', false );
        $_instance->set( $_instance->_get_system_structure() );

        if( !empty( $data ) ) {
          $_instance->set( $data );
        }

        // Return Instance.
        return $_instance;

      }

      /**
       * Get default Settings from schema
       *
       */
      private function _get_system_settings( $path = '/static/schemas/default.settings.json' ) {

        if( file_exists( $file = get_stylesheet_directory() . $path ) ) {
          return \UsabilityDynamics\Utility::l10n_localize( json_decode( file_get_contents( $file ), true ) );
        }

        return array();

      }

      /**
       *
       * @param type $path
       * @return type
       */
      private function _get_system_structure( $path = '/static/schemas/default.structure.json' ) {

        if( file_exists( $file = get_stylesheet_directory() . $path ) ) {
          return \UsabilityDynamics\Utility::l10n_localize( json_decode( file_get_contents( $file ), true ) );
        }

        return array();
      }

    }

  }

}



