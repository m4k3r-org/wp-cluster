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
      public function events() {

        $_objects = get_posts(array(
            'post_type' => 'event',
            'posts_per_page' => -1,
            'tax_query' => array(
              array(
                'taxonomy' => $this->_term->taxonomy,
                'field' => 'slug',
                'terms' => $this->_term->slug
              )
            ),
//            'meta_query' => array(
//              array(
//                'key' => 'dateStart',
//                'value' => date( 'Y-m-d H:i' ),
//                'compare' => '>=',
//                'type' => 'DATE'
//              )
//            )
        ));

        if ( !empty( $_objects ) ) {

          foreach( (array)$_objects as $_object ) {

            $this->_events[] = new Event( $_object->ID, false);

          }

        }

      }

      /**
       *
       */
      public function photos() {}

      /**
       *
       */
      public function videos() {}

    }

  }

}