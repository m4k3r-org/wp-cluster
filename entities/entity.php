<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Entity' ) ) {

    class Entity {

      /**
       *
       * @var type
       */
      public $_id;

      /**
       *
       * @var type
       */
      public $_post;

      /**
       *
       * @var type
       */
      public $_meta;

      /**
       *
       * @var type
       */
      public $_taxonomies;

      /**
       *
       * @param type $id
       */
      public function __construct( $id = null ) {

        if ( !$id ) {
          $id = get_the_ID();
        }

        $this->_id   = $id;

        $this->_post = $this->load_post();

        $this->_meta = $this->load_meta();

        $this->_taxonomies = $this->load_taxonomies();

      }

      /**
       *
       * @return type
       */
      private function load_post() {
        return get_post( $this->_id );
      }

      /**
       *
       * @return type
       */
      private function load_meta() {
        return get_metadata( 'post', $this->_id );
      }

      /**
       *
       * @return boolean
       */
      private function load_taxonomies() {

        $_taxonomies = get_post_taxonomies( $this->_id );

        $_return = array();

        if ( !empty( $_taxonomies ) ) {

          foreach( (array)$_taxonomies as $taxonomy_slug ) {

            $_return[$taxonomy_slug] = wp_get_post_terms( $this->_id, $taxonomy_slug );

          }

          if ( !empty( $_return ) ) return $_return;

        }

        return false;

      }

      /**
       * Getter for meta
       * @param type $key
       * @return type
       */
      public function meta( $key = null, $value = null ) {

        if ( !$key ) {
          return $this->_meta;
        }

        if ( !$value ) {
          if ( count( $this->_meta[ $key ] ) > 1 ) {
            return $this->_meta[ $key ];
          } elseif ( !count( $this->_meta[ $key ] ) ) {
            return false;
          }
          return maybe_unserialize( $this->_meta[ $key ][0] );
        }

        $this->_meta[ $key ] = array( $value );
      }

      /**
       *
       */
      public function type() {
        return $this->_type;
      }

      /**
       *
       */
      public function post( $field ) {
        return $this->_post->{$field};
      }

      /**
       *
       * @param type $slug
       * @param type $format
       * @param type $separator
       * @return boolean
       */
      public function taxonomies( $slug, $format = 'link', $separator = ', ' ) {

        if ( empty( $this->_taxonomies[ $slug ] ) ) return false;

        switch( $format ) {

          case 'link':

            return $this->termsToString( $slug, $this->_taxonomies[ $slug ], $separator );

            break;

          case 'raw':

            return $this->_taxonomies[ $slug ];

            break;

          default: break;

        }

        return false;
      }

      /**
       *
       * @param type $options
       * @return \DiscoDonniePresents\Event|boolean
       */
      public function load_events( $options = array() ) {

        switch( $options['period'] ) {
          case 'upcoming':
            $period = array(
                'key' => 'dateStart',
                'value' => date( 'Y-m-d H:i' ),
                'compare' => '>=',
                'type' => 'DATE'
            );
            break;
          case 'past':
            $period = array(
                'key' => 'dateStart',
                'value' => date( 'Y-m-d H:i' ),
                'compare' => '<',
                'type' => 'DATE'
            );
            break;
          default:
            $period = array();
            break;
        }

        $args = wp_parse_args( $args, array(
          'post_type' => 'event',
          'posts_per_page' => -1,
          'meta_query' => array(
              array(
                  'key' => $this->_meta_key,
                  'value' => $this->_id
              ),
              $period
          )
        ) );

        $_events = array();

        $query = new \WP_Query( $args );

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
       * @param type $args
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
      protected function termsToString( $slug, $terms, $separator ) {
        $links = array();

        if ( empty( $terms ) ) return false;

        foreach( $terms as $term ) {
          $links[] = '<a href="'.get_term_link( $term->slug, $slug ).'">'.$term->name.'</a>';
        }

        return implode( $separator, $links );
      }

      /**
       *
       * @param type $param
       */
      public function toElastic($param) {

      }

    }

  }

}