<?php
/**
 * Single Media Item.
 *
 * @version 2.4.0
 * @package wp-elastic
 * @author potanin@UD
 */
namespace UsabilityDynamics\wpElastic\Document {

  if( !class_exists( 'UsabilityDynamics\wpElastic\Document\Media' ) ) {

    class Media extends Document {

      static function normalize() {

        //** We can use get_events() for other types because it works well for them too. */
        $object = (object) get_event( $object->ID );

        //** Date for photo gallery may not include the time. */
        $time = ( $object->meta['hdp_event_date'] ? strtotime( $object->meta['hdp_event_date'] ) : 0 );
        $time = $time ? date( 'Y/m/d H:i:s', $time ) : '';

        /** @var $return array */
        $return = array(
          'id'        => $object->ID,
          'title'     => html_entity_decode( $object->post_title ),
          'type'      => $object->post_type,
          'summary'   => $object->post_excerpt,
          'time'      => $time,
          'thumbnail' => $object->meta['hdp_poster_id'] ? UD_Functions::get_image_link( $object->meta['hdp_poster_id'], 'hd_small' ) : '',
          'url'       => get_permalink( $object->ID ),
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