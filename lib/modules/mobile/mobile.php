<?php
  /**
   * Flawless
   *
   * @author potanin@UD
   * @version 0.0.1
   * @namespace Flawless
   */
  namespace Flawless {

    /**
     * Mobile
     *
     * @author potanin@UD
     * @version 0.1.0
     * @class Mobile
     */
    class Mobile {

      /**
       * Version of class.
       *
       * @property version
       * @type {Number}
       */
      public $version = '0.1.1';

      /**
       * Constructor for the Mobile class.
       *
       * @author potanin@UD
       * @version 0.0.1
       * @method __construct
       *
       * @constructor
       * @for Mobile
       *
       * @param array $options
       */
      public function __construct( $options = array() ) {
        add_action( 'flawless::wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
      }

      /**
       * Frontned Mobile Scripts
       *
       * @param $flawless {Object} Instance context.
       *
       * @method wp_enqueue_scripts
       * @for Mobile
       */
      public function wp_enqueue_scripts( &$flawless ) {

        if ( wp_is_mobile() ) {
          wp_enqueue_script( 'jquery-touch-punch' );
        }

      }

    }

  }