<?php
/**
 * Flawless
 *
 * Premium WordPress Theme - functions and definitions.
 *
 * @module Flawless
 * @namespace Flawless
 *
 * @author team@UD
 * @version 0.1.1
 * @since 0.0.2
 */
namespace Flawless {

  /**
   * Flawless
   *
   * Premium WordPress Theme - functions and definitions.
   *
   * @version 0.1.1
   * @author team@UD
   * @class Flawless
   * @since 0.0.2
   */
  final class Flawless {

    public $paths = array();

    /**
     * Initializes Theme
     *
     * Sets up global $flawless variable, loads defaults and binds primary actions.
     *
     *    $this->Loader     = require( 'core/controllers/flawless/loader.php' );
     *
     * @constructor
     * @method Connstruct
     * @since 0.0.6
     */
    public function __construct() {

      define( 'Flawless_Core_Version', '0.1.1' );                                       // This Version
      define( 'Flawless_Option_Key', 'settings::' . Flawless_Core_Version );            // Option Key for this version's settings.
      define( 'Flawless_Directory', basename( TEMPLATEPATH ) );                         // Get Directory name
      define( 'Flawless_Path', untrailingslashit( get_template_directory() ) );         // Path for Includes
      define( 'Flawless_URL', untrailingslashit( get_template_directory_uri() ) );      // Path for front-end links
      define( 'Flawless_Admin_URL', admin_url( 'themes.php?page=functions.php' ) );     // Settings page URL.
      define( 'Flawless_Transdomain', 'flawless' );                                     // Locale slug

      // Define Paths.
      $this->paths = (object) array(
        'controllers'   => untrailingslashit( get_template_directory() ) . DIRECTORY_SEPARATOR . 'core/controllers',
        'modules'       => untrailingslashit( get_template_directory() ) . '/core/modules',
        'extend'        => untrailingslashit( get_template_directory() ) . '/core/extend',
        'helpers'       => untrailingslashit( get_template_directory() ) . '/core/helpers',
        'vendor'        => untrailingslashit( get_template_directory() ) . '/core/vendor',
        'models'        => untrailingslashit( get_template_directory() ) . '/static/models',
        'schemas'       => untrailingslashit( get_template_directory() ) . '/static/schemas',
        'templates'     => untrailingslashit( get_template_directory() ) . '/templates',
        'fonts'         => untrailingslashit( get_template_directory() ) . '/ux/styles',
        'images'        => untrailingslashit( get_template_directory() ) . '/ux/styles',
        'views'         => untrailingslashit( get_template_directory() ) . '/ux/views',
        'scripts'       => untrailingslashit( get_template_directory() ) . '/ux/scripts'
      );

      // Get Loader Class.
      require_once( $this->paths->controllers . '/loader.php' );

      // Load Controllers, Modules, Helpers and Schemas.
      $this->_structure = new Loader( array(
        'controllers' => array(
          'Flawless\\'            => array( $this->paths->controllers ),
          'UsabilityDynamics\\'   => array( $this->paths->vendor ),
          'JsonSchema\\'          => array( $this->paths->vendor . '/justinrainbow' )
        ),
        'modules' => array(
          'modules'   => $this->paths->modules,
          'extend'    => $this->paths->extend
        ),
        'helpers' => array(
          'Helpders' => $this->paths->helpers . '/template.php'
        ),
        'schemas' => array(
          'settings'   => $this->paths->schemas . '/settings.json',
          'features'   => $this->paths->schemas . '/features.json',
          'headers'    => $this->paths->schemas . '/headers.json',
          'state'      => $this->paths->schemas . '/state.json'
        )
      ));

      // Controllers.
      $this->API        = new API();
      $this->Content    = new Content();
      $this->Settings   = new Settings();
      $this->Legacy     = new Legacy();
      $this->Utility    = new Utility();
      $this->Theme      = new Theme();
      $this->Views      = new Views();
      $this->Log        = new Log();

      // Classses.
      $this->Asset      = new Asset();
      $this->Element    = new Element();
      $this->Module     = new Module();
      $this->Schema     = new Schema();
      $this->Shortcode  = new Shortcode();
      $this->Widget     = new Widget();


      //** Load default Flawless settings and configurations */
      $flawless[ 'have_static_home' ] = ( get_option( 'show_on_front' ) == 'page' ? true : false );
      $flawless[ 'using_permalinks' ] = ( get_option( 'permalink_structure' ) != '' ? true : false );
      $flawless[ 'have_blog_home' ] = ( $flawless[ 'have_static_home' ] ? ( get_option( 'page_for_posts' ) ? true : false ) : false );
      $flawless[ 'protocol' ] = ( is_ssl() ? 'https://' : 'http://' );
      $flawless[ 'deregister_empty_widget_areas' ] = false;

      $flawless[ 'asset_directories' ] = apply_filters( 'flawless_asset_location', array(
        untrailingslashit( get_template_directory() ) => untrailingslashit( get_template_directory_uri() ),
        untrailingslashit( get_stylesheet_directory() ) => untrailingslashit( get_stylesheet_directory_uri() )
      ) );

      //** Get Core settings */
      $flawless[ 'theme_data' ] = Loader::get_file_data( TEMPLATEPATH . '/style.css' );
      $flawless[ 'child_theme_data' ] = Loader::get_file_data( untrailingslashit( get_stylesheet_directory() ) . '/style.css' );

      //** Earliest available callback */
      do_action( 'flawless::loaded', $this );

      //** Setup Primary Actions */
      add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
      add_action( 'init', array( $this, 'init_upper' ), 0 );
      add_action( 'init', array( $this, 'init_lower' ), 500 );

    }

