<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Tour' ) ) {

    class Tour extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'tour';

      /**
       *
       * @var type
       */
      public $_events;

      /**
       *
       * @param type $id
       */
      public function __construct($id = null, $preload = true) {
        parent::__construct($id);

        if ( $preload ) {
          $this->_events = $this->load_events();
        }

      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function events( $args = array() ) {

        if ( empty( $this->_events ) ) {
          $this->_events = $this->load_events( $args );
        }

        return $this->_events;
      }

    }

  }

}