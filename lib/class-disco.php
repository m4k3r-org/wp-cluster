<?php

namespace UsabilityDynamics\Theme {

  /**
   * Disco Theme
   *
   * @author Usability Dynamics
   */
  class Disco {

    /**
     * Adds input fields to taxonomy term editing pages.
     * The submitted data is saved in \UsabilityDynamics\Flawless\Utility::term_editor_loader();
     * Task: https://basecamp.com/1847866/projects/419234-hddp-new-website/messages/2100601-event-post-type-and
     *
     */
    static public function extended_term_form_fields( $tag, $post ) {
      include 'templates/admin.extended_term_form_fields.php';
    }

    /**
     * Get HDP-Event Posts.
     *
     */
    static public function _get_event_posts( $args = array() ) {
      global $wpdb, $hddp;

      return $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ( '" . implode( "','", array_keys( array( self::$_types ) ) ) . "' ) AND post_status = 'publish' " );
    }

    /**
     * Gets total events in the db
     */
    static public function get_events_count() {
      global $wpdb;

      $wpdb->show_errors();

      return number_format( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'hdp_event' AND post_status = 'publish'" ), 0 );
    }

    /**
     * Maintanance Tasks that are run once a day
     *
     */
    static public function daily_maintenance_cron() {
      $stats = array();

      //** Attempt geolocation for all events that are likely to have locatoins */
      foreach( self::_get_event_posts() as $post_id ) {
        if( !get_post_meta( $post_id, 'formatted_address', true ) ) {
          $stats[ 'geo_located' ][ ] = self::update_event_location( $post_id, array( 'add_log_entries' => false ) );
        }
      }

      if( array_filter( (array) $stats[ 'geo_located' ] ) ) {
        \Flawless_F::log( 'Geo-located (' . count( $stats[ 'geo_located' ] ) . ') events during maintanance. ' );
      }

    }

    /**
     * Placeholder so we can update post's location
     *
     * @version 1.1.0
     */
    static public function save_post( $post_id, $post ) {
      global $hddp, $wpdb;

      //**  Verify if this is an auto save routine.  */
      if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
      }

      if( wp_is_post_revision( $post_id ) ) {
        return;
      }

      if( !in_array( $post->post_type, array_keys( self::$_types ) ) ) {
        return;
      }

      self::update_event_location( $post_id );

      if( $_REQUEST[ 'do_not_generate_post_title' ] ) {
        update_post_meta( $post_id, 'do_not_generate_post_title', $_REQUEST[ 'do_not_generate_post_title' ] );
      }

      if( $_REQUEST[ 'do_not_generate_post_name' ] ) {
        update_post_meta( $post_id, 'do_not_generate_post_name', $_REQUEST[ 'do_not_generate_post_name' ] );
      }

      // @ticket https://projects.usabilitydynamics.com/projects/discodonniepresentscom-november-2012/tasks/55
      if( $_REQUEST[ 'disable_cross_domain_tracking' ] ) {
        update_post_meta( $post_id, 'disable_cross_domain_tracking', $_REQUEST[ 'disable_cross_domain_tracking' ] );
      }

      foreach( (array) $hddp[ 'automated_attributes' ][ $post->post_type ] as $key ) {

        switch( $key ) {

          case 'post_title':
            if( $_REQUEST[ 'do_not_generate_post_title' ] != 'true' ) {
              $wpdb->update( $wpdb->posts, array( 'post_title' => self::get_post_title( $post->ID ) ), array( 'ID' => $post_id ) );
            }
            break;

          case 'post_excerpt':
            $wpdb->update( $wpdb->posts, array( 'post_excerpt' => self::get_post_excerpt( $post->ID ) ), array( 'ID' => $post_id ) );
            break;

          case 'post_name':
            if( $_REQUEST[ 'do_not_generate_post_name' ] != 'true' ) {
              $wpdb->update( $wpdb->posts, array( 'post_name' => self::get_post_name( $post->ID ) ), array( 'ID' => $post_id ) );
            }
            break;

        }

      }

      /** Do different things based on the status of the post */
      if( $post->post_status != 'publish' ) {

        /** Not published, get it out of the QA table */
        $wpdb->query( "DELETE FROM {$wpdb->prefix}ud_qa_{$post->post_type} WHERE post_id = {$post->ID}" );

      }
      if( $post->post_status == 'publish' ) {

        \Flawless_F::update_qa_table_item( $post_id, array(
          'attributes' => self::_get_qa_attributes( $post->post_type ),
          'table_name' => "{$wpdb->prefix}ud_qa_{$post->post_type}"
        ));

      }

    }

    /**
     * Build QA-Table friendly array of attributes
     *
     * @author potanin@UD
     */
    static public function _get_qa_attributes( $post_type ) {
      global $hddp;

      $return = array();

      foreach( (array) $hddp[ 'attributes' ][ $post_type ] as $key => $settings ) {
        //if( $settings[ 'qa' ] ) {
        $return[ $key ] = $settings[ 'type' ];
        //}
      }

      return $return;

    }

    /**
     * Handle addition of shortcode and listener
     *
     * @action template_redirect (10)
     * @author potanin@UD
     */
    static public function dynamic_filter_shortcode_handler() {
      global $post;

      // Disable Elastic shortcodes since unused. - potanin@UD
      // @ticket https://projects.usabilitydynamics.com/projects/discodonniepresentscom-november-2012/tasks/55
      // add_shortcode('elastic_results', array(get_class(), 'shortcode_elastic_results'));
      // add_shortcode('elastic_facets', array(get_class(), 'shortcode_elastic_facets'));
      // add_shortcode('elastic_popup_filter', array(get_class(), 'shortcode_elastic_popup_filter'));

      /* Add Shortcode */
      add_shortcode( 'dynamic_filter', array( get_class(), 'shortcode_dynamic_filter' ) );
      add_shortcode( 'hdp_custom_loop', array( get_class(), 'shortcode_hdp_custom_loop' ) );
      add_shortcode( 'hddp_gallery', array( get_class(), 'shortcode_hddp_gallery' ) );

      //** New Elastic Search Shortcodes */
      add_shortcode( 'elasticsearch_results', array( get_class(), 'elasticsearch_results' ) );
      add_shortcode( 'elasticsearch_facets', array( get_class(), 'elasticsearch_facets' ) );

      /* Detect shortcode usage in this post - and add Sidebar */
      if( isset( $post ) && strpos( $post->post_content, '[dynamic_filter' ) !== false ) {
        add_action( 'flawless::sidebar_top', function () {
          echo '<div class="cfct-module single-widget-area"><div id="df_sidebar_filters" class="df_sidebar_filters flawless_widget theme_widget widget  widget_text clearfix"></div></div>';
        } );
      }

    }

