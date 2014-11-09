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
          'city' => 'venue.address.city',
          'state' => 'venue.address.state',
          'country' => 'venue.address.country'
      );

    }

  }

}