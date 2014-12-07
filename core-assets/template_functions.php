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

    if( $return = wp_cache_get( $post_id, 'hdp_event_object' ) ) {
      return $return;
    }

    /** Get the post */
    $event = get_post( $post_id, ARRAY_A );

    /** If we can't find it, return */
    if( !in_array( $event[ 'post_type' ], array_keys( $hddp[ 'attributes' ] ) ) ) return false;

    /** Go ahead and go through and setup our initial arrays */
    $attributes = & $event[ 'attributes' ];
    $meta       = & $event[ 'meta' ];
    $summary    = & $event[ 'summary' ];
    $summary_qa = & $event[ 'summary_qa' ];
    $terms      = & $event[ 'terms' ];

    /** STEP 1: Get all meta values for the post object.  */
    foreach( (array) $hddp[ 'attributes' ][ $event[ 'post_type' ] ] as $key => $att ) {

      /** See if we need to summarize */
      if( $att[ 'summarize' ] ) $summarize[ $key ] = $att;

      /** If we're not meta, continue */
      if( $att[ 'type' ] != 'post_meta' ) continue;

      /** Do different things based on the attribute */
      $event[ 'meta' ][ $key ] = get_post_meta( $post_id, $key, true );

    }

    /** Get all taxonomies and terms associated with the object */
    foreach( $wpdb->get_results( "SELECT term_id, taxonomy FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id = {$post_id}", ARRAY_A ) as $term ) {

      $term = get_term( $term[ 'term_id' ], $term[ 'taxonomy' ], ARRAY_A );

      $term[ 'extended_post_id' ] = get_post_for_extended_term( $term[ 'term_id' ], $term[ 'taxonomy' ] )->ID;

      /** Manually get my post meta */
      foreach( $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID WHERE p.post_type = '_tp_{$term['taxonomy']}' AND pm.post_id = ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'extended_term_id' AND meta_value = '{$term['term_id']}' LIMIT 0,1 )", ARRAY_A ) as $m ) {
        $term[ $m[ 'meta_key' ] ] = $m[ 'meta_value' ];
      }

      $terms[ $term[ 'taxonomy' ] ][ ] = array_filter( (array) $term );

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

          if( isset( $event[ 'terms' ] ) && is_array( $event[ 'terms' ] ) && isset( $event[ 'terms' ][ $key ] ) && is_array( $event[ 'terms' ][ $key ] ) ) {
            foreach( $event[ 'terms' ][ $key ] as $term ) {
              $t[ ] = '<a href="' . get_term_link( $term[ 'slug' ], $term[ 'taxonomy' ] ) . '">' . $term[ 'name' ] . "</a>";
            }
            $t = implode( ', ', (array) $t );
          }
          break;

      }

      /** Do different things based on what we're working with */
      switch( $key ) {
        case 'hdp_event_date':
          if( strtotime( $t ) ) {
            $t = date( get_option( 'date_format' ), strtotime( $t ) );
          }
          break;
        case 'hdp_venue':
          if( isset( $event[ 'terms' ][ 'hdp_city' ][ 0 ] ) && isset( $event[ 'terms' ][ 'hdp_state' ][ 0 ] ) ) {
            $t .= '<br />';
            $t .= '<a href="' . get_term_link( $event[ 'terms' ][ 'hdp_city' ][ 0 ][ 'slug' ], $event[ 'terms' ][ 'hdp_city' ][ 0 ][ 'taxonomy' ] ) . '">' . $event[ 'terms' ][ 'hdp_city' ][ 0 ][ 'name' ] . "</a>, ";
            $t .= '<a href="' . get_term_link( $event[ 'terms' ][ 'hdp_state' ][ 0 ][ 'slug' ], $event[ 'terms' ][ 'hdp_state' ][ 0 ][ 'taxonomy' ] ) . '">' . $event[ 'terms' ][ 'hdp_state' ][ 0 ][ 'name' ] . "</a>";
          }
          break;
      }

      if( !$t ) {
        continue;
      }

      /** Now that we have our value, lets setup the final array */
      $summary[ $atts[ 'summarize' ] ] = array(
        'slug'  => $key,
        'label' => $atts[ 'label' ],
        'value' => $t,
      );
      $summary_qa[ $key ]              = $t;

      $attributes[ $key ] = strip_tags( $t );

    }

    /** Global actions */
    /** Thumbnail */
    $event[ 'post_thumbnail' ] = get_post_thumbnail_id( $post_id );

    /** Images */
    if( $args[ 'load_images' ] ) {
      $event[ 'images' ] = get_posts( array(
        'post_parent'    => $post_id,
        'exclude'        => array( $event[ 'post_thumbnail' ] ),
        'post_status'    => 'inherit',
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'order'          => 'ASC',
        'numberposts'    => -1,
        'orderby'        => 'menu_order ID'
      ) );
    }

    /** Now do things based on the type */
    switch( $event[ 'post_type' ] ) {

      case 'hdp_event':

        // @ticket https://projects.usabilitydynamics.com/projects/discodonniepresentscom-november-2012/tasks/55
        $event[ 'meta' ][ 'disable_cross_domain_tracking' ] = get_post_meta( $post_id, 'disable_cross_domain_tracking', true );

        break;

      case 'hdp_photo_gallery':

        foreach( (array) $event[ 'terms' ][ 'credit' ] as $credit_term ) {
          $event[ 'attributes' ][ 'print_credit' ][ ] = ( $credit_term[ 'hdp_website_url' ] ? '<a href="' . $credit_term[ 'hdp_website_url' ] . '" target="_blank">' : '' ) . $credit_term[ 'name' ] . ( $credit_term[ 'hdp_website_url' ] ? '</a>' : '' );
        }

        if( $event[ 'attributes' ][ 'print_credit' ] ) {
          $event[ 'attributes' ][ 'print_credit' ] = 'Photos by ' . implode( ' and ', (array) $event[ 'attributes' ][ 'print_credit' ] ) . '.';
        }

        break;

      case 'hdp_video':

        foreach( (array) $event[ 'terms' ][ 'credit' ] as $credit_term ) {
          $event[ 'attributes' ][ 'print_credit' ][ ] = ( $credit_term[ 'hdp_website_url' ] ? '<a href="' . $credit_term[ 'hdp_website_url' ] . '" target="_blank">' : '' ) . $credit_term[ 'name' ] . ( $credit_term[ 'hdp_website_url' ] ? '</a>' : '' );
        }

        if( $event[ 'attributes' ][ 'print_credit' ] ) {
          $event[ 'attributes' ][ 'print_credit' ] = 'Video by ' . implode( ' and ', (array) $event[ 'attributes' ][ 'print_credit' ] ) . '.';
        }

        break;

    }

    $event[ 'event_poster_id' ] = $event[ 'meta' ][ 'hdp_poster_id' ] ? $event[ 'meta' ][ 'hdp_poster_id' ] : $event[ 'post_thumbnail' ];

    if( $event[ 'meta' ][ 'longitude' ] && $event[ 'meta' ][ 'latitude' ] ) {

      $event[ 'location' ] = array(
        'location_type' => $event[ 'location_type' ],
        'city'          => $event[ 'meta' ][ 'city' ],
        'state'         => $event[ 'meta' ][ 'state' ],
        'state_code'    => $event[ 'meta' ][ 'state_code' ],
        'postal_code'   => $event[ 'meta' ][ 'postal_code' ],
        'geo_hash'      => $event[ 'geo_hash' ],
        'coordinates'   => array(
          'lat' => $event[ 'meta' ][ 'latitude' ],
          'lon' => $event[ 'meta' ][ 'longitude' ]
        )
      );

      $event[ 'geo_located' ] = true;

      // Build output for frontend JS
      $event[ 'json' ] = array(
        'geo_located' => 'true',
        'latitude'    => $event[ 'meta' ][ 'latitude' ],
        'longitude'   => $event[ 'meta' ][ 'longitude' ]
      );

    }

    /** Filter blank, or null values */
    $event           = array_filter( (array) $event );
    $event[ 'meta' ] = array_filter( (array) $event[ 'meta' ] );

    /** Set cache */
    wp_cache_set( $post_id, $event, 'hdp_event_object' );

    /** Return */

    return $event;

  }
}

