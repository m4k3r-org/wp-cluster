<?php
/**
 * Carrington Build Loop Module
 *
 *
 *
 */
if( !class_exists( 'EventLoopModule' ) ) {

  class EventLoopModule extends \UsabilityDynamics\Theme\Module {

    /**
     *
     */
    public function __construct() {
      $opts = array(
        'description' => __( 'Display Events.', 'carrington-build' ),
        'icon'        => plugins_url( '/icon.png', __DIR__ )
      );

      parent::__construct( 'nc-module-events', __( 'Events', 'carrington-build' ), $opts );

      add_filter( 'json_api_controllers', function ( $controllers ) {
        $controllers[ ] = 'nc';

        return $controllers;
      } );

      add_filter( 'json_api_nc_controller_path', function () {
        return get_template_directory() . '/lib/carrington-build/modules/events-module/api/nc.php';
      } );

      // wp_enqueue_script( 'events-module', home_url() . '/themes/rockstar/lib/carrington-build/modules/events-module/scripts.js', array( 'jquery' ) );
    }

    /**
     *
     * @param type $data
     *
     * @return type
     */
    public function display( $data ) {
      global $wpdb;

      $title     = $data[ $this->get_field_name( 'title' ) ] ? $data[ $this->get_field_name( 'title' ) ] : false;
      $period    = $data[ $this->get_field_name( 'period' ) ];
      $limit     = empty( $data[ $this->get_field_name( 'events_number' ) ] ) ? 5 : $data[ $this->get_field_name( 'events_number' ) ];
      $direction = $data[ $this->get_field_name( 'order_direction' ) ];
      $see_all   = $data[ $this->get_field_name( 'see_all' ) ] == 'on' ? true : false;
      $order_by  = $data[ $this->get_field_name( 'order_by' ) ];
      $city      = $data[ $this->get_field_name( 'city' ) ];

      $args = array(
        'post_type'      => 'events',
        'posts_per_page' => $limit,
        'meta_query'     => array()
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

      return $this->load_view( $data, compact( 'query', 'title', 'see_all', 'period', 'limit', 'direction', 'order_by', 'city' ) );

    }

    /**
     *
     * @param type $data
     *
     * @return type
     */
    public function admin_form( $data ) {
      ob_start();
      ?>
      <fieldset>
        <ul>
          <li>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>_id"><?php _e( 'Title', 'carrington-build' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>_id" name="<?php echo $this->get_field_name( 'title' ) ?>" type="text" value="<?php echo $data[ $this->get_field_name( 'title' ) ] ?>"/>
          </li>
          <li>
            <label for="<?php echo $this->get_field_id( 'period' ); ?>_id"><?php _e( 'Period', 'carrington-build' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'period' ); ?>_id" name="<?php echo $this->get_field_name( 'period' ) ?>">
              <option value="all" <?php selected( 'all', $data[ $this->get_field_name( 'period' ) ] ) ?>><?php _e( 'All', '' ); ?></option>
              <option value="past" <?php selected( 'past', $data[ $this->get_field_name( 'period' ) ] ) ?>><?php _e( 'Past', '' ); ?></option>
              <option value="future" <?php selected( 'future', $data[ $this->get_field_name( 'period' ) ] ) ?>><?php _e( 'Future', '' ); ?></option>
            </select>
          </li>
          <li>
            <label for="<?php echo $this->get_field_id( 'city' ); ?>_id"><?php _e( 'City (optional)', 'carrington-build' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'city' ); ?>_id" name="<?php echo $this->get_field_name( 'city' ) ?>" type="text" value="<?php echo $data[ $this->get_field_name( 'city' ) ] ?>"/>
          </li>
          <li>
            <label for="<?php echo $this->get_field_id( 'events_number' ); ?>_id"><?php _e( 'Listing Limit', 'carrington-build' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'events_number' ); ?>_id" name="<?php echo $this->get_field_name( 'events_number' ) ?>" type="text" value="<?php echo $data[ $this->get_field_name( 'events_number' ) ] ? $data[ $this->get_field_name( 'events_number' ) ] : 5 ?>"/>
          </li>
          <li>
            <label for="<?php echo $this->get_field_id( 'order_by' ); ?>_id"><?php _e( 'Order By', 'carrington-build' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'order_by' ); ?>_id" name="<?php echo $this->get_field_name( 'order_by' ) ?>">
              <option value="default" <?php selected( 'default', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Default', 'carrington-build' ); ?></option>
              <option value="ncevents_venue_state" <?php selected( 'ncevents_venue_state', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Venue State', 'carrington-build' ); ?></option>
              <option value="ncevents_venue_city" <?php selected( 'ncevents_venue_city', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Venue City', 'carrington-build' ); ?></option>
              <option value="ncevents_venue_address" <?php selected( 'ncevents_venue_address', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Venue Address', 'carrington-build' ); ?></option>
              <option value="ncevents_venue_name" <?php selected( 'ncevents_venue_name', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Venue Name', 'carrington-build' ); ?></option>
              <option value="ncevents_event_age" <?php selected( 'ncevents_event_age', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Event Age', 'carrington-build' ); ?></option>
              <option value="ncevents_event_featured" <?php selected( 'ncevents_event_featured', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Event Featured', 'carrington-build' ); ?></option>
              <option value="ncevents_featured_artist" <?php selected( 'ncevents_featured_artist', $data[ $this->get_field_name( 'order_by' ) ] ) ?>><?php _e( 'Featured Artist', 'carrington-build' ); ?></option>
            </select>
          </li>
          <li>
            <label for="<?php echo $this->get_field_id( 'order_direction' ); ?>_id"><?php _e( 'Order Direction', 'carrington-build' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'order_direction' ); ?>_id" name="<?php echo $this->get_field_name( 'order_direction' ) ?>">
              <option value="default" <?php selected( 'default', $data[ $this->get_field_name( 'order_direction' ) ] ) ?>><?php _e( 'Default', '' ); ?></option>
              <option value="ASC" <?php selected( 'ASC', $data[ $this->get_field_name( 'order_direction' ) ] ) ?>><?php _e( 'Ascending', '' ); ?></option>
              <option value="DESC" <?php selected( 'DESC', $data[ $this->get_field_name( 'order_direction' ) ] ) ?>><?php _e( 'Descending', '' ); ?></option>
            </select>
          </li>
          <li>
            <label for="<?php echo $this->get_field_id( 'see_all' ); ?>_id"><?php _e( 'Show "See All" link?', 'carrington-build' ); ?></label>
            <input <?php checked( 'on', $data[ $this->get_field_name( 'see_all' ) ] ); ?> id="<?php echo $this->get_field_id( 'see_all' ); ?>_id" name="<?php echo $this->get_field_name( 'see_all' ) ?>" type="checkbox" value="on"/>
          </li>
        </ul>
      </fieldset>
      <?php
      return ob_get_clean();
    }

    /**
     *
     * @param type $data
     *
     * @return type
     */
    public function text( $data ) {
      return sprintf( __( 'Display %s %s event(s).', 'carrington-build' ), $data[ $this->get_field_name( 'events_number' ) ], $data[ $this->get_field_name( 'period' ) ] == 'all' ? '' : $data[ $this->get_field_name( 'period' ) ] );
    }

    /**
     *
     * @param type $new_data
     * @param type $old_data
     *
     * @return type
     */
    public function update( $new_data, $old_data ) {
      return $new_data;
    }

  }

}