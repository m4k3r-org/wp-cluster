<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Tour' ) ) {

    class Tour extends Entity {

      /**
       *
       * @var type
       */
      public $_type = 'tour';

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
       */
      private function load_events() {

        $_events = array();

        $query = new \WP_Query( array(
          'post_type' => 'event',
          'posts_per_page' => -1,
          'meta_key' => 'tour',
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

    }

  }

}