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

      /**
       *
       * @var type
       */
      public $_taxToElasticField = array(
          'venue-type' => 'venue.type',
          'city' => 'venue.city',
          'state' => 'venue.state',
          'country' => 'venue.country'
      );

    }

  }

}