    /**
     * Elasticsearch Query
     *
     * @global type $post
     */
    static public function elasticsearch_query() {

      try {

        //** Server connection. Settings go from ElasticSearch Plugin Settings page. */
        $elastica_client = new Elastica\Client(
          array(
            'connections' => array(
              'config' => array(
                'headers' => array( 'Accept' => 'application/json' ),
                'url'     => elasticsearch\Config::option( 'server_url' )
              )
            )
          )
        );
        $index           = $elastica_client->getIndex( elasticsearch\Config::option( 'server_index' ) );
        $type            = $index->getType( $_REQUEST[ 'type' ] );
        $path            = $index->getName() . '/' . $type->getName() . '/_search';

        $params[ 'body' ] = array();

        //** Set size */
        $params[ 'body' ][ 'size' ] = $_REQUEST[ 'size' ];

        //** Set offset */
        $params[ 'body' ][ 'from' ] = $_REQUEST[ 'from' ];

        switch( $_REQUEST[ 'sort_by' ] ) {
          case 'hdp_event_date':
            $params[ 'body' ][ 'sort' ] = array(
              array(
                'event_date_time' => array(
                  'order' => strtolower( $_REQUEST[ 'sort_dir' ] )
                )
              )
            );
            break;

          case 'distance':
            $lat                        = ( isset( $_COOKIE[ 'latitude' ] ) && is_numeric( $_COOKIE[ 'latitude' ] ) ? $_COOKIE[ 'latitude' ] : false );
            $lon                        = ( isset( $_COOKIE[ 'longitude' ] ) && is_numeric( $_COOKIE[ 'longitude' ] ) ? $_COOKIE[ 'longitude' ] : false );
            $params[ 'body' ][ 'sort' ] = array(
              array(
                '_geo_distance' => array(
                  'location' => array(
                    $lat ? $lat : 0, $lon ? $lon : 0
                  ),
                  'order'    => strtolower( $_REQUEST[ 'sort_dir' ] )
                )
              )
            );
            break;

          default:
            break;
        }

        //** Determine period */
        if( $_REQUEST[ 'period' ] ) {
          switch( $_REQUEST[ 'period' ] ) {
            case 'upcoming':
              $params[ 'body' ][ 'filter' ][ 'bool' ][ 'must' ][ ] = array(
                'range' => array(
                  'event_date_time' => array(
                    'gte' => 'now'
                  )
                )
              );
              break;
            case 'past':
              $params[ 'body' ][ 'filter' ][ 'bool' ][ 'must' ][ ] = array(
                'range' => array(
                  'event_date_time' => array(
                    'lte' => 'now'
                  )
                )
              );
              break;
            default:
              break;
          }
        }

        parse_str( $_REQUEST[ 'query' ], $query );

        $query[ 'date_range' ] = array_filter( $query[ 'date_range' ] );
        if( !empty( $query[ 'date_range' ] ) ) {
          $params[ 'body' ][ 'filter' ][ 'bool' ][ 'must' ][ ] = array(
            'range' => array(
              'event_date_time' => $query[ 'date_range' ]
            )
          );
        }

        if( $query[ 'q' ] ) {
          $query_string                = array( 'query' => $query[ 'q' ] );
          $params[ 'body' ][ 'query' ] = array(
            'query_string' => $query_string
          );
        }

        if( !empty( $query[ 'terms' ] ) ) {
          foreach( $query[ 'terms' ] as $field => $term ) {
            if( $term != '0' ) {
              $params[ 'body' ][ 'filter' ][ 'bool' ][ 'must' ][ ] = array(
                'term' => array(
                  $field => htmlspecialchars( stripslashes( $term ) )
                )
              );
            }
          }
        }

        $params[ 'body' ][ 'fields' ] = array( "raw" );

        $params[ 'body' ][ 'facets' ] = array(
          'hdp_artist_name'    => array(
            'terms' => array(
              'field' => 'hdp_artist_name',
              'size'  => 999
            )
          ),
          'hdp_state_name'     => array(
            'terms' => array(
              'field' => 'hdp_state_name',
              'size'  => 999
            )
          ),
          'hdp_city_name'      => array(
            'terms' => array(
              'field' => 'hdp_city_name',
              'size'  => 999
            )
          ),
          'hdp_venue_name'     => array(
            'terms' => array(
              'field' => 'hdp_venue_name',
              'size'  => 999
            )
          ),
          'hdp_promoter_name'  => array(
            'terms' => array(
              'field' => 'hdp_promoter_name',
              'size'  => 999
            )
          ),
          'hdp_tour_name'      => array(
            'terms' => array(
              'field' => 'hdp_tour_name',
              'size'  => 999
            )
          ),
          'hdp_type_name'      => array(
            'terms' => array(
              'field' => 'hdp_type_name',
              'size'  => 999
            )
          ),
          'hdp_genre_name'     => array(
            'terms' => array(
              'field' => 'hdp_genre_name',
              'size'  => 999
            )
          ),
          'hdp_age_limit_name' => array(
            'terms' => array(
              'field' => 'hdp_age_limit_name',
              'size'  => 999
            )
          )
        );

        $result = array();

        $result[ 'raw' ] = $elastica_client->request( $path, Elastica\Request::POST, json_encode( $params[ 'body' ] ) )->getData();
      } catch( Exception $e ) {
        $error = array(
          'success' => false
        );
        $error = array_merge( $error, array( 'error' => $e->getMessage() ) );
        die( json_encode( $error ) );
      }

      ob_start();
      foreach( (array) $result[ 'raw' ][ 'facets' ] as $facet_key => $facet_data ) {
        include "templates/facets/facet-{$facet_data['_type']}.php";
      }
      $facets = ob_get_clean();

      ob_start();
      if( $result[ 'raw' ][ 'hits' ][ 'total' ] != 0 ) {
        foreach( (array) $result[ 'raw' ][ 'hits' ][ 'hits' ] as $hit_data ) {
          global $post;
          $post = $hit_data[ 'fields' ][ 'raw' ];
          include "templates/results/result-{$hit_data['_type']}.php";
        }
      } else {
        echo '<li class="df_not_found">Nothing found for current filter</li>';
      }
      $results = ob_get_clean();

      $result[ 'facets' ]  = $facets;
      $result[ 'results' ] = $results;
      $result[ 'query' ]   = $params[ 'body' ];

      die( json_encode( $result ) );
    }

    /**
     * New Elastic Search Facets
     *
     * @param type $atts
     *
     * @return type
     */
    static public function elasticsearch_facets( $atts ) {
      extract( shortcode_atts( array(
        'id'     => 'none',
        'action' => 'elasticsearch_query',
        'type'   => '',
        'size'   => 10
      ), $atts ) );

      flawless_render_in_footer(
        '<script type="text/javascript">
        if( typeof jQuery.prototype.new_ud_elasticsearch === "function" ) { jQuery(document).ready(function(){ jQuery("#elasticsearch-facets-' . $id . '").new_ud_elasticsearch()}); }
  </script>'
      );

      ob_start();
      include 'templates/elasticsearch_facets.php';

      return ob_get_clean();
    }

    /**
     * New Elastic Search Results
     *
     * @param $atts
     *
     * @return type
     */
    static public function elasticsearch_results( $atts ) {
      extract( shortcode_atts( array(
        'id' => 'none'
      ), $atts ) );

      ob_start();
      include 'templates/elasticsearch_results.php';

      return ob_get_clean();
    }

