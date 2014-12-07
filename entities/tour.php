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
      public $_meta_key = 'tour';

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
       * @return type
       */
      public function toElasticFormat() {

        $_object = array();

        $photo = wp_get_attachment_image_src( $this->meta('posterImage'), 'full' );

        $_object[ 'summary' ] = $this->post( 'post_title' );
        $_object[ 'url' ]     = get_permalink( $this->_id );
        $_object[ 'logo' ]    = is_array( $photo ) ? $photo[0] : '';

        return $_object;

      }

    }
  }
}