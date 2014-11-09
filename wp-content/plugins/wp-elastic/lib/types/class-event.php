<?php
/**
 * Single Blog / Site
 *
 * @version 2.4.0
 * @package wp-elastic
 * @author potanin@UD
 */
namespace UsabilityDynamics\wpElastic\Document {

  if( !class_exists( 'UsabilityDynamics\wpElastic\Document\Event' ) ) {

    class Event extends \UsabilityDynamics\wpElastic\Document {

      static function normalize() {

        $object = (object) get_event( $object->ID );

        $time = ( $object->meta['hdp_event_date'] && $object->meta['hdp_event_time'] ? strtotime( $object->meta['hdp_event_date'] . ' ' . $object->meta['hdp_event_time'] ) : '' );

        $time = $time ? date( 'Y/m/d H:i:s', $time ) : '';

        /** @var $return array */
        $return = array(
          'id'        => $object->ID,
          'title'     => html_entity_decode( $object->post_title ),
          'type'      => $object->post_type,
          'summary'   => $object->post_excerpt,
          'time'      => $time,
          'thumbnail' => $object->meta['_thumbnail_id'] ? UD_Functions::get_image_link( $object->meta['_thumbnail_id'], 'events_flyer_thumb' ) : '',
          'url'       => get_permalink( $object->ID ),
          'rsvp'      => $object->meta['facebook_rsvp_url'],
          'purchase'  => $object->meta['hdp_purchase_url'],
          'venue'     => array(),
          'artists'   => array(),
          '_meta'     => array(
            'status'    => $object->post_status,
            'modified'  => date( 'Y/m/d H:i:s', strtotime( $object->post_modified ) ),
            'published' => date( 'Y/m/d H:i:s', strtotime( $object->post_date ) )
          ),
        );


      }
      function __construct() {

      }

    }

  }

}