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
      public $_taxToElasticField = array(
          'age-limit' => 'age_restriction',
          'event-type' => 'event_type'
      );

    }

  }

}