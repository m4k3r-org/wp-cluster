<?php
/**
 * Theme Settings
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Theme
 * @since 2.0.0
 */
namespace UsabilityDynamics\Festival {

  if( !class_exists( 'UsabilityDynamics\Festival\Settings' ) ) {

    class Settings extends \UsabilityDynamics\Settings {
    
      /**
       * Create Settings Instance
       *
       * @author potanin@UD
       * @since 2.0.0
       */
      static function define( $args = false ) {
        
        // STEP 1. Prepare options
        $thminfo = get_file_data( get_stylesheet_directory() . '/style.css', array( 'name' => 'Theme Name' ) );
        $name = sanitize_key( $thminfo[ 'name' ] );
        $key = $name == 'festival' ? 'festival_settings' : 'festival_settings_' . $key;
        
        // STEP 2. Instantiate Settings object
        $_instance = new Settings( \wp_parse_args( $args, array(
          "store" => "options",
          "key"   => $key,
        ) ) );
        
        // STEP 3. Prepare default data which is used for storing in DB.
        $_data = $_instance->get();
        if( empty( $_data ) ) {
          $_instance->set( $_instance->_get_system_settings() );
          //$_instance->commit();
        }
        
        // Return Instance.
        return $_instance;

      }
      
      /**
       * Get default Settings from schema
       *
       */
      private function _get_system_settings() {
        $short_path = '/static/schemas/default.settings.json';
        $file = get_stylesheet_directory() . $short_path;
        if( !file_exists( $file ) ) {
          $file = get_template_directory() . $short_path;
        }
        if( !file_exists( $file ) ) {
          return array();
        }
        return $this->_localize( json_decode( file_get_contents( $file ), true ) );
      }
      
      /**
       * Localization functionality.
       * Replaces array's l10n data.
       * Helpful for localization of data which is stored in JSON files ( see /schemas )
       *
       * @param type $data
       *
       * @return type
       * @author peshkov@UD
       */
      private function _localize( $data ) {

        if ( !is_array( $data ) ) return $data;

        //** The Localization's list. */
        $l10n = apply_filters( 'ud::theme::festival', array(
          //@TODO: replace all strings in schema files with l10n.{key} and set all locale data here. peshkov@UD
        ));

        //** Replace l10n entries */
        foreach ( $data as $k => $v ) {
          if ( is_array( $v ) ) {
            $data[ $k ] = self::_localize( $v );
          } elseif ( is_string( $v ) ) {
            if ( strpos( $v, 'l10n' ) !== false ) {
              preg_match_all( '/l10n\.([^\s]*)/', $v, $matches );
              if ( !empty( $matches[ 1 ] ) ) {
                foreach ( $matches[ 1 ] as $i => $m ) {
                  if ( key_exists( $m, $l10n ) ) {
                    $data[ $k ] = str_replace( $matches[ 0 ][ $i ], $l10n[ $m ], $data[ $k ] );
                  }
                }
              }
            }
          }
        }

        return $data;
      }

    }

  }

}