if( !function_exists( 'flawlessss_breadcrumbs' ) ) {
  /**
   * Prints out breadcrumbs
   *
   * @todo Improve the way term and taxonomy is handled here.
   * @version 0.1
   * @author potanin@UD
   */
  function flawless_breadcrumbs( $args = false ) {
    global $wp_query, $post, $flawless;

    $args = wp_parse_args( $args, array(
      'hide_breadcrumbs' => get_post_meta( $post->ID, 'hide_breadcrumbs', true ) == 'true' || $flawless[ 'hide_breadcrumbs' ]? true : false,
      'return' => false,
      'home_label' => __( 'Home' ),
      'home_link' => home_url(),
      'wrapper_class' => 'breadcrumbs',
      'divider' => ' <span class="divider">&raquo;</span> ',
      'hide_on_home' => true
    ));

    if( $args[ 'hide_breadcrumbs' ] ) {
      return;
    }

    $before = '<span class="current">';
    $after = '</span>';

    if ( $args[ 'hide_on_home' ] && ( is_home() || is_front_page() ) ) {
      return;
    }

    $html[] = '<a class="home_link" href="' . $args[ 'home_link' ]. '">' . $args[ 'home_label' ] . '</a> ' . $delimiter . ' ';

    if ( is_home() || is_front_page() ) {


    } elseif ( is_category() ) {

      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category( $thisCat );
      $parentCat = get_category( $thisCat->parent );
      if ( $thisCat->parent != 0 ) $html[] =( get_category_parents( $parentCat, TRUE, ' ' . $delimiter . ' ' ));
      $html[] = $before . single_cat_title( '', false ) . $after;

    } elseif ( is_day() ) {
      $html[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . $delimiter . ' ';
      $html[] = '<a href="' . get_month_link( get_the_time( 'Y' ),get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a> ' . $delimiter . ' ';
      $html[] = $before . get_the_time( 'd' ) . $after;

    } elseif ( is_month() ) {
      $html[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . $delimiter . ' ';
      $html[] = $before . get_the_time( 'F' ) . $after;

    } elseif ( is_year() ) {
      $html[] = $before . get_the_time( 'Y' ) . $after;

    } elseif ( is_single() && !is_attachment() ) {

      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object( get_post_type());
        $slug = $post_type->rewrite;

        $content_type_home = '';

        //** Check if this content type has a custom Root page and only display a link in this case */
        $root_page = hddp::get_root_page( get_post_type() );
        if ( $root_page ) {
          $content_type_home = get_permalink( $root_page );
        }

        /** Fix 'Pages' */
        if ( $post->post_type == 'page' ) {
          if ( $anc = get_post_ancestors( $post ) ) {
            $anc = wp_get_single_post( $anc[0] );
            $content_type_home = get_permalink( $anc->ID );
          }
        }

        if ( $anc ) {
          $title = $anc->post_title;
        } else {
          $title = $post_type->labels->name;
        }

        if ( !empty( $content_type_home ) ) {
          $html[ 'content_type_home' ] = '<a href="' . $content_type_home . '">' . $title . '</a>';
        } else {
          $html[ 'content_type_home' ] = '<span>' . $title . '</span>';
        }
        $html[ 'this_page' ] = $before . get_the_title() . $after;

      } else {
        $cat = get_the_category(); $cat = $cat[0];

        if( $cat ) {
          $html[] = get_category_parents( $cat, TRUE, ' ' . $delimiter . ' ' );
        }

        $html[] = $before . get_the_title() . $after;
      }

    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_search() ) {

      $taxonomy = get_taxonomy( $wp_query->query_vars[ 'taxonomy' ] );
      $post_type = get_post_type_object( get_post_type() );

      //** Check if this content type has a custom Root page */
      $root_page = hddp::get_root_page( get_post_type() );
      if ( $root_page ) {
        $content_type_home = get_permalink( $root_page );
      } else {
        $content_type_home = flawless_theme::filter_post_link( $args[ 'home_link' ]. '/' . $slug[ 'slug' ] . '/', $post );
      }

      /** Fix 'Pages' */
      if ( $post->post_type == 'page' ) {
        if ( $anc = get_post_ancestors( $post ) ) {
          $anc = wp_get_single_post( $anc[0] );
          $content_type_home = get_permalink( $anc->ID );
        }
      }

      if ( $anc ) {
        $title = $anc->post_title;
      } else {
        $title = $post_type->labels->name;
      }

      switch ( true ) {

        case is_tag():
          $html[ 'content_type_home' ] = '<a href="' . $content_type_home . '">' . $title . '</a>';
          $html[] = $before . get_queried_object()>name . $after;
          break;

        case is_tax():
          $html[ 'content_type_home' ] = '<a href="' . $content_type_home . '">' . $title . '</a>';
          $html[] = $before . get_queried_object()->name . $after;
          break;

        default:
          $html[] = $before . $post_type->labels->name . $after;
          break;

      }

    } elseif ( is_attachment() ) {
      $parent = get_post( $post->post_parent );
      $cat = get_the_category( $parent->ID ); $cat = $cat[0];

      //** Must check a category was found */
      if( $cat && !is_wp_error( $cat ) ) {
        $html[] = get_category_parents( $cat, TRUE, ' ' . $delimiter . ' ' );
      }

      $html[] = '<a href="' . get_permalink( $parent ) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
      $html[] = $before . get_the_title() . $after;

    } elseif ( is_page() && !$post->post_parent ) {
      $html[] = $before . get_the_title() . $after;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ( $parent_id ) {
        $page = get_page( $parent_id );
        $breadcrumbs[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse( $breadcrumbs );
      foreach ( $breadcrumbs as $crumb ) $html[] = $crumb . ' ' . $delimiter . ' ';
      $html[] = $before . get_the_title() . $after;

    } elseif ( is_search() ) {

      $html[] = $before . 'Search results for "' . get_search_query() . '"' . $after;

    } elseif ( is_tag() ) {

      $html[] = $before . 'Posts tagged "' . single_tag_title( '', false ) . '"' . $after;

    } elseif ( is_author() ) {
      global $author;
      $userdata = get_userdata( $author );
      $html[] = $before . 'Content by ' . $userdata->display_name . $after;

    } elseif ( is_404() ) {
      $html[] = $before . '404 Error' . $after;
    } elseif( is_tax() ) {

      $taxonomy = get_taxonomy( $wp_query->query_vars[ 'taxonomy' ] );

      $html[] = '<a href="' . $args[ 'home_link' ]. '/' . $taxonomy->rewrite[ 'slug' ]  . '">' . $taxonomy->labels->name . '</a> ';
      $html[] = $before . $wp_query->get_queried_object()->name . $after;

    } else {

      //$html[] = "<pre>";print_r( $wp_query );$html[] = "</pre>";
    }

    if ( get_query_var( 'paged' ) ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $page[] = ' ( ';
      $page[] = __( 'Page' ) . ' ' . get_query_var( 'paged' );
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $page[] = ' )';

      $html[] = implode( '', ( array ) $page );
    }

    $html = apply_filters( 'flawless::breadcrumb_trail', $html );

    $final_html = '<div class="' . $args[ 'wrapper_class' ] . '">' . implode(  apply_filters( 'flawless_bcreadcrumbs::delimiter', $args[ 'divider' ] ) , $html )  . '</div>';

    if( $args[ 'return' ] ) {
      return $final_html;
    }

    echo $final_html;

  }
}

function ddp_password_form() {
  global $post;

  $output = '<div class="password-protected">';
  $output .= '<h1>Password Protected: ' . $post->post_title . '</h1>';
  if ( $post->post_name == 'api' ) {
    $output .= '<p>The API is password protected. To use it please enter your password below.</p>';
  } else {
    $output .= '<p>The content &quot;' . $post->post_title . '&quot; is password protected. To view it please enter your password below.</p>';
  }
  $output .= '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">';
  $output .= '<input type="password" id="pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID ) . '" name="post_password" placeholder="Post Password" size="20" maxlength="20">';
  $output .= '<button type="submit" name="Submit" class="btn btn-blue">Submit</button>';
  $output .= '</form>';
  $output .= '</div>';

  return $output;
}
add_filter( 'the_password_form', 'ddp_password_form' );

/**
 * Helpful wrapper functions for microdata
 * @author Felix Arntz
 */
if ( !function_exists( 'microdata_manual' ) ) {
  function microdata_manual( $prop = '', $type = '', $echo = false ) {
    $ret = \DiscoDonniePresents\Microdata::manual( $prop, $type );
    if ( $echo ) {
      echo $ret;
    }
    return $ret;
  }
}
if ( !function_exists( 'microdata_type' ) ) {
  function microdata_type( $object, $prop = '', $echo = false ) {
    $ret = \DiscoDonniePresents\Microdata::type( $object, $prop );
    if ( $echo ) {
      echo $ret;
    }
    return $ret;
  }
}
if ( !function_exists( 'microdata_link' ) ) {
  function microdata_link( $objects, $wrap = false, $wrapper_property = '', $separator = ', ', $before = '', $after = '', $novalidate = false, $echo = false ) {
    $ret = \DiscoDonniePresents\Microdata::link( $objects, $wrap, $wrapper_property, $separator, $before, $after, $novalidate );
    if ( $echo ) {
      echo $ret;
    }
    return $ret;
  }
}
if ( !function_exists( 'microdata_meta' ) ) {
  function microdata_meta( $object, $fields = array(), $echo = false ) {
    $ret = \DiscoDonniePresents\Microdata::meta( $object, $fields );
    if ( $echo ) {
      echo $ret;
    }
    return $ret;
  }
}
if ( !function_exists( 'microdata_handler' ) ) {
  function microdata_handler( $microdata_args, $echo = false ) {
    $ret = \DiscoDonniePresents\Microdata::handler( $microdata_args );
    if ( $echo ) {
      echo $ret;
    }
    return $ret;
  }
}
if ( !function_exists( 'microdata_prepare_args' ) ) {
  function microdata_prepare_args( $args, $fields, $origin_class = '', $origin_function = '', $more_args = array() ) {
    return \DiscoDonniePresents\Microdata::prepare_args( $args, $fields, $origin_class, $origin_function, $more_args );
  }
}
// Microdata initialisation
add_action( 'wp_loaded', array( '\\DiscoDonniePresents\\Microdata', 'init' ) );

/**
 * Template function for the share button that will be in the breadcrumb bar
 *
 * @author williams@ud
 */
if( !function_exists( 'hdp_share_button' ) ) {
  function hdp_share_button( $for_iphone = false, $return = false ) {
    ob_start(); ?>

    <div class='hdp_share_wrapper <?php if( !$for_iphone ) { ?>not-for-iphone not-for-ipad<?php } ?>'>
      <div class='hdp_share_button'>
        <a class='btn' href='#'>Share</a>
      </div>
      <div class='hdp_share_links'>
        <ul>
          <li><a href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>" target="_blank">Share on Facebook</a></li>
          <li><a href="http://twitter.com/home/?status=<?php the_title(); ?>%20<?php the_permalink(); ?>" target="_blank">Share on Twitter</a></li>
          <li><a href="https://plusone.google.com/_/+1/confirm?hl=en&url=<?php the_permalink(); ?>" target="_blank">Share on Google+</a></li>
        </ul>
      </div>
    </div>

    <script type="text/javascript" language="javascript">
      if( typeof jQuery == 'function' ) {
        jQuery( document ).ready( function() {

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

        } );
      }
    </script> <?php

    $output = ob_get_clean();
    if( $return ) return $output;
    echo $output;

  }
}

if( !function_exists( 'hdp_get_image_link_with_custom_size' ) ) {
  /**
   * Returns Image link (url) with custom size.
   * Generates images with custom sizes if they don't exist.
   *
   * @param      $attachment_id
   * @param      $width
   * @param      $height
   * @param bool $crop
   *
   * @global     $wpdb
   *
   * @internal param \type $atts
   *
   * @return string
   * @author peshkov@UD
   */
  function hdp_get_image_link_with_custom_size( $attachment_id, $width, $height, $crop = false ) {
    global $wpdb;

    // Sanitize
    $height       = absint( $height );
    $width        = absint( $width );
    $needs_resize = true;

    // Look through the attachment meta data for an image that fits our size.
    $meta = wp_get_attachment_metadata( $attachment_id );

    $upload_dir = wp_upload_dir();
    $base_url   = strtolower( $upload_dir[ 'baseurl' ] );
    $src        = trailingslashit( $base_url ) . $meta[ 'file' ];
    foreach( $meta[ 'sizes' ] as $key => $size ) {
      if( !empty( $size ) && ( ( $size[ 'width' ] == $width && $size[ 'height' ] == $height ) || $key == sprintf( 'resized-%dx%d', $width, $height ) ) ) {
        $src          = str_replace( basename( $src ), $size[ 'file' ], $src );
        $needs_resize = false;
        break;
      }
    }

    // If an image of such size was not found, we can create one.
    if( $needs_resize ) {
      $attached_file = get_attached_file( $attachment_id );
      $resized       = image_make_intermediate_size( $attached_file, $width, $height, $crop );
      if( !is_wp_error( $resized ) && !empty( $resized ) ) {
        // Let metadata know about our new size.
        $key                     = sprintf( 'resized-%dx%d', $width, $height );
        $meta[ 'sizes' ][ $key ] = $resized;
        $src                     = str_replace( basename( $src ), $resized[ 'file' ], $src );
        wp_update_attachment_metadata( $attachment_id, $meta );

        // Record in backup sizes so everything's cleaned up when attachment is deleted.
        $backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
        if( !is_array( $backup_sizes ) ) $backup_sizes = array();
        $backup_sizes[ $key ] = $resized;
        update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backup_sizes );
      }
    }

    return array(
      'url'    => esc_url( $src ),
      'width'  => absint( $width ),
      'height' => absint( $height ),
    );
  }
}