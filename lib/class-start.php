<?php

namespace UsabilityDynamics\Theme\Disco {

  if( !class_exists( 'UsabilityDynamics\Theme\Disco\Start' ) ) {


    /**
     * Disco Theme
     *
     * @author Usability Dynamics
     */
    class Start extends \UsabilityDynamics\Flawless\Bootstrap {

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
       * WP-Disco Theme Constructor.
       *
       */
      public function __construct() {

        $this->id = 'disco';
        $this->version = '1.0.0';

        /* Define Child Theme Version */
        define( 'HDDP_Version', $this->version );

        /* Transdomain */
        define( 'HDDP', $this->domain );

        include_once( untrailingslashit( __DIR__ ) . '/legacy/ud_saas.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/ud_functions.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/ud_tests.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/backend-functions.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/business-card.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/class-flawless-utility.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/front-end-editor.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/login_module.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/theme_ui.php' );
        include_once( untrailingslashit( __DIR__ ) . '/legacy/shortcodes.php' );

        // Disco Libraries.
        include_once( untrailingslashit( __DIR__ ) . '/widgets.php' );
        include_once( untrailingslashit( __DIR__ ) . '/template.php' );

        if( !class_exists( 'UsabilityDynamics\Theme\Disco' ) ) {
          wp_die( '<h1>Fatal Error</h1><p>Disco Theme not found.</p>' );
        }

        add_action( 'flawless::init', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'admin' ) );
        add_action( 'flawless::theme_setup::after', array( $this, 'setup' ) );
        add_action( 'template_redirect', array( $this, 'redirect' ) );

      }

      /**
       * Primary Loader.
       * Scripts and styles are registered here so they overwriten Flawless scripts if needed.
       *
       * @author potanin@UD
       */
      public function init() {

        // Configure Theme.
        $this->initialize( array(
          'minify'    => true,
          'obfuscate' => true
        ) );

        // Initialize Settings.
        $this->settings( array(
          'key' => 'hddp_options'
        ));

        // Declare Dynamic Assets.
        $this->dynamic( array(
          'scripts' => array(
            'app.js' => 'console.log( "app.js" );'
          ),
          'models'  => array(
            'theme.json'  => '{}',
            'locale.json' => '{}'
          )
        ) );

        // Enable Public Assets.
        $this->rewrites( array(
          'scripts' => true,
          'styles'  => true
        ) );

        // Handle Theme Version Changes.
        $this->upgrade();

        // Configure Post Types and Meta.
        $this->structure( array(
          'artist'            => array(
            'type' => 'post'
          ),
          'venue'             => array(
            'type' => 'post'
          ),
          'location'          => array(
            'type' => 'post'
          ),
          'hdp_event'         => array(
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
          'hdp_video'         => array(
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
              'hdp_facebook_url',
              'hdp_poster_id',
              'hdp_city',
              'hdp_state'
            )
          )
        ) );

        // Configure API Methods.
        $this->api( array(
          'search.Elastic'       => array(),
          'search.DynamicFilter' => array()
        ) );

        // Configure Image Sizes.
        $this->media( array(
          'thumbnail'      => array(),
          'hd_large'       => array(),
          'hd_small'       => array(),
          'gallery'        => array(),
          'sidebar_poster' => array(),
          'tiny_thumbnail' => array(),
          'sidebar_thumb'  => array(),
        ) );

        // Declare Supported Theme Features.
        $this->supports( array(
          'custom-header'        => array(),
          'custom-skins'         => array(),
          'custom-background'    => array(),
          'header-dropdowns'     => array(),
          'header-business-card' => array(),
          'frontend-editor'      => array()
        ) );

        // Enables Customizer for Options.
        $this->customizer( array(
          'background-color' => array(),
          'header-banner'    => array()
        ) );

        // Register Scripts.
        wp_register_script( 'app', home_url( '/scripts/app.js' ), array( 'jquery', 'jquery-jqtransform', 'jquery-flexslider', 'jquery-cookie', 'flawless' ), HDDP_Version, true );
        wp_register_script( 'app.admin', home_url( '/scripts/app.admin.js' ), array( 'jquery', 'jquery-jqtransform', 'jquery-flexslider', 'jquery-cookie', 'flawless' ), HDDP_Version, true );
        wp_register_script( 'knockout', get_stylesheet_directory_uri() . '/scripts/knockout.js', array(), '2.1', true );
        wp_register_script( 'jquery-ud-form_helper', get_stylesheet_directory_uri() . '/scripts/jquery.ud.form_helper.js', array( 'jquery-ui-core' ), '1.1.3', true );
        wp_register_script( 'jquery-ud-elastic_filter', get_stylesheet_directory_uri() . '/scripts/jquery.ud.elastic_filter.js', array( 'jquery' ), '0.5', true );
        wp_register_script( 'jquery-new-ud-elasticsearch', get_stylesheet_directory_uri() . '/scripts/jquery.new.ud.elasticsearch.js', array( 'jquery' ), '1.0', true );
        wp_register_script( 'jquery-ud-dynamic_filter', get_stylesheet_directory_uri() . '/scripts/jquery.ud.dynamic_filter.js', array( 'jquery' ), '1.1.3', true );
        wp_register_script( 'jquery-ud-date-slector', get_stylesheet_directory_uri() . '/scripts/jquery.ud.date_selector.js', array( 'jquery-ui-core' ), '0.1.1', true );
        wp_register_script( 'jquery-jqtransform', get_stylesheet_directory_uri() . '/scripts/jquery.jqtransform.js', array( 'jquery' ), HDDP_Version, true );
        wp_register_script( 'jquery-simplyscroll', get_stylesheet_directory_uri() . '/scripts/jquery.simplyscroll.min.js', array( 'jquery' ), HDDP_Version, true );
        wp_register_script( 'jquery-flexslider-1.8', get_stylesheet_directory_uri() . '/scripts/jquery.flexslider.1.8.js', array( 'jquery' ), '1.8', true );
        wp_register_script( 'jquery-flexslider', get_stylesheet_directory_uri() . '/scripts/jquery.flexslider.js', array( 'jquery' ), '2.2.2', true );
        wp_register_script( 'jquery-cookie', get_stylesheet_directory_uri() . '/scripts/jquery.cookie.js', array( 'jquery' ), '1.7.3', false );
        wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?sensor=true' );
        wp_register_script( 'jquery-ud-smart_buttons', '//cdn.usabilitydynamics.com/scripts/jquery.ud.smart_buttons/0.6/jquery.ud.smart_buttons.js', array( 'jquery-ui-core' ), '0.6', true );
        wp_register_script( 'jquery-ud-social', 'http' . '//cdn.usabilitydynamics.com/scripts/jquery.ud.social/0.3/jquery.ud.social.js', array( 'jquery-ui-core' ), '0.3', true );
        wp_register_script( 'jquery-ud-execute_triggers', '//cdn.usabilitydynamics.com/scripts/jquery.ud.execute_triggers/0.2/jquery.ud.execute_triggers.js', array( 'jquery-ui-core' ), '0.2', true );
        wp_register_script( 'jquery-fitvids', get_stylesheet_directory_uri() . '/scripts/jquery.fitvids.js', array( 'jquery' ), HDDP_Version, true );

        // Register Styles.
        wp_register_style( 'app', home_url( '/styles/app.css' ) );
        wp_register_style( 'app.admin', home_url( '/styles/app.admin.css' ) );
        wp_register_style( 'jquery-jqtransform', home_url( '/styles/jquery-jqtransform.css' ) );
        wp_register_style( 'jquery.simplyscroll', home_url( '/styles/jquery.simplyscroll.css' ) );

        add_filter( 'the_content', 'featured_image_in_feed' );

        add_action( 'widgets_init', function () {
          if( class_exists( 'HDP_Latest_Posts_Widget' ) ) {
            register_widget( 'HDP_Latest_Posts_Widget' );
          }
        } );

        add_action( 'edit_term', function ( $term_id, $something, $taxonomy ) {
          self::update_event_location( get_post_for_extended_term( $term_id, $taxonomy )->ID );
        }, 10, 3 );

        //** HD video standard - 1.77:1 (16:9)  */
        add_image_size( 'hd_large', 890, 500, true );

        //** HD video standard - 1.77:1 (16:9)  */
        add_image_size( 'hd_small', 230, 130, true );

        //** Used on Event Pages */
        add_image_size( 'gallery', 200, 999 );

        //** Fit for maximum sidebar width, unlimited height */
        add_image_size( 'sidebar_poster', 310, 999 );

        //** Fit for events filter flyer width, unlimited height */
        add_image_size( 'events_flyer_thumb', 140, 999 );

        //** Fit for maximum sidebar width, unlimited height */
        add_image_size( 'tiny_thumbnail', 100, 80 );

        //** Fit for maximum sidebar width, unlimited height */
        add_image_size( 'sidebar_thumb', 120, 100, true );

        //** Don't know, seems good */
        set_post_thumbnail_size( 120, 90 );

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

        flawless_set_color_scheme( 'skin-default.css' );

        // Must be defined for UD Cloud API to work in static mode
        define( 'UD_Site_UID', \UD_Functions::get_key( 'site_uid' ) );
        define( 'UD_Customer_Key', \UD_Functions::get_key( 'customer_key' ) );
        define( 'UD_Public_Key', \UD_Functions::get_key( 'public_key' ) ? \UD_Functions::get_key( 'public_key' ) : md5( UD_Customer_Key ) );

        // Define which post types to store in cloud
        // UD_Cloud::initialize( array( 'types' => array( 'hdp_event', 'hdp_video', 'hdp_photo_gallery' ) ));

        /**
         * Structure Documents for CloudData
         * Following rules applied via CloudAPI:
         * - All properties with underscore prefix are excluded from output automatically
         *
         * @author potanin@UD
         */
        add_filter( 'ud::cloud::document', function ( $object ) {

          //** Flush vars */
          $return = array();

          //** Do different things for different types */
          switch( $object->post_type ) {

            //** Process photos */
            case 'hdp_photo_gallery':
            {

              //** We can use get_events() for other types because it works well for them too. */
              $object = (object) get_event( $object->ID );

              //** Date for photo gallery may not include the time. */
              $time = ( $object->meta[ 'hdp_event_date' ] ? strtotime( $object->meta[ 'hdp_event_date' ] ) : 0 );
              $time = $time ? date( 'Y/m/d H:i:s', $time ) : '';

              /** @var $return array */
              $return = array(
                'id'        => $object->ID,
                'title'     => html_entity_decode( $object->post_title ),
                'type'      => $object->post_type,
                'summary'   => $object->post_excerpt,
                'time'      => $time,
                'thumbnail' => $object->meta[ 'hdp_poster_id' ] ? \UD_Functions::get_image_link( $object->meta[ 'hdp_poster_id' ], 'hd_small' ) : '',
                'url'       => get_permalink( $object->ID ),
                'venue'     => array(),
                'artists'   => array(),
                '_meta'     => array(
                  'status'    => $object->post_status,
                  'modified'  => date( 'Y/m/d H:i:s', strtotime( $object->post_modified ) ),
                  'published' => date( 'Y/m/d H:i:s', strtotime( $object->post_date ) )
                ),
              );

              break;
            }

            //** Process videos */
            case 'hdp_video':
            {

              //** We can use get_events() for other types because it works well for them too. */
              $object = (object) get_event( $object->ID );

              //** Date for photo gallery may not include the time. */
              $time = ( $object->meta[ 'hdp_event_date' ] ? strtotime( $object->meta[ 'hdp_event_date' ] ) : 0 );
              $time = $time ? date( 'Y/m/d H:i:s', $time ) : '';

              /** @var $return array */
              $return = array(
                'id'        => $object->ID,
                'title'     => html_entity_decode( $object->post_title ),
                'type'      => $object->post_type,
                'summary'   => $object->post_excerpt,
                'time'      => $time,
                'thumbnail' => $object->meta[ 'hdp_poster_id' ] ? \UD_Functions::get_image_link( $object->meta[ 'hdp_poster_id' ], 'hd_small' ) : '',
                'url'       => get_permalink( $object->ID ),
                'venue'     => array(),
                'artists'   => array(),
                '_meta'     => array(
                  'status'    => $object->post_status,
                  'modified'  => date( 'Y/m/d H:i:s', strtotime( $object->post_modified ) ),
                  'published' => date( 'Y/m/d H:i:s', strtotime( $object->post_date ) )
                ),
              );

              break;
            }

            //** Process events */
            case 'hdp_event':
            {

              $object = (object) get_event( $object->ID );

              $time = ( $object->meta[ 'hdp_event_date' ] && $object->meta[ 'hdp_event_time' ] ? strtotime( $object->meta[ 'hdp_event_date' ] . ' ' . $object->meta[ 'hdp_event_time' ] ) : '' );

              $time = $time ? date( 'Y/m/d H:i:s', $time ) : '';

              /** @var $return array */
              $return = array(
                'id'        => $object->ID,
                'title'     => html_entity_decode( $object->post_title ),
                'type'      => $object->post_type,
                'summary'   => $object->post_excerpt,
                'time'      => $time,
                'thumbnail' => $object->meta[ '_thumbnail_id' ] ? \UD_Functions::get_image_link( $object->meta[ '_thumbnail_id' ], 'events_flyer_thumb' ) : '',
                'url'       => get_permalink( $object->ID ),
                'rsvp'      => $object->meta[ 'facebook_rsvp_url' ],
                'purchase'  => $object->meta[ 'hdp_purchase_url' ],
                'venue'     => array(),
                'artists'   => array(),
                '_meta'     => array(
                  'status'    => $object->post_status,
                  'modified'  => date( 'Y/m/d H:i:s', strtotime( $object->post_modified ) ),
                  'published' => date( 'Y/m/d H:i:s', strtotime( $object->post_date ) )
                ),
              );

              break;
            }

          }

          //** Common part for every type */
          foreach( (array) $object->terms as $slug => $items ) {
            foreach( (array) $items as $data ) {
              $return[ 'terms' ][ str_replace( 'hdp_', '', $data[ 'taxonomy' ] ) ][ ] = $data[ 'name' ];
            }
          }

          foreach( (array) $object->terms[ 'hdp_artist' ] as $artist_data ) {

            $return[ 'artists' ][ ] = array(
              'id'      => $artist_data[ 'term_id' ],
              'name'    => $artist_data[ 'name' ],
              'summary' => get_post_for_extended_term( $artist_data[ 'term_id' ], 'hdp_artist' )->post_excerpt,
              'url'     => !is_wp_error( get_term_link( $artist_data[ 'slug' ], 'hdp_artist' ) ) ? get_term_link( $artist_data[ 'slug' ], 'hdp_artist' ) : ''
            );

          }

          $venue = get_post_for_extended_term( $object->terms[ 'hdp_venue' ][ 0 ][ 'term_id' ], 'hdp_venue' );

          foreach( (array) get_post_custom( $venue->ID ) as $key => $value ) {
            $venue->{$key} = $value[ 0 ];
          }

          $return[ 'venue' ] = array(
            'id'       => $venue->extended_term_id,
            'name'     => $venue->post_title,
            'summary'  => $venue->post_excerpt,
            'url'      => !is_wp_error( get_term_link( $venue->post_name, 'hdp_venue' ) ) ? get_term_link( $venue->post_name, 'hdp_venue' ) : '',
            'address'  => $venue->formatted_address,
            'location' => array(
              '@type'        => $venue->location_type,
              '@precision'   => $venue->precision,
              'city'         => $venue->city,
              'county'       => $venue->county,
              'state'        => $venue->state,
              'country'      => $venue->country,
              'state_code'   => $venue->state_code,
              'country_code' => $venue->country_code,
              'coordinates'  => array(
                'lat' => $venue->latitude,
                'lon' => $venue->longitude
              )
            )
          );

          $return = \UD_Functions::array_filter_deep( $return );

          if( !$return[ 'venue' ] ) {
            return array();
          }

          return $return;

        } );

        /** First, go through my local items, and update my attributes */
        $__attributes = (array) self::$_attributes;

        foreach( $__attributes as $key => &$arr ) {
          $arr = \UsabilityDynamics\Utility::extend( self::$default_attribute, $arr );
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
        $hddp = \UsabilityDynamics\Utility::extend( array(
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

        // Enqueue Frontend Scripts & Styles.
        add_action( 'wp_enqueue_scripts', function () {

          // Enqueue Scripts.
          wp_enqueue_script( 'app' );
          wp_enqueue_script( 'jquery-new-ud-elasticsearch' );
          wp_enqueue_script( 'jquery-ui-tabs' );
          wp_enqueue_script( 'google-maps' );
          wp_enqueue_script( 'jquery-fitvids' );
          wp_enqueue_script( 'jquery-simplyscroll' );
          wp_enqueue_script( 'jquery-ui-datepicker' );

          // Enqueue Styles.
          wp_enqueue_style( 'jquery-simplyscroll' );
          wp_enqueue_style( 'jquery-jqtransform' );

        } );

        // Enqueue Admin Scripts & Styles.
        add_action( 'admin_enqueue_scripts', function () {

          wp_enqueue_script( 'app.admin' );
          wp_enqueue_script( 'jquery-cookie' );

          wp_enqueue_style( 'app.admin' );

        } );

        // Admin Footer Scripts.
        add_action( 'admin_print_footer_scripts', function () {
          global $hddp, $post, $pagenow;

          /* Determine if current post is a "Event Related" post */
          if( in_array( $post->post_type, $hddp[ 'event_related_post_types' ] ) ) {
            $hddp[ 'automated_title' ] = true;
          }

          echo '<script type="text/javascript">var hddp_dynamic = jQuery.parseJSON( ' . json_encode( json_encode( $hddp ) ) . ' );</script>';

        } );

        add_action( 'admin_menu', function () {
          global $hddp;
          $hddp[ 'manage_page' ] = add_dashboard_page( __( 'Manage', HDDP ), __( 'Manage', HDDP ), $hddp[ 'manage_options' ], 'hddp_manage', array( '\UsabilityDynamics\Theme\Disco', 'hddp_manage' ) );
        } );

        // add_action( 'wp_ajax_nopriv_ud_df_post_query', create_function( '', ' die( json_encode( hddp::df_post_query( $_REQUEST )));' ));
        // add_action( 'wp_ajax_ud_df_post_query', create_function( '', ' die( json_encode( hddp::df_post_query( $_REQUEST )));' ));
        // add_action( 'wp_ajax_elasticsearch_query', array( '\UsabilityDynamics\Theme\Disco', 'elasticsearch_query' ) );
        // add_action( 'wp_ajax_nopriv_elasticsearch_query', array( '\UsabilityDynamics\Theme\Disco', 'elasticsearch_query' ) );

        // Setup Dynamic Filter
        add_action( 'template_redirect', array( '\UsabilityDynamics\Theme\Disco', 'dynamic_filter_shortcode_handler' ) );

        // Saving and deleting posts from QA table
        add_action( 'save_post', array( '\UsabilityDynamics\Theme\Disco', 'save_post' ), 1, 2 );

        /** Setup maintanance cron and handler function */
        if( !wp_next_scheduled( 'hddp_daily_cron' ) ) {
          wp_schedule_event( time(), 'daily', 'hddp_daily_cron' );
        }

        add_action( 'hddp_daily_cron', array( '\UsabilityDynamics\Theme\Disco', 'daily_maintenance_cron' ) );

        add_filter( 'the_category', function ( $c ) {
          return self::_backtrace_function( 'wp_popular_terms_checklist' ) ? '<span class="do_inline_hierarchial_taxonomy_stuff do_not_esc_html">' . $c . '</span>' : $c;
        } );

        add_filter( 'esc_html', function ( $s, $u = '' ) {
          return strpos( $s, 'do_not_esc_html' ) ? $u : $s;
        }, 10, 2 );

        add_action( 'flawless::extended_term_form_fields', array( '\UsabilityDynamics\Theme\Disco', 'extended_term_form_fields' ), 10, 2 );

        add_filter( 'flawless_remote_assets', function ( $assets ) {
          $assets[ 'css' ][ 'google-font-droid-sans' ] = 'http://fonts.googleapis.com/css?family=Droid+Sans';

          return $assets;
        } );

        add_action( 'flawless::header_bottom', function () {
          $header = flawless_breadcrumbs( array( 'hide_breadcrumbs' => false, 'wrapper_class' => 'breadcrumbs container', 'hide_on_home' => false, 'return' => true ) );
          $share  = hdp_share_button( false, true );
          /** Do a preg replace to add our share button */
          /**$header = preg_replace( '/(<div[^>]*?>)/i', '$1' . $share, $header );
           * /** Echo it out */
          echo $share . $header;
        } );

        // Add post date to editor screen of "Event Related" post types
        $x = 1;
        foreach( $hddp[ 'attributes' ] as $type => $attribs ) {
          foreach( $attribs as $slug => $vars ) {

            // If we have an admin label, we're shoing
            if( !$vars[ 'admin_label' ] ) continue;

            // If we made it, add the item
            //\Flawless\Management::add_post_type_option( array( 'post_type' => $type, 'type' => $vars[ 'admin_type' ], 'position' => $x++, 'meta_key' => $slug, 'label' => $vars[ 'admin_label' ], 'placeholder' => $vars[ 'placeholder' ], ));

          }

        }

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

        add_filter( 'img_caption_shortcode', array( '\UsabilityDynamics\Theme\Disco', 'img_caption_shortcode' ), 10, 3 );

      }

      /**
       * Primary Loader
       *
       * @author potanin@UD
       */
      public function setup() {

        remove_theme_support( 'custom-header' );
        remove_theme_support( 'custom-skins' );
        remove_theme_support( 'custom-background' );
        remove_theme_support( 'header-dropdowns' );
        remove_theme_support( 'header-business-card' );
        remove_theme_support( 'frontend-editor' );

        load_theme_textdomain( HDDP, get_stylesheet_directory() . '/languages' );

      }

      /**
       * Force our custom template to load for Event post types
       *
       * @method redirect
       * @action template_redirect (10)
       * @author potanin@UD
       */
      public function redirect() {
        global $post, $flawless;

        // Modify our HTML for the mobile nav bar
        if( isset( $flawless[ 'mobile_navbar' ] ) ) {
          $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ] = hdp_share_button( true, true ) . $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ];
        }

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
       * Return JSON post results Dynamic Filter requests
       *
       * @action admin_init (10)
       * @author potanin@UD
       */
      public function admin() {
        global $wpdb, $hddp, $current_user;

        /* Adds options to Publish metabox */
        add_action( 'post_submitbox_misc_actions', array( '\UsabilityDynamics\Theme\Disco', 'post_submitbox_misc_actions' ) );

        /* Add Address column to Venues taxonomy overview */
        add_filter( 'manage_edit-hdp_venue_columns', function ( $columns ) {

            $columns[ 'formatted_address' ] = __( 'Address', HDDP );

            return $columns;
          }
        );

        add_filter( 'manage_hdp_venue_custom_column', array( '\UsabilityDynamics\Theme\Disco', 'event_venue_columns_data' ), 10, 3 );

        /* Add Event Date column to Event listings */
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

        add_filter( 'manage_hdp_event_posts_custom_column', array( '\UsabilityDynamics\Theme\Disco', 'manage_hdp_event_posts_custom_column' ), 10, 2
        );

        /* HDDP Options Update - monitor for nonce */
        if( !empty( $_REQUEST[ 'hddp_options' ] ) && wp_verify_nonce( $_POST[ 'hddp_save_form' ], 'hddp_save_form' ) ) {
          update_option( 'hddp_options', $_REQUEST[ 'hddp_options' ] );

          foreach( (array) $_POST[ '_options' ] as $option_name => $option_value ) {
            update_option( $option_name, $option_value );
          }

          die( wp_redirect( admin_url( 'index.php?page=hddp_manage&message=updated' ) ) );
        }

        /* Temporary placement */
        switch( $_GET[ 'request' ] ) {

          case 'test' :
            //wp_die('sdaf');
            break;

          case 'synchronize_with_cloud' :
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
                $result[ ] = UD_Cloud::index( get_event( $post_id ) );
              }

            }

            Flawless_F::log( 'Batch Cloud API update complete, pushed ' . count( $result ) . ' batches of 50 items in ' . \UD_Functions::timer_stop( 'synchronize_with_cloud' ) . ' seconds.' );

            break;

          case 'update_qa_all_tables' :

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

            break;

          case 'update_lat_long' :
            $updated = array();

            foreach( self::_get_event_posts() as $post_id ) {
              $updated[ ] = self::update_event_location( $post_id, array( 'add_log_entries' => false ) );
            }

            if( count( $updated ) > 0 ) {
              Flawless_F::log( 'Successfully updated addresses for (' . count( $updated ) . ') event(s).' );
            } else {
              Flawless_F::log( 'Attempted to do a bulk address update for all events, but no events were updated.' );
            }

            break;

          case 'clear_event_log' :
            Flawless_F::delete_log();
            Flawless_F::log( 'Event log cleared by ' . $current_user->data->display_name . '.' );
            break;

          case 'delete_hddp_options' :
            delete_option( 'hddp_options' );
            Flawless_F::log( 'HDDP-Theme options deleted by ' . $current_user->data->display_name . '.' );
            break;
        }

        add_filter( 'update_footer', function ( $text ) {

            global $wpdb;

            return $text . ' | ' . timer_stop() . ' seconds | ' . $wpdb->num_queries . ' queries | ' . round( ( memory_get_peak_usage() / 1048576 ) ) . ' mb';
          }, 15
        );

        add_action( 'all_admin_notices', array( '\UsabilityDynamics\Theme\Disco', 'all_admin_notices' ) );

      }

    }
  }

}