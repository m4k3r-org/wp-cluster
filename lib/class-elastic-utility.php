<?php
namespace UsabilityDynamics\wpElastic {

  if( !class_exists( 'UsabilityDynamics' ) ) {

    class Utility extends \UsabilityDynamics\Utility {

      /**
       * Load Schemas.
       *
       * @method load_schemas
       * @param null $path
       *
       * @return array
       */
      static public function load_schemas( $path = null ) {

        $_result = array();

        foreach( (array) explode( ';', $path || '' ) as $_path ) {

          if( !$_path || !is_dir( $_path ) ) {
            $_result[ ] = new \WP_Error( __( 'Unable to load defaults, directory does not exist.' ) );
          }

          if( is_dir( $path ) && $handle = opendir( $path ) ) {

            while( false !== ( $entry = readdir( $handle ) ) ) {

              if( $entry === '.' || $entry === '..' ) {
                continue;
              }

              $_slug = self::create_slug( str_replace( array( '-', '.json' ), '', $entry ) );

              $_result[ ] = \UsabilityDynamics\wpElastic::define( $_slug, file_get_contents( trailingslashit( $path ) . $entry ) );

            }

            closedir( $handle );

          }

        }

        return $_result;

      }

      /**
       * Generate Valid Index Name
       *
       * @param $data
       *
       * @return string
       */
      static public function indexName( $data ) {
        return self::create_slug( $data );
      }
    }

  }

}