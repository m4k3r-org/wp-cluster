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
      public $_meta_key = 'venue';

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
       */
      public function photos() {

        if ( empty( $this->_events ) ) {
          $this->_events = $this->load_events();
        }

        if ( empty( $this->_events ) ) return array();

        $_photos = array();

        foreach( $this->_events as $_event ) {
          if ( $_photo = $_event->photo() ) {
            $_photos[] = $_photo;
          }
        }

        return $_photos;

      }

      /**
       *
       */
      public function videos() {

        if ( empty( $this->_events ) ) {
          $this->_events = $this->load_events();
        }

        if ( empty( $this->_events ) ) return array();

        $_videos = array();

        foreach( $this->_events as $_event ) {
          if ( $_video = $_event->video() ) {
            $_videos[] = $_video;
          }
        }

        return $_videos;

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