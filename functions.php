<?php
/**
 * Author: Insidedesign
 * Author URI: http://www.insidedesign.info/
 *
 * @version 0.10
 * @author Insidedesign
 * @subpackage Flawless
 * @package Flawless
 */

// wp_clean_themes_cache();
// die('flawless-hddp');
@include_once( untrailingslashit( TEMPLATEPATH ) . '/core-assets/class_ud.php' );
@include_once( untrailingslashit( STYLESHEETPATH ) . '/core-assets/ud_saas.php' );
@include_once( untrailingslashit( STYLESHEETPATH ) . '/core-assets/ud_functions.php' );

// UD_Tests::http_methods( 'http://' );

/* Define Child Theme Version */
define( 'HDDP_Version', '1.0.0' );

/* Transdomain */
define( 'HDDP', 'HDDP' );

/* Initialize */
add_action( 'flawless::theme_setup::after', array( 'hddp', 'theme_setup' ) );
add_action( 'flawless::init_upper', array( 'hddp', 'init_upper' ) );
add_action( 'flawless::init_lower', array( 'hddp', 'init_lower' ) );

/**
 * Functionality for Theme
 * Adding Admin Notices: $hddp[ 'runtime' ][ 'notices' ][ 'error'][] = 'This is a notice';
 *
 * @author potanin@UD
 */
class hddp extends Flawless_F {

  /** Setup our post types */
  public static $hdp_post_types = array(
    'hdp_event'         => array(
      'post_title',
      'post_name',
      'hdp_event_date',
      'hdp_event_time',
      'hdp_artist',
      'hdp_tour',
      'hdp_age_limit',
      'hdp_genre',
      'hdp_venue',
      'hdp_promoter',
      'hdp_type',
      'hdp_city',
      'hdp_state',
      '_thumbnail_id',
      'latitude',
      'longitude',
      'city',
      'state',
      'state_code',
      'hdp_purchase_url',
      'hdp_facebook_rsvp_url',
      'hdp_date_range'
    ),
    'hdp_video'         => array(
      'post_title',
      'post_name',
      'hdp_event_date',
      'hdp_event_time',
      'hdp_artist',
      'hdp_tour',
      'hdp_age_limit',
      'hdp_genre',
      'hdp_venue',
      'hdp_promoter',
      'hdp_type',
      'hdp_credit',
      'hdp_video_url',
      'hdp_poster_id',
      'hdp_city',
      'hdp_state',
    ),
    'hdp_photo_gallery' => array(
      'post_title',
      'post_name',
      'hdp_event_date',
      'hdp_event_time',
      'hdp_artist',
      'hdp_tour',
      'hdp_age_limit',
      'hdp_genre',
      'hdp_venue',
      'hdp_promoter',
      'hdp_type',
      'hdp_credit',
      'hdp_facebook_url',
      'hdp_poster_id',
      'hdp_city',
      'hdp_state',
    )
  );

