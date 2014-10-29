<?php
namespace UsabilityDynamics\RPC {

  if( !class_exists( 'UsabilityDynamics\RPC\UD' ) && class_exists( 'UsabilityDynamics\RPC\XMLXMLRPC' ) ) {
    /**
     * UD XML-RPC Server Library. May be extended with a class with public methods.
     *
     * @author korotkov@ud
     */
    class UD_XMLRPC extends XMLRPC {

      /**
       * Validate all incoming requests using callback to this method.
       *
       * @param md5 string $request_data
       *
       * @return boolean
       */
      public function validate( $request_data ) {
        if( md5( $_SERVER[ 'HTTP_HOST' ] . $this->public_key ) == $request_data[ 0 ] ) {
          return true;
        }

        return false;
      }

      /**
       * Notificator
       *
       * @param type $request_data
       */
      public function notify( $request_data ) {
        /**
         * @todo: Implement
         */
        return array( 'Notification received' );
      }

      /**
       * Test call to check if request is correct. Sent data will be returned in decrypted state.
       *
       * @return mixed
       */
      public function test( $request_data ) {
        return $request_data;
      }

      public function register( $request_data ) {
        return $request_data;
      }

    }
  }

}