    /**
     * Setups up core theme functions
     *
     * Adds image header section and default headers
     *
     * @todo Check if get_option( 'flawless_settings' ) is not cached on second load if self::repair_serialized_array() was ran. - potanin@UD, 6/8/2012
     * @todo flawless::theme_settings_loaded filter should verify that there is a valid return - potanin@UD
     * @todo $flawless[ 'deregister_empty_widget_areas' ] is staticly set right now, need to add menu to configure. - potanin@UD
     * @action after_setup_theme ( 10 )
     * @since 0.0.2
     */
    public function after_setup_theme() {
      Log::add( 'Executed: Flawless::after_setup_theme();' );
      do_action( 'flawless::theme_setup', $this );
      do_action( 'flawless::theme_setup::after', $this );
    }

    /**
     * Run on init hook, loads all other hooks and filters
     *
     * Ran as early as possible, before:
     * - widgets_init ( 1 )
     *
     * @WPA init ( 0 )
     * @since 0.0.2
     *
     */
    public function init_upper() {
      Log::add( 'Executed: Flawless::init_upper();' );

      wp_register_script( 'require', '//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.8/require.min.js', array(), '2.1.8', true );
      wp_register_script( 'twitter-bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0/js/bootstrap.min.js', array( 'jquery' ), '3.0.0', true );
      wp_register_script( 'knockout', '//cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js', array( 'jquery' ), '2.0.0', true );
      wp_register_script( 'knockout-mapping', '//cdnjs.cloudflare.com/ajax/libs/knockout.mapping/2.3.5/knockout.mapping.min.js', array( 'jquery', 'knockout' ), '2.3.5', true );
      wp_register_script( 'knockout-bootstrap', '//cdnjs.cloudflare.com/ajax/libs/knockout-bootstrap/0.2.1/knockout-bootstrap.min.js', array( 'jquery', 'knockout', 'bootstrap' ), '0.2.1', true );
      wp_register_script( 'google-prettify', '//cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js', array( 'jquery' ), 'r298', true );
      wp_register_script( 'jquery-lazyload', '//cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.8.4/jquery.lazyload.min.js', array( 'jquery' ), '1.8.4', true );
      wp_register_script( 'jquery-cookie', '//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.3.1/jquery.cookie.min.js', array( 'jquery' ), '1.3.1', true );
      wp_register_script( 'jquery-touch-punch', '//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.2/jquery.ui.touch-punch.min.js', array( 'jquery' ), '0.2.2', true );
      wp_register_script( 'jquery-equalheights', '//cdn.jsdelivr.net/jquery.equalheights/1.3/jquery.equalheights.min.js', array( 'jquery' ), '1.3', true );
      wp_register_script( 'jquery-placeholder', '//cdn.jsdelivr.net/jquery.placeholder/2.0.7/jquery.placeholder.min.js', array( 'jquery' ), '2.0.7', true );
      wp_register_script( 'jquery-masonry', '//cdnjs.cloudflare.com/ajax/libs/masonry/3.1.1/masonry.pkgd.js', array( 'jquery' ), '3.1.1', true );
      wp_register_script( 'jquery-fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.0.4/jquery.fancybox.pack.js', array( 'jquery' ), '2.0.4', true );
      wp_register_script( 'flawless-core', get_bloginfo( 'template_url' ) . '/ux/scripts/flawless-core.js', array( 'twitter-bootstrap', 'require' ), Flawless_Version, true );
      wp_register_script( 'flawless-frontend', get_bloginfo( 'template_url' ) . '/ux/scripts/flawless-frontend.js', array( 'flawless-core' ), Flawless_Version, true );

      // Admin Only Actions
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );
      add_action( 'admin_init', array( $this, 'admin_init' ) );

