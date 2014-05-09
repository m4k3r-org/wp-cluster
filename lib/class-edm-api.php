<?php
/**
 * API Handler
 *
 */
namespace wpCloud\Vertical\EDM {

  use \UsabilityDynamics;

  if( !class_exists( 'wpCloud\Vertical\EDM\API' ) ) {

    /**
     * Class Feed
     *
     * @package wpCloud\Vertical\EDM\API
     */
    class API extends UsabilityDynamics\API {

      static public function getArtists() {}

      static public function getArtist() {}

      static public function getVenues() {}

      static public function getVenue() {}

    }

  }

}