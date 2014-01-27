<?php
/**
 * Name: HDDP Template Functions
 * Description: Functions meant to be used on frontend.
 * Author: Insidedesign
 * Author URI: http://www.insidedesign.info/
 *
 */

if( !function_exists( 'get_event' ) ) {
  /**
   * Get modified post object for events containing addition information.
   *
   * The object returned by this function inherits data from related taxonomies,
   * to include the term's post meta.
   *
   * @author potanin@UD
   */
  function get_event( $post_id = false, $args = array() ) {
    global $hddp, $wpdb, $post;

    $args = wp_parse_args( array(
      'load_images' => true
    ), $args );

    if( !is_numeric( $post_id ) ) {
      $post_id = $post->ID;
    }

    if( $return = wp_cache_get( $post_id , 'hdp_event_object' )) {
      return $return;
    }

    /** Get the post */
    $event = get_post( $post_id , ARRAY_A );

    /** If we can't find it, return */
    if( !in_array( $event[ 'post_type' ], array_keys( $hddp[ 'attributes' ] ) ) ) return false;

    /** Go ahead and go through and setup our initial arrays */
    $attributes = &$event[ 'attributes' ];
    $meta = &$event[ 'meta' ];
    $summary = &$event[ 'summary' ];
    $summary_qa = &$event[ 'summary_qa' ];
    $terms = &$event[ 'terms' ];

    /** STEP 1: Get all meta values for the post object.  */
    foreach( (array) $hddp[ 'attributes' ][ $event[ 'post_type' ] ] as $key => $att ) {

      /** See if we need to summarize */
      if( $att[ 'summarize' ] ) $summarize[ $key ] = $att;

      /** If we're not meta, continue */
      if( $att[ 'type'] != 'post_meta' ) continue;

      /** Do different things based on the attribute */
      $event[ 'meta' ][ $key ] = get_post_meta( $post_id, $key, true );

    }


    /** Get all taxonomies and terms associated with the object */
    foreach( $wpdb->get_results( "SELECT term_id, taxonomy FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id = {$post_id}" , ARRAY_A ) as $term ) {

      $term = get_term( $term[ 'term_id' ], $term[ 'taxonomy' ] , ARRAY_A);

      $term[ 'extended_post_id' ] = get_post_for_extended_term( $term[ 'term_id'], $term[ 'taxonomy' ] )->ID;

      /** Manually get my post meta */
      foreach( $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID WHERE p.post_type = '_tp_{$term[ 'taxonomy' ]}' AND pm.post_id = ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'extended_term_id' AND meta_value = '{$term[ 'term_id' ]}' LIMIT 0,1 )", ARRAY_A ) as $m ){
        $term[ $m[ 'meta_key' ] ] = $m[ 'meta_value' ];
      }

      $terms[ $term[ 'taxonomy' ] ][] = array_filter( (array) $term );

    }

    /** Finally, we need to summarize the ones that are needed */
    foreach( (array) $summarize as $key => $atts ) {

      $t = false;
      /** We do different things based on the type */
      switch( $atts[ 'type' ] ) {

        case 'primary':
          $t = $event[ $key ];
        break;

        case 'post_meta':
          $t = $event[ 'meta' ][ $key ];
        break;

        case 'taxonomy':
          $t = array();

          /** We need to specially handle, venues + city + state */

          if( isset( $event[ 'terms' ] ) && is_array( $event[ 'terms' ] ) && isset( $event[ 'terms' ][ $key ] ) && is_array( $event[ 'terms' ][ $key ] ) ){
            foreach( $event[ 'terms' ][ $key ] as $term ){
              $t[] = '<a href="'.get_term_link( $term[ 'slug' ], $term[ 'taxonomy' ] ).'">'.$term[ 'name' ]."</a>";
            }
            $t = implode( ', ', (array) $t );
          }
        break;

      }

      /** Do different things based on what we're working with */
      switch( $key ){
        case 'hdp_event_date':
          if( strtotime( $t ) ) {
            $t = date( get_option( 'date_format' ), strtotime( $t ) );
          }
          break;
        case 'hdp_venue':
          if( isset( $event[ 'terms' ][ 'hdp_city' ][ 0 ] ) && isset( $event[ 'terms' ][ 'hdp_state' ][ 0 ] ) ){
            $t .= '<br />';
            $t .= '<a href="'.get_term_link( $event[ 'terms' ][ 'hdp_city' ][ 0 ][ 'slug' ], $event[ 'terms' ][ 'hdp_city' ][ 0 ][ 'taxonomy' ] ).'">'.$event[ 'terms' ][ 'hdp_city' ][ 0 ][ 'name' ]."</a>, ";
            $t .= '<a href="'.get_term_link( $event[ 'terms' ][ 'hdp_state' ][ 0 ][ 'slug' ], $event[ 'terms' ][ 'hdp_state' ][ 0 ][ 'taxonomy' ] ).'">'.$event[ 'terms' ][ 'hdp_state' ][ 0 ][ 'name' ]."</a>";
          }
          break;
      }

      if( !$t ) {
        continue;
      }

      /** Now that we have our value, lets setup the final array */
      $summary[ $atts[ 'summarize' ] ] = array(
        'slug' => $key,
        'label' => $atts[ 'label' ],
        'value' => $t,
      );
      $summary_qa[ $key ] = $t;

      $attributes[ $key ] = strip_tags( $t );

    }

    /** Global actions */
    /** Thumbnail */
    $event[ 'post_thumbnail' ] = get_post_thumbnail_id( $post_id );

    /** Images */
    if( $args[ 'load_images' ] ) {
      $event[ 'images' ] = get_posts( array(
        'post_parent' => $post_id,
        'exclude' => array( $event[ 'post_thumbnail' ] ),
        'post_status' => 'inherit',
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'order' => 'ASC',
        'numberposts' => -1,
        'orderby' => 'menu_order ID'
      ));
    }

    /** Now do things based on the type */
    switch( $event[ 'post_type' ] ) {

      case 'hdp_event':

        // @ticket https://projects.usabilitydynamics.com/projects/discodonniepresentscom-november-2012/tasks/55
        $event[ 'meta'][ 'disable_cross_domain_tracking' ] = get_post_meta( $post_id, 'disable_cross_domain_tracking', true );

      break;

      case 'hdp_photo_gallery':

        foreach( (array) $event[ 'terms' ][ 'credit' ] as $credit_term ) {
          $event[ 'attributes' ][ 'print_credit' ][] = ( $credit_term[ 'hdp_website_url' ] ? '<a href="' . $credit_term[ 'hdp_website_url' ] . '" target="_blank">' : '' ) . $credit_term[ 'name' ] . ( $credit_term[ 'hdp_website_url' ] ? '</a>' : '' );
        }

        if( $event[ 'attributes' ][ 'print_credit' ] ) {
          $event[ 'attributes' ][ 'print_credit'] = 'Photos by ' . implode( ' and ', (array) $event[ 'attributes' ][ 'print_credit' ] ) . '.';
        }

      break;

      case 'hdp_video':

        foreach( (array) $event[ 'terms' ][ 'credit' ] as $credit_term ) {
          $event[ 'attributes' ][ 'print_credit' ][] = ( $credit_term[ 'hdp_website_url' ] ? '<a href="' . $credit_term[ 'hdp_website_url' ] . '" target="_blank">' : '' ) . $credit_term[ 'name' ] . ( $credit_term[ 'hdp_website_url' ] ? '</a>' : '' );
        }

        if( $event[ 'attributes' ][ 'print_credit' ] ) {
          $event[ 'attributes' ][ 'print_credit'] = 'Video by ' . implode( ' and ', (array) $event[ 'attributes' ][ 'print_credit' ] ) . '.';
        }

      break;

    }

    $event[ 'event_poster_id' ] = $event[ 'meta' ][ 'hdp_poster_id' ] ? $event[ 'meta' ][ 'hdp_poster_id' ] : $event[ 'post_thumbnail' ];

    if( $event[ 'meta' ][ 'longitude' ] && $event[ 'meta' ][ 'latitude' ] ) {

      $event[ 'location' ] = array(
        'location_type' => $event[ 'location_type' ],
        'city' => $event[ 'meta' ][ 'city' ],
        'state' => $event[ 'meta' ][ 'state' ],
        'state_code' => $event[ 'meta' ][ 'state_code' ],
        'postal_code' => $event[ 'meta' ][ 'postal_code' ],
        'geo_hash' => $event[ 'geo_hash' ],
        'coordinates' => array(
          'lat' => $event[ 'meta' ][ 'latitude' ],
          'lon' => $event[ 'meta' ][ 'longitude' ]
        )
      );

      $event[ 'geo_located' ] = true;

      // Build output for frontend JS
      $event[ 'json' ] = array(
        'geo_located' => 'true',
        'latitude' => $event[ 'meta' ][ 'latitude' ],
        'longitude' => $event[ 'meta' ][ 'longitude' ]
      );

    }

    /** Filter blank, or null values */
    $event = array_filter( (array) $event );
    $event[ 'meta' ] = array_filter( (array) $event[ 'meta' ] );

    /** Set cache */
    wp_cache_set( $post_id, $event, 'hdp_event_object' );

    /** Return */
    return $event;

  }
}

