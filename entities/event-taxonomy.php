<?php

namespace DiscoDonniePresents {

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\EventTaxonomy' ) ) {

    /**
     * Event related taxonomies
     */
    class EventTaxonomy extends Taxonomy {

      public function __construct($id = false, $taxonomy = false) {
        parent::__construct($id, $taxonomy);

        $this->load_data();

        echo '<pre>';
        print_r( $this );
        echo '</pre>';
      }

      /**
       *
       * @var type
       */
      public $_events;

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
       */
      public function load_data() {

        $_objects = get_posts(array(
          'post_type' => 'event',
          'posts_per_page' => -1,
          'tax_query' => array(
            array(
              'taxonomy' => $this->_term->taxonomy,
              'field' => 'slug',
              'terms' => $this->_term->slug
            )
          )
        ));

        if ( !empty( $_objects ) ) {

          foreach( (array)$_objects as $_object ) {

            $this->_events[] = new Event( $_object->ID, false);

          }

        }

        if ( !empty( $this->_events ) ) {

          foreach( $this->_events as $_event ) {

            if ( $_photo = $_event->photo() ) {
              $this->_photos[] = $_photo;
            }

            if ( $_video = $_event->video() ) {
              $this->_videos[] = $_video;
            }

          }

        }

      }

      /**
       *
       */
      public function events() {
        return $this->_events;
      }

      /**
       *
       */
      public function photos() {
        return $this->_photos;
      }

      /**
       *
       */
      public function videos() {
        return $this->_videos;
      }

    }

  }

}