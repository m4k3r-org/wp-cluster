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

        if ( !empty( $video ) ) return new Video( $video[0]->ID, false );

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

          default: break;

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

    }

  }

}