/**
 * Template function for the share button that will be in the breadcrumb bar
 * @author williams@ud
 */
if( !function_exists( 'hdp_share_button' ) ){
  function hdp_share_button( $for_iphone = false, $return = false ) {
    ob_start(); ?>

    <div class='hdp_share_wrapper <?php if( !$for_iphone ) { ?>not-for-iphone not-for-ipad<?php } ?>'>
      <div class='hdp_share_button'>
        <a class='btn' href='#'>Share</a>
      </div>
      <div class='hdp_share_links'>
        <ul>
          <li><a href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>" target="_blank">Share on Facebook</a></li>
          <li><a href="http://twitter.com/home/?status=<?php the_title();?>%20<?php the_permalink();?>" target="_blank">Share on Twitter</a></li>
          <li><a href="https://plusone.google.com/_/+1/confirm?hl=en&url=<?php the_permalink();?>" target="_blank">Share on Google+</a></li>
        </ul>
      </div>
    </div>

    <script type="text/javascript" language="javascript">
      if( typeof jQuery == 'function' ){ jQuery( document ).ready( function() {

        /** Hook into our share button, to show the wrapper */
        jQuery( '.hdp_share_button a' ).unbind( 'click' );
        jQuery( '.hdp_share_button a' ).click( function( e ) {

          e.preventDefault();
          jQuery( this ).parent().parent().find( '.hdp_share_links' ).toggle();

        } );

        /** Hook into when we leave the box */
        jQuery( '.hdp_share_links' ).mouseleave( function( e ) {

          /** Hide the div */
          jQuery( this ).hide();

        } );

      } ); }
    </script> <?php

    $output = ob_get_clean();
    if( $return ) return $output;
    echo $output;

  }
}

