<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\ImageGallery' ) ) {

    class ImageGallery extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'imagegallery';

      /**
       *
       * @var type
       */
      public $_meta_key = 'imagegallery';

      /**
       *
       * @var type
       */
      public $_event;

      /**
       *
       * @param type $id
       */
      public function __construct($id = null, $preload = true) {
        parent::__construct($id);

        if ( $preload ) {
          $this->_event = $this->load_event();
        }

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