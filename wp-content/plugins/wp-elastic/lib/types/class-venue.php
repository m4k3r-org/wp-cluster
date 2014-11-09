<?php
/**
 * Single Blog / Site
 *
 * @version 2.4.0
 * @package wp-elastic
 * @author potanin@UD
 */
namespace UsabilityDynamics\wpElastic\Document {

  if( !class_exists( 'UsabilityDynamics\wpElastic\Document\Venue' ) ) {

    class Venue extends \UsabilityDynamics\wpElastic\Document {

      static function normalize() {

        $venue = (object) get_event( $object->ID );
        $time = $time ? date( 'Y/m/d H:i:s', $time ) : '';

        /** @var $return array */
        $return = array(
          'id'       => $venue->ID,
          'name'     => $venue->post_title,
          'summary'  => $venue->post_excerpt,
          'url'      => ! is_wp_error( get_term_link( $venue->post_name, 'hdp_venue' ) ) ? get_term_link( $venue->post_name, 'hdp_venue' ) : '',
          'address'  => $venue->formatted_address,
          'location' => array(
            '@type'        => $venue->location_type,
            '@precision'   => $venue->precision,
            'city'         => $venue->city,
            'county'       => $venue->county,
            'state'        => $venue->state,
            'country'      => $venue->country,
            'state_code'   => $venue->state_code,
            'country_code' => $venue->country_code,
            'coordinates'  => array(
              'lat' => $venue->latitude,
              'lon' => $venue->longitude
            )
          )
        );


      }
      function __construct() {

      }

    }

  }

}