    /**
     * Geo-locates and event based on venue address and updates the event meta.
     *
     * @todo Ideally address information should only be stored in the term's meta, not duplicated. - potanin@UD 5/17/12
     *
     * @param bool  $post_id
     * @param array $args
     *
     * @return bool true if posts location information was updated, false if it was not geolocated or already exists
     * @author potanin@UD
     */
    static public function update_event_location( $post_id = false, $args = array() ) {

      if( !is_numeric( $post_id ) ) {
        return false;
      }

      $args = wp_parse_args( $args, array( 'add_log_entries' => true ) );

      /* Set coordinates for event */
      $post = get_post( $post_id );

      if( $post->post_type == '_tp_hdp_venue' ) {

        $extended_term_post_id = $post_id;
        $formatted_address     = get_post_meta( $extended_term_post_id, 'formatted_address', true );

      } else {

        $hdp_venue         = wp_get_object_terms( $post_id, 'hdp_venue' );
        $venue_term_id     = !is_wp_error( $hdp_venue ) ? $hdp_venue[ 0 ]->term_id : false;
        $formatted_address = get_term_meta( $venue_term_id, 'formatted_address', true );

      }

      if( $formatted_address && $geo_located = \UD_Functions::geo_locate_address( $formatted_address ) ) {

        foreach( (array) $geo_located as $attribute => $value ) {
          update_post_meta( $post_id, $attribute, $value );

          if( $venue_term_id ) {
            update_term_meta( $venue_term_id, $attribute, $value );
          }

        }

        if( $args[ 'add_log_entries' ] ) {
          \Flawless_F::log( '<a href="' . get_edit_post_link( $post_id ) . '">' . $post->post_title . '</a> geo-located, formatted address: ' . $geo_located->formatted_address );
        }

        return true;

      } elseif( !empty( $formatted_address ) ) {

        if( $args[ 'add_log_entries' ] ) {
          \Flawless_F::log( 'Could not geo-locate <a href="' . get_edit_post_link( $post_id ) . '">' . $post->post_title . '</a> after update.' );
        }

      } else {

        if( $args[ 'add_log_entries' ] ) {
          \Flawless_F::log( 'Warning. Could not get physical address for <a href="' . get_edit_post_link( $post_id ) . '">' . $post->post_title . '</a> from venue.' );
        }

      }

      return false;

    }

    /**
     * Automated QA table updating by having pre-configured attributes for the used DF Post Types
     *
     * @author potanin@UD
     */
    static public function all_admin_notices() {
      global $hddp;

      foreach( (array) $hddp[ 'runtime' ][ 'notices' ][ 'error' ] as $notice ) {
        echo '<div class="error"><p>' . $notice . '</p></div>';
      }

      foreach( (array) $hddp[ 'runtime' ][ 'notices' ][ 'update' ] as $notice ) {
        echo '<div class="fade updated"><p>' . $notice . '</p></div>';
      }

    }

    /**
     * Adds Address Column to Event Venue taxonomy table
     *
     * @action admin_init (10)
     * @author potanin@UD
     */
    static public function event_venue_columns_data( $null, $column, $term_id ) {

      if( $column != 'formatted_address' ) {
        return;
      }

      return get_term_meta( $term_id, 'formatted_address', true );

    }

    /**
     * Adds Address Column to Event Venue taxonomy table
     *
     * @action admin_init (10)
     * @author potanin@UD
     */
    static public function manage_hdp_event_posts_custom_column( $column, $post_id ) {

      $event = get_event( $post_id );

      switch( $column ) {

        case 'post_excerpt':
        {
          echo $event[ 'post_excerpt' ] ? $event[ 'post_excerpt' ] : ' - ';

          break;
        }

        case 'formatted_address':
        {

          $_items = array();

          $formatted_address = get_post_meta( $post_id, 'formatted_address', true );
          $_items[ ]         = $formatted_address ? $formatted_address : ' -';

          if( $synchronized = get_post_meta( $post_id, 'ud::cloud::synchronized', true ) ) {
            $_items[ ] = 'Synchronized ' . human_time_diff( $synchronized ) . ' ago.';
          } else {
            $_items[ ] = 'Not Synchronized.';
          }

          echo implode( '<br />', (array) $_items );

          break;
        }

        case 'hdp_event_date':
        {
          $hdp_event_date = strtotime( get_post_meta( $post_id, 'hdp_event_date', true ) );
          $hdp_event_time = strtotime( get_post_meta( $post_id, 'hdp_event_time', true ) );

          if( $hdp_event_date ) {
            $print_date[ ] = date( get_option( 'date_format' ), $hdp_event_date );
          }

          if( $hdp_event_time ) {
            $print_date[ ] = date( get_option( 'time_format' ), $hdp_event_time );
          }

          if( $print_date ) {
            echo implode( '<br />', (array) $print_date );
          }

          break;
        }

      }

    }

    /**
     * Checks to see if the value is blank
     *
     */
    static public function check_blank_array( $value ) {
      $value = trim( $value );

      return !empty( $value );
    }