  /** Some variables to hold our QA table items */
  public static $all_attributes = array(
    'post_title'            => array(
      'label' => 'Title',
    ),
    'post_name'             => array(
      'label' => 'Slug',
    ),
    'hdp_event_date'        => array(
      'label'       => 'Date',
      'admin_label' => 'Date',
      'admin_type'  => 'datetime',
      'type'        => 'post_meta',
      'summarize'   => 105,

    ),
    'hdp_event_time'        => array(
      'label'       => 'Time',
      'admin_label' => 'Time',
      'type'        => 'post_meta',
      'summarize'   => 110,

    ),
    'hdp_artist'            => array(
      'label'     => 'Artist',
      'type'      => 'taxonomy',
      'summarize' => 225,

    ),
    'hdp_tour'              => array(
      'label'     => 'Tour',
      'type'      => 'taxonomy',
      'summarize' => 220,

    ),
    'hdp_age_limit'         => array(
      'label'     => 'Age Limit',
      'type'      => 'taxonomy',
      'summarize' => 115,

    ),
    'hdp_genre'             => array(
      'label'     => 'Genre',
      'type'      => 'taxonomy',
      'summarize' => 215,

    ),
    'hdp_venue'             => array(
      'label'     => 'Venue',
      'type'      => 'taxonomy',
      'summarize' => 120,

    ),
    'hdp_promoter'          => array(
      'label'     => 'Promoter',
      'type'      => 'taxonomy',
      'summarize' => 205,

    ),
    'hdp_type'              => array(
      'label'     => 'Type',
      'type'      => 'taxonomy',
      'summarize' => 210,

    ),
    'hdp_credit'            => array(
      'type'      => 'taxonomy',
      'label'     => 'Credit',
      'summarize' => 230,
    ),
    'credit'                => array(
      'type'      => 'taxonomy',
      'label'     => 'Credit',
      'summarize' => 231,

    ),
    '_thumbnail_id'         => array(
      'type' => 'post_meta',

    ),
    'latitude'              => array(
      'type' => 'post_meta',

    ),
    'longitude'             => array(
      'type' => 'post_meta',

    ),

    'hdp_city'              => array(
      'type'      => 'taxonomy',

      'label'     => 'City',
      'summarize' => -1,
    ),
    'hdp_state'             => array(
      'type'      => 'taxonomy',

      'label'     => 'State',
      'summarize' => -2,
    ),
    'city'                  => array(
      'type' => 'post_meta',

    ),
    'state'                 => array(
      'type' => 'post_meta',

    ),
    'state_code'            => array(
      'type' => 'post_meta',

    ),
    'formatted_address'     => array(
      'type' => 'post_meta',
    ),
    'hdp_purchase_url'      => array(
      'label'       => 'Buy Tickets',
      'type'        => 'post_meta',
      'admin_label' => 'Purchase',
      'placeholder' => 'Full Purchase URL',
    ),
    'hdp_facebook_rsvp_url' => array(
      'label'       => 'RSVP on Facebook',
      'type'        => 'post_meta',
      'placeholder' => 'Full RSVP URL',
      'admin_label' => 'RSVP',
    ),
    'hdp_facebook_url'      => array(
      'label'       => 'View on Facebook',
      'type'        => 'post_meta',
      'admin_label' => 'Facebook',
      'placeholder' => 'Full Facebook URL',
    ),
    'hdp_video_url'         => array(
      'label'       => 'View on Source',
      'type'        => 'post_meta',
      'admin_label' => 'Source',
      'placeholder' => 'Full Source URL',
    ),
    'hdp_poster_id'         => array(
      'type'        => 'post_meta',
      'admin_label' => 'Poster ID'
    ),
    'hdp_date_range'        => array(
      'type' => 'post_meta'
    )
  );

  /** Defaults */
  public static $default_attribute = array(
    'type'        => 'primary',
    'summarize'   => false, /** False, or # in sort order */
    'label'       => false,
    'admin_label' => false,
    'admin_type'  => 'input',
    'qa'          => false,
    'placeholder' => ''
  );

  /** Setup the global per page number */
  public static $hdp_posts_per_page = 15;

  /**
   * Primary Loader
   *
   * @author potanin@UD
   */
  static function theme_setup() {

    remove_theme_support( 'header-dropdowns' );
    remove_theme_support( 'custom-header' );
    remove_theme_support( 'custom-background' );
    remove_theme_support( 'header-business-card' );
    remove_theme_support( 'frontend-editor' );
    remove_theme_support( 'custom-skins' );

    UsabilityDynamics\Feature\Flag::set( 'hddp-flawless' );

    // Enable post-foramts and html5 only if Feature Flags match.
    if( UsabilityDynamics\Feature\Flag::get( 'ddp::2014', 'edm' ) ) {

      // add_theme_support( 'post-formats', array( 'video' ));

      add_theme_support( 'html5', array(
        'search-form',
        'gallery'
      ) );

    }

    remove_theme_support( 'custom-background' );
    remove_theme_support( 'custom-header' );

    load_theme_textdomain( HDDP, get_template_directory() . '/lang' );

  }

