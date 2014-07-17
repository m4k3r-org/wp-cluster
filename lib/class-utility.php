<?php
/**
 * Helper Class
 * Contains the list of most useful functions
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Utility' ) ) {

    /**
     * Eventbrite Helper
     *
     * @author Usability Dynamics
     */
    class Utility extends \UsabilityDynamics\Utility {
    
      /**
       * Returns specific schema from file.
       *
       * @param string $name Filename without EXT
       * @param array $l10n Locale data
       * @author peshkov@UD
       */
      public static function get_schema( $name = '', $l10n = array() ) {
        if( !empty( $name ) && file_exists( $file = dirname( __DIR__ ) . '/static/schemas/' . $name . '.json' ) ) {
          return (array)self::l10n_localize( json_decode( file_get_contents( $file ), true ), $l10n );
        }
        return array();
      }

    }
  
  }

}