    /**
     * Return JSON post results Dynamic Filter requests (Quick Access Table)
     *
     * @author potanin@UD
     */
    static public function df_post_query( $request = false ) {

      //    $client = new Elasticsearch\Client(array(
      //        'hosts' => array(
      //            'http://91.240.22.17:9200'
      //        )
      //    ));
      //
      //    $params['index'] = 'eney';
      //    $params['type']  = 'hdp_event';
      //
      //    $params['body']['query']['bool']['must'] = array(
      //        "term" => array(
      //            'hdp_promoter' => 'nightculture'
      //        )
      //    );
      //
      //    $params['body']['facets'] = array(
      //        'hdp_promoter' => array(
      //            'terms' => array(
      //                'field' => 'hdp_promoter'
      //            )
      //        )
      //    );
      //
      //    $results = $client->search($params);
      //
      //    die( json_encode($results));

      global $wpdb, $hddp;

      set_time_limit( 0 );

      $myFile = "request.log";
      $fh = fopen( $myFile, 'w' ) or die( "can't open file" );
      fwrite( $fh, print_r( $_REQUEST, 1 ) );
      fwrite( $fh, print_r( $_COOKIE, 1 ) );
      fclose( $fh );

      if( !$request ) {
        return array();
      }

      $args = wp_parse_args( $request, array( 'post_type' => 'post' ) );

      /** Go through our static var to get this information */
      if( !isset( $hddp[ 'attributes' ][ $args[ 'post_type' ] ] ) ) {
        return false;
      }

      $attributes = $hddp[ 'attributes' ][ $args[ 'post_type' ] ];

      /** Setup our temp table name */
      $filtered_table = "U" . md5( time() ) . "D";

      /** Go ahead and setup the query */
      $query = "SELECT * FROM {$wpdb->prefix}ud_qa_{$args['post_type']} WHERE 1=1";

      /** If we're an event, we don't want past events */
      if( $args[ 'post_type' ] == 'hdp_event' ) {
        if( !isset( $args[ 'filter_events' ] ) || isset( $args[ 'filter_events' ] ) && $args[ 'filter_events' ] == 'upcoming' ) {
          $query .= " AND STR_TO_DATE( CONCAT(hdp_event_date,' ',hdp_event_time), '%m/%d/%Y %h:%i %p' ) >= DATE_ADD(CONCAT(CURDATE(), ' 00:00:01'), INTERVAL 3 HOUR)";
        }
        if( isset( $args[ 'filter_events' ] ) && $args[ 'filter_events' ] == 'past' ) {
          $query .= " AND STR_TO_DATE( CONCAT(hdp_event_date,' ',hdp_event_time), '%m/%d/%Y %h:%i %p' ) < DATE_ADD(CONCAT(CURDATE(), ' 00:00:01'), INTERVAL 3 HOUR)";
        }
      }

      /** If we have a request, go through each one and filter the results */
      if( isset( $request ) && isset( $request[ 'filter_query' ] ) && is_array( $request[ 'filter_query' ] ) && count( $request[ 'filter_query' ] ) ) {
        $filter_query =& $request[ 'filter_query' ];
        foreach( $filter_query as $key => $filter ) {
          if( is_array( $filter ) && $filter[ 0 ] == 'Show All' ) continue;
          /** Determine the kind of filter we're looking for */
          if( !in_array( $key, array_keys( $attributes ) ) ) continue;

          /** Determine the query we need */
          switch( $key ) {

            case 'hdp_date_range':
              if( !empty( $filter[ 'max' ] ) ) {
                $query .= " AND STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) <= STR_TO_DATE( '" . $filter[ 'max' ] . "', '%m/%d/%Y' ) ";
              }
              if( !empty( $filter[ 'min' ] ) ) {
                $query .= " AND STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) >= STR_TO_DATE( '" . $filter[ 'min' ] . "', '%m/%d/%Y' ) ";
              }
              break;

            case 'post_title':
              $q = $wpdb->escape( $filter );
              if( $args[ 'post_type' ] == 'hdp_event' ) {
                $query .= " AND (
                  LOWER(hdp_artist) LIKE LOWER('%{$q}%') OR
                  LOWER(hdp_venue) LIKE LOWER('%{$q}%') OR
                  LOWER(city) LIKE LOWER('%{$q}%') OR
                  LOWER(state) LIKE LOWER('%{$q}%') OR
                  LOWER(state_code) LIKE LOWER('%{$q}%') OR
                  LOWER(post_title) LIKE LOWER('%{$q}%') OR
                  LOWER(hdp_tour) LIKE LOWER('%{$q}%') OR
                  LOWER(hdp_genre) LIKE LOWER('%{$q}%') OR
                  LOWER(hdp_venue) LIKE LOWER('%{$q}%') OR
                  LOWER(hdp_promoter) LIKE LOWER('%{$q}%') OR
                  LOWER(hdp_type) LIKE LOWER('%{$q}%')
                  )";
              } else {
                $query .= " AND (
                  LOWER(hdp_artist) LIKE LOWER('%{$q}%') OR
                  LOWER(hdp_venue) LIKE LOWER('%{$q}%') OR
                  LOWER(city) LIKE LOWER('%{$q}%') OR
                  LOWER(state) LIKE LOWER('%{$q}%') OR
                  LOWER(state_code) LIKE LOWER('%{$q}%')
                  )";
              }
              break;

            default:
              switch( $attributes[ $key ][ 'type' ] ) {
                case 'taxonomy':
                  $filter = array_filter( (array) $filter, '\UsabilityDynamics\Theme\Disco\Bootstrap::check_blank_array' );
                  if( !count( $filter ) ) break;
                  $query .= " AND ( 1=2";
                  foreach( (array) $filter as $q ) {
                    if( empty( $q ) ) continue;
                    $query .= " OR FIND_IN_SET( '" . $wpdb->escape( $q ) . "', `{$key}_ids` )";
                  }
                  $query .= " )";
                  break;
                case 'post_meta':
                  $filter = array_filter( (array) $filter, '\UsabilityDynamics\Theme\Disco\Bootstrap::check_blank_array' );
                  if( !count( $filter ) ) break;
                  $query .= " AND ( 1=2";
                  foreach( (array) $filter as $q ) {
                    if( empty( $q ) ) continue;
                    $query .= " OR `{$key}` LIKE '%" . $wpdb->escape( $q ) . "%'";
                  }
                  $query .= " )";
                  break;
                default:
                  break;
              }
              break;
          }
        }
      }

      /** Add on the sorting query */
      /** Hack to make it default by date */
      if( !isset( $args[ 'sort_by' ] ) || empty( $args[ 'sort_by' ] ) ) {
        $args[ 'sort_by' ] = 'hdp_event_date';
      }
      if( isset( $args[ 'sort_by' ] ) && !empty( $args[ 'sort_by' ] ) ) {
        if( $args[ 'post_type' ] == 'hdp_event' ) {
          $direction = 'ASC';
          if( isset( $args[ 'filter_events' ] ) ) {
            if( $args[ 'filter_events' ] == 'past' || $args[ 'filter_events' ] == 'all' ) {
              $direction = 'DESC';
            }
          }
        } else {
          $direction = 'DESC';
        }
        if( isset( $args[ 'sort_direction' ] ) && $args[ 'sort_direction' ] == 'DESC' ) $direction = 'DESC';
        /** Determine what we're sorting by */
        switch( $args[ 'sort_by' ] ) {
          case 'hdp_event_date':
            $query .= " ORDER BY STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) {$direction}";
            break;
          case 'distance':
            /** First, make sure we have latitude and longitude */
            $lat = ( isset( $_COOKIE[ 'latitude' ] ) && is_numeric( $_COOKIE[ 'latitude' ] ) ? $_COOKIE[ 'latitude' ] : false );
            $lon = ( isset( $_COOKIE[ 'longitude' ] ) && is_numeric( $_COOKIE[ 'longitude' ] ) ? $_COOKIE[ 'longitude' ] : false );
            if( $lat === false || $lon === false ) break;
            /** Continue here with writing our query */
            $query .= " ORDER BY ROUND(((ACOS(SIN($lat * PI() / 180) * SIN(`latitude` * PI() / 180) + COS($lat * PI() / 180) * COS(`latitude` * PI() / 180) * COS(($lon - `longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515), -1) {$direction}, STR_TO_DATE( hdp_event_date, '%W, %M %e, %Y' ) ASC";
            break;
          default:
            break;
        }
      }

      /** Setup the query hash */
      $query_hash   = 'df_' . $args[ 'post_type' ] . '_' . md5( $query );
      $force_update = isset( $_REQUEST[ 'force_update' ] ) ? true : false;
      $cached       = false;

      /** Check to see if we need to use the query */
      if( !$force_update && $cached = get_transient( $query_hash ) ) {
        die( $cached );
      }

      $all_ids        = array();
      $mapped_results = array();
      $full_results   = $wpdb->get_results( $query );
      foreach( $full_results as $res ) {
        $mapped_results[ $res->post_id ] = $res;
        $all_ids[ ]                      = $res->post_id;
      }

