<?php
/**
 *
 */
namespace UsabilityDynamics\wpElastic {

  if( !class_exists( 'UsabilityDynamics\wpElastic\Service' ) ) {

    class Service {

      /**
       * Singleton Instance Reference.
       *
       * @public
       * @static
       * @property $instance
       * @type {Object}
       */
      public static $queue = array();

      /**
       * Make Request to Elasticsearch Service.
       *
       * @param array  $data
       * @param string $url
       */
      static public function request( $data = array(), $url = '_bulk' ) {
        global $wp_version;

        if( empty( $data ) ) {
          return;
        }

        $body = array();

        foreach( (array) $data as $item ) {

          if( $item->action === 'delete' ) {}
          if( $item->action === 'udpate' ) {}
          if( $item->action === 'index' ) {}

        }

        $full_url = trailingslashit( wp_elastic( 'service.url' ) ) . trailingslashit( wp_elastic( 'service.index' ) ) . $url;

        if( !$data ) {
          return;
        }

        $body = array(

          // Delete Docs
          // '{ "delete" : { "_type" : "type1", "_id" : "1" } }',
          // '{ "delete" : { "_type" : "type1", "_id" : "2" } }',
          // '{ "delete" : { "_type" : "type1", "_id" : "3" } }',
          // '{ "delete" : { "_type" : "type1", "_id" : "4" } }',
          // '{ "delete" : { "_type" : "type1", "_id" : "5" } }',

          // Index Doc
          // '{ "index" : { "_type" : "type1", "_id" : "1" } }',
          // '{ "field1" : "value1", "field2" : "value2", "field3" : "value3" }',
          // '{ "index" : { "_type" : "type1", "_id" : "2" } }',
          // '{ "field1" : "value1", "field2" : "value2", "field3" : "value3" }',

          // Update Doc
          // '{ "update" : { "_id" : "1", "_type" : "type1" } }',
          // '{ "doc" : {"field2" : "value2"} }'

        );

        try {

          $result = wp_remote_request( $full_url, array(
            'method'      => 'POST',
            'timeout'     => 10,
            'redirection' => 5,
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
            'blocking'    => true,
            'headers'     => array(
              'x-access-token' => wp_elastic( 'service.secret_key' )
            ),
            'body'        => implode( "\n", $body ) . "\n"
          ));

        } catch( Exception $error ) {
          // echo $error->getMessage(), "\n";
        }

      }

      /**
       *
       * @method getQueue
       * @return array
       */
      static public function processQueue() {
        return self::request( self::getQueue() );
      }

      static public function getQueue() {
        return (array) Service::$queue;
      }

      /**
       * Add Object to Queue
       *
       * @method push
       * @param       $action
       * @param array $data
       *
       * @return array
       */
      static public function push( $action, $data = array() ) {

        Service::$queue[] = (object) array(
          'action' => $action,
          'time' => time(),
          'data' => (object) $data
        );

        return Service::$queue;

      }

    }

  }

}