  /**
   * Primary Loader.
   * Scripts and styles are registered here so they overwriten Flawless scripts if needed.
   *
   * @author potanin@UD
   */
  static function init_upper() {
    global $wpdb, $flawless;

    wp_register_script( 'knockout', get_stylesheet_directory_uri() . '/js/knockout.js', array(), '2.1', true );
    wp_register_script( 'jquery-ud-form_helper', get_stylesheet_directory_uri() . '/js/jquery.ud.form_helper.js', array( 'jquery-ui-core' ), '1.1.3', true );
    wp_register_script( 'jquery-ud-smart_buttons', 'http' . ( is_ssl() ? 's' : '' ) . '://cdn.usabilitydynamics.com/js/jquery.ud.smart_buttons/0.6/jquery.ud.smart_buttons.js', array( 'jquery-ui-core' ), '0.6', true );
    wp_register_script( 'jquery-ud-social', 'http' . ( is_ssl() ? 's' : '' ) . '://cdn.usabilitydynamics.com/js/jquery.ud.social/0.3/jquery.ud.social.js', array( 'jquery-ui-core' ), '0.3', true );
    wp_register_script( 'jquery-ud-execute_triggers', 'http' . ( is_ssl() ? 's' : '' ) . '://cdn.usabilitydynamics.com/js/jquery.ud.execute_triggers/0.2/jquery.ud.execute_triggers.js', array( 'jquery-ui-core' ), '0.2', true );

    wp_register_script( 'jquery-ud-elastic_filter', get_stylesheet_directory_uri() . '/js/jquery.ud.elastic_filter.js', array( 'jquery' ), HDDP_Version, true );
    wp_register_script( 'jquery-new-ud-elasticsearch', get_stylesheet_directory_uri() . '/js/jquery.new.ud.elasticsearch.js', array( 'jquery' ), HDDP_Version, true );

    wp_register_script( 'jquery-ud-dynamic_filter', get_stylesheet_directory_uri() . '/js/jquery.ud.dynamic_filter.js', array( 'jquery' ), HDDP_Version, true );
    wp_register_script( 'jquery-ud-date-slector', get_stylesheet_directory_uri() . '/js/jquery.ud.date_selector.js', array( 'jquery-ui-core' ), '0.1.1', true );
    wp_register_script( 'jquery-jqtransform', get_stylesheet_directory_uri() . '/js/jquery.jqtransform.js', array( 'jquery' ), HDDP_Version, true );
    wp_register_script( 'jquery-simplyscroll', get_stylesheet_directory_uri() . '/js/jquery.simplyscroll.min.js', array( 'jquery' ), HDDP_Version, true );
    wp_register_script( 'jquery-flexslider-1.8', get_stylesheet_directory_uri() . '/js/jquery.flexslider.1.8.js', array( 'jquery' ), '1.8', true );
    wp_register_script( 'jquery-flexslider', get_stylesheet_directory_uri() . '/js/jquery.flexslider.js', array( 'jquery' ), '2.2.2', true );
    wp_register_script( 'jquery-cookie', get_stylesheet_directory_uri() . '/js/jquery.cookie.js', array( 'jquery' ), '1.7.3', false );

    wp_register_script( 'hddp-frontend-js', get_stylesheet_directory_uri() . '/js/hddp.frontend.js', array( 'jquery', 'jquery-jqtransform', 'jquery-flexslider', 'jquery-cookie', 'flawless-frontend' ), HDDP_Version, true );
    wp_register_script( 'hddp-backend-js', get_stylesheet_directory_uri() . '/js/hddp.backend.js', array( 'jquery-ui-tabs', 'flawless-admin-global', 'jquery-ud-execute_triggers' ), HDDP_Version, true );
    wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?sensor=true' );

    wp_register_style( 'hddp-backend-css', get_stylesheet_directory_uri() . '/css/hddp.backend.css' );
    wp_register_style( 'jquery-jqtransform', get_stylesheet_directory_uri() . '/css/jqtransform.css' );
    wp_register_style( 'jquery-simplyscroll', get_stylesheet_directory_uri() . '/css/jquery.simplyscroll.css' );

    wp_register_script( 'jquery-fitvids', get_stylesheet_directory_uri() . '/js/jquery.fitvids.js', array( 'jquery' ), HDDP_Version, true );

    //** DDP Elastic */
    wp_register_script( 'jquery-ddp-elastic-suggest', get_stylesheet_directory_uri() . '/js/jquery.elasticSearch.js', array( 'jquery' ), HDDP_Version, true );
    wp_register_script( 'elastic-dsl', get_stylesheet_directory_uri() . '/js/elastic.dsl.js', array(), HDDP_Version, true );
    wp_register_script( 'xmlhttpclient', get_stylesheet_directory_uri() . '/js/xmlhttpclient.js', array(), HDDP_Version, true );

    add_filter( 'the_content', array( 'hddp', 'featured_image_in_feed' ) );

    add_action( 'widgets_init', function () {
      if( class_exists( 'HDP_Latest_Posts_Widget' ) ) {
        register_widget( 'HDP_Latest_Posts_Widget' );
      }
    } );

    add_image_size( 'hd_large', 890, 460, true );
    add_image_size( 'hd_small', 230, 130, true );
    add_image_size( 'gallery', 200, 999 );
    add_image_size( 'sidebar_poster', 310, 999 );
    add_image_size( 'events_flyer_thumb', 140, 999 );
    add_image_size( 'tiny_thumbnail', 100, 80 );
    add_image_size( 'sidebar_thumb', 120, 100, true );

    set_post_thumbnail_size( 230, 130 );

    unregister_nav_menu( 'footer-menu' );

    //** Disable unsupported Carrington Build modules */
    add_action( 'cfct-modules-included', function () {
      cfct_build_deregister_module( 'cfct_module_hero' );
      cfct_build_deregister_module( 'cfct_module_notice' );
      cfct_build_deregister_module( 'cfct_module_pullquote' );
      cfct_build_deregister_module( 'cfct_module_loop_subpages' );
      cfct_build_deregister_module( 'cfct_module_plain_text' );
    } );

    add_filter( 'elasticsearch_indexer_build_document', function ( $doc, $post ) {
      $doc[ 'event_date_time' ]         = date( 'c', strtotime( get_post_meta( $post->ID, 'hdp_event_date', 1 ) . ' ' . get_post_meta( $post->ID, 'hdp_event_time', 1 ) ) );
      $doc[ 'event_date_human_format' ] = date( 'F j, Y', strtotime( get_post_meta( $post->ID, 'hdp_event_date', 1 ) . ' ' . get_post_meta( $post->ID, 'hdp_event_time', 1 ) ) );
      $lat                              = get_post_meta( $post->ID, 'latitude', 1 );
      $lon                              = get_post_meta( $post->ID, 'longitude', 1 );
      $doc[ 'location' ]                = array(
        'lat' => (float) ( !empty( $lat ) ? $lat : 0 ),
        'lon' => (float) ( !empty( $lon ) ? $lon : 0 )
      );
      $doc[ 'raw' ]                     = get_event( $post->ID );
      $doc[ 'permalink' ]               = get_permalink( $post->ID );
      $doc[ 'image_url' ]               = flawless_image_link( $doc[ 'raw' ][ 'event_poster_id' ], 'events_flyer_thumb' );

      return $doc;
    }, 10, 2 );

    flawless_set_color_scheme( 'skin-default.css' );

    flush_rewrite_rules();

  }

