<?php
namespace UsabilityDynamics\RPC {

  if( !class_exists( 'UsabilityDynamics\RPC\IXR_Client' ) && class_exists( '\IXR_Client' ) ) {

    /**
     * UD IXR extended from standard
     *
     * @author korotkov@ud
     */
    class IXR_Client extends \IXR_Client {

      /**
       * Construct
       *
       * @param type                         $server
       * @param                              $public_key
       * @param string                       $useragent
       * @param array                        $headers
       * @param bool|\UsabilityDynamics\type $path
       * @param int|\UsabilityDynamics\type  $port
       * @param int|\UsabilityDynamics\type  $timeout
       * @param bool                         $debug
       *
       * @internal param bool $secret_key
       * @author korotkov@ud
       */
      function __construct( $server, $public_key, $useragent = 'UD XML-RPC-SAAS Client', $headers = array(), $path = false, $port = 80, $timeout = 15, $debug = false ) {
        /**
         * No go w/o PK
         */
        if( empty( $public_key ) ) return false;

        $raas_user = get_user_by( 'login', 'raas@' . $_SERVER[ 'HTTP_HOST' ] );

        /**
         * IMPORTANT
         */
        parent::__construct( $server, $path, $port, $timeout );

        /**
         * Basic Authorization Header
         */
        $headers[ 'Authorization' ] = 'Basic ' . base64_encode( $public_key . ":" . get_user_meta( $raas_user->ID, md5( 'raas_secret' ), 1 ) );

        /**
         * Connection
         */
        $headers[ 'Connection' ] = 'keep-alive';

        /**
         * Set Callback URL Header
         */
        $headers[ 'X-Callback' ] = get_bloginfo( 'pingback_url' );

        /**
         * Set Sourse host
         */
        $headers[ 'X-Source-Host' ] = $_SERVER[ 'HTTP_HOST' ];

        /**
         * Remember PK
         */
        $this->public_key = $public_key;

        /**
         * Custom useragent
         */
        $this->useragent = $useragent;

        /**
         * Custom headers
         */
        $this->headers = $headers;

        /**
         * Enable debug
         */
        $this->debug = $debug;

      }

    }
  }

}