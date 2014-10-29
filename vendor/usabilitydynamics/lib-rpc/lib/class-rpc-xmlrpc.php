<?php
namespace UsabilityDynamics\RPC {

  /**
   * Prevent class redeclaration
   */
  if( !class_exists( 'UsabilityDynamics\RPC\XMLRPC' ) ) {

    /**
     * Base XML-RPC handler
     *
     * @author korotkov@ud
     */
    abstract class XMLRPC {

      /**
       * End point for client
       *
       * @var type
       */
      public $endpoint;

      /**
       * Available calls
       *
       * @var type
       */
      private $calls = Array();

      /**
       * Current methods' namespace
       *
       * @var type
       */
      public $namespace;

      /**
       * Root namespace for ALL methods. For WordPress is 'wp'.
       *
       * @var type
       */
      protected $root_namespace = 'wp';

      /**
       * Secret key
       *
       * @var type
       */
      public $secret_key;

      /**
       * Public Key
       *
       * @var type
       */
      public $public_key;

      /**
       * UI Object
       *
       * @var type
       */
      public $ui;

      /**
       *
       * @var type
       */
      public $useragent;

      /**
       * Construct
       *
       * @param type $namespace
       */
      function __construct( $server, $public_key, $useragent = 'UD XML-RPC SAAS Client', $namespace = 'ud' ) {
        //** End point */
        $this->endpoint = $server;

        //** Set namespace */
        $this->namespace = $namespace;

        //** Set public key */
        $this->public_key = $public_key;

        //** */
        $this->useragent = $useragent;

        //** Abort if no end point set */
        if( empty( $server ) ) return false;

        //** Init UI Object in any case */
        $this->ui = new API_UI( $this );

        //** Abort if no public key passed */
        if( empty( $public_key ) ) return false;

        //** Find all public methods in child classes and make them to be callable via XML-RPC */
        $reflector = new \ReflectionClass( $this );
        foreach( $reflector->getMethods( \ReflectionMethod::IS_PUBLIC ) as $method ) {
          if( $method->isUserDefined() && $method->getDeclaringClass()->name != get_class() ) {
            $this->calls[ ] = $method;
          }
        }

        //** Add methods to XML-RPC */
        add_filter( 'xmlrpc_methods', array( $this, 'xmlrpc_methods' ) );
      }

      /**
       * Register methods
       *
       * @param type $methods
       *
       * @return array
       */
      public function xmlrpc_methods( $methods ) {
        foreach( $this->calls as $call ) {
          //** Check if need multiple namespaces */
          $namespace = $call->getDeclaringClass()->name != 'UsabilityDynamics\UD_XMLRPC' ? $this->namespace . '.' : '';

          if( !empty( $namespace ) ) continue;

          //** Register ALL to point to dispatch */
          $methods[ $this->root_namespace . '.' . $namespace . $call->name ] = array( $this, "dispatch" );
        }

        return $methods;
      }

      /**
       * Call methods (__call similar)
       *
       * @global type $wp_xmlrpc_server
       *
       * @param type  $args
       *
       * @return string
       */
      public function dispatch( $args ) {
        //** Get method that is currently called */
        $call = $this->_get_called_method();

        //** Method should exist */
        if( method_exists( $this, $call ) ) {
          return call_user_func_array( array( $this, $call ), array( $args ) );
        } else {
          //** If method not found */
          return "Method not allowed";
        }
      }

      /**
       * Get method that was actually called to find it in child class
       *
       * @global $wp_xmlrpc_server
       * @return type
       */
      private function _get_called_method() {
        global $wp_xmlrpc_server;

        $call   = $wp_xmlrpc_server->message->methodName;
        $pieces = explode( ".", $call );

        //** Return last piece since there may be some namespaces */
        return array_pop( $pieces );
      }
    }
  }

}