      /** No go through and setup our returned filters */
      $current_filters = array();
      foreach( (array) $args[ 'filterable_attributes' ] as $name => $att ) {
        $filter_query = false;
        $filter_key   = false;
        switch( $att[ 'filter' ] ) {
          case 'checkbox':
          case 'dropdown':
            switch( $attributes[ $name ][ 'type' ] ) {
              case 'taxonomy':
                $filter_query = "SELECT t.name AS 'value', t.term_id AS 'filter_key', COUNT(*) AS 'value_count' FROM {$wpdb->term_relationships} AS tr LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id LEFT JOIN {$wpdb->terms} AS t ON t.term_id = tt.term_id WHERE tr.object_id IN ( " . implode( ',', $all_ids ) . ") AND tt.taxonomy = '{$wpdb->escape( $name )}' GROUP BY t.term_id ORDER BY COUNT(*) DESC, t.name ASC";
                break;
              case 'post_meta':
                $filter_query = "SELECT meta_value AS 'value', meta_value AS 'filter_key', COUNT(*) AS 'value_count' FROM {$wpdb->postmeta} WHERE meta_key = '{$wpdb->escape( $name )}' AND post_id IN ( " . implode( ',', $all_ids ) . " ) GROUP BY meta_value ORDER BY COUNT(*) DESC, meta_value ASC";
                break;
              case 'primary': /* Not used with these type of inputs */
              default:
                break;
            }
            break;
          case 'input':
            /** We'll bring these in later, because they are combined */
            switch( $name ) {
              case 'post_title':
                break;
              default:
                break;
            }
            break;
          default:
            break;
        }
        if( $filter_query ) {
          $current_filters[ $name ] = $wpdb->get_results( $filter_query, ARRAY_A );
        }
      }

      /** Get our count */
      $total_results = count( $all_ids );
      /** Setup 'all_results' */
      $all_results = array();
      /** Now get the requested range subset of IDs */
      $start = ( isset( $args[ 'request_range' ][ 'start' ] ) && is_numeric( $args[ 'request_range' ][ 'start' ] ) ? $args[ 'request_range' ][ 'start' ] : false );
      $end   = ( isset( $args[ 'request_range' ][ 'end' ] ) && is_numeric( $args[ 'request_range' ][ 'end' ] ) ? $args[ 'request_range' ][ 'end' ] : false );
      if( $start !== false && $end !== false ) {
        /** We're here, calculate the limit and slice the array */
        $limit   = $end - $start;
        $all_ids = array_slice( $all_ids, $start, $limit );
      }
      foreach( $all_ids as $id ) {
        global $post;
        $post = json_decode( $mapped_results[ $id ]->object, true );

        $all_results[ ] = array( 'id'       => $id, 'df_attribute_class' => join( ' ', get_post_class( $class, $id ) ),
                                 'template' => 'loop-' . $args[ 'post_type' ],
                                 'raw_html' => '<ul>' . \Flawless_F::get_template_part( 'loop', $args[ 'post_type' ] ), '</ul>', );
      }

      $response = array( 'query'         => false, //$query,
                         'total_results' => $total_results, 'all_results' => $all_results, 'current_filters' => $current_filters, );

      /** We're here, go ahead and cache the response */
      if( !$cached ) {
        set_transient( $query_hash, json_encode( $response ), 60 * 30 );
      }

