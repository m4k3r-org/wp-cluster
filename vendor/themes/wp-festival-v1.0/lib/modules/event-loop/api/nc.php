<?php
/**
 * NightCulture API Controller
 */
class JSON_API_Nc_Controller {

  /**
   * Load more function for events since it is custom.
   * @global type $json_api
   * @global type $wpdb
   * @return type
   */
  public function load_more_events() {
    global $json_api, $wpdb;

    $period    = $json_api->query->period;
    $limit     = $json_api->query->limit;
    $direction = $json_api->query->direction;
    $order_by  = $json_api->query->order_by;
    $city      = $json_api->query->city;
    $paged     = $json_api->query->_paged;

    $args = array(
      'post_type'      => 'events',
      'posts_per_page' => $limit,
      'meta_query'     => array(),
      'paged'          => $paged
    );

    switch( $period ) {

      case 'past':

        $direction               = $direction == 'default' ? 'DESC' : $direction;
        $args[ 'meta_query' ][ ] = array(
          'key'     => 'ncevents_event_start_date_time',
          'value'   => current_time( 'timestamp' ),
          'compare' => '<'
        );

        break;

      case 'future':

        $direction               = $direction == 'default' ? 'ASC' : $direction;
        $args[ 'meta_query' ][ ] = array(
          'key'     => 'ncevents_event_start_date_time',
          'value'   => current_time( 'timestamp' ),
          'compare' => '>',
          'type'    => 'NUMERIC'
        );

        break;

      default:

        $direction = $direction == 'default' ? 'DESC' : $direction;

        break;

    }

    if( !empty( $city ) ) {
      $args[ 'meta_query' ][ ] = array(
        'key'   => 'ncevents_venue_city',
        'value' => $city
      );
    }

    if( $order_by == 'default' ) {
      $args[ 'meta_key' ] = 'ncevents_event_start_date_time';
    } else {
      $args[ 'meta_key' ] = $order_by;
    }

    $args[ 'orderby' ] = 'meta_value';
    $args[ 'order' ]   = $direction;

    $query = new WP_Query( $args );

    ob_start();
    while ( $query->have_posts() ): $query->the_post();
      get_template_part( 'templates/article/listing', get_post_type() );
    endwhile;  wp_reset_query();
    $html = ob_get_clean();

    return array(
      "objects" => $query->posts,
      "html" => $html
    );
  }

}