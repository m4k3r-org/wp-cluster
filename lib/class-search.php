<?php
/**
 * Utility Customizer.
 *
 * @author team@UD
 * @version 0.2.4
 * @namespace UsabilityDynamics
 * @module Disco
 * @author potanin@UD
 */
namespace UsabilityDynamics\Theme {

  if( !class_exists( '\UsabilityDynamics\Disco\Search' ) ) {

    /**
     * Utility Class
     *
     * @class Utility
     * @author potanin@UD
     */
    class Search {


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
                    $filter = array_filter( (array) $filter, 'UsabilityDynamics\Disco\Bootstrap::check_blank_array' );
                    if( !count( $filter ) ) break;
                    $query .= " AND ( 1=2";
                    foreach( (array) $filter as $q ) {
                      if( empty( $q ) ) continue;
                      $query .= " OR FIND_IN_SET( '" . $wpdb->escape( $q ) . "', `{$key}_ids` )";
                    }
                    $query .= " )";
                    break;
                  case 'post_meta':
                    $filter = array_filter( (array) $filter, 'UsabilityDynamics\Disco\Bootstrap::check_blank_array' );
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
                                   'raw_html' => '<ul>' . \Flawless_F::get_template_part( 'templates/article/loop', $args[ 'post_type' ] ), '</ul>', );
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
       * Elasticsearch Query
       *
       * @global type $post
       */
      static public function elasticsearch_query() {

        try {

          //** Server connection. Settings go from ElasticSearch Plugin Settings page. */
          $elastica_client = new Elastica\Client( array(
            'connections' => array(
              'config' => array(
                'headers' => array( 'Accept' => 'application/json' ),
                'url'     => elasticsearch\Config::option( 'server_url' )
              )
            )
          ));

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

          $error = array( 'success' => false );

          $error = array_merge( $error, array( 'error' => $e->getMessage() ) );

          die( json_encode( $error ) );

        }

        ob_start();

        foreach( (array) $result[ 'raw' ][ 'facets' ] as $facet_key => $facet_data ) {
          include dirname( __DIR__ ) . "/templates/facets/facet-{$facet_data['_type']}.php";
        }

        $facets = ob_get_clean();

        ob_start();
        if( $result[ 'raw' ][ 'hits' ][ 'total' ] != 0 ) {
          foreach( (array) $result[ 'raw' ][ 'hits' ][ 'hits' ] as $hit_data ) {
            global $post;
            $post = $hit_data[ 'fields' ][ 'raw' ];
            include dirname( __DIR__ ) . "/templates/results/result-{$hit_data['_type']}.php";
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
          '<script type="text/javascript">if( typeof jQuery.prototype.new_ud_elasticsearch === "function" ) { jQuery(document).ready(function(){ jQuery("#elasticsearch-facets-' . $id . '").new_ud_elasticsearch()}); }</script>'
        );

        ob_start();
        include dirname( __DIR__ ) . '/templates/elasticsearch_facets.php';

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
        include dirname( __DIR__ ) . '/templates/elasticsearch_results.php';

        return ob_get_clean();
      }

    }

  }

}