<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Venue' ) ) {

    class Venue extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'venue';

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

        $this->transform();
      }

      /**
       *
       * @return type
       */
      public function events( $args = array() ) {

        if ( empty( $this->_events ) ) {
          $this->_events = $this->load_events( $args );
        }

        return $this->_events;
      }

      /**
       *
       */
      private function transform() {

        if ( $this->meta('locationGoogleMap') ) {

          $coords = explode( ',', $this->meta('locationGoogleMap') );

          $this->meta( 'latitude', $coords[0] );
          $this->meta( 'longitude', $coords[1] );

          $this->meta( 'geo_located', true );

        }

      }

    }

  }

}