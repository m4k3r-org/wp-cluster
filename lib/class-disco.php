<?php
/**
 * Disco Theme
 *
 */
namespace UsabilityDynamics {

  /**
   * Disco Theme
   *
   * @author Usability Dynamics
   */
  class Disco extends \UsabilityDynamics\Theme\Scaffold {

    /**
     * Theme ID.
     *
     * @param $id
     * @var string
     */
    public $id;

    /**
     * Theme Version.
     *
     * @param $version
     * @var string
     */
    public $version;

    /**
     * Theme Text Domain.
     *
     * @param $domain
     * @var string
     */
    public $domain = 'wp-disco';

    /** Setup our post types */
    static public $_types = array(
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
    static public $_attributes = array(
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
    static public $default_attribute = array(
      'type'        => 'primary',
      'summarize'   => false, /** False, or # in sort order */
      'label'       => false,
      'admin_label' => false,
      'admin_type'  => 'input',
      'qa'          => false,
      'placeholder' => ''
    );

    /** Setup the global per page number */
    static public $hdp_posts_per_page = 15;

    /**
     * Initialize Drop Theme.
     *
     *
     * @todo https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js
     * @todo https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js
     *
     *
     * @example
     *      $this->set( 'stuff.ss', 'asdfdsf' );
     *      $this->set( 'stuff.gruff', 'asdfdsf' );
     *
     */
    public function __construct() {
      global $wp_disco;

      $this->id = 'hddp';
      $this->version = '1.0.0';

      // Configure Theme.
      $this->initialize(array(
        'minify'    => true,
        'obfuscate' => true
      ));

      // Initialize Settings.
      $this->settings(array(
        'key' => 'hddp_options',
        'version' => $this->version
      ));

      // Declare Public Scripts.
      $this->scripts(array(
        'app' => get_stylesheet_directory() . '/scripts/app.js',
        'app.admin' => get_stylesheet_directory() . '/scripts/app.admin.js',
        'jquery.elasticsearch' => get_stylesheet_directory() . '/scripts/jquery.new.ud.elasticsearch.js',
        'jquery.fitvids' => get_stylesheet_directory() . '/scripts/jquery.fitvids.js',
        'jquery.cookie' => get_stylesheet_directory() . '/scripts/jquery.cookie.js',
        'jquery.flexslider' => get_stylesheet_directory() . '/scripts/jquery.flexslider.js',
        'jquery.jqtransform' => get_stylesheet_directory() . '/scripts/jquery.jqtransform.js',
        'jquery.simplyscroll' => get_stylesheet_directory() . '/scripts/jquery.simplyscroll.js'
      ));

      // Declare Public Styles.
      $this->styles(array(
        'app' => get_stylesheet_directory() . '/styles/app.css',
        'app.admin' => get_stylesheet_directory() . '/styles/app.admin.css',
        'content' => get_stylesheet_directory() . '/styles/content.css',
        'bootstrap' => get_stylesheet_directory() . '/styles/bootstrap.css',
        'jquery.jqtransform' => get_stylesheet_directory() . '/styles/jqtransform.css',
        'jquery.simplyscroll' => get_stylesheet_directory() . '/styles/simplyscroll.css'
      ));

      // Declare Public Models.
      $this->models(array(
        'theme'  => array(),
        'locale' => array()
      ));

      // Configure Post Types and Meta.
      $this->structure(array(
        'artist' => array(
          'type' => 'post'
        ),
        'venue' => array(
          'type' => 'post'
        ),
        'location' => array(
          'type' => 'post'
        ),
        'hdp_event' => array(
          'type'       => 'post',
          'attributes' => array(
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
          )
        ),
        'hdp_video' => array(
          'type'       => 'post',
          'attributes' => array(
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
            'hdp_state'
          )
        ),
        'hdp_photo_gallery' => array(
          'type' => 'post',
          'attributes' => array(
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
            'hdp_state'
          )
        )
      ));

      // Configure API Methods.
      $this->api(array(
        'search.AutoSuggest' => array(
          'key' => 'search_auto_suggest'
        ),
        'search.ElasticSearch' => array(
          'key' => 'search_elastic_search'
        ),
        'search.ElasticFilter' => array(
          'key' => 'search_elastic_filter'
        ),
        'compute.QuickAccessTables' => array(
          'key' => 'update_qa_all_tables',
          'callback' => function() {
            set_time_limit( 0 );

            foreach( (array) $hddp[ 'dynamic_filter_post_types' ] as $post_type ) {
              if( is_wp_error( Flawless_F::update_qa_table( $post_type, array( 'update'     => $post_type,
                                                                               'attributes' => self::_get_qa_attributes( $post_type ) )
                )
              )
              ) {
                $hddp[ 'runtime' ][ 'notices' ][ 'error' ][ ] = 'Could not create QA table for ' . $post_type . ' Post Type.';
              } else {
                Flawless_F::log( 'Succesfully created QA table for ' . $post_type . ' Post Type.' );
              }
            }

            wp_die( 'done updating' );

          }
        ),
        'compute.Coordinates' => array(
          'key' => 'update_lat_long',
          'callback' => function() {
            $updated = array();

            foreach( self::_get_event_posts() as $post_id ) {
              $updated[ ] = self::update_event_location( $post_id, array( 'add_log_entries' => false ) );
            }

            if( count( $updated ) > 0 ) {
              Flawless_F::log( 'Successfully updated addresses for (' . count( $updated ) . ') event(s).' );
            } else {
              Flawless_F::log( 'Attempted to do a bulk address update for all events, but no events were updated.' );
            }


            }
        ),
        'cloud.Synchronize' => array(
          'key' => 'synchronize_with_cloud',
          'callback' => function() {
            \UD_Functions::timer_start( 'synchronize_with_cloud' );

            foreach( (array) $hddp[ 'dynamic_filter_post_types' ] as $post_type ) {

              set_time_limit( 0 );

              $batch = array();

              if( $_GET[ 'start' ] && $_GET[ 'limit' ] ) {
                $query = "SELECT ID FROM {$wpdb->posts} WHERE post_type = '$post_type' ORDER BY post_date DESC LIMIT {$_GET['start']}, {$_GET['limit']};";
              } else {
                $query = "SELECT ID FROM {$wpdb->posts} WHERE post_type = '$post_type' ORDER BY post_date DESC;";
              }

              foreach( $wpdb->get_col( $query ) as $post_id ) {
                $result[] = UD_Cloud::index( get_event( $post_id ) );
              }

            }

            Flawless_F::log( 'Batch Cloud API update complete, pushed ' . count( $result ) . ' batches of 50 items in ' . \UD_Functions::timer_stop( 'synchronize_with_cloud' ) . ' seconds.' );

          }
        ),
        'flush.Logs' => array(
          'key' => 'clear_event_log',
          'callback' => function() {
            Flawless_F::delete_log();
            Flawless_F::log( 'Event log cleared by ' . $current_user->data->display_name . '.' );
          }
        ),
        'flush.Settings' => array(
          'key' => 'delete_hddp_options',
          'callback' => function() {
            delete_option( 'hddp_options' );
            Flawless_F::log( 'HDDP-Theme options deleted by ' . $current_user->data->display_name . '.' );
          }
        )
      ));

      // Configure Image Sizes.
      $this->media(array(
        'post-thumbnail' => array(
          'description' => '',
          'width' => 120,
          'height' => 90,
          'crop' => true
        ),
        'hd_large' => array(
          'description' => '',
          'width' => 890,
          'height' => 500,
          'crop' => true
        ),
        'hd_small' => array(
          'description' => '',
          'width' => 230,
          'height' => 130,
          'crop' => true
        ),
        'gallery' => array(
          'description' => '',
          'width' => 200,
          'height' => 999,
          'crop' => false
        ),
        'sidebar_poster' => array(
          'description' => 'Fit for maximum sidebar width, unlimited height',
          'width' => 310,
          'height' => 999,
          'crop' => false
        ),
        'tiny_thumbnail' => array(
          'description' => '',
          'width' => 180,
          'height' => 80
        ),
        'events_flyer_thumb' => array(
          'description' => 'Fit for events filter flyer width, unlimited height',
          'width' => 140,
          'height' => 999,
          'crop' => false
        ),
        'sidebar_thumb'  => array(
          'description' => '',
          'width' => 120,
          'height' => 100,
          'crop' => true
        ),
      ));

      // Declare Supported Theme Features.
      $this->supports(array(
        'custom-header'        => array(),
        'custom-skins'         => array(),
        'custom-background'    => array(),
        'header-dropdowns'     => array(),
        'header-business-card' => array(),
        'frontend-editor'      => array()
      ));

      // Head Tags.
      $this->head(array(
        array(
          'tag' => 'meta',
          'http-equip' => 'X-UA-Compatible',
          'content' => 'IE=edge'
        ),
        array(
          'tag' => 'meta',
          'name' => 'viewport',
          'content' => 'width=device-width, initial-scale=1.0'
        ),
        array(
          'tag' => 'meta',
          'charset' => get_bloginfo( 'charset' )
        ),
        array(
          'tag' => 'meta',
          'name' => 'description',
          'content' => ''
        ),
        array(
          'tag' => 'link',
          'rel' => 'shortcut icon',
          'href' => home_url( '/images/favicon.png' )
        ),
        array(
          'tag' => 'link',
          'rel' => 'pingback',
          'href' => get_bloginfo( 'pingback_url' )
        ),
        array(
          'tag' => 'link',
          'rel' => 'profile',
          'href' => 'http://gmpg.org/xfn/11'
        ),
        array(
          'tag' => 'script',
          'type' => 'application/javascript',
          'data-main' => '/scripts/app',
          'href' => 'cdn.udx.io/udx.requires.js'
        ),
        array(
          'tag' => 'link',
          'rel' => 'pingback',
          'href' => get_bloginfo( 'pingback_url' )
        )
      ));

      // Enables Customizer for Options.
      $this->customizer(array(
        'background-color' => array(),
        'header-banner'    => array()
      ));

      // Handle Theme Version Changes.
      $this->upgrade();

      return $wp_disco = $this;

    }

    /**
     * Primary Loader.
     * Scripts and styles are registered here so they overwriten Flawless scripts if needed.
     *
     * @author potanin@UD
     */
    public function init() {

      /* Define Child Theme Version */
      define( 'HDDP_Version', $this->version );

      /* Transdomain */
      define( 'HDDP', $this->domain );

      load_theme_textdomain( $this->domain, get_stylesheet_directory() . '/languages' );

      // Register Standard Scripts.
      wp_register_script( 'jquery-ud-form_helper', get_stylesheet_directory_uri() . '/scripts/jquery.ud.form_helper.js', array( 'jquery-ui-core' ), '1.1.3', true );
      wp_register_script( 'jquery-ud-elastic_filter', get_stylesheet_directory_uri() . '/scripts/jquery.ud.elastic_filter.js', array( 'jquery' ), '0.5', true );
      wp_register_script( 'jquery-new-ud-elasticsearch', get_stylesheet_directory_uri() . '/scripts/jquery.new.ud.elasticsearch.js', array( 'jquery' ), '1.0', true );
      wp_register_script( 'jquery-ud-dynamic_filter', get_stylesheet_directory_uri() . '/scripts/jquery.ud.dynamic_filter.js', array( 'jquery' ), '1.1.3', true );
      wp_register_script( 'jquery-ud-date-slector', get_stylesheet_directory_uri() . '/scripts/jquery.ud.date_selector.js', array( 'jquery-ui-core' ), '0.1.1', true );
      wp_register_script( 'jquery-ud-smart_buttons', '//cdn.usabilitydynamics.com/scripts/jquery.ud.smart_buttons/0.6/jquery.ud.smart_buttons.js', array( 'jquery-ui-core' ), '0.6', true );
      wp_register_script( 'jquery-ud-social', 'http' . '//cdn.usabilitydynamics.com/scripts/jquery.ud.social/0.3/jquery.ud.social.js', array( 'jquery-ui-core' ), '0.3', true );
      wp_register_script( 'jquery-ud-execute_triggers', '//cdn.usabilitydynamics.com/scripts/jquery.ud.execute_triggers/0.2/jquery.ud.execute_triggers.js', array( 'jquery-ui-core' ), '0.2', true );
      wp_register_script( 'jquery-cookie', get_stylesheet_directory_uri() . '/scripts/jquery.cookie.js', array( 'jquery' ), '1.7.3', false );
      wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?sensor=true' );

      add_filter( 'the_content', 'featured_image_in_feed' );

      add_action( 'widgets_init', function () {
        if( class_exists( 'HDP_Latest_Posts_Widget' ) ) {
          register_widget( 'HDP_Latest_Posts_Widget' );
        }
      } );

      add_action( 'edit_term', function ( $term_id, $something, $taxonomy ) {
        self::update_event_location( get_post_for_extended_term( $term_id, $taxonomy )->ID );
      }, 10, 3 );

      // Disable unsupported Carrington Build modules
      add_action( 'cfct-modules-included', function () {
        cfct_build_deregister_module( 'cfct_module_hero' );
        cfct_build_deregister_module( 'cfct_module_notice' );
        cfct_build_deregister_module( 'cfct_module_pullquote' );
        cfct_build_deregister_module( 'cfct_module_loop_subpages' );
        cfct_build_deregister_module( 'cfct_module_plain_text' );
      } );

      add_filter( 'elasticsearch_indexer_build_document', function ( $doc, $post ) {
        $doc[ 'event_date_time' ] = date( 'c', strtotime( get_post_meta( $post->ID, 'hdp_event_date', 1 ) . ' ' . get_post_meta( $post->ID, 'hdp_event_time', 1 ) ) );
        $lat                      = get_post_meta( $post->ID, 'latitude', 1 );
        $lon                      = get_post_meta( $post->ID, 'longitude', 1 );
        $doc[ 'location' ]        = array(
          'lat' => !empty( $lat ) ? $lat : 0,
          'lon' => !empty( $lon ) ? $lon : 0
        );
        $doc[ 'raw' ]             = get_event( $post->ID );

        return $doc;
      }, 10, 2 );

      /** First, go through my local items, and update my attributes */
      $__attributes = (array) self::$_attributes;

      foreach( $__attributes as $key => &$arr ) {
        $arr = Utility::extend( \UsabilityDynamics\Disco::$default_attribute, $arr );
      }

      /** Now go through our attributes */
      $attributes = array();

      foreach( self::$_types as $key => $val ) {
        $attributes[ $key ] = array();
        foreach( (array) $val as $att ) {
          $attributes[ $key ][ $att ] = $__attributes[ $att ];
        }
      }

      /* Merge default settings with DB settings */
      $hddp = Utility::extend( array(
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
        'page_template'             => array( 'template-all-events.php' ),
        'event_related_post_types'  => array( 'hdp_event', 'hdp_video', 'hdp_photo_gallery' ),
        'dynamic_filter_post_types' => array( 'hdp_photo_gallery', 'hdp_event', 'hdp_video' )
      ), get_option( 'hddp_options' ) );

      // Saving and deleting posts from QA table
      add_action( 'save_post', array( 'UsabilityDynamics\Disco', 'save_post' ), 1, 2 );

      // Enqueue Frontend Scripts & Styles.
      add_action( 'wp_enqueue_scripts', function () {
        wp_enqueue_script( 'app' );
        wp_enqueue_script( 'jquery-new-ud-elasticsearch' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'google-maps' );
      });

      // Enqueue Admin Scripts & Styles.
      add_action( 'admin_enqueue_scripts', function () {
        wp_enqueue_script( 'app.admin' );
      });

      // Admin Footer Scripts.
      add_action( 'admin_menu', function () {
        global $hddp;
      } );

      add_filter( 'the_category', function ( $c ) {
        return self::_backtrace_function( 'wp_popular_terms_checklist' ) ? '<span class="do_inline_hierarchial_taxonomy_stuff do_not_esc_html">' . $c . '</span>' : $c;
      } );

      add_filter( 'esc_html', function ( $s, $u = '' ) {
        return strpos( $s, 'do_not_esc_html' ) ? $u : $s;
      }, 10, 2 );

      add_action( 'flawless::extended_term_form_fields', function( $tag, $post ) {
        include dirname( __DIR__ ) . '/templates/admin.extended_term_form_fields.php';
      }, 10, 2 );

      add_action( 'flawless::header_bottom', function() {
        $header = flawless_breadcrumbs( array( 'hide_breadcrumbs' => false, 'wrapper_class' => 'breadcrumbs container', 'hide_on_home' => false, 'return' => true ) );
        $share  = hdp_share_button( false, true );
        echo $share . $header;
      } );

      add_filter( 'cfct-module-carousel-control-layout-order', function ( $order ) {
        return is_home() || is_front_page() ? array() : $order;
      } );

      add_filter( 'gform_shortcode_form', function ( $form ) {
        return preg_replace( '%(<select.*?>.+?<\/select>)%', '<div class="select-styled">$1</div>', $form );
      }, 10, 3 );

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

      // Remove URL from Comments Form.
      add_filter( 'comment_form_default_fields', function ( $fields ) {
        unset( $fields[ 'url' ] );

        return $fields;
      } );

      // Filter to replace the [caption] shortcode text with HTML5 compliant code.
      add_filter( 'img_caption_shortcode', function( $val, $attr, $content = null ) {

        $args = extract( shortcode_atts( array(
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
      }, 10, 3 );

    }

    /**
     * Display Nav Menu.
     *
     * @todo Add a way to configure depth from setings.
     *
     * @temporary
     */
    public function nav( $name, $location ) {

      $_class = array( 'flawless-menu', $name, $location );

      $_menu = wp_nav_menu( apply_filters( $name, array(
        'theme_location' => $location,
        'menu_class' => implode( ' ', $_class ),
        'fallback_cb' => false,
        'echo' => false )
      ));

    }

    /**
     * Force our custom template to load for Event post types
     *
     * @method redirect
     * @action template_redirect (10)
     * @author potanin@UD
     */
    public function redirect() {

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

      self::dynamic_filter_shortcode_handler();

    }

    /**
     * Return JSON post results Dynamic Filter requests
     *
     * @action admin_init (10)
     * @author potanin@UD
     */
    public function admin() {
      global $wpdb, $hddp, $current_user;

      // Adds options to Publish metabox.
      add_action( 'post_submitbox_misc_actions', function() {
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

      });

      // Add Address column to Venues taxonomy overview.
      add_filter( 'manage_edit-hdp_venue_columns', function ( $columns ) {

          $columns[ 'formatted_address' ] = __( 'Address', HDDP );

          return $columns;
        }
      );

      // Venue Column Content.
      add_filter( 'manage_hdp_venue_custom_column', function( $null, $column, $term_id ) {

        if( $column != 'formatted_address' ) {
          return;
        }

        return get_term_meta( $term_id, 'formatted_address', true );

      }, 10, 3 );

      // Add Event Date column to Event listings
      add_filter( 'manage_hdp_event_posts_columns', function ( $columns ) {

          unset( $columns[ 'tags' ] );
          unset( $columns[ 'date' ] );
          $columns[ 'formatted_address' ] = __( 'Geolocation & Cloud API Status', HDDP );
          //$columns[ 'sync_status' ] = __( 'Cloud API Status' , HDDP);
          //$columns[ 'hdp_event_date' ] = __( 'Event Date' , HDDP);
          $columns[ 'post_excerpt' ] = __( 'Tagline', HDDP );

          return $columns;
        }
      );

      // Event Column Content.
      add_filter( 'manage_hdp_event_posts_custom_column', function( $column, $post_id ) {
        $event = get_event( $post_id );

        switch( $column ) {

          case 'post_excerpt':
            echo $event[ 'post_excerpt' ] ? $event[ 'post_excerpt' ] : ' - ';
          break;

          case 'formatted_address':

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

          case 'hdp_event_date':
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

      }, 10, 2 );

      add_action( 'all_admin_notices', function() {
        global $hddp;

        foreach( (array) $hddp[ 'runtime' ][ 'notices' ][ 'error' ] as $notice ) {
          echo '<div class="error"><p>' . $notice . '</p></div>';
        }

        foreach( (array) $hddp[ 'runtime' ][ 'notices' ][ 'update' ] as $notice ) {
          echo '<div class="fade updated"><p>' . $notice . '</p></div>';
        }

      });

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
    public function get_events_count() {
      global $wpdb;

      $wpdb->show_errors();

      return number_format( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'hdp_event' AND post_status = 'publish'" ), 0 );
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
     * @action redirect)
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
      add_shortcode( 'dynamic_filter', array( get_class(), '_shortcode_dynamic_filter' ) );
      add_shortcode( 'hdp_custom_loop', array( get_class(), '_shortcode_loop' ) );
      add_shortcode( 'hddp_gallery', array( get_class(), '_shortcode_gallery' ) );

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
     * Checks to see if the value is blank
     *
     */
    static public function check_blank_array( $value ) {
      $value = trim( $value );

      return !empty( $value );
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
     * Renders JS for Event Search
     *
     * =USAGE=
     * [dynamic_filter post_title="label=Post Title,filter=input" hdp_artist="label=Artist,filter=dropdown" city="label=City,filter=dropdown" raw_html="label=Raw HTML,display=false"]
     *
     * @shortcode dynamic_filter
     * @author potanin@UD
     */
    static public function _shortcode_dynamic_filter( $args = false, $content = '' ) {
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
    static public function _shortcode_gallery( $args = false ) {
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

      include dirname( __DIR__ ) . '/templates/gallery.php';

      /** Get the content */
      $content = ob_get_clean();

      return $content;
    }

    /**
     * Simply a short code wrapper for the 'custom_loop' function we already created
     * =USAGE=
     * [hdp_custom_loop type="event"
     */
    static public function _shortcode_loop( $args = false, $content = '' ) {

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

      $attributes = Utility::extend( $attributes, $args );

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

      include dirname( __DIR__ ) . '/templates/custom_loop.php';

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