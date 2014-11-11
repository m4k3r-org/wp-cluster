<?php
namespace UsabilityDynamics\RPC {

  /**
   * Prevent class redeclaration
   */
  if( !class_exists( 'UsabilityDynamics\RPC\Products' ) && class_exists( 'UsabilityDynamics\RPC\UD' ) ) {
    /**
     * WP UD Products should initialize this server to listen for incoming commands connected to premium features management
     */
    class Products extends UD {

      /**
       * Add Premium Feature to the client's site
       *
       * @param type $request_data
       */
      public function add_feature( $request_data ) {
        /**
         * @todo: implement
         */
        return $this->namespace;
      }

      /**
       * Update Premium Feature on client's site
       *
       * @param type $request_data
       */
      public function update_feature( $request_data ) {
        /**
         * @todo: implement
         */
        return $this->namespace;
      }

      /**
       * Delete Premium Feature from client's site
       *
       * @param type $request_data
       */
      public function delete_feature( $request_data ) {
        /**
         * @todo: implement
         */
        return $this->namespace;
      }

    }
  }

}