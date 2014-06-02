<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Video' ) ) {

    class Video extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'videoobject';

      /**
       *
       * @var type
       */
      public $_meta_key = 'video';

      /**
       *
       * @var type
       */
      public $_event;

      /**
       *
       * @var type
       */
      public $_credit;

      /**
       *
       * @param type $id
       */
      public function __construct($id = null, $preload = true) {
        parent::__construct($id);

        if ( $preload ) {
          $this->_event = $this->load_event();

          $this->_credit = $this->load_credit();
        }

      }

      /**
       *
       */
      public function load_credit() {
        return new Credit( $this->meta( 'creator' ), false );
      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function credit() {

        if ( empty( $this->_credit ) ) {
          $this->_credit = $this->load_credit();
        }

        return $this->_credit;
      }

      /**
       *
       * @return type
       */
      public function load_event() {
        return $this->_event = new Event( $this->meta( 'event' ), false );
      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function event( $args = array() ) {

        if ( empty( $this->_event ) ) {
          $this->_event = $this->load_event();
        }

        return $this->_event;
      }

    }

  }

}