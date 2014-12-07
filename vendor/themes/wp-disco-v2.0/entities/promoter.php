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

      /**
       *
       * @return type
       */
      public function toElasticFormat() {

        $_object = array();

        $photo = wp_get_attachment_image_src( $this->meta('logoImage'), 'full' );

        $_object[ 'summary' ] = $this->post( 'post_title' );
        $_object[ 'url' ]     = get_permalink( $this->_id );
        $_object[ 'official_url' ] = $this->meta( 'officialLink' ) ? $this->meta( 'officialLink' ) : '';
        $_object[ 'social_urls' ]  = $this->meta( 'socialLinks' ) ? $this->meta( 'socialLinks' ) : array();
        $_object[ 'logo' ]         = is_array( $photo ) ? $photo[0] : '';

        return $_object;

      }
    }
  }
}