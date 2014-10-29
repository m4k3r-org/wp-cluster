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
      public $_taxToElasticField = array(
          'genre' => 'artists.genre'
      );

    }

  }

}