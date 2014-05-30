<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Artist' ) ) {

    class Artist extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'artist';

      /**
       *
       * @var type 
       */
      public $_meta_key = 'artists';

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
    }
  }
}