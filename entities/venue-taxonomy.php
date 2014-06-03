<?php

namespace DiscoDonniePresents {

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\VenueTaxonomy' ) ) {

    /**
     * Venue related taxonomies
     */
    class VenueTaxonomy extends Taxonomy {

      public function __construct($id = false, $taxonomy = false) {
        parent::__construct($id, $taxonomy);

        $this->load_data();
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
          'post_type' => 'venue',
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
          foreach( (array)$_objects as $_o ) {

            $_venue = new Venue( $_o->ID, false );

            if ( $_venue->events() ) {

              foreach( (array)$_venue->events() as $_event ) {

                if ( !array_key_exists( $_event->post('ID'), (array)$this->_events ) ) {
                  $this->_events[$_event->post('ID')] = $_event;
                }

              }

            }

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