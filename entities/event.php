<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Event' ) ) {

    class Event extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'event';

      /**
       *
       * @var type
       */
      public $_meta_key = 'event';

      /**
       *
       * @var type
       */
      public $_venue;

      /**
       *
       * @var type
       */
      public $_artists;

      /**
       *
       * @var type
       */
      public $_promoters;

      /**
       *
       * @var type
       */
      public $_tour;

      /**
       *
       * @var type
       */
      public $_photo;

      /**
       *
       * @var type
       */
      public $_video;

      /**
       *
       * @param type $id
       */
      public function __construct( $id = null, $preload = true ) {
        parent::__construct( $id );

        if ( $preload ) {
          $this->_venue   = $this->load_venue();

          $this->_artists = $this->load_artists();

          $this->_tour    = $this->load_tour();

          $this->_photo   = $this->load_photo();

          $this->_video   = $this->load_video();

          $this->_promoters = $this->load_promoters();
        }

        $this->apply_formatting();
      }

      /**
       *
       * @return \DiscoDonniePresents\Venue
       */
      public function load_venue() {
        return new Venue( $this->meta('venue'), false );
      }

      /**
       *
       */
      public function load_photo() {

        $photo = get_posts( array(
            'post_type' => 'imagegallery',
            'meta_key' => 'event',
            'meta_value' => $this->_id,
            'posts_per_page' => 1
        ) );

        if ( !empty( $photo ) ) return new ImageGallery( $photo[0]->ID, false );

        return false;

      }

      /**
       *
       */
      public function load_video() {

        $video = get_posts( array(
            'post_type' => 'videoobject',
            'meta_key' => 'event',
            'meta_value' => $this->_id,
            'posts_per_page' => 1
        ) );

        if ( !empty( $video ) ) return new VideoObject( $video[0]->ID, false );

        return false;

      }

      /**
       *
       */
      private function load_artists() {
        $_artists = array();
        if ( $artists = $this->meta('artists') ) {

          foreach( (array)$artists as $artist_id ) {
            $_artists[] = new Artist( $artist_id, false );
          }

        }

        return $_artists;
      }

      /**
       *
       */
      private function load_promoters() {
        $_promoters = array();
        if ( $promoters = $this->meta('promoters') ) {

          foreach( (array)$promoters as $promoter_id ) {
            $_promoters[] = new Promoter( $promoter_id, false );
          }

        }

        return $_promoters;
      }

      /**
       *
       * @return \DiscoDonniePresents\Tour
       */
      private function load_tour() {
        return new Tour( $this->meta('tour') );
      }

      /**
       *
       */
      private function apply_formatting() {

        //** Date and time */
        $this->meta( 'eventDateHuman', date( 'l, F j, Y', strtotime( $this->meta( 'dateStart' ) ) ) );
        $this->meta( 'eventTimeHuman', date( 'g:i A', strtotime( $this->meta( 'dateStart' ) ) ) );

      }

      /**
       *
       * @return type
       */
      public function venue() {
        if ( empty( $this->_venue ) ) {
          $this->_venue = $this->load_venue();
        }

        return $this->_venue;
      }

      /**
       *
       * @return type
       */
      public function tour() {
        if ( empty( $this->_tour ) ) {
          $this->_tour = $this->load_tour();
        }

        return $this->_tour;
      }

      /**
       *
       * @return type
       */
      public function photo() {
        if ( empty( $this->_photo ) ) {
          $this->_photo = $this->load_photo();
        }

        return $this->_photo;
      }

      /**
       *
       * @return type
       */
      public function video() {
        if ( empty( $this->_video ) ) {
          $this->_video = $this->load_video();
        }

        return $this->_video;
      }

      /**
       *
       * @return type
       */
      public function artists( $format = 'link', $separator = ', ' ) {

        if ( empty( $this->_artists ) ) {

          $this->_artists = $this->load_artists();

          if ( empty( $this->_artists ) ) return false;

        }

        switch( $format ) {

          case 'link':

            $_titles = array();

            foreach( $this->_artists as $artist ) {
              $_titles[] = '<a href="'.get_permalink( $artist->post('ID') ).'">'.$artist->post('post_title').'</a>';
            }

            return implode( $separator, $_titles );

            break;

          default:

            return $this->_artists;

            break;

        }

        return false;

      }

      /**
       *
       * @return type
       */
      public function promoters( $format = 'link', $separator = ', ' ) {

        if ( empty( $this->_promoters ) ) {

          $this->_promoters = $this->load_promoters();

          if ( empty( $this->_promoters ) ) return false;

        }

        switch( $format ) {

          case 'link':

            $_titles = array();

            foreach( $this->_promoters as $promoter ) {
              $_titles[] = '<a href="'.get_permalink( $promoter->post('ID') ).'">'.$promoter->post('post_title').'</a>';
            }

            return implode( $separator, $_titles );

            break;

          default:

            return $this->_promoters;

            break;

        }

        return false;

      }

      /**
       *
       */
      public function genre() {
        $_genre = array();

        if ( empty( $this->_artists ) ) {

          $this->_artists = $this->load_artists();

        }

        foreach( $this->_artists as $artist ) {
          if ( $artist->taxonomies( 'genre', 'raw' ) ) {
            foreach( $artist->taxonomies( 'genre', 'raw' ) as $term ) {
              if ( !array_key_exists( $term->term_id, $_genre ) ) {
                $_genre[$term->term_id] = $term;
              }
            }
          }
        }

        return $this->termsToString( 'genre', $_genre, ', ' );

      }

      /**
       * ElasticSearch object of Event item
       * @return type
       */
      public function toElasticFormat() {

        $_object = array();

        $photo = wp_get_attachment_image_src( $this->meta('posterImage'), 'full' );
        $poster = wp_get_attachment_image_src( $this->meta('posterImage'), 'sidebar_poster' );

        $_object[ 'summary' ] = $this->post( 'post_title' );
        $_object[ 'url' ]     = get_permalink( $this->_id );
        $_object[ 'description' ] = $this->post( 'post_excerpt' );
        $_object[ 'start_date' ] = date( 'c', strtotime( $this->meta('dateStart') ) );
        $_object[ 'end_date' ] = date( 'c', strtotime( $this->meta('dateEnd') ) );
        $_object[ 'event_type' ] = $this->taxonomies( 'event-type', 'elasticsearch' );
        $_object[ 'age_restriction' ] = $this->taxonomies( 'age-limit', 'elasticsearch' );
        $_object[ 'photo' ] = $photo[0];
        $_object[ 'tickets' ] = $this->meta('urlTicket');
        $_object[ 'image' ] = array(
            'poster' => $poster[0]
        );

        $_object[ 'artists' ] = array();

        foreach( $this->_artists as $_artist ) {
          $_object[ 'artists' ][] = array(
              'name' => $_artist->post('post_title'),
              'url' => get_permalink( $_artist->post('ID') ),
              'genre' => $_artist->taxonomies( 'genre', 'elasticsearch' ) ? $_artist->taxonomies( 'genre', 'elasticsearch' ) : array()
          );
        }

        $_object[ 'promoters' ] = array();

        foreach( $this->_promoters as $_promoter ) {
          $_object[ 'promoters' ][] = array(
              'name' => $_promoter->post('post_title'),
              'url' => get_permalink( $_promoter->post('ID') )
          );
        }

        $city = $this->venue()->taxonomies('city', 'elasticsearch');
        $state = $this->venue()->taxonomies('state', 'elasticsearch');
        $country = $this->venue()->taxonomies('country', 'elasticsearch');
        $_object[ 'venue' ] = array(
          'name' => $this->venue()->post( 'post_title' ),
          'url' => get_permalink( $this->venue()->post( 'ID' ) ),
          'address' => array(
            'full' => $this->venue()->meta( 'locationAddress' ),
            'city' => $city[0],
            'state' => $state[0],
            'country' => $country[0],
            'geo' => array(
              'lat' => (float)$this->venue()->meta('latitude'),
              'lon' => (float)$this->venue()->meta('longitude')
            )
          )
        );

        $_object[ 'tour' ] = array(
          'name' => $this->tour()->post('post_title'),
          'url' => get_permalink( $this->tour()->post('ID') )
        );

        return $_object;

      }

    }

  }

}