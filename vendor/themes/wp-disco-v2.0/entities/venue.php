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

      /**
       *
       * @return type
       */
      public function toElasticFormat() {

        $_object = array();

        $photo = wp_get_attachment_image_src( $this->meta('imageLogo'), 'full' );
        $city = $this->taxonomies('city', 'elasticsearch');
        $state = $this->taxonomies('state', 'elasticsearch');
        $country = $this->taxonomies('country', 'elasticsearch');

        $_object[ 'summary' ] = $this->post( 'post_title' );
        $_object[ 'type' ]    = $this->taxonomies('venue-type', 'elasticsearch') ? $this->taxonomies('venue-type', 'elasticsearch') : array();
        $_object[ 'url' ]     = get_permalink( $this->post( 'ID' ) );
        $_object[ 'logo' ]    = is_array( $photo ) ? $photo[0] : '';
        $_object[ 'address' ] = array(
          'full' => $this->meta( 'locationAddress' ),
          'city' => $city[0],
          'state' => $state[0],
          'country' => $country[0],
          'geo' => array(
            'lat' => (float)$this->meta('latitude'),
            'lon' => (float)$this->meta('longitude')
          )
        );

        return $_object;

      }

    }

  }

}