      return $response;

    }

    /**
     * Shows a JSON error for DF requests (Temporary Table)
     *
     * @author williams@UD
     */
    static public function post_query_error( $err ) {

      $response = array( 'all_results' => array(), 'total_results' => 0, 'current_filters' => array(), 'error' => $err, );

      die( json_encode( $response ) );
    }

    /**
     * Post Box Options
     *
     * @author potanin@UD
     */
    static public function post_submitbox_misc_actions() {

      global $post, $hddp;

      /* Check if this Post Type is Event Related */
      if( !in_array( $post->post_type, (array) $hddp[ 'event_related_post_types' ] ) ) {
        return;
      }

      if( $post->post_status == 'post_status' ) {

      }

      $html[ ] = sprintf( '<input type="hidden" name="%1s" value="false" /><label><input type="checkbox" name="%2s" value="true" %3s />%4s</label>', 'do_not_generate_post_title', 'do_not_generate_post_title', checked( 'true', get_post_meta( $post->ID, 'do_not_generate_post_title', true ), false ), 'Do not generate title.' );

      $html[ ] = sprintf( '<input type="hidden" name="%1s" value="false" /><label><input type="checkbox" name="%2s" value="true" %3s />%4s</label>', 'do_not_generate_post_name', 'do_not_generate_post_name', checked( 'true', get_post_meta( $post->ID, 'do_not_generate_post_name', true ), false ), 'Do not generate permalink.' );

      // Added cross-domain tracking support.
      // @task https://projects.usabilitydynamics.com/projects/discodonniepresentscom-november-2012/tasks/55
      // @author potanin@UD
      if( $post->post_type === 'hdp_event' ) {
        $html[ ] = sprintf( '<input type="hidden" name="%1s" value="false" /><label><input type="checkbox" name="%2s" value="true" %3s />%4s</label>', 'disable_cross_domain_tracking', 'disable_cross_domain_tracking', checked( 'true', get_post_meta( $post->ID, 'disable_cross_domain_tracking', true ), false ), 'Disable cross domain tracking.' );
      }

      if( is_array( $html ) ) {
        echo '<ul class="flawless_post_type_options wp-tab-panel"><li>' . implode( '</li><li>', $html ) . '</li></ul>';
      }

    }

    /**
     * Renders JS for Event Search
     *
     * =USAGE=
     * [dynamic_filter post_title="label=Post Title,filter=input" hdp_artist="label=Artist,filter=dropdown" city="label=City,filter=dropdown" raw_html="label=Raw HTML,display=false"]
     *
     * @shortcode dynamic_filter
     * @author potanin@UD
     */
    static public function shortcode_dynamic_filter( $args = false, $content = '' ) {
      global $flawless;

      /** Setup the shortcode attributes first */
      $shortcode_attributes = array(
        'post_type'      => 'hdp_event',
        'filter_dom_id'  => 'dynamic_filter',
        'filter_element' => '#df_sidebar_filters', 'sorter_element' => "<div></div>",
        'per_page'       => (int) self::$hdp_posts_per_page, );

      /** Now setup our attributes */
      $attributes = array();

      foreach( $args as $key => $arg ) {
        if( !in_array( $key, array_keys( $shortcode_attributes ) ) ) {
          $attributes[ $key ] = $arg;
        }
      }

      /** Now get the shortcode atts by combining with the defaults */
      $args = shortcode_atts( $shortcode_attributes, $args );

      /** Now loop through our different things, and split by comma */
      foreach( $attributes as $key => &$att ) {
        $att = \Flawless_F::split_shortcode_att( $att );
        /** Check the att for the default value */
        if( in_array( 'default_value', array_keys( $att ) ) ) {
          /** We know we have a default value */
          $filter_query = array( $key => $att[ 'default_value' ] );
        }
      }

      /** Ensure per page is numeric */
      $args[ 'per_page' ] = (int) $args[ 'per_page' ];

      /** Setup our debug options */
      $debug = false; //array( 'dom_detail' => true, 'filter_detail' => false, 'filter_ux' => false, 'attribute_detail' => false, 'supported' => false, 'procedurals' => false, 'helpers' => false );

      wp_enqueue_script( 'jquery-ud-dynamic_filter' );

      /* We require attributes. If none passed, display message for administrator */
      if( empty( $attributes ) ) {
        return current_user_can( 'manage_options' ) ? __( 'Dynamic Filter Error: You have not specified any attributes' ) : '';
      }

      /** Add on the raw_html to ensure it is shown */
      $attributes[ 'raw_html' ] = array( 'display' => true );
      /** Get our post type object */
      $post_type_object = get_post_type_object( $args[ 'post_type' ] );
      $filter_config    = array(
        'attributes' => $attributes,
        'ajax'       => array(
          'url'  => admin_url( 'admin-ajax.php' ) . ( isset( $_REQUEST[ 'force_update' ] ) ? '?force_update=1' : '' ),
          'args' => array( 'action' => 'ud_df_post_query', 'post_type' => $args[ 'post_type' ] )
        ),
        'ux'         => array(
          'filter'       => $args[ 'filter_element' ],
          'sorter'       => $args[ 'sorter_element' ],
          'filter_label' => '<span></span>',
        ),
        'classes'    => array(
          'wrappers' => array(
            'results_wrapper' => 'hdp_results',
            'results'         => 'hdp_results_items',
            'element'         => 'df_top_wrapper ' . $args[ 'post_type' ],
          ),
          'results'  => array( 'row' => 'hdp_results_item' )
        ),
        'settings'   => array(
          'dom_limit'  => 9999,
          'per_page'   => $args[ 'per_page' ],
          'unique_tag' => 'id',
          'debug'      => $debug,
          'messages'   => array(
            'no_results' => sprintf( __( 'No %s Found.', HDDP ), $post_type_object->labels->name ),
            'load_more'  => sprintf( __( '<div class="df_load_status"><span>Displaying {1}</span> of {2} %s</div><a class="btn"><span>Show <em>{3}</em> More</span></a>', HDDP ), $post_type_object->labels->name ),
          )
        )
      );

      /** Add on our filter query if we have one */
      if( isset( $filter_query ) ) {
        $filter_config[ 'ajax' ][ 'args' ][ 'filter_query' ] = $filter_query;
      }
      /** Build/return our html */
      $html[ ] = $content;

      flawless_render_in_footer( '
  <script type="text/javascript">
  if( typeof jQuery.prototype.dynamic_filter === "function" ) { var ' . $args[ 'filter_dom_id' ] . '; jQuery(document).ready(function(){ ' . $args[ 'filter_dom_id' ] . ' = jQuery("#' . $args[ 'filter_dom_id' ] . '").dynamic_filter(jQuery.parseJSON( ' . json_encode( json_encode( $filter_config ) ) . ' ))}); }
  </script>'
      );

      if( $debug && current_user_can( 'manage_options' ) ) {
        $html[ ] = '<pre class="flawless_toggable_debug">$filter_config debug: ' . print_r( $filter_config, true ) . '</pre>';
      }

      return implode( '', (array) $html );

    }

    /**
     * Renders Gallery
     *
     * =USAGE=
     * [hdp_gallery title="{Some title}" content="{Some content}" show_as="{list}"] - Show images of current post. Default view is 'list'
     * [hdp_gallery post_id=3] - Show images of custom post. I.e., it can be hdp_gallery
     *
     * @author peshkov@UD
     */
    static public function shortcode_hddp_gallery( $args = false ) {
      global $post;

      $content = '';

      $data = wp_parse_args( $args, array(
        'post_id' => is_object( $post ) ? $post->ID : false,
        'title'   => false, // Optional. Custom title
        'content' => false, // Optional. Custom content
        'ids'     => '', // Optional. List of media item IDs.
        'show_as' => 'gallery', // Optional. Default is 'gallery'. Values: 'gallery', 'list'
        'orderby' => false, // Optional. Allows custom (random) sorting. Values: 'rand', 'ID', 'title', 'name', 'date', 'modified'
      ) );

      $_post = ( is_object( $post ) && $post->ID == $data[ 'post_id' ] ) ? $post : get_post( $data[ 'post_id' ], ARRAY_A );

      // Break if there is no post
      if( empty( $_post ) ) {
        return $content;
      }

      $ids = explode( ',', trim( $data[ 'ids' ] ) );
      foreach( $ids as $k => $id ) {
        $ids[ $k ] = trim( $id );
        if( empty( $ids[ $k ] ) || !is_numeric( $ids[ $k ] ) ) {
          unset( $ids[ $k ] );
        }
      }

      $orderby = $data[ 'orderby' ] && in_array( $data[ 'orderby' ], array( 'rand', 'ID', 'title', 'name', 'date', 'modified' ) ) ? $data[ 'orderby' ] : false;

      if( !empty( $ids ) ) {
        $query = array(
          'post__in'       => $ids,
          'post_type'      => 'attachment',
          'post_mime_type' => 'image',
          'numberposts'    => -1,
          'orderby'        => ( $orderby ? $orderby : 'post__in' ),
        );
      } else {
        $query = array(
          'post_parent'    => $_post->ID,
          'exclude'        => get_post_thumbnail_id(),
          'post_status'    => 'inherit',
          'post_type'      => 'attachment',
          'post_mime_type' => 'image',
          'order'          => 'ASC',
          'numberposts'    => -1,
          'orderby'        => ( $orderby ? $orderby : 'menu_order ID' ),
        );
      }

      $data[ 'images' ] = get_posts( $query );

      // Break if there are no images
      if( empty( $data[ 'images' ] ) ) {
        return $content;
      }

      // Set title and content if they are undefined.
      $data[ 'title' ]   = ( ( !is_object( $post ) || $post->ID != $_post->ID ) && !$data[ 'title' ] ) ? $_post->post_title : (string) $data[ 'title' ];
      $data[ 'content' ] = ( ( !is_object( $post ) || $post->ID != $_post->ID ) && !$data[ 'content' ] ) ? $_post->post_content : (string) $data[ 'content' ];

      // Check and fix if needed 'show_as' argument
      $data[ 'show_as' ] = in_array( $data[ 'show_as' ], array( 'gallery', 'list' ) ) ? $data[ 'show_as' ] : 'gallery';

      $data[ 'anchors' ] = array(
        'gallery' => array(
          'icon' => 'icon-events',
          'text' => __( 'Show As List' )
        ),
        'list'    => array(
          'icon' => 'icon-hdp_photo_gallery',
          'text' => __( 'Show As Gallery' )
        )
      );

      $data[ 'anchor' ] = $data[ 'anchors' ][ $data[ 'show_as' ] ];

      wp_enqueue_script( 'hddp_shortcode', get_stylesheet_directory_uri() . '/scripts/hddp.gallery.js', array( 'jquery', 'jquery-flexslider' ), HDDP_Version, true );

      /** Buffer output */
      ob_start();

      include 'templates/gallery.php';

      /** Get the content */
      $content = ob_get_clean();

      return $content;
    }

    /**
     * Simply a short code wrapper for the 'custom_loop' function we already created
     * =USAGE=
     * [hdp_custom_loop type="event"
     */
    static public function shortcode_hdp_custom_loop( $args = false, $content = '' ) {

      /** Setup the shortcode attributes first */
      $shortcode_attributes = array( 'post_type'    => 'hdp_event', 'per_page' => self::$hdp_posts_per_page,
                                     'do_shortcode' => true, 'include_filter' => false, );

      /** Now setup our attributes */
      $attributes = array();
      if( $args && is_array( $args ) && count( $args ) ) {
        foreach( $args as $key => $arg ) {
          if( !in_array( $key, array_keys( $shortcode_attributes ) ) ) {
            $attributes[ $key ] = $arg;
          }
        }
      }

      /* Combine then with the defalts */
      $args = shortcode_atts( $shortcode_attributes, $args );

      $type = $args[ 'post_type' ];
      unset( $args[ 'post_type' ] );
      $do_shortcode = $args[ 'do_shortcode' ];
      unset( $args[ 'do_shortcode' ] );

      $attributes = \UsabilityDynamics\Utility::extend( $attributes, $args );

      /** Call the function */
      $ret = self::custom_loop( $type, $attributes, true, $do_shortcode );

      return $ret;
    }

    /**
     * Hold our custom function for show the events header
     */
    static public function custom_loop( $type = false, $filter = array(), $from_shortcode = false, $do_shortcode = true ) {
      global $wpdb, $post, $WP_Query;

      $post_backup = $post;

      /** Setup our shortcode array */
      /** Old date shortcode - depreciating for the time being
       * '[dynamic_filter per_page='.hddp::$hdp_posts_per_page.' sorter_element="#hdp_results_sorter" post_title="label=Post Title,filter=input,filter_show_label=false,display=false" hdp_artist="label=Artist,filter=dropdown,display=false,filter_show_count=true" state="label=State,filter=dropdown,display=false,filter_show_count=true" city="label=City,filter=dropdown,display=false,filter_show_count=true" hdp_venue="label=Venue,filter=dropdown,display=false,filter_show_count=true" hdp_promoter="label=Promoter,filter=dropdown,display=false,filter_show_count=true" hdp_event_tour="label=Tour,filter=dropdown,display=false,filter_show_count=true" hdp_type="label=Event Type,filter=dropdown,display=false,filter_show_count=true" hdp_genre="label=Genre(s),filter=dropdown,display=false,filter_show_count=true" hdp_age_limit="label=Age Limit,filter=dropdown,display=false,filter_show_count=true" raw_html="label=Raw HTML" hdp_meta_date="label=Date,filter_ux=date_selector,filter=range,display=false,sortable=true" distance="label=Distance,sortable=true"]',
       */
      $shortcode_array = array( 'hdp_event'         => '[dynamic_filter per_page=' . self::$hdp_posts_per_page . ' sorter_element="#hdp_results_sorter" post_title="label=Post Title,filter=input,filter_show_label=false,display=false,filter_placeholder=Enter Artist&#44; City&#44; State&#44; or Venue" hdp_artist="label=Artist,filter=dropdown,display=false,filter_show_count=true" hdp_state="label=State,filter=dropdown,display=false,filter_show_count=true" hdp_city="label=City,filter=dropdown,display=false,filter_show_count=true" hdp_venue="label=Venue,filter=dropdown,display=false,filter_show_count=true" hdp_promoter="label=Promoter,filter=dropdown,display=false,filter_show_count=true" hdp_tour="label=Tour,filter=dropdown,display=false,filter_show_count=true" hdp_type="label=Event Type,filter=dropdown,display=false,filter_show_count=true" hdp_genre="label=Genre,filter=dropdown,display=false,filter_show_count=true" hdp_age_limit="label=Age Limit,filter=dropdown,display=false,filter_show_count=true" hdp_date_range="label=Date Range,filter=range" raw_html="label=Raw HTML" hdp_event_date="label=Date,display=false,sortable=true" distance="label=Distance,sortable=true"]',
                                'hdp_video'         => '[dynamic_filter post_type="hdp_video" per_page=' . self::$hdp_posts_per_page . ' sorter_element="#hdp_results_sorter" hdp_artist="label=Artist,filter=dropdown,display=false,filter_show_count=true" hdp_state="label=State,filter=dropdown,display=false,filter_show_count=true" hdp_city="label=City,filter=dropdown,display=false,filter_show_count=true" hdp_venue="label=Venue,filter=dropdown,display=false,filter_show_count=true" hdp_promoter="label=Promoter,filter=dropdown,display=false,filter_show_count=true" hdp_type="label=Event Type,filter=dropdown,display=false,filter_show_count=true" raw_html="label=Raw HTML"]',
                                'hdp_photo_gallery' => '[dynamic_filter post_type="hdp_photo_gallery" per_page=' . self::$hdp_posts_per_page . ' sorter_element="#hdp_results_sorter" hdp_artist="label=Artist,filter=dropdown,display=false,filter_show_count=true" hdp_state="label=State,filter=dropdown,display=false,filter_show_count=true" hdp_city="label=City,filter=dropdown,display=false,filter_show_count=true" hdp_venue="label=Venue,filter=dropdown,display=false,filter_show_count=true" hdp_promoter="label=Promoter,filter=dropdown,display=false,filter_show_count=true" hdp_type="label=Event Type,filter=dropdown,display=false,filter_show_count=true" raw_html="label=Raw HTML"]', );

      /** If w can't find the shortcode, return */
      if( !in_array( $type, array_keys( $shortcode_array ) ) ) return false;

      /** Setup the shortcode */
      $shortcode = $shortcode_array[ $type ];

      /** If we have a per_page, set it up now */
      $per_page = self::$hdp_posts_per_page;
      if( isset( $filter[ 'per_page' ] ) && is_numeric( $filter[ 'per_page' ] ) ) {
        $shortcode = str_ireplace( 'per_page=' . self::$hdp_posts_per_page, 'per_page=' . $filter[ 'per_page' ], $shortcode );
        $per_page  = $filter[ 'per_page' ];
        unset( $filter[ 'per_page' ] );
      }

      /** If we have a GET request, lets add it to the filter */
      if( isset( $_REQUEST[ 'df_q' ] ) && !empty( $_REQUEST[ 'df_q' ] ) && $type == 'hdp_event' ) {
        $dfq       = str_ireplace( '"', '', $_REQUEST[ 'df_q' ] );
        $dfq       = str_ireplace( ',', '', $dfq );
        $shortcode = str_ireplace( 'post_title="', 'post_title="default_value=' . $dfq . ',', $shortcode );
      }

      $include_filter = $filter[ 'include_filter' ];
      unset( $filter[ 'include_filter' ] );

      /** If we have a filter, we need to update our call to the shortcode */
      $where = '';
      if( count( $filter ) ) {
        $key   = array_keys( $filter );
        $key   = $key[ 0 ];
        $value = $filter[ $key ];
        /** We have our key and value, modify the shortcode to reflect */
        $shortcode = str_ireplace( $key . '="', $key . '="default_value=' . $value . ',', $shortcode );
        /** Modify our where statement */
        if( is_numeric( $value ) ) $where = " AND FIND_IN_SET( {$value}, `{$key}_ids` )"; else
          $where = " AND LOWER(`{$key}`) LIKE LOWER('%{$wpdb->escape( $value )}%')";
      }

      /** Setup the query array */
      $query_array = array( 'hdp_event'         => "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}ud_qa_hdp_event WHERE 1=1 {$where} AND STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) >= CURDATE() ORDER BY STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) ASC LIMIT " . $per_page,
                            'hdp_video'         => "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}ud_qa_hdp_video WHERE 1=1 {$where} ORDER BY STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) DESC LIMIT " . $per_page,
                            'hdp_photo_gallery' => "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}ud_qa_hdp_photo_gallery WHERE 1=1 {$where} ORDER BY STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) DESC LIMIT " . $per_page, );

      /** If the per page is 0, remove the limit factor */
      if( (int) $per_page === 0 ) {
        $query_array[ $type ] = str_ireplace( 'LIMIT 0', '', $query_array[ $type ] );
      }

      /** Buffer output */
      ob_start();

      include 'templates/custom_loop.php';

      if( $do_shortcode === true ) echo do_shortcode( $shortcode );

      /** Get the content */
      $content = ob_get_clean();

      /** Restore the post */
      $post = $post_backup;

      /** See if we need to return it, or not */
      if( $from_shortcode ) {
        return $content;
      } else {
        echo $content;
      }
    }

    /**
     * Management Page
     *
     * @author potanin@UD
     */
    static public function hddp_manage() {
      global $wpdb, $hddp;

      $ud_log = \Flawless_F::get_log( array( 'limit' => 100 ) );

      include 'templates/admin.site_management.php';

    }

    /**
     * Returns Post-Type specific tagline
     *
     * @author potanin@UD
     */
    static public function get_post_title( $post = false ) {

      if( !is_object( $post ) ) {
        $post = get_post( $post );
      }

      if( !$post ) {
        return;
      }

      switch( $post->post_type ) {

        case 'hdp_video':
        case 'hdp_event':
        case 'hdp_photo_gallery':

          $return[ 'artists' ] = implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_artist', array( 'fields' => 'names' ) ) ) . '';
          $return[ ]           = 'at';
          $return[ 'venues' ]  = implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_venue', array( 'fields' => 'names' ) ) ) . '';

          break;

      }

      $return = array_filter( (array) $return );

      if( empty( $return[ 'artists' ] ) || empty( $return[ 'artists' ] ) ) {
        return $post->post_title;
      }

      $return = html_entity_decode( implode( ' ', (array) $return ) );

      return $return;

    }

    /**
     * Create Post Name, which is used in the URL
     *
     * @author potanin@UD
     */
    static public function get_post_name( $post = false ) {

      $post = get_post( $post );
      if( !is_object( $post ) ) {
      }

      if( !$post ) {
        return;
      }

      $hdp_event_date = strtotime( get_post_meta( $post->ID, 'hdp_event_date', true ) );

      if( $hdp_event_date ) {
        $return[ ] = date( 'Y-md', $hdp_event_date );
      }

      switch( $post->post_type ) {

        case 'hdp_video' :
          $return[ ] = \Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_city', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );
          $return[ ] = \Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_venue', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );

          break;

        case 'hdp_photo_gallery' :
          $return[ ] = \Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_city', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );
          $return[ ] = \Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_venue', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );

          break;

        case 'hdp_event' :
          $return[ ] = \Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_city', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );
          $return[ ] = \Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_venue', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );

          break;
      }

      return wp_unique_post_slug( sanitize_title( implode( ' ', array_filter( ( array ) $return ) ) ), $post->ID, $post->post_status, $post->post_type, $post->post_parent );
    }

    /**
     * Filter to replace the [caption] shortcode text with HTML5 compliant code
     *
     * @param      $val
     * @param      $attr
     * @param null $content
     *
     * @return text HTML content describing embedded figure
     */
    static public function img_caption_shortcode( $val, $attr, $content = null ) {
      extract( shortcode_atts( array(
        'id'      => '',
        'align'   => '',
        'width'   => '',
        'caption' => ''
      ), $attr ) );

      if( 1 > (int) $width || empty( $caption ) ) {
        return $val;
      }

      $capid = '';
      if( $id ) {
        $id    = esc_attr( $id );
        $capid = 'id="figcaption_' . $id . '" ';
        $id    = 'id="' . $id . '" aria-labelledby="figcaption_' . $id . '" ';
      }

      return '<figure ' . $id . 'class="wp-caption ' . esc_attr( $align ) . '" >' . do_shortcode( $content ) . '<figcaption ' . $capid . 'class="wp-caption-text">' . $caption . '</figcaption></figure>';
    }

    /**
     * Returns Post-Type specific tagline
     * Photos: Photos from [POSTS NAME] in [City], [State] on [Date].
     * Videos: Video from [POSTS NAME] in [City], [State] on [Date].
     * Events: [POSTS NAME] in [City], [State] on [Date] at [TIME].
     *
     * @author potanin@UD
     */
    static public function get_post_excerpt( $event_id = false ) {

      global $post, $wpdb;

      if( !$event_id && $post ) {
        $event_id = $post->ID;
      }

      if( !is_object( $event_id ) ) {
        $event = get_event( $event_id );
      }

      if( !$event_id ) {
        return;
      }

      //$do_not_generate_post_title = get_post_meta( $event_id, 'do_not_generate_post_title', true );
      $post_tite = $wpdb->get_var( "SELECT post_title FROM {$wpdb->posts} WHERE ID = {$event_id}" );

      switch( $event[ 'post_type' ] ) {

        case 'hdp_video' :
          $return[ ] = 'Video from ';
          break;

        case 'hdp_photo_gallery' :
          $return[ ] = 'Photos from ';
          break;
      }

      $return[ ] = $post_tite;

      if( $event[ 'attributes' ][ 'hdp_city' ] ) {
        $return[ 'city' ] = trim( 'in ' . $event[ 'attributes' ][ 'hdp_city' ] );
      }

      if( $event[ 'attributes' ][ 'hdp_city' ] && $event[ 'attributes' ][ 'hdp_state' ] ) {
        $return[ 'city' ] = $return[ 'city' ] . ',';
      }

      if( $event[ 'attributes' ][ 'hdp_state' ] ) {
        $return[ 'state' ] = trim( $event[ 'attributes' ][ 'hdp_state' ] );
      }

      $return = array_filter( (array) $return );

      $hdp_event_date = strtotime( get_post_meta( $event_id, 'hdp_event_date', true ) );
      $hdp_event_time = strtotime( get_post_meta( $event_id, 'hdp_event_time', true ) );

      if( !empty( $return ) && $event[ 'attributes' ][ 'hdp_event_date' ] ) {
        $return[ ] = 'on ' . $event[ 'attributes' ][ 'hdp_event_date' ];
      }

      if( !empty( $return ) && $event[ 'attributes' ][ 'hdp_event_time' ] ) {
        $return[ ] = 'at ' . $event[ 'attributes' ][ 'hdp_event_time' ];
      }

      if( empty( $return ) ) {
        return;
      }

      $return = implode( ' ', (array) $return ) . '.';

      $return = strip_tags( $return );

      return $return;

    }

  }

}