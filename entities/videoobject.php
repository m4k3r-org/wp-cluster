<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Video' ) ) {

    class VideoObject extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'videoobject';

      /**
       *
       * @var type
       */
      public $_meta_key = 'video';

      /**
       *
       * @var type
       */
      public $_event;

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

        if ( $preload ) {
          $this->_event = $this->load_event();

          $this->_credit = $this->load_credit();
        }

      }

      /**
       *
       */
      public function load_credit() {
        return new Credit( $this->meta( 'creator' ), false );
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
      public function load_event() {
        return $this->_event = new Event( $this->meta( 'event' ), false );
      }

      /**
       *
       * @param type $args
       * @return type
       */
      public function event( $args = array() ) {

        if ( empty( $this->_event ) ) {
          $this->_event = $this->load_event();
        }

        return $this->_event;
      }

      /**
       *
       * @return type
       */
      public function toElasticFormat() {

        $_object = array();

        $photo = wp_get_attachment_image_src( $this->meta('primaryImageOfPage'), 'full' );
        $poster = wp_get_attachment_image_src( $this->meta('primaryImageOfPage'), 'sidebar_poster' );
        $small = wp_get_attachment_image_src( $this->meta('primaryImageOfPage'), 'hd_small' );

        $_object[ 'summary' ] = $this->post('post_title');
        $_object[ 'url' ] = get_permalink( $this->_id );
        $_object[ 'source_url' ] = $this->meta( 'isBasedOnUrl' );
        $_object[ 'description' ] = $this->post('post_excerpt');
        $_object[ 'photo' ] = $photo[0];
        $_object[ 'event_date' ] = date( 'c', strtotime( $this->event()->meta('dateStart') ) );
        $_object[ 'event_type' ] = $this->event()->taxonomies( 'event-type', 'elasticsearch' );
        $_object[ 'image' ] = array(
          'poster' => $poster[0],
          'small'  => $small[0]
        );

        $_object[ 'artists' ] = array();

        foreach( $this->event()->artists( 'raw' ) as $_artist ) {
          $_object[ 'artists' ][] = array(
              'name' => $_artist->post('post_title'),
              'url' => get_permalink( $_artist->post('ID') ),
              'genre' => $_artist->taxonomies( 'genre', 'elasticsearch' ) ? $_artist->taxonomies( 'genre', 'elasticsearch' ) : array()
          );
        }

        $_object[ 'promoters' ] = array();

        foreach( $this->event()->promoters( 'raw' ) as $_promoter ) {
          $_object[ 'promoters' ][] = array(
              'name' => $_promoter->post('post_title'),
              'url' => get_permalink( $_promoter->post('ID') )
          );
        }

        $_object[ 'tour' ] = array(
          'name' => $this->event()->tour()->post('post_title'),
          'url' => get_permalink( $this->event()->tour()->post('ID') )
        );

        $city = $this->event()->venue()->taxonomies('city', 'elasticsearch');
        $state = $this->event()->venue()->taxonomies('state', 'elasticsearch');
        $_object[ 'venue' ] = array(
          'name' => $this->event()->venue()->post( 'post_title' ),
          'url' => get_permalink( $this->event()->venue()->post( 'ID' ) ),
          'address' => array(
            'city' => $city[0],
            'state' => $state[0],
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