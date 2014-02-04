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
      $this->id      = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => ':' ) );
      $this->domain  = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => '-' ) );
      $this->version = wp_get_theme()->get( 'Version' );

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
      ) );

      // Configure Image Sizes.
      $this->media( array(
        'post-thumbnail' => array(
          'description' => '',
          'width'       => 120,
          'height'      => 90,
          'crop'        => true
        ),
        'gallery'        => array(
          'description' => '',
          'width'       => 200,
          'height'      => 999,
          'crop'        => false
        ),
        'sidebar_thumb'  => array(
          'description' => '',
          'width'       => 120,
          'height'      => 100,
          'crop'        => true
        )
      ) );

      // Declare Supported Theme Features.
      $this->supports( array(
        'html5'                => array(),
        'comment-list'         => array(),
        'relative-urls'        => array(),
        'rewrites'             => array(),
        'bootstrap-grid'       => array(),
        'bootstrap-top-navbar' => array(),
        'bootstrap-gallery'    => array(),
        'nice-search'          => array(),
        'jquery-cdn'           => array(),
        'automatic-feed-links' => array(),
        'post-thumbnails'      => array(),
        'custom-header'        => array(),
        'custom-skins'         => array(),
        'custom-background'    => array(),
        'header-dropdowns'     => array(),
        'header-business-card' => array(),
        'frontend-editor'      => array(),
        'saas.udx.io'          => array(),
        'raas.udx.io'          => array(),
        'cdn.udx.io'           => array()
      ) );

      // Enables Customizer for Options.
      $this->customizer( array(
        'disable' => array(
          'static_front_page',
          'nav',
          'title_tagline'
        ),
        'enable'  => array(),
      ) );

      // Add Management UI.
      $this->manage( array(
        'id'       => 'hddp_manage',
        'title'    => __( 'Manage', $this->domain ),
        'template' => dirname( __DIR__ ) . '/templates/admin.manage.php'
      ) );

      // Enable Carrington Build.
      $this->carrington( array(
        'bootstrap'          => true,
        'templates'          => true,
        'styles'             => array(),
        'module_directories' => array(
          __DIR__ . '/modules'
        ),
        'rows'               => array(),
        'post_types'         => array(
          'artist',
          '_aside'
        )
      ));
      
      // Register Theme Settings Model.
      $this->requires( array(
        'id'    => 'festival.model',
        'cache' => 'private, max-age: 0',
        'vary'  => 'user-agent, x-client-type',
        'base'  => home_url( '/assets/scripts' ),
        'data'  => $this->get_model()
      ) );

      // Register Theme Locale Model.
      $this->requires( array(
        'id'    => 'festival.locale',
        'cache' => 'public, max-age: 30000',
        'vary'  => 'x-user',
        'base'  => home_url(),
        'data'  => $this->get_locale()
      ) );

      // Disable Unused Features.
      remove_theme_support( 'custom-header' );


      // Core Actions
      add_action( 'init', array( $this, 'init' ), 100 );
      add_action( 'after_setup_theme', array( $this, 'setup' ) );
      add_action( 'template_redirect', array( $this, 'redirect' ), 100 );
      add_action( 'admin_init', array( $this, 'admin' ) );
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );
      add_action( 'widgets_init', array( $this, 'widgets_init' ), 100 );
      add_action( 'wp_head', array( $this, 'wp_head' ) );
      add_action( 'wp_footer', array( $this, 'wp_footer' ) );
      add_action( 'widgets_init', array( $this, 'widgets' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

      add_filter( 'body_class', array( $this, 'body_class' ) );

      if( isset( $_GET[ 'test' ] ) ) {
        // $this->_updated();
      }

      // Initializes Wordpress Menufication
      if( class_exists( '\Menufication' ) ) {
        $this->menufication = \Menufication::getInstance();
      }

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
      return array_merge( $class, array( is_external_referrer() ? 'external-referrer' : 'internal-referrer' ) );
    }

    /**
     * On settings init we also merge structure with global network settings
     *
     */
    public function settings( $args = array(), $data = array() ) {
      parent::settings( $args, $data );
    }

    private function _updated( $type = '' ) {

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
      ) );

      if( is_wp_error( $response ) ) {
        wp_die( $response->get_error_message() );
      }

      // Have Encoded Data.
      if( is_object( $response ) && isset( $response->data ) ) {
        die( base64_decode( $response->data ) );
      }

      die( '<pre>' . print_r( $response, true ) . '</pre>' );

    }

    /**
     * Initial Theme Setup
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

    private function get_model() {

      $_home_url = parse_url( home_url() );

      return (array) apply_filters( 'festival:model:settings', array(
        'ajax'       => admin_url( 'admin-ajax.php' ),
        'domain'     => trim( $_home_url[ 'host' ] ? $_home_url[ 'host' ] : array_shift( explode( '/', $_home_url[ 'path' ], 2 ) ) ),
        'permalinks' => get_option( 'permalink_structure' ) == '' ? false : true,
        'settings'   => $this->get(),
      ) );

    }

    /**
     * Initial Theme Setup
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function setup() {

      // Make theme available for translation
      if( is_dir( get_template_directory() . '/static/languages' ) ) {
        load_theme_textdomain( $this->domain, get_template_directory() . '/static/languages' );
      }

      // Register Navigation Menus
      register_nav_menu( 'primary', __( 'Primary Menu', $this->domain ) );
      register_nav_menu( 'social', __( 'Social Links', $this->domain ) );
      register_nav_menu( 'footer', __( 'Footer Menu', $this->domain ) );
      register_nav_menu( 'mobile', __( 'Mobile Menu', $this->domain ) );
      
      // Add custom header functionality
      add_custom_image_header( '', create_function('',''));

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
      ) ) );

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
        'class'          => 'wp-festival-aside',
        'more_link_text' => null,
        'strip_teaser'   => null,
        'return'         => false,
      ) );

      // Preserve Post.
      $_post = $post;

      // Using query_posts() will not work because we must not change the global query.
      $custom_loop = new \WP_Query( array(
        'name'      => $name,
        'post_type' => $args->type
      ) );

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
        get_template_part( 'templates/aside/' . $name, get_post_type() );
        $content = ob_get_clean();
      }

      $content = apply_filters( 'festival:aside', isset( $content ) ? '<div class="' . $args->class . '" data-aside="' . $name . '">' . $content . '</div>' : null, $name );

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
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
      ) );

      register_sidebar( array(
        'name'          => __( 'Left Sidebar' ),
        'description'   => __( 'Shown on Pages with specific template.' ),
        'id'            => 'left-sidebar',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
      ) );

      register_sidebar( array(
        'name'          => __( 'Single Page Sidebar' ),
        'description'   => __( 'Shown on all Single Pages.' ),
        'id'            => 'single-sidebar',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
      ) );

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
        $this->carrington->registerModule( 'HTMLModule' );
        $this->carrington->registerModule( 'EditorModule' );
        $this->carrington->registerModule( 'VideoModule' );
        $this->carrington->registerModule( 'SidebarModule' );
        $this->carrington->registerModule( 'HeroModule' );
        $this->carrington->registerModule( 'ImageModule' );
        $this->carrington->registerModule( 'GalleryModule' );
        $this->carrington->registerModule( 'LoopModule' );
        $this->carrington->registerModule( 'CarouselModule' );
        $this->carrington->registerModule( 'CalloutModule' );
        $this->carrington->registerModule( 'EventHeroModule' );
        $this->carrington->registerModule( 'ArtistListModule' );
        $this->carrington->registerModule( 'EventHeroModule' );
        $this->carrington->registerModule( 'EventLoopModule' );
      }
      
      // Register Custom Post Types and set their taxonomies
      $this->structure( $this->get( 'structure' ) );

      // Sync 'Social Streams' data with social networks
      $this->sync_streams();

      // Register Script and Styles.
      wp_register_style( 'app', home_url( '/assets/styles/app.css' ), array(), $this->version, 'all' );

      //add_editor_style( home_url( '/assets/editor-style.css' ) );

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
        ) );

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

      wp_enqueue_style( 'app' );

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
        $template = str_replace( '.php', '', basename( $template ) );
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
      global $wpp_query;

      $args = (object) wp_parse_args( $args, array(
        'size'             => 'full', // Get image by predefined image_size. If width and height are set - it's ignored.
        'width'            => '', // Custom size
        'height'           => '', // Custom size
        // Optionals:
        'post_type'        => false, // Different post types can have different default images
        'default'          => true, // Use default image if images doesn't exist or not.
        'default_file'     => ( get_template_directory() . '/images/src/no-image.jpg' ), // Filename
      ) );

      if( has_post_thumbnail( $post_id ) ) {
        $attachment_id = get_post_thumbnail_id( $post_id );
      } else {

        // Use default image if image for post doesn't exist
        if( $args->default ) {

          $wp_upload_dir = wp_upload_dir();
          $dir           = $wp_upload_dir[ 'basedir' ] . '/no_image/' . md5( $this->domain ) . '';
          $url           = $wp_upload_dir[ 'baseurl' ] . '/no_image/' . md5( $this->domain ) . '';
          $path          = $dir . '/' . basename( $args->default_file );
          $default_path  = $args->default_file;
          $guid          = $url . '/' . basename( $path );

          if( !is_dir( $dir ) ) {
            wp_mkdir_p( $dir );
          }

          // If attachment for default image doesn't exist
          if( !$attachment_id = \UsabilityDynamics\Utility::get_image_id_by_guid( $guid ) ) {
            // Determine if image exists. Check image by post_type at first if post_type is passed.\
            
            
            if( !file_exists( $default_path ) ) {
              return false;
            }
            if( !file_exists( $path ) ) {
              copy( $default_path, $path );
            }

            $wp_filetype = wp_check_filetype( basename( $path ), null );

            $attachment = array(
              'guid'           => $guid,
              'post_mime_type' => $wp_filetype[ 'type' ],
              'post_title'     => __( 'No Image', $this->domain ),
              'post_content'   => '',
              'post_status'    => 'inherit'
            );

            if( !$attachment_id = wp_insert_attachment( $attachment, $path ) ) {
              return false;
            }

            // image.php file must be included at first
            // for the function wp_generate_attachment_metadata() to work
            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $path );

            wp_update_attachment_metadata( $attachment_id, $attachment_data );

          }

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

      return json_decode( json_encode( $_result ) );

    }

  }

}
