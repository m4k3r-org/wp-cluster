<?php
/**
 *
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\wpElastic' ) ) {

    /**
     * Class wpElastic
     *
     * @package UsabilityDynamics
     */
    class wpElastic {

      /**
       * Get Single Schema or all schemas
       *
       * @author potanin@UD
       * @method getSchema
       * @param $name
       * @return array
       */
      public static function getSchema( $name ) {
        return \UsabilityDynamics\Model::getSchema( $name );
      }

      /**
       * Define a Model.
       *
       * @method define
       * @param string $model
       * @param array  $args
       * @return mixed
       */
      public static function define( $model = '', $args = array() ) {

        if( did_action( 'wp_loaded' ) ) {
          _doing_it_wrong( 'UsabilityDynamics\wpElastic\Bootstrap::define', __( 'Called too late.' ), '' );
        }

        if( is_string( $args ) ) {
          $args = json_decode( $args, true );
        }

        $args = Utility::parse_args( $args, array(
          'types' => array(),
          'meta' => array(),
          'taxonomies' => array()
        ));

        return \UsabilityDynamics\Model::define( $args );

      }

      /**
       * Update Available Schemas from api.wpElastic.io
       *
       * @method update
       * @return bool
       */
      public static function update() {

        return true;

      }

    }

  }

}