if( !function_exists( 'hdp_get_image_link_with_custom_size' ) ){
  /**
   * Returns Image link (url) with custom size.
   * Generates images with custom sizes if they don't exist.
   * 
   * @global $wpdb
   * @param type $atts
   * @return string
   * @author peshkov@UD
   */
  function hdp_get_image_link_with_custom_size( $attachment_id, $width, $height ) {
    global $wpdb;
    
    // Sanitize
    $height = absint( $height );
    $width = absint( $width );
    $needs_resize = true;

    // Look through the attachment meta data for an image that fits our size.
    $meta = wp_get_attachment_metadata( $attachment_id );
    $upload_dir = wp_upload_dir();
    $base_url = strtolower( $upload_dir['baseurl'] );
    $src = trailingslashit( $base_url ) . $meta[ 'file' ];
    foreach( $meta['sizes'] as $key => $size ) {
      if ( ( $size['width'] == $width && $size['height'] == $height ) || $key == sprintf( 'resized-%dx%d', $width, $height ) ) {
        $src = str_replace( basename( $src ), $size['file'], $src );
        $needs_resize = false;
        break;
      }
    }
    
    // If an image of such size was not found, we can create one.
    if ( $needs_resize ) {
      $attached_file = get_attached_file( $attachment_id );
      $resized = image_make_intermediate_size( $attached_file, $width, $height, true );
      if ( is_wp_error( $resized ) ) {
        return $resized;
      }
      
      // Let metadata know about our new size.
      $key = sprintf( 'resized-%dx%d', $width, $height );
      $meta['sizes'][$key] = $resized;
      $src = str_replace( basename( $src ), $resized['file'], $src );
      wp_update_attachment_metadata( $attachment_id, $meta );

      // Record in backup sizes so everything's cleaned up when attachment is deleted.
      $backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
      if ( ! is_array( $backup_sizes ) ) $backup_sizes = array();
      $backup_sizes[$key] = $resized;
      update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backup_sizes );
    }

    return array(
      'url' => esc_url( $src ),
      'width' => absint( $width ),
      'height' => absint( $height ),
    );
  }
}