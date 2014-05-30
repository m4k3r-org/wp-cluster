<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Promoter' ) ) {

    class Promoter extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'promoter';

      /**
       *
       * @var type
       */
      public $_meta_key = 'promoters';

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