  /**
   * Primary Loader
   *
   * @author potanin@UD
   */
  static function init_lower() {
    global $hddp, $wpdb;

    /** First, go through my local items, and update my attributes */
    $_all_attributes = (array) hddp::$all_attributes;

    foreach( $_all_attributes as $key => &$arr ) {
      $arr = hddp::array_merge_recursive_distinct( hddp::$default_attribute, $arr );
    }

    /** Now go through our attributes */
    $attributes = array();

    foreach( hddp::$hdp_post_types as $key => $val ) {
      $attributes[ $key ] = array();
      foreach( (array) $val as $att ) {
        $attributes[ $key ][ $att ] = $_all_attributes[ $att ];
      }
    }

    /* Merge default settings with DB settings */
    $hddp = self::array_merge_recursive_distinct( array(
      'runtime'                   => array(
        'notices' => array()
      ),
      'automated_attributes'      => array(
        'hdp_event'         => array(
          'post_title',
          'post_excerpt',
          'post_name'
        ),
        'hdp_video'         => array(
          'post_title',
          'post_excerpt',
          'post_name'
        ),
        'hdp_photo_gallery' => array(
          'post_title',
          'post_excerpt',
          'post_name'
        )
      ),
      'attributes'                => $attributes,
      'manage_options'            => 'manage_options',
      'page_template'             => array( '_template-all-events.php' ),
      'event_related_post_types'  => array( 'hdp_event', 'hdp_video', 'hdp_photo_gallery' ),
      'dynamic_filter_post_types' => array( 'hdp_photo_gallery', 'hdp_event', 'hdp_video' )
    ), get_option( 'hddp_options' ) );

    add_action( 'wp_enqueue_scripts', function () {

      wp_enqueue_script( 'knockout' );
      wp_enqueue_script( 'hddp-frontend-js' );
      wp_enqueue_script( 'jquery-ui-tabs' );
      wp_enqueue_script( 'google-maps' );
      wp_enqueue_script( 'jquery-fitvids' );
      wp_enqueue_script( 'jquery-ui-datepicker' );
      wp_enqueue_style( 'jquery-jqtransform' );

      /** If we're on the front page, do simply scroll */
      if( is_front_page() ) {
        wp_enqueue_script( 'jquery-simplyscroll' );
        wp_enqueue_style( 'jquery-simplyscroll' );
      }

      wp_enqueue_script( 'jquery-ddp-elastic-suggest' );
      wp_enqueue_script( 'xmlhttpclient' );

    } );

    add_action( 'admin_enqueue_scripts', array( 'hddp', 'admin_enqueue_scripts' ) );

    add_action( 'admin_init', array( 'hddp', 'admin_init' ) );

    add_action( 'template_redirect', array( 'hddp', 'template_redirect' ) );
    add_action( 'template_redirect', array( 'hddp', 'dynamic_filter_shortcode_handler' ) );

    /** Saving and deleting posts from QA table */
    add_action( 'save_post', array( 'hddp', 'save_post' ), 1, 2 );

    add_filter( 'the_category', function ( $c ) {
      return hddp::_backtrace_function( 'wp_popular_terms_checklist' ) ? '<span class="do_inline_hierarchial_taxonomy_stuff do_not_esc_html">' . $c . '</span>' : $c;
    } );

    add_filter( 'esc_html', function ( $s, $u = '' ) {
      return strpos( $s, 'do_not_esc_html' ) ? $u : $s;
    }, 10, 2 );

    add_action( 'flawless::header_bottom', function () {
      $header = flawless_breadcrumbs( array( 'hide_breadcrumbs' => false, 'wrapper_class' => 'breadcrumbs container', 'hide_on_home' => false, 'return' => true ) );
      $share  = hdp_share_button( false, true );
      /** Do a preg replace to add our share button */
      /**$header = preg_replace( '/(<div[^>]*?>)/i', '$1' . $share, $header );
       * /** Echo it out */
      echo $share . $header;
    } );

    add_filter( 'cfct-module-carousel-control-layout-order', function ( $order ) {
      return is_home() || is_front_page() ? array() : $order;
    } );

    add_filter( 'gform_shortcode_form', function ( $form ) {
      return preg_replace( '%(<select.*?>.+?<\/select>)%', '<div class="select-styled">$1</div>', $form );
    }, 10, 3 );

    /**
     * Custom Blog Pagination
     *
     * @author korotkov@ud
     * @ticket https://ud-dev.com/projects/projects/discodonniepresentscom-november-2012/tasks/19
     */
    add_filter( 'cfct-module-loop-query-args', function ( $args, $data ) {
      if( !empty( $_REQUEST[ 'paging' ] ) ) {
        $args[ 'paged' ] = $_REQUEST[ 'paging' ];
      }

      return $args;
    }, 10, 2 );
    add_filter( 'cfct-module-looploop-html', function ( $html, $data, $args, $query ) {
      $pagination = '';
      if( function_exists( 'wp_pagenavi' ) ) {
        $pagination = wp_pagenavi( array(
          'query' => $query,
          'echo'  => false
        ) );
      }

      return $html . $pagination;
    }, 10, 4 );
    add_filter( 'get_pagenum_link', function ( $link ) {
      $link = preg_replace( '%/blog\?paging.*%', '/blog', $link );
      if( strpos( $link, 'blog/page' ) ) {
        $link = preg_replace( '%/page/(\d{1,10}).*%', '?paging=$1', $link );
      }

      return $link;
    } );

    //** Remove URL from comments form */
    add_filter( 'comment_form_default_fields', function ( $fields ) {
      unset( $fields[ 'url' ] );

      return $fields;
    } );

    add_filter( 'img_caption_shortcode', array( 'hddp', 'img_caption_shortcode' ), 10, 3 );

  }

