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
       */
      private function load_events() {

        $_events = array();

        $query = new \WP_Query( array(
          'post_type' => 'event',
          'posts_per_page' => -1,
          'meta_key' => 'venue',
          'meta_value' => $this->_id
        ) );

        if ( !is_wp_error( $query ) && !empty( $query->posts ) ) {

          foreach( $query->posts as $event ) {
            $_events[] = new Event( $event->ID, false );
          }

          return $_events;

        }

        return false;

      }

      /**
       *
       * @return type
       */
      public function events() {
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