      // Frontend Actions
      add_action( 'template_redirect', array( $this, 'template_redirect' ), 0 );

      do_action( 'flawless::init_upper', $this );

    }

    /**
     * Run on init hook, intended to load functionality towards the end of init.  Scripts are loaded here so they can be overwritten by regular init.
     *
     * 500 priority is ran pretty much after everything, to include widgets_init, which is ran @level 1 of init
     *
     * @filter init ( 500 )
     * @since 0.0.2
     */
    public function init_lower() {
      Log::add( 'Executed: Flawless::init_lower();' );

      //** Enqueue front-end assets */
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 100 );
      add_action( 'wp_print_styles', array( $this, 'wp_print_styles' ), 100 );

      do_action( 'flawless::init_lower', $this );

    }

    /**
     * Admin Menu Handler
     *
     * @method admin_menu
     * @for Flawless
     */
    public function admin_menu() {
      Log::add( 'Executed: Flawless::admin_menu();' );
      do_action( 'flawless::admin_menu', $this );
    }

    /**
     * Displays first-time setup splash screen
     *
     *
     * @since 0.0.2
     */
    public function admin_init() {
      Log::add( 'Executed: Flawless::admin_init();' );
      do_action( 'flawless::admin_init', $this );
    }

    /**
     * Primary function for handling front-end actions
     *
     * @filter template_redirect ( 0 )
     * @since 0.0.2
     */
    public function template_redirect() {
      do_action( 'flawless::template_redirect', $this );
      Log::add( 'Executed: Flawless::template_redirect();' );
    }

    /**
     * Front-end script loading
     *
     * Loads all local and remote assets, checks conditionally loaded assets, etc.
     * Modifies body class based on loaded assets.
     *
     * @method wp_enqueue_scripts
     * @for Flawless
     *
     * @filter wp_enqueue_scripts ( 100 )
     * @since 0.0.2
     * @todo Scripts should be registered here, but enqueved at a differnet level. - potanin@UD
     */
    public function wp_enqueue_scripts() {
      Log::add( 'Executed: Flawless::wp_enqueue_scripts();' );

      //** Do not load these styles if we are on admin side or the WP login page */
      if ( strpos( $_SERVER[ 'SCRIPT_NAME' ], 'wp-login.php' ) ) {
        return;
      }

      do_action( 'flawless::wp_enqueue_scripts', $this );

      do_action( 'flawless::extra_local_assets', $this );

      if ( wp_is_mobile() ) {
        wp_enqueue_script( 'jquery-touch-punch' );
      }

      // API Access
      $this[ 'remote_assets' ] = apply_filters( 'flawless::remote_assets', (array) $this[ 'remote_assets' ] );

      //** Check and Load Remote Scripts */
      foreach ( (array) $this[ 'remote_assets' ][ 'script' ] as $asset_handle => $remote_asset ) {

        //** Remove prix if passed, we set them automatically */
        $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );

        if ( Asset::can_get( $this[ 'protocol' ] . $remote_asset, array( 'handle' => $asset_handle ) ) ) {
          wp_enqueue_script( $asset_handle, $this[ 'protocol' ] . $remote_asset, array(), Flawless_Version );

        } else {
          Log::add( sprintf( __( 'Could not load remote asset script: %1s.', 'flawless' ), $remote_asset ) );
        }
      }

      wp_enqueue_script( 'flawless-frontend' );

      if ( current_user_can( 'edit_theme_options' ) ) {
        wp_enqueue_script( 'customize-preview' );
      }

      do_action( 'flawless::wp_enqueue_scripts::end', $this );

    }

    /**
     * Enqueue Frontend Styles. Loaded late so plugin styles can be used for compiling and so theme CSS has more specificity.
     *
     * @method wp_print_styles
     * @for Flawless
     *
     * @filter wp_print_styles ( 100 )
     * @since 0.0.6
     */
    public function wp_print_styles() {
      Log::add( 'Executed: Flawless::wp_print_styles();' );

      global $wp_styles;

      $this[ 'remote_assets' ] = apply_filters( 'flawless::remote_assets', (array) $this[ 'remote_assets' ] );

      // wp_enqueue_style( 'flawless-bootstrap-css', Asset::load( 'bootstrap.less', 'less', array( 'return' => 'url' ) ), array(), '2.0.4', 'screen' );

      //** Enqueue core style.css (always). */
      if ( file_exists( TEMPLATEPATH . '/style.css' ) ) {
        wp_enqueue_style( 'flawless-style', get_bloginfo( 'template_url' ) . '/style.css', array( 'flawless-bootstrap-css' ), Flawless_Version, 'all' );
      }

      //** Enqueue remote styles if they are accessible */
      foreach ( (array) $this[ 'remote_assets' ][ 'css' ] as $asset_handle => $remote_asset ) {
        $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );
        if ( Asset::can_get( $this[ 'protocol' ] . $remote_asset, array( 'handle' => $asset_handle ) ) ) {
          wp_enqueue_style( $asset_handle, $this[ 'protocol' ] . $remote_asset, array(), Flawless_Version );
        }
      }

      //** Enqueue Google Fonts if specified by theme or skin */
      foreach ( (array) $this[ 'current_theme_options' ][ 'Google Fonts' ] as $google_font ) {
        wp_enqueue_style( 'google-font-' . sanitize_file_name( $google_font ), 'https://fonts.googleapis.com/css?family=' . str_replace( ' ', '+', ucfirst( trim( $google_font ) ) ), array( 'flawless-style' ) );
      }

      //** Enqueue Google Pretify */
      // https://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.css
      if ( $this[ 'enable_google_pretify' ] == 'true' ) {
        wp_enqueue_style( 'google-prettify', Asset::load( 'prettify.css', 'css' ), array( 'flawless-style' ), Flawless_Version, 'screen' );
      }

      //** Enqueue Fancybox */
      if ( $this[ 'disable_fancybox' ] != 'true' ) {
        wp_enqueue_style( 'jquery-fancybox' );
      }

      //** Enqueue CSS for active plugins */
      foreach ( apply_filters( 'flawless::active_plugins', (array) Utility::get_active_plugins() ) as $plugin ) {

        //** Get a plugin name slug */
        $plugin = dirname( plugin_basename( trim( $plugin ) ) );

        //** Look for plugin-specific scripts and load them */
        foreach ( (array) $this[ 'asset_directories' ] as $this_directory => $this_url ) {
          if ( file_exists( $this_directory . '/css/' . $plugin . '.css' ) ) {
            $asset_url = apply_filters( 'flawless-asset-url', $this_url . '/css/' . $plugin . '.css', $plugin );
            $file_data = get_file_data( $this_directory . '/css/' . $plugin . '.css', $this[ 'default_header' ][ 'flawless_style_assets' ], 'flawless_style_assets' );
            wp_enqueue_style( 'flawless-asset-' . $plugin, $asset_url, array( 'flawless-style' ), $file_data[ 'Version' ] ? $file_data[ 'Version' ] : Flawless_Version, $file_data[ 'Media' ] ? $file_data[ 'Media' ] : 'screen' );
            Log::add( sprintf( __( 'CSS found for %1s plugin and loaded: %2s.', 'flawless' ), $plugin, $asset_url ), 'info' );
          }
        }
      }

      //** Enqueue Content Styles - before child style.css and skins are loaded */
      if ( Asset::load( 'content.css', 'css' ) ) {
        wp_enqueue_style( 'flawless-content', Asset::load( 'content.css', 'css' ), array( 'flawless-style' ), Flawless_Version );
      }

      //** Enqueue Skin / Color Scheme CSS */
      if ( file_exists( $this[ 'loaded_color_scheme' ][ 'css_path' ] ) ) {
        wp_enqueue_style( 'flawless-colors', $this[ 'loaded_color_scheme' ][ 'css_url' ], array( 'flawless-style' ), Flawless_Version );
      }

      //** Enqueue child theme style.css */
      if ( is_child_theme() ) {
        wp_enqueue_style( 'flawless-child-style', get_bloginfo( 'stylesheet_directory' ) . '/style.css', array( 'flawless-style' ), Flawless_Version );
        wp_enqueue_style( 'flawless-child-app-style', get_bloginfo( 'stylesheet_directory' ) . '/ux/styles/app.css', array( 'flawless-style' ), Flawless_Version );
      }

      //** Check for and load conditional browser styles */
      foreach ( (array) apply_filters( 'flawless::conditional_asset_types', array( 'IE', 'lte IE 7', 'lte IE 8', 'IE 7', 'IE 8', 'IE 9', '!IE' ) ) as $type ) {

        //** Fix slug for URL - remove white space and lowercase */
        $url_slug = strtolower( str_replace( ' ', '-', $type ) );

        foreach ( (array) $this[ 'asset_directories' ] as $assets_path => $assets_url ) {

          if ( file_exists( $assets_path . "/css/conditional-{$url_slug}.css" ) ) {
            wp_enqueue_style( 'conditional-' . $url_slug, $assets_url . "/css/conditional-{$url_slug}.css", array( 'flawless-style' ), Flawless_Version );
            $wp_styles->add_data( 'conditional-' . $url_slug, 'conditional', $type );
          }

        }

      }

      do_action( 'flawless::wp_print_styles', $this );

    }

  }

  // Initialize the theme.
  new Flawless();

}