  /**
   * Force our custom template to load for Event post types
   *
   * @action template_redirect (10)
   * @author potanin@UD
   */
  static function template_redirect() {
    global $post, $flawless;

    //die( '<pre>' . print_r( wp_get_theme(), true ) . '</pre>' );
    /** Modify our HTML  for the mobile nav bar */
    $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ] = hdp_share_button( true, true ) . $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ];

    add_filter( 'single_template', function ( $template ) {
      global $post, $hddp;

      if( !in_array( $post->post_type, (array) $hddp[ 'event_related_post_types' ] ) ) {
        return $template;
      }

      add_filter( 'body_class', function ( $classes ) {
        return array_merge( $classes, array( 'single_event_page' ) );
      } );

      return $template = locate_template( (array) $hddp[ 'page_template' ] );

    } );

  }

  /**
   * Get HDP-Event Posts.
   *
   */
  static function _get_event_posts( $args = array() ) {
    global $wpdb, $hddp;

    return $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ( '" . implode( "','", array_keys( array( hddp::$hdp_post_types ) ) ) . "' ) AND post_status = 'publish' " );
  }

  /**
   * Gets total events in the db
   */
  static function get_events_count() {
    global $wpdb;

    $wpdb->show_errors();

    return number_format( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'hdp_event' AND post_status = 'publish'" ), 0 );
  }

  /**
   * Placeholder so we can update post's location
   *
   * @version 1.1.0
   */
  static function save_post( $post_id, $post ) {
    global $hddp, $wpdb;

    //**  Verify if this is an auto save routine.  */
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return $post_id;
    }

    if( wp_is_post_revision( $post_id ) ) {
      return;
    }

    if( !in_array( $post->post_type, array_keys( self::$hdp_post_types ) ) ) {
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
            $wpdb->update( $wpdb->posts, array( 'post_title' => hddp::get_post_title( $post->ID ) ), array( 'ID' => $post_id ) );
          }
          break;

        case 'post_excerpt':
          $wpdb->update( $wpdb->posts, array( 'post_excerpt' => hddp::get_post_excerpt( $post->ID ) ), array( 'ID' => $post_id ) );
          break;

        case 'post_name':
          if( $_REQUEST[ 'do_not_generate_post_name' ] != 'true' ) {
            $wpdb->update( $wpdb->posts, array( 'post_name' => hddp::get_post_name( $post->ID ) ), array( 'ID' => $post_id ) );
          }
          break;

      }

    }
  }

  /**
   * Handle addition of shortcode and listener
   *
   * @todo Replace with wp_elastic()->query()
   * @action template_redirect (10)
   * @author potanin@UD
   */
  static function dynamic_filter_shortcode_handler() {
    global $post;

    //** New Elastic Search Shortcodes */
    add_shortcode( 'elasticsearch_results', array('hddp', 'elasticsearch_results') );
    add_shortcode( 'elasticsearch_facets', array('hddp', 'elasticsearch_facets') );


    // Used on home, vieo and gallery pages.
    add_shortcode( 'hdp_custom_loop', array( 'hddp', 'shortcode_hdp_custom_loop' ) );

    // Used a lot.
    add_shortcode( 'hddp_gallery', array( 'hddp', 'shortcode_hddp_gallery' ) );

    // Used in Attachments' post_content
    add_shortcode( 'hddp_url', array( 'hddp', 'shortcode_hddp_url' ) );

  }

  /**
   * New Elastic Search Facets
   * @param type $atts
   * @return type
   */
  public static function elasticsearch_facets( $atts ) {
    extract( shortcode_atts(array(
      'id' => 'none',
      'action' => 'elasticsearch_query',
      'type' => '',
      'size' => 10
    ), $atts ) );

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
  public static function elasticsearch_results( $atts ) {

    extract( shortcode_atts(array(
      'id' => 'none'
    ), $atts ) );

    ob_start();
    include 'templates/elasticsearch_results.php';
    return ob_get_clean();

  }

  /**
   *
   * @global type $post
   */

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
  static function update_event_location( $post_id = false, $args = array() ) {

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

    if( $formatted_address && $geo_located = UD_Functions::geo_locate_address( $formatted_address ) ) {

      foreach( (array) $geo_located as $attribute => $value ) {
        update_post_meta( $post_id, $attribute, $value );

        if( $venue_term_id ) {
          update_term_meta( $venue_term_id, $attribute, $value );
        }

      }

      return true;

    } elseif( !empty( $formatted_address ) ) {

    } else {

    }

    return false;

  }

  /**
   * Return JSON post results Dynamic Filter requests
   *
   * @action admin_init (10)
   * @author potanin@UD
   */
  static function admin_init() {
    global $wpdb, $hddp, $current_user;

    add_action( 'post_submitbox_misc_actions', array( 'hddp', 'post_submitbox_misc_actions' ) );

  }

  /**
   * Adds Address Column to Event Venue taxonomy table
   *
   * @action admin_init (10)
   * @author potanin@UD
   */
  static function event_venue_columns_data( $null, $column, $term_id ) {

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
  static function manage_hdp_event_posts_custom_column( $column, $post_id ) {

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
   * Admin Scripts
   *
   * @author potanin@UD
   */
  static function admin_enqueue_scripts() {

    /* General Scripts and CSS styles */
    wp_enqueue_script( 'hddp-backend-js' );
    wp_enqueue_style( 'hddp-backend-css' );

  }

  /**
   * Checks to see if the value is blank
   *
   */
  static function check_blank_array( $value ) {
    $value = trim( $value );

    return !empty( $value );
  }

  /**
   * Shows a JSON error for DF requests (Temporary Table)
   *
   * @author williams@UD
   */
  static function post_query_error( $err ) {

    $response = array( 'all_results' => array(), 'total_results' => 0, 'current_filters' => array(), 'error' => $err, );

    die( json_encode( $response ) );
  }

  /**
   * Post Box Options
   *
   * @author potanin@UD
   */
  static function post_submitbox_misc_actions() {
    global $post, $hddp;

    /* Check if this Post Type is Event Related */
    if( !in_array( $post->post_type, (array) $hddp[ 'event_related_post_types' ] ) ) {
      return;
    }

    $html[ ] = sprintf( '<input type="hidden" name="%1s" value="false" /><label><input type="checkbox" name="%2s" value="true" %3s />%4s</label>', 'do_not_generate_post_title', 'do_not_generate_post_title', checked( 'true', get_post_meta( $post->ID, 'do_not_generate_post_title', true ), false ), 'Do not generate title.' );
    $html[ ] = sprintf( '<input type="hidden" name="%1s" value="false" /><label><input type="checkbox" name="%2s" value="true" %3s />%4s</label>', 'do_not_generate_post_name', 'do_not_generate_post_name', checked( 'true', get_post_meta( $post->ID, 'do_not_generate_post_name', true ), false ), 'Do not generate permalink.' );

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
  static function shortcode_dynamic_filter( $args = false, $content = '' ) {
    global $flawless;

    /** Setup the shortcode attributes first */
    $shortcode_attributes = array(
      'post_type'      => 'hdp_event',
      'filter_dom_id'  => 'dynamic_filter',
      'filter_element' => '#df_sidebar_filters', 'sorter_element' => "<div></div>",
      'per_page'       => (int) hddp::$hdp_posts_per_page, );

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
      $att = Flawless_F::split_shortcode_att( $att );
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
          'load_more'  => sprintf( __( '
<div class="df_load_status">
	<span>Displaying {1}</span> of {2} %s
</div><a class="btn"><span>Show <em>{3}</em> More</span></a>', HDDP ), $post_type_object->labels->name ),
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
   * Dummy shortcode.
   * Returns empty string.
   * It's used by self::shortcode_hddp_gallery()
   *
   * =USAGE=
   * Add [hddp_url href="http://example.com"] to Image Description textarea.
   * shortcode_hddp_gallery will parse it and will set custom url for current image in gallery.
   *
   * @author peshkov@UD
   */
  static public function shortcode_hddp_url( $args = false ) {
    return '';
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
      'post_id'  => is_object( $post ) ? $post->ID : false,
      'fancybox' => false, // Optional. Enable/disable fancybox. Values: 'true', 'on'. By default, disabled.
      'title'    => false, // Optional. Custom title
      'content'  => false, // Optional. Custom content
      'ids'      => '', // Optional. List of media item IDs.
      'show_as'  => 'gallery', // Optional. Default is 'gallery'. Values: 'gallery', 'list'
      'orderby'  => false, // Optional. Allows custom (random) sorting. Values: 'rand', 'ID', 'title', 'name', 'date', 'modified'
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

    // Loop images and parse postr_content ( description ) for custom url ( e.g. [hddp_url="http://example.com"] )
    $pattern = "\[\[?hddp_url(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)\]?";
    foreach( $data[ 'images' ] as $k => $image ) {
      $url = false;
      preg_match( "/$pattern/s", $image->post_content, $matches );
      if( !empty( $matches ) ) {
        $attrs = shortcode_parse_atts( $matches[ 1 ] );
        if( !empty( $attrs[ 'href' ] ) ) {
          $url = $attrs[ 'href' ];
        }
      }
      $data[ 'images' ][ $k ]->custom_url = $url;
    }

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

    wp_enqueue_style( 'hddp-jquery-fancybox', get_stylesheet_directory_uri() . '/css/jquery.fancybox.css', array(), HDDP_Version );
    wp_enqueue_script( 'hddp-jquery-fancybox', get_stylesheet_directory_uri() . '/js/jquery.fancybox.min.js', array( 'jquery', 'jquery-flexslider' ), HDDP_Version, true );
    wp_enqueue_script( 'hddp_shortcode', get_stylesheet_directory_uri() . '/js/hddp.gallery.js', array( 'jquery', 'jquery-flexslider', 'hddp-jquery-fancybox' ), HDDP_Version, true );

    /** Buffer output */
    ob_start();

    include 'templates/gallery.php';

    /** Get the content */
    $content = ob_get_clean();

    return $content;
  }

  /**
   *
   * @todo Utizlie wp_elastic() results.
   */
  static public function shortcode_hdp_custom_loop( $args = false, $content = '' ) {

    $args = shortcode_atts( array(), $args );

    return '<pre>' . print_r( $args, true ) . '</pre>';
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
        $return[ ] = Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_city', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );
        $return[ ] = Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_venue', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );

        break;

      case 'hdp_photo_gallery' :
        $return[ ] = Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_city', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );
        $return[ ] = Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_venue', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );

        break;

      case 'hdp_event' :
        $return[ ] = Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_city', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );
        $return[ ] = Flawless_F::create_slug( implode( ', ', (array) wp_get_object_terms( $post->ID, 'hdp_venue', array( 'fields' => 'names' ) ) ), array( 'separator' => '' ) );

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

  static public function featured_image_in_feed( $content ) {
    global $post;
    if( is_feed() ) {
      if( has_post_thumbnail( $post->ID ) ) {
        $output  = get_the_post_thumbnail( $post->ID, 'hd_large', array( 'style' => 'margin:10px 0 10px 0;' ) );
        $content = $output . $content;
      }
    }

    return $content;
  }

}


