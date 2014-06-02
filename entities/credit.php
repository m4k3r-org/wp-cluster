<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Credit' ) ) {

    class Credit extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'credit';

      /**
       *
       * @var type
       */
      public $_meta_key = 'creator';

      /**
       *
       * @var type
       */
      public $_photos;

      /**
       *
       * @var type
       */
      public $_videos;

      /**
       *
       * @param type $id
       * @param type $preload
       */
      public function __construct($id = null, $preload = true) {
        parent::__construct($id);

        if ( $preload ) {
          $this->_photos = $this->load_photos();

          $this->_videos = $this->load_videos();
        }
      }

      /**
       *
       * @return type
       */
      public function photos() {
        if ( empty( $this->_photos ) ) {
          $this->_photos = $this->load_photos();
        }

        return $this->_photos;
      }

      /**
       *
       * @return type
       */
      public function videos() {
        if ( empty( $this->_videos ) ) {
          $this->_videos = $this->load_videos();
        }

        return $this->_videos;
      }

      /**
       *
       */
      public function load_photos() {

        $args = wp_parse_args( $args, array(
          'post_type' => 'imagegallery',
          'posts_per_page' => -1,
          'meta_query' => array(
              array(
                  'key' => $this->_meta_key,
                  'value' => $this->_id
              )
          )
        ) );

        $query = get_posts( $args );

        $_photos = array();

        if ( !empty( $query ) ) {
          foreach( $query as $_photo ) {
            $_photos[] = new ImageGallery( $_photo->ID, false );
          }
        }

        return $_photos;

      }

      /**
       *
       */
      public function load_videos() {

        $args = wp_parse_args( $args, array(
          'post_type' => 'videoobject',
          'posts_per_page' => -1,
          'meta_query' => array(
              array(
                  'key' => $this->_meta_key,
                  'value' => $this->_id
              )
          )
        ) );

        $query = get_posts( $args );

        $_videos = array();

        if ( !empty( $query ) ) {
          foreach( $query as $_video ) {
            $_videos[] = new ImageGallery( $_video->ID, false );
          }
        }

        return $_videos;

      }
    }
  }
}