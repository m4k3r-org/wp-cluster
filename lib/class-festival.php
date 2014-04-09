<?php
/**
 * Festival Core
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics {

  /**
   * Festival Theme
   *
   * @author Usability Dynamics
   */
  class Festival extends \UsabilityDynamics\Theme\Scaffold {

    /**
     * Version of theme
     *
     * @public
     * @property version
     * @var string
     */
    public $version = null;

    /**
     * Textdomain String
     *
     * Parses namespace, should be something like "wpp-theme-festival"
     *
     * @public
     * @property domain
     * @var string
     */
    public $domain = null;

    /**
     * ID of instance, used for settings.
     *
     * Parses namespace, should be something like wpp:theme:festival
     *
     * @public
     * @property id
     * @var string
     */
    public $id = null;

    /**
     * Settings.
     *
     * @public
     * @property id
     * @var string
     */
    public $carrington;

    /**
     * Class Initializer
     *
     *    http://umesouthpadre.com/manage/admin-ajax.php?action=main
     *    http://umesouthpadre.com/manage/admin-ajax.php?action=festival.model
     *    http://umesouthpadre.com/manage/admin-ajax.php?action=main
     *
     *
     * @example
     *
     *    // JavaScript
     *    require( 'festival.model' )
     *    require( 'festival.settings' )
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function __construct() {

      // Configure Properties.
      $this->id      = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => ':' ));
      $this->domain  = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => '-' ));
      $this->version = wp_get_theme()->get( 'Version' );

      // Initialize Settings.
      $this->initialize( array(
        'key'       => 'festival',
        'version'   => $this->version
      ));

      // Register Custom Post Types, meta and set their taxonomies
      $this->structure = \UsabilityDynamics\Structure::define( $this->get_schema( '/static/schemas/schema.structure.json' ) );

      // Set Theme UI
      if( class_exists( '\UsabilityDynamics\UI\Settings' ) ) {
        $this->ui = new \UsabilityDynamics\UI\Settings( $this->settings, $this->get_schema( '/static/schemas/schema.ui.json' ) );
      }

      // Configure API Methods.
      $this->api( array(
        'search.AutoSuggest'   => array(
          'key' => 'search_auto_suggest'
        ),
        'search.ElasticSearch' => array(
          'key' => 'search_elastic_search'
        ),
        'search.Elastic'       => array(),
        'search.DynamicFilter' => array()
      ));

      // Configure Image Sizes.
      $this->media( array(
        'post-thumbnail' => array(
          'description' => __( 'Standard Thumbnail.' ),
          'width'       => 230,
          'height'      => 130,
          'crop'        => true
        ),
        'gallery'        => array(
          'description' => __( 'Gallery Image Thumbnail.' ),
          'post_types'  => array( 'page', 'artist' ),
          'width'       => 300,
          'height'      => 170,
          'crop'        => false
        ),
        'tablet'         => array(
          'description' => __( 'Tablet Maximum Resolution.' ),
          'post_types'  => array( '_aside' ),
          'width'       => 670,
          'height'      => 999,
          'crop'        => true
        )
      ));

      // Declare Supported Theme Features.
      $this->supports( array(
        'admin-bar'         => array(
          'callback' => '__return_false'
        ),
        'asides'            => array(
          'header',
          'banner',
          'footer'
        ),
        'html5'             => array(
          'main',
          'nav',
          'comment-list',
          'comment-form',
          'search-form'
        ),
        'attachment:audio'  => array(
          'enabled' => true
        ),
        'attachment:video'  => array(
          'enabled' => true
        ),
        'custom-background' => array(
          'default-color' => '',
          'default-image' => '',
          'wp-head-callback' => '__return_false'
        ),
        'post-thumbnails'   => array(
          'event',
          'artist',
          'page',
          'post'
        ),
        'saas.udx.io'       => array(
          'cloudSearch',
          'cloudIdentity'
        ),
        'raas.udx.io'       => array(
          'build.compileLESS',
          'build.compileScripts'
        ),
        'cdn.udx.io'        => array(
          'jquery'
        )
      ));

      // Head Tags.
      $this->head( array(
        array(
          'tag'     => 'meta',
          'name'    => 'apple-mobile-web-app-status-bar-style',
          'content' => 'black'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'apple-mobile-web-app-capable',
          'content' => 'yes'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'HandheldFriendly',
          'content' => 'True'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'MobileOptimized',
          'content' => '360'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'viewport',
          'content' => 'width=device-width, initial-scale=1.0'
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-icon',
          'href'    => content_url( '/assets/apple-touch-icon-72x72.png' )
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-startup-image',
          'href'    => content_url( '/assets/apple-touch-icon-72x72.png' )
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-icon',
          'href'    => content_url( '/assets/apple-touch-icon-72x72.png' ),
          'sizes'   => '72x72'
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-icon',
          'href'    => content_url( '/assets/apple-touch-icon-114x114.png' ),
          'sizes'   => '114x114'
        ),
        array(
          'tag'     => 'link',
          'href'    => content_url( '/assets/apple-touch-icon-72x72.png' )
        ),
        array(
          'tag'  => 'link',
          'rel'  => 'shortcut icon',
          'href' => home_url( '/assets/favicon.png' )
        ),
        array(
          'tag'  => 'link',
          'rel'  => 'api',
          'href' => admin_url( 'admin-ajax.php' )
        ),
        array(
          'tag'  => 'link',
          'rel'  => 'profile',
          'href' => 'http://gmpg.org/xfn/11'
        ),
        array(
          'tag'  => 'link',
          'rel'  => 'pingback',
          'href' => get_bloginfo( 'pingback_url' )
        )
      ));

      // Add Management UI.
      $this->manage( array(
        'id'       => 'fesival_manage',
        'title'    => __( 'Manage', $this->domain ),
        'template' => dirname( __DIR__ ) . '/templates/admin.manage.php'
      ));

      //** Enables Customizer for Options. */
      $this->customizer();

      // Enable Carrington Build.
      $this->carrington( array(
        'bootstrap'          => true,
        'templates'          => true,
        'styles'             => array(),
        'module_directories' => array(
          __DIR__ . '/modules'
        ),
        'post_types'         => array(
          'page',
          'post',
          'artist',
          '_aside'
        )
      ));

      // Register Navigation Menus
      $this->menus( array(
        'primary' => array(
          'name' => __( 'Primary', $this->domain )
        ),
        'secondary'  => array(
          'name' => __( 'Secondary', $this->domain )
        ),
        'social'  => array(
          'name' => __( 'Social', $this->domain )
        ),
        'footer'  => array(
          'name' => __( 'Footer', $this->domain )
        ),
        'mobile'  => array(
          'name' => __( 'Mobile', $this->domain )
        )
      ));

      // Define Dynamic Aside Sections.
      $this->sections( array(
        'header' => array(
          'title'       => __( 'Header', $this->domain ),
          'description' => __( 'Header Section', $this->domain ),
        ),
        'header-banner' => array(
          'title'       => __( 'Header Banner', $this->domain ),
          'description' => __( 'Babber located directly below the header section.', $this->domain )
        ),
        'left-sidebar' => array(
          'title'       => __( 'Left Sidebar', $this->domain ),
          'description' => __( 'Shown on Pages with specific template.' ),
          'sidebar' => true,
          'options' => array(
            'id'      => 'left-sidebar-section',
            'class'   => 'left-sidebar-section col-md-3 section',
            'before'  => '<div class="module widget %1$s %2$s"><div class="module-inner">',
            'after'   => '</div></div>',
          )
        ),
        'right-sidebar' => array(
          'title'       => __( 'Right Sidebar', $this->domain ),
          'description' => __( 'Right widget area.', $this->domain ),
          'sidebar' => true,
          'options'     => array(
            'id'      => 'right-sidebar-section',
            'class'   => 'right-sidebar-section col-md-3 section',
            'before'  => '<div class="module widget %1$s %2$s"><div class="module-inner">',
            'after'   => '</div></div>'
          )
        ),
        'above-content'  => array(
          'title'       => __( 'Above Content' ),
          'description' => __( 'Above content but below header.' ),
          'options'     => array(
            'type' => 'swiper'
          )
        ),
        'below-content'  => array(
          'title'       => __( 'Below Content' ),
          'description' => __( 'Below content but above header.' ),
          'options'     => array(
            'type' => 'swiper'
          )
        ),
        'footer' => array(
          'title' => __( 'Footer' ),
          'description' => __( 'Footer Section.' ),
          'options'     => array(
            'type' => 'swiper'
          )
        )
      ));

      // Set Pluggable Module Directory.
      $this->modules( __DIR__ . '/modules' );

      // Core Actions
      add_action( 'init', array( $this, 'init' ), 100 );
      add_action( 'template_redirect', array( $this, 'redirect' ), 100 );
      add_action( 'admin_init', array( $this, 'admin' ));
      add_action( 'get_model', array( $this, 'admin_menu' ));
      add_action( 'widgets_init', array( $this, 'widgets' ), 100 );

      // Initializes Wordpress Menufication
      if( class_exists( '\Menufication' ) ) {
        $this->menufication = \Menufication::getInstance();
      }

      // Remove URL from comments form
      add_filter('comment_form_default_fields', array( $this, 'url_filtered' ) );

      // Alter gallery container to load masonry
      add_filter('gallery_style', array( $this, 'gallery_attributes' ));

      // Auto-wrap videos with container to make them responsive
      add_filter('embed_oembed_html', array( $this, 'wrap_video' ), 99, 4);

    }

    /**
     * Auto-wrap videos with container to make them responsive
     * @param type $html
     * @param type $url
     * @param type $attr
     * @param type $post_id
     * @return type
     */
    function wrap_video($html, $url, $attr, $post_id) {
      return '<div class="video-container">' . $html . '</div>';
    }

    /**
     * Make standard gallery "masonarable"
     * @param type $current
     * @return type
     */
    public function gallery_attributes( $current ) {
      return preg_replace('/(id=\'gallery.+?\')/', '$1 data-requires="gallery-masonry"', $current);
    }

    /**
     * Remove URL from comments form
     * @param type $fields
     * @return type
     */
    public function url_filtered( $fields ) {
      if( isset( $fields['url'] ) ) {
        unset($fields['url']);
      }
      return $fields;
    }

    /**
     * Adds settings to customizer
     */
    public function customizer() {
      //** Add Global JS and CSS handlers ( wp-amd ) */
      if( defined( 'WP_VENDOR_PATH' ) && file_exists( WP_VENDOR_PATH . '/usabilitydynamics/wp-amd/wp-amd.php' ) ) {
        include_once( WP_VENDOR_PATH . '/usabilitydynamics/wp-amd/wp-amd.php' );
      }
      if( class_exists( '\UsabilityDynamics\Festival\Customizer' ) ) {
        \UsabilityDynamics\Festival\Customizer::define( array(
          'text_domain' => $this->domain,
        ) );
      }
    }

    /**
     * On settings init we also merge structure with global network settings
     *
     */
    public function settings( $args = array(), $data = array() ) {
      parent::settings( $args, $data );
    }

    /**
     * Compile Site-Specific Assets.
     *
     * @param string $type
     */
    private function build_site( $type = '' ) {

      // Combile LESS.
      $response = $this->raasRequest( 'build.compileLESS', array(
        'variables' => array(
          'brand-warning' => 'red',
          'body-bg'       => 'green'
        ),
        'main'      => 'app.less',
        'files'     => array(
          get_stylesheet_directory_uri() . '/styles/src/app.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/mixins.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/normalize.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/print.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/scaffolding.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/type.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/code.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/grid.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/tables.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/forms.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/buttons.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/component-animations.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/glyphicons.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/dropdowns.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/button-groups.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/input-groups.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/navs.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/navbar.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/breadcrumbs.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/pagination.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/pager.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/labels.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/badges.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/jumbotron.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/thumbnails.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/alerts.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/progress-bars.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/media.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/list-group.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/panels.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/wells.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/close.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/modals.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/tooltip.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/popovers.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/carousel.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/utilities.less',
          get_stylesheet_directory_uri() . '/styles/src/bootstrap/responsive-utilities.less',
          get_stylesheet_directory_uri() . '/styles/src/carousel.less',
          get_stylesheet_directory_uri() . '/styles/src/color.less',
          get_stylesheet_directory_uri() . '/styles/src/countdown.less',
          get_stylesheet_directory_uri() . '/styles/src/custom.less',
          get_stylesheet_directory_uri() . '/styles/src/editor-style.less',
          get_stylesheet_directory_uri() . '/styles/src/font-awesome.less',
          get_stylesheet_directory_uri() . '/styles/src/fonts.less',
          get_stylesheet_directory_uri() . '/styles/src/glyphicons.less',
          get_stylesheet_directory_uri() . '/styles/src/responsive.less',
          get_stylesheet_directory_uri() . '/styles/src/style.less',
          get_stylesheet_directory_uri() . '/styles/src/variables.less'
        )
      ));

      if( is_wp_error( $response ) ) {
        wp_die( $response->get_error_message());
      }

      // Have Encoded Data.
      if( is_object( $response ) && isset( $response->data ) ) {
        die( base64_decode( $response->data ));
      }

      die( '<pre>' . print_r( $response, true ) . '</pre>' );

    }

    /**
     * Register Sidebars
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function widgets() {
      unregister_widget( 'WP_Widget_Recent_Comments' );
      unregister_widget( 'WP_Widget_RSS' );
      unregister_widget( 'WP_Widget_Calendar' );
      unregister_widget( 'WP_Widget_Tag_Cloud' );
      unregister_widget( 'WP_Widget_Meta' );
      unregister_widget( 'WP_Widget_Archives' );
      unregister_widget( 'WP_Widget_Categories' );
    }

    /**
     * Primary Frontend Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function redirect() {

      //** Hack. De-Register jquery.spin ( used in Jetpack ) to prevent javascript errors. */
      if ( wp_script_is( 'jquery.spin', 'registered' ) ) {
        wp_deregister_script( 'jquery.spin' );
      } elseif ( wp_script_is( 'jquery.spin', 'enqueued' ) ) {
        wp_dequeue_script( 'jquery.spin' );
      }

      // Disable WP Gallery styles
      add_filter( 'use_default_gallery_style', function () {
        return false;
      } );

    }

    /**
     * Primary Admin Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function admin() {
    }

    /**
     * Primary Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function init() {

      // Register Carrington Modules.
      if( is_object( $this->carrington ) ) {
        $this->carrington->deregisterModule( 'HeroModule' );
        $this->carrington->deregisterModule( 'LoopModule' );

        $this->carrington->registerModule( 'FestivalLoopModule' ); // Modified LoopModule
        $this->carrington->registerModule( 'FestivalHeroModule' ); // Modified HeroModule
        $this->carrington->registerModule( 'VideoModule' );
        $this->carrington->registerModule( 'EventHeroModule' );
        $this->carrington->registerModule( 'ArtistListModule' );
        $this->carrington->registerModule( 'EventHeroModule' );
        $this->carrington->registerModule( 'EventLoopModule' );
        $this->carrington->registerModule( 'AdvancedHeroModule' );
        $this->carrington->registerModule( 'CollapseModule' );
        $this->carrington->registerModule( 'SectionBreakModule' );
        if ( class_exists( 'SocialStreamModule' ) ) {
          $this->carrington->registerModule( 'SocialStreamModule' );
        }
      }

      // Declare Dynamic / Public Scripts.
      $this->scripts(array(
        // 'app.config'            => array( 'url' => content_url( '/assets/app.config.js' ), 'deps' => array( 'app.require' ) ),
        // 'app.bootstrap'         => array( 'url' => content_url( '/assets/scripts/app.bootstrap.js' ), 'deps' => array( 'app.require', 'app.config' ) ),
        // 'app.main'              => array( 'url' => content_url( '/assets/scripts/app.main.js' ), 'deps' => array( 'app.require', 'app.bootstrap' ) )
      ));

      // Declare Public Styles.
      $this->styles(array(
        'app-admin'             => content_url( '/assets/styles/app-admin.css' ),
        'app-bootstrap'         => content_url( '/assets/styles/app-bootstrap.css' ),
        'app-main'              => array( 'url' => content_url( '/assets/styles/app-main.css' ), 'deps' => array( 'app-bootstrap' ) ),
        'app-editor'            => content_url( '/assets/styles/app-editor.css' ),
        'content'               => content_url( '/assets/styles/content.css' ),
        'bootstrap'             => content_url( '/assets/styles/bootstrap.css' ),
        'jquery.jqtransform'    => content_url( '/assets/styles/jqtransform.css' ),
        'jquery.simplyscroll'   => content_url( '/assets/styles/simplyscroll.css' )
      ));

      //
      $this->load_shortcodes();

      // Register Editor Style.
      add_editor_style( home_url( '/assets/styles/app-editor.css' ) );

      // Custom Hooks
      add_filter( 'wp_get_attachment_image_attributes', array( $this, 'wp_get_attachment_image_attributes' ), 10, 2 );

      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 600 );

    }

    /**
     * Enqueue Frontend Scripts
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_enqueue_scripts() {

      //wp_deregister_script( 'jquery' );
      wp_deregister_script( 'devicepx' );

      wp_enqueue_style( 'app-main' );
      wp_enqueue_script( 'app-main' );

    }

    /**
     * Adds 'img-responsive' Bootstrap class to all images
     *
     * @param array $attr
     * @param type  $attachment
     *
     * @return array
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_get_attachment_image_attributes( $attr, $attachment ) {
      $attr[ 'class' ] = trim( $attr[ 'class' ] . ' img-responsive' );

      return $attr;
    }

    /**
     * Return name of Navigation Menu
     *
     * @param string $slug
     *
     * @return string If menus not found, boolean false will be returned
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function get_menus_name( $slug ) {
      $cippo_menu_locations = (array) get_nav_menu_locations();
      $menu                 = get_term_by( 'id', (int) $cippo_menu_locations[ $slug ], 'nav_menu', ARRAY_A );

      return !empty( $menu[ 'name' ] ) ? $menu[ 'name' ] : false;
    }

    /**
     * Make RPC Request.
     *
     * @example
     *
     *      // Create Import Request.
     *      $_response = self::raasRequest( 'build.compileLESS', array(
     *        'asdf' => 'sadfsadfasdfsadf'
     *      ));
     *
     * @param string $method
     * @param array  $data
     *
     * @method raasRequest
     * @since 5.0.0
     *
     * @return array
     * @author potanin@UD
     */
    public function raasRequest( $method = '', $data = array() ) {

      include_once( ABSPATH . WPINC . '/class-IXR.php' );
      include_once( ABSPATH . WPINC . '/class-wp-http-ixr-client.php' );

      $client = new \WP_HTTP_IXR_Client( 'raas.udx.io', '/rpc/v1', 80, 20000 );

      // Set User Agent.
      $client->useragent = 'WordPress/3.7.1 WP-Property/3.6.1 WP-Festival/' . $this->version;

      // Request Headers.
      $client->headers = array(
        'authorization'    => 'Basic ' . $this->get( 'raas.token' ) . ':' . $this->get( 'raas.session', defined( 'NONCE_KEY' ) ? NONCE_KEY : null ),
        'x-request-id'     => uniqid(),
        'x-client-name'    => get_bloginfo( 'name' ),
        'x-client-token'   => $this->get( 'raas.client', defined( 'AUTH_KEY' ) ? AUTH_KEY : null ),
        'x-callback-token' => $this->get( 'raas.callback.token', md5( wp_get_current_user()->data->user_pass ) ),
        'x-callback-url'   => site_url( 'xmlrpc.php' ),
        'content-type'     => 'text/xml; charset=utf-8'
      );

      // Execute Request.
      $client->query( $method, $data );

      if( $client->error ) {
        return new \WP_Error( $client->error->code, $client->error->message );
      }

      // Return Message.
      $_result = isset( $client->message ) && isset( $client->message->params ) && is_array( $client->message->params ) ? $client->message->params[ 0 ] : array();

      return json_decode( json_encode( $_result ));

    }

    /**
     *
     */
    private function load_shortcodes() {
      // Inits shortcodes
      \UsabilityDynamics\Shortcode\Utility::maybe_load_shortcodes( get_stylesheet_directory() . '/lib/shortcodes' );
    }

    /**
     * Determine if called method is stored in Utility class.
     * Allows to call \UsabilityDynamics\Festival\Utility methods directly.
     *
     * @author peshkov@UD
     */
    public function __call( $name , $arguments ) {
      if( !is_callable( '\UsabilityDynamics\Festival\Utility', $name ) ) {
        die( "Method $name is not found." );
      }
      return call_user_func_array( array( '\UsabilityDynamics\Festival\Utility', $name ), $arguments );
    }

  }

}
