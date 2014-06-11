<?php

namespace DiscoDonniePresents {

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\ArtistTaxonomy' ) ) {

    /**
     * Venue related taxonomies
     */
    class ArtistTaxonomy extends Taxonomy {

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
       * @var type
       */
      public $_taxToElasticField = array(
          'genre' => 'artists.genre'
      );

      /**
       *
       * @param type $id
       * @param type $taxonomy
       */
      public function __construct($id = false, $taxonomy = false) {
        parent::__construct($id, $taxonomy);

        /**
         * Do not use for now
         */
        //$this->load_data();
      }

      /**
       *
       */
      public function load_data() {

        $_objects = get_posts(array(
          'post_type' => 'artist',
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

            $_artist = new Artist( $_o->ID, false );

            if ( $_artist->events() ) {

              foreach( (array)$_artist->events() as $_event ) {

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