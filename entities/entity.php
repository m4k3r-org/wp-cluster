<?php

namespace DiscoDonniePresents {

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\Entity' ) ) {

    /**
     * Post Object Util
     */
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
       * Init
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
       * Load post data
       * @return type
       */
      private function load_post() {
        return get_post( $this->_id );
      }

      /**
       * Load meta data
       * @return type
       */
      private function load_meta() {
        return get_metadata( 'post', $this->_id );
      }

      /**
       * Load taxonomies
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
       * @param null $value
       * @return type
       */
      public function meta( $key = null, $value = null, $microdata_args = false ) {

        //die( '<pre>' . print_r( $this->_meta, true ) . '</pre>');
        //echo "\n {$key} -> {$value}";

        if ( !$key ) {
          return $this->_meta;
        }

        if ( !$value ) {
          if ( count( $this->_meta[ $key ] ) > 1 ) {
            return $this->_meta[ $key ];
          } elseif ( !count( $this->_meta[ $key ] ) ) {
            return false;
          }
          $ret = maybe_unserialize( $this->_meta[ $key ][0] );
          $fields = array();
          $fields[ $key ] = $ret;
          $microdata_args = \DiscoDonniePresents\Microdata::prepare_args( $microdata_args, $fields, get_class( $this ), __FUNCTION__ );
          if ( $microdata_args !== false ) {
            $ret = \DiscoDonniePresents\Microdata::handler( $microdata_args );
          }
          return $ret;
        }

        $this->_meta[ $key ] = array( $value );
      }

      public function image( $key = null, $size = 'thumbnail', $microdata_args = false ) {
        $attachment_id = absint( $this->meta( $key ) );
        $fields = array();
        $fields[ $key ] = $attachment_id;
        $microdata_args = \DiscoDonniePresents\Microdata::prepare_args( $microdata_args, $fields, get_class( $this ), __FUNCTION__, array( 'image_size' => $size ) );
        if ( $microdata_args !== false ) {
          return \DiscoDonniePresents\Microdata::handler( $microdata_args );
        }
        return wp_get_attachment_image( $attachment_id, $size );
      }

      /**
       * Return type of current object
       * @return type
       */
      public function type() {
        return $this->_type;
      }

      /**
       * Return post data by field
       * @param type $field
       * @return type
       */
      public function post( $field, $microdata_args = false ) {
        $ret = $this->_post->{$field};

        $fields = array();
        $fields[ $field ] = $ret;
        $microdata_args = \DiscoDonniePresents\Microdata::prepare_args( $microdata_args, $fields, get_class( $this ), __FUNCTION__ );
        if ( $microdata_args !== false ) {
          $ret = \DiscoDonniePresents\Microdata::handler( $microdata_args );
        }

        return $ret;
      }

      /**
       * Return taxonomies by parameters
       * @param type $slug
       * @param \DiscoDonniePresents\type|string $format
       * @param \DiscoDonniePresents\type|string $separator
       * @return boolean
       */
      public function taxonomies( $slug, $format = 'link', $separator = ', ', $microdata_args = false ) {

        if ( empty( $this->_taxonomies[ $slug ] ) ) return false;

        switch( $format ) {

          case 'link':

            return $this->termsToString( $slug, $this->_taxonomies[ $slug ], $separator, $microdata_args );

            break;

          case 'raw':

            return $this->_taxonomies[ $slug ];

            break;

          case 'elasticsearch':

            $_return = array();

            foreach( $this->_taxonomies[ $slug ] as $_term ) {
              $_return[] = $_term->name;
            }

            return $_return;

            break;

          default: break;

        }

        return false;
      }

      /**
       * Load images for current object
       * @return type
       */
      public function load_images() {

        $args = array(
            'post_mime_type' => 'image',
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_parent' => $this->_id,
            'exclude' => get_post_thumbnail_id( $this->_id )
        );

        return get_posts($args);

      }

      /**
       * Load events for current meta if exist
       * @param array|\DiscoDonniePresents\type $options
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
       * Return events if exist
       * @param array|\DiscoDonniePresents\type $args
       * @return type
       */
      public function events( $args = array() ) {

        if ( empty( $this->_events ) ) {
          $this->_events = $this->load_events( $args );
        }

        return $this->_events;
      }

      /**
       * Convert terms to string and return
       * @param type $slug
       * @param type $terms
       * @param type $separator
       * @return boolean
       */
      protected function termsToString( $slug, $terms, $separator, $microdata_args = false ) {
        $links = array();

        if ( count( $terms ) == 0 ) return false;

        // special case genre
        if ( $slug == 'genre' ) {

          $before = $after = '';
          if ( $microdata_args !== false ) {
            $before = '<span itemprop="genre">';
            $after = '</span>';
          }
          foreach ( $terms as $term ) {
            $links[] = '<a href="' . get_term_link( $term->slug, $slug ) . '">' . $before . $term->name . $after . '</a>';
          }

        } else {
          
          foreach ( $terms as $term ) {
            $name = $term->name;
            $url = get_term_link( $term->slug, $slug );
            $fields = compact( 'name', 'url' );
            $microdata_args = \DiscoDonniePresents\Microdata::prepare_args( $microdata_args, $fields, get_class( $this ), __FUNCTION__, array( 'super_type' => ucfirst( $slug ) ) );
            if ( $microdata_args !== false ) {
              $links[] = \DiscoDonniePresents\Microdata::handler( $microdata_args );
            } else {
              $links[] = '<a href="' . $url . '">' . $name . '</a>';
            }
          }

        }

        return implode( $separator, $links );
      }

      /**
       * To Elastic Format
       * Need to extend by child class
       * @internal param \DiscoDonniePresents\type $param
       * @return \DiscoDonniePresents\type
       */
      public function toElasticFormat() {
        $this->_post->post_content = do_shortcode( $this->_post->post_content );
        return $this->_post;
      }

    }

  }

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\Taxonomy' ) ) {

    /**
     * Taxonomy object util
     */
    class Taxonomy {

      /**
       *
       * @var type
       */
      public $_term;

      /**
       *
       * @var type
       */
      public $_taxonomy;

      /**
       * Init
       */
      public function __construct( $id = false, $taxonomy = false ) {

        //if ( !is_tax() ) return;

        if ( !$id || !$taxonomy ) {
          $this->_term = get_queried_object();
        } else {
          $this->_term = get_term_by( 'id', $id, $taxonomy );
        }

        $this->_taxonomy = get_taxonomy( $this->_term->taxonomy );
      }

      /**
       *
       * @return type
       */
      public function getUrl() {
        return get_term_link( $this->term()->slug, $this->term()->taxonomy );
      }

      /**
       *
       * @return type
       */
      public function getField() {
        return $this->_taxToElasticField[ $this->term()->taxonomy ];
      }

      /**
       *
       * @return type
       */
      public function getValue() {
        return $this->term()->name;
      }

      /**
       *
       */
      public function getID() {
        return $this->term()->term_id;
      }

      /**
       *
       */
      public function getType() {
        return $this->term()->taxonomy;
      }

      /**
       *
       * @return type
       */
      public function term() {
        return $this->_term;
      }

      /**
       *
       * @return type
       */
      public function taxonomy() {
        return $this->_taxonomy;
      }

      /**
       *
       */
      public function toElasticFormat() {

        $_object = array();

        $_object['summary'] = $this->getValue();
        $_object['url']     = $this->getUrl();
        
        return $_object;

      }

    }
  }
}