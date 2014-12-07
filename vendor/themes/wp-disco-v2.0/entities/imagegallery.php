<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\ImageGallery' ) ) {

    class ImageGallery extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'imagegallery';

      /**
       *
       * @var type
       */
      public $_meta_key = 'imagegallery';

      /**
       *
       * @var type
       */
      public $_event;

      /**
       *
       * @var type
       */
      public $_images;

      /**
       *
       * @var type
       */
      public $_credit;

      /**
       *
       * @param type $id
       */
      public function __construct($id = null, $preload = true) {
        parent::__construct($id);

        if ( $this->event()->meta( 'posterImage' ) ) {
          $this->meta( 'primaryImageOfPage', $this->event()->meta( 'posterImage' ) );
        }

        if ( $preload ) {
          $this->_event = $this->load_event();

          $this->_images = $this->load_images();

          $this->_credit = $this->load_credit();
        }

      }

      /**
       *
       * @return type
       */
      public function load_event() {
        if ( $this->meta( 'event' ) ) {
          return new Event( $this->meta( 'event' ), false );
        }
        return false;
      }

      /**
       *
       */
      public function load_credit() {
        if ( $this->meta( 'creator' ) ) {
          return new Credit( $this->meta( 'creator' ), false );
        }
        return false;
      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function event() {

        if ( empty( $this->_event ) ) {
          $this->_event = $this->load_event();
        }

        return $this->_event;
      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function credit() {

        if ( empty( $this->_credit ) ) {
          $this->_credit = $this->load_credit();
        }

        return $this->_credit;
      }

      /**
       *
       * @return type
       */
      public function images() {

        if ( empty( $this->_images ) ) {
          $this->_images = $this->load_images();
        }

        return $this->_images;

      }

      /**
       * imageGallery to ES format
       */
      public function toElasticFormat() {

        $_object = array();

        $photo = wp_get_attachment_image_src( $this->meta('primaryImageOfPage'), 'full' );
        $poster = wp_get_attachment_image_src( $this->event()->meta( 'posterImage' ), 'sidebar_poster' );
        $small = wp_get_attachment_image_src( $this->meta('primaryImageOfPage'), 'hd_small' );

        $_object[ 'summary' ] = $this->post('post_title');
        $_object[ 'url' ] = get_permalink( $this->_id );
        $_object[ 'description' ] = $this->post('post_excerpt');
        $_object[ 'source_url' ] = $this->meta( 'isBasedOnUrl' );
        $_object[ 'photo' ] = is_array( $photo ) ? $photo[0] : '';
        $_object[ 'event_date' ] = date( 'c', strtotime( $this->event()->meta('dateStart') ) );
        $_object[ 'event_type' ] = $this->event()->taxonomies( 'event-type', 'elasticsearch' );
        $_object[ 'age_restriction' ] = $this->event()->taxonomies( 'age-limit', 'elasticsearch' );
        $_object[ 'image' ] = array(
          'poster' => is_array( $poster ) ? $poster[0] : '',
          'small'  => is_array( $small ) ? $small[0] : ''
        );

        $_object[ 'artists' ] = array();

        if ( $this->event()->artists( 'raw' ) ) {
          foreach( $this->event()->artists( 'raw' ) as $_artist ) {
            $_object[ 'artists' ][] = array(
                'name' => $_artist->post('post_title'),
                'url' => get_permalink( $_artist->post('ID') ),
                'genre' => $_artist->taxonomies( 'genre', 'elasticsearch' ) ? $_artist->taxonomies( 'genre', 'elasticsearch' ) : array()
            );
          }
        }

        $_object[ 'promoters' ] = array();

        if ( $this->event()->promoters( 'raw' ) ) {
          foreach( $this->event()->promoters( 'raw' ) as $_promoter ) {
            $_object[ 'promoters' ][] = array(
                'name' => $_promoter->post('post_title'),
                'url' => get_permalink( $_promoter->post('ID') )
            );
          }
        }

        if ( $this->event()->tour() ) {
          $_object[ 'tour' ] = array(
            'name' => $this->event()->tour()->post('post_title'),
            'url' => get_permalink( $this->event()->tour()->post('ID') )
          );
        } else {
          $_object[ 'tour' ] = array(
            'name' => '',
            'url' => ''
          );
        }

        $city = $this->event()->venue()->taxonomies('city', 'elasticsearch');
        $state = $this->event()->venue()->taxonomies('state', 'elasticsearch');
        $country = $this->event()->venue()->taxonomies('country', 'elasticsearch');
        $venue_type = $this->event()->venue()->taxonomies('venue-type', 'elasticsearch');
        $_object[ 'venue' ] = array(
          'name' => $this->event()->venue()->post( 'post_title' ),
          'type' => $venue_type[0],
          'url' => get_permalink( $this->event()->venue()->post( 'ID' ) ),
          'address' => array(
            'city' => $city[0],
            'state' => $state[0],
            'country' => $country[0]
          )
        );

        $_object[ 'credit' ] = array(
          'name' => $this->credit()->post( 'post_title' ),
          'url' => get_permalink( $this->credit()->post( 'ID' ) )
        );

        return $_object;

      }

    }

  }

}