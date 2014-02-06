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

      // Make theme available for translation
      if( is_dir( get_template_directory() . '/static/languages' ) ) {
        load_theme_textdomain( $this->domain, get_template_directory() . '/static/languages' );
      }

      // Initialize Settings.
      $this->settings();

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

      // Enables Customizer for Options.
      $this->customizer( array(
        'disable' => array(
          'static_front_page',
          'nav',
          'title_tagline'
        ),
        'enable'  => array(),
      ));
      
      // @temp
      add_action( 'customize_register', function( $wp_customize ) {
        
        // Register new settings to the WP database...
        $wp_customize->add_setting( 'content_bg_color', //Give it a SERIALIZED name (so all theme settings can live under one db record)
          array(
            'default'    => '#fcfcf9', //Default setting/value to save
            'type'       => 'option', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport'  => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
          )
        );

        // Define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control( new \WP_Customize_Color_Control( //Instantiate the color control class
          $wp_customize, //Pass the $wp_customize object (required)
          'content_bg_color', //Set a unique ID for the control
          array(
            'label'    => __( 'Content Background Color', $this->domain ), //Admin-visible name of the control
            'section'  => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'content_bg_color', //Which setting to load and manipulate (serialized is okay)
            'priority' => 10, //Determines the order this control appears in for the specified section
          )
        ) );
        
      });


      // Add Management UI.
      $this->manage( array(
        'id'       => 'fesival_manage',
        'title'    => __( 'Manage', $this->domain ),
        'template' => dirname( __DIR__ ) . '/templates/admin.manage.php'
      ));

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

      // Register Theme Bootstrap Scripts.
      $this->requires( array(
        'id'    => 'app.bootstrap',
        'path'  => home_url( '/assets/scripts/app.bootstrap.js' ),
        'base'  => home_url( '/assets/scripts' )
      ));

      // Register Theme Settings Model.
      $this->requires( array(
        'id'    => 'site.model',
        'cache' => 'private, max-age: 0',
        'vary'  => 'user-agent, x-client-type',
        'base'  => home_url( '/assets/scripts' ),
        'data'  => $this->get_model()
      ));

      // Register Theme Locale Model.
      $this->requires( array(
        'id'    => 'site.locale',
        'cache' => 'public, max-age: 30000',
        'vary'  => 'x-user',
        'base'  => home_url( '/assets/scripts' ),
        'data'  => $this->get_locale()
      ));

      // Register Navigation Menus
      $this->menus( array(
        'primary' => array(
          'name' => __( 'Primary', $this->domain )
        ),
        'social'  => array(
          'name' => __( 'Secondary', $this->domain )
        ),
        'footer'  => array(
          'name' => __( 'Footer', $this->domain )
        ),
        'mobile'  => array(
          'name' => __( 'Mobile', $this->domain )
        )
      ));

      // Core Actions
      add_action( 'init', array( $this, 'init' ), 100 );
      add_action( 'template_redirect', array( $this, 'redirect' ), 100 );
      add_action( 'admin_init', array( $this, 'admin' ));
      add_action( 'admin_menu', array( $this, 'admin_menu' ));
      add_action( 'widgets_init', array( $this, 'widgets_init' ), 100 );
      add_action( 'wp_head', array( $this, 'wp_head' ));
      add_action( 'wp_footer', array( $this, 'wp_footer' ));
      add_action( 'widgets_init', array( $this, 'widgets' ));
      add_filter( 'body_class', array( $this, 'body_class' ));
      add_filter( 'intermediate_image_sizes_advanced', array( $this, 'image_sizes' ));
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 600 );

      // Initializes Wordpress Menufication
      if( class_exists( '\Menufication' ) ) {
        $this->menufication = \Menufication::getInstance();
      }

    }

    /**
     * Return Post Type Image Sizes
     *
     * @todo Take thumbnail, large and medium into account.
     *
     * @filter intermediate_image_sizes_advanced
     *
     * @param $_sizes
     *
     * @return array
     */
    public function image_sizes( $_sizes ) {
      global $_wp_additional_image_sizes;

      $_available_sizes = $_wp_additional_image_sizes;

      $_available_sizes[ 'thumbnail' ] = array(
        'width'  => get_option( "thumbnail_size_w" ),
        'height' => get_option( "thumbnail_size_h" ),
        'crop'   => get_option( "thumbnail_crop" )
      );

      $_available_sizes[ 'large' ] = array(
        'width'  => get_option( "large_size_w" ),
        'height' => get_option( "large_size_h" ),
        'crop'   => get_option( "large_crop" )
      );

      $_available_sizes[ 'medium' ] = array(
        'width'  => get_option( "medium_size_w" ),
        'height' => get_option( "medium_size_h" ),
        'crop'   => get_option( "medium_crop" )
      );

      // Upload attachment Unassociated with post.
      if( !isset( $_POST[ 'action' ] ) && $_POST[ 'post_id' ] == 0 ) {
        return $_sizes;
      }

      // Uploading image to post.
      if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] === 'upload-attachment' && $_POST[ 'post_id' ] ) {

        $_allowed = array();

        foreach( (array) $_available_sizes as $size => $settings ) {

          // Post type sizes not configured, allow by deafult.
          if( !isset( $settings[ 'post_types' ] ) ) {
            $_allowed[ $size ] = $settings;
          }

          // Size Allowed.
          if( isset( $settings[ 'post_types' ] ) && in_array( $_post_type, (array) $settings[ 'post_type' ] ) ) {
            $_allowed[ $size ] = $settings;
          }

        }

        // Return Image Sizes for Post Type.
        return $_allowed;

      }

      return $_sizes;

    }

    /**
     * Add Body Classes.
     *
     *
     * * external-referrer Added when visitor is new to the site.
     * * internal-referrer Added when visitor opened current view after being referred.
     *
     * @param $class
     *
     * @return array
     */
    public function body_class( $class ) {
      return array_merge( $class, array( is_external_referrer() ? 'external-referrer' : 'internal-referrer' ));
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
    private function compile_site( $type = '' ) {

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
     * Get Site / Theme Locale
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    private function get_locale() {

      // Include Translation File.
      //$locale = include_once $this->get( '_computed.path.root' ) . '/l10n.php';

      $locale = array();

      // Noramlize HTML Strings.
      foreach( (array) $locale as $key => $value ) {

        if( !is_scalar( $value ) ) {
          continue;
        }

        $locale[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );

      }

      return (array) apply_filters( 'festival:model:locale', $locale );

    }

    /**
     * Get Site Model.
     *
     * See http://www.dancingastronaut.com/ (DancingAstronaut_AppState)
     * See http://www.livenation.com/geo.js
     *
     * @return array
     */
    private function get_model() {

      $_home_url = parse_url( home_url());

      return (array) apply_filters( 'festival:model:settings', array(
        'settings'    => array(
          'permalinks'  => get_option( 'permalink_structure' ) == '' ? false : true,
        ),
        'geo'    => array(
          'latitude' => '',
          'longitude' => '',
          'city' => '',
          'state' => '',
          'country' => ''
        ),
        'user'    => array(
          'id'  => '',
          'login'  => ''
        ),
        'url'        => array(
          'domain'      => trim( $_home_url[ 'host' ] ? $_home_url[ 'host' ] : array_shift( explode( '/', $_home_url[ 'path' ], 2 ) ) ),
          'ajax'        => admin_url( 'admin-ajax.php' ),
          'home'      => admin_url( 'admin-ajax.php' ),
          'assets'      => admin_url( 'admin-ajax.php' ),
        )
      ));

    }

    /**
     * Display Nav Menu.
     *
     * @example
     *
     *      // Show Primary Navigation with depth of 2
     *      wp_festival()->nav( 'primary', 2 );
     *
     *      // Show My Menu in footer location.
     *      wp_festival()->nav( 'my-menu', 'footer' );
     *
     * @param $name {String|Integer|Null}
     * @param $location {String|Integer|Null}
     *
     * @return bool|mixed|string|void
     */
    public function nav( $name = null, $location = null ) {

      return wp_nav_menu( apply_filters( $name, array(
        'theme_location' => is_string( $location ) ? $location : $name,
        'depth'          => is_numeric( $location ) ? $location : 2,
        'menu_class'     => implode( ' ', array_filter( array( 'festival-menu', 'nav', 'navbar-nav', $name, is_string( $location ) ? $location : '' ) ) ),
        'fallback_cb'    => false,
        'container'      => false,
        'items_wrap'     => '<ul data-menu="%1$s" class="%2$s">%3$s</ul>',
        'walker'         => new \UsabilityDynamics\Theme\Nav_Menu,
        'echo'           => false
      ) ));

    }

    /**
     * Get a Content Section.
     *
     * If section can not be found, will attempt to find template of same name in /templates directory.
     *
     * @example
     *
     *        wp_festival()->aside( 'header' );
     *
     *
     * @param null  $name
     * @param array $args
     *
     * @return mixed|null
     */
    public function aside( $name = null, $args = array() ) {
      global $post;

      $args = (object) wp_parse_args( $args, $default = array(
        'type'           => '_aside',
        'class'          => 'modular-aside',
        'more_link_text' => null,
        'strip_teaser'   => null,
        'return'         => false,
      ));

      // Preserve Post.
      $_post = $post;

      // Using query_posts() will not work because we must not change the global query.
      $custom_loop = new \WP_Query( array(
        'name'      => $name,
        'post_type' => $args->type
      ));

      // die(json_encode( $custom_loop ));

      if( $custom_loop->have_posts() ) {
        while( $custom_loop->have_posts() ) {
          $custom_loop->the_post();
          $content = get_the_content( $args->more_link_text, $args->strip_teaser );
          $content = apply_filters( 'the_content', $content );
          $content = str_replace( ']]>', ']]&gt;', $content );
        }
      }

      // Return post.
      $post = $_post;

      // Try to locale regular aside.
      if( !$content ) {
        ob_start();
        get_template_part( 'templates/aside/' . $name, get_post_type());
        $content = ob_get_clean();
      }

      $content = apply_filters( 'festival:aside', isset( $content ) ? '<aside class="' . $args->class . ' aside-' . $name . '" data-aside="' . $name . '">' . $content . '</aside>' : null, $name );

      if( $args->return ) {
        return $content;
      } else {
        echo $content;
      }

    }

    /**
     * Register Sidebars
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function widgets_init() {

      register_sidebar( array(
        'name'          => __( 'Right Sidebar' ),
        'description'   => __( 'Default Sideber. Shown on pages with specific template and blog pages.' ),
        'id'            => 'right-sidebar',
        'before_widget' => '<div class="module widget %1$s %2$s"><div class="module-inner">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h3 class="module-title">',
        'after_title'   => '</h3>',
      ));

      register_sidebar( array(
        'name'          => __( 'Left Sidebar' ),
        'description'   => __( 'Shown on Pages with specific template.' ),
        'id'            => 'left-sidebar',
        'before_widget' => '<div class="module widget %1$s %2$s"><div class="module-inner">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h3 class="module-title">',
        'after_title'   => '</h3>',
      ));

      register_sidebar( array(
        'name'          => __( 'Single Page Sidebar' ),
        'description'   => __( 'Shown on all Single Pages.' ),
        'id'            => 'single-sidebar',
        'before_widget' => '<div class="module widget %1$s %2$s"><div class="module-inner">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h3 class="module-title">',
        'after_title'   => '</h3>',
      ));

    }

    /**
     * Primary Frontend Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function redirect() {

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
     * Add "Sections" link to Appearance menu.
     *
     * @todo Figure out a way to keep the Appearance menu open while editing a menu.
     *
     * @param $menu
     */
    public function admin_menu( $menu ) {
      global $submenu;
      global $menu;

      $submenu[ 'themes.php' ][ 20 ] = array(
        __( 'Asides' ),
        'edit_theme_options',
        'edit.php?post_type=_aside'
      );

    }

    /**
     * Unregister Unsued Widgets.
     *
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
     * Primary Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function init() {

      // Register Carrington Modules.
      if( is_object( $this->carrington ) ) {
        $this->carrington->registerModule( 'VideoModule' );
        $this->carrington->registerModule( 'EventHeroModule' );
        $this->carrington->registerModule( 'ArtistListModule' );
        $this->carrington->registerModule( 'EventHeroModule' );
        $this->carrington->registerModule( 'EventLoopModule' );
      }

      // Register Custom Post Types and set their taxonomies
      $this->structure( $this->get( 'structure' ));

      // Sync 'Social Streams' data with social networks
      $this->sync_streams();

      // Register Scripts. (for reference only, not enqueued);
      wp_register_style( 'app.bootstrap', home_url( '/assets/styles/app.bootstrap.css' ), array(), $this->version, 'all' );
      wp_register_script( 'app.main', home_url( '/assets/scripts/app.main.js' ), array(), $this->version, false );

      // Register Styles.
      wp_register_script( 'app.bootstrap', home_url( '/assets/scripts/app.bootstrap.js' ), array(), $this->version, true );
      wp_register_style( 'app.main', home_url( '/assets/styles/app.main.css' ), array(), $this->version, 'all' );

      // Register Editor Style.
      add_editor_style( home_url( '/assets/editor-style.css' ));

      // Custom Hooks
      add_filter( 'wp_get_attachment_image_attributes', array( $this, 'wp_get_attachment_image_attributes' ), 10, 2 );

    }

    /**
     * Sync 'Social Streams' data with social networks
     *
     */
    private function sync_streams() {

      // Enable Twitter
      if( class_exists( '\UsabilityDynamics\Festival\Sync_Twitter' ) ) {

        $tw = new \UsabilityDynamics\Festival\Sync_Twitter( array(
          'id'        => 'twitter',
          'interval'  => false,
          'post_type' => 'social',
          'oauth'     => array(
            'oauth_access_token'        => '101485804-shGXjN0D43uU7CtCBHaML5K8uycHqgvEMd5gHtrY',
            'oauth_access_token_secret' => 'YcCOXWu1bidAv1APgRAd8ATNBl2UmTDXFkoGzicJny5aw',
            'consumer_key'              => 'yZUAnH7GkJGtCVDpjD5w',
            'consumer_secret'           => 'j8o75Fd5MUCtPYWCH9xV4X0AT8qPECcwdIpNl9sHCU',
          ),
          'request'   => array(
            'screen_name' => 'UMESouthPadre',
          )
        ));

      }

    }

    /**
     * Frontend Footer
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_footer() {

    }

    /**
     * Enqueue Frontend Scripts
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_enqueue_scripts() {
      wp_enqueue_style( 'app.bootstrap' );
      //wp_enqueue_script( 'app.bootstrap' );
    }

    /**
     * Frontend Header
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_head() {

    }

    /**
     * Returns path to page's template
     *
     * @param bool $basename
     *
     * @return string
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function get_query_template( $basename = true ) {
      $object = get_queried_object();

      if( is_404() && $template = get_404_template() ) :
      elseif( is_search() && $template = get_search_template() ) :
      elseif( is_tax() && $template = get_taxonomy_template() ) :
      elseif( is_front_page() && $template = get_front_page_template() ) :
      elseif( is_home() && $template = get_home_template() ) :
      elseif( is_attachment() && $template = get_attachment_template() ) :
      elseif( is_single() && $template = get_single_template() ) :
      elseif( is_page() && $template = get_page_template() ) :
      elseif( is_category() && $template = get_category_template() ) :
      elseif( is_tag() && $template = get_tag_template() ) :
      elseif( is_author() && $template = get_author_template() ) :
      elseif( is_date() && $template = get_date_template() ) :
      elseif( is_archive() && $template = get_archive_template() ) :
      elseif( is_comments_popup() && $template = get_comments_popup_template() ) :
      elseif( is_paged() && $template = get_paged_template() ) :
      else : $template = get_index_template();
      endif;

      $template = apply_filters( 'template_include', $template );

      if( $basename ) {
        $template = str_replace( '.php', '', basename( $template ));
      }

      return $template;
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
     * Returns post image's url with required size.
     *
     * Examples:
     * 1) wp_festival()->get_image_link_by_post_id( get_the_ID()); // Returns Full image
     * 2) wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'size' => 'medium' )); // Returns image with predefined size
     * 3) wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '430', 'height' => '125' )); // Returns image with custom size
     *
     * @param int          $post_id
     * @param array|string $args
     *
     * @global array       $wpp_query
     * @return string Returns false if image can not be returned
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function get_image_link_by_post_id( $post_id, $args = array() ) {

      $args = (object) wp_parse_args( $args, array(
        'size'    => 'large', // Get image by predefined image_size. If width and height are set - it's ignored.
        'width'   => '', // Custom size
        'height'  => '', // Custom size
        // Optionals:
        'default' => true, // Use default image if images doesn't exist or not.
      ));

      if( has_post_thumbnail( $post_id ) ) {
        $attachment_id = get_post_thumbnail_id( $post_id );
      } else {

        // Use default image if image for post doesn't exist
        if( $args->default ) {

          $url = false;

          if( !empty( $args->width ) && !empty( $args->height ) ) {
            $url = 'http://placehold.it/' . $args->width . 'x' . $args->height;
          } else {
            $sizes = \UsabilityDynamics\Utility::all_image_sizes();
            if( key_exists( $args->size, $sizes ) ) {
              $url = 'http://placehold.it/' . $sizes[ $args->size ][ 'width' ] . 'x' . $sizes[ $args->size ][ 'height' ];
            }
          }

          return $url;

        } else {
          return false;
        }
      }

      if( !empty( $args->width ) && !empty( $args->height ) ) {
        $_attachment = \UsabilityDynamics\Utility::get_image_link_with_custom_size( $attachment_id, $args->width, $args->height );
      } else {
        if( $args->size == 'full' ) {
          $_attachment          = wp_get_attachment_image_src( $attachment_id, $args->size );
          $_attachment[ 'url' ] = $_attachment[ 0 ];
        } else {
          $_attachment = \UsabilityDynamics\Utility::get_image_link( $attachment_id, $args->size );
        }
      }

      return is_wp_error( $_attachment ) ? false : $_attachment[ 'url' ];
    }

    /**
     * Set/reset excerpt filters: excerpt_length, excerpt_more
     *
     * Example:
     * $wp_escalade->set_excerpt_filter( '30', 'length' ); // Set excerpt length = 30
     * the_excerpt(); // Excerpt's length will be 30.
     * $wp_escalade->set_excerpt_filter( false, 'length' ); // Reset applied above filter.
     *
     * @staticvar array $_function
     *
     * @param mixed  $val
     * @param string $filter Available values: length, more
     *
     * @return boolean
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function set_excerpt_filter( $val = false, $filter = 'length' ) {
      static $_function = array( 'excerpt_length' => '', 'excerpt_more' => '' );

      if( !in_array( $filter, array( 'length', 'more' ) ) ) {
        return false;
      }

      $_filter = 'excerpt_' . $filter;

      if( has_action( $_filter, $_function[ $_filter ] ) ) {
        remove_filter( $_filter, $_function[ $_filter ] );
      }

      if( !$val ) {
        $_function[ $_filter ] = '';

        return true;
      }

      $_function[ $_filter ] = create_function( '$val', 'return "' . $val . '";' );

      add_filter( $_filter, $_function[ $_filter ] );

      return true;
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
     * Prints styled bloginfo name
     *
     * @return type
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function the_bloginfo_name() {
      $name = get_bloginfo( 'name', 'display' );
      echo $name;
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

  }

}
