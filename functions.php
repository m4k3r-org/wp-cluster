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

    /**
     * Initializes Theme
     *
     * Sets up global $flawless variable, loads defaults and binds primary actions.
     *
     * @constructor
     * @method Connstruct
     * @since 0.0.6
     */
    public function __construct() {

      // This Version
      define( 'Flawless_Core_Version', '0.1.1' );

      // Option Key for this version's settings.
      define( 'Flawless_Option_Key', 'settings::' . Flawless_Core_Version );

      // Get Directory name
      define( 'Flawless_Directory', basename( TEMPLATEPATH ) );

      // Path for Includes
      define( 'Flawless_Path', untrailingslashit( get_template_directory() ) );
      // define( 'Flawless_Path', untrailingslashit( get_stylesheet_directory() ) );

      // Path for front-end links
      //define( 'Flawless_URL', untrailingslashit( get_template_directory_uri() ) );
      define( 'Flawless_URL', untrailingslashit( get_stylesheet_directory_uri() ) );

      // Settings page URL.
      define( 'Flawless_Admin_URL', admin_url( 'themes.php?page=functions.php' ) );

      // Directory path to permium modules
      define( 'Flawless_Premium', Flawless_Path . 'core/premium' );

      // Directory path JSON schemas
      define( 'Flawless_Schemas', Flawless_Path . 'schemas' );

      // Locale slug
      define( 'Flawless_Transdomain', 'flawless' );

      // Bail early if old server.
      if ( version_compare( phpversion(), 5.3 ) < 0 ) {
        switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
        return wp_die( sprintf( __( 'Your version of PHP, %1s, is old, and this theme cannot support it, so it has been disabled. Please consider upgrading to 5.3, or newer. <a href="%2s">Back to Safety.</a>', HDDP ), phpversion(), admin_url() ) );
      }

      // Load Controllers
      // @todo Migrate load.php into this file and have it reference UsabilityDynamics\Loader class manually.
      require_once( 'core/load.php' );

      $this->loader = new Loader();
      $this->api = new API();
      $this->settings = new Settings();
      $this->module = new Module();
      $this->models = new \UsabilityDynamics\Models;

      //die( '<pre>' . print_r( $this->models, true ) . '</pre>' );
      // $this->legacy     = new Legacy();
      $this->utility = new Utility();

      // add_filter( 'template_directory', call_user_func(array( $this, template_directory ), $stylesheet_dir) );
      add_filter( 'template_directory', array( $this->utility, 'fix_path' ) );
      add_filter( 'stylesheet_directory', array( $this->utility, 'fix_path' ) );

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

      $flawless[ 'paths' ] = array(
        'templates' => untrailingslashit( get_template_directory() ) . '/templates',
        'controllers' => untrailingslashit( get_template_directory() ) . '/core/controllers',
        'modules' => untrailingslashit( get_template_directory() ) . '/core/modules',
        'helpers' => untrailingslashit( get_template_directory() ) . '/core/helpers',
        'premium' => untrailingslashit( get_template_directory() ) . '/core/premium',
        'vendor' => untrailingslashit( get_template_directory() ) . '/core/vendor',
        'static' => untrailingslashit( get_template_directory() ) . '/static',
        'ux' => untrailingslashit( get_template_directory() ) . '/ux'
      );

      $flawless[ 'default_header' ][ 'flawless_style_assets' ] = array(
        'Name' => __( 'Name', 'Flawless' ),
        'Description' => __( 'Description', 'Flawless' ),
        'Media' => __( 'Media', 'Flawless' ),
        'Version' => __( 'Version', 'Flawless' )
      );

      $flawless[ 'default_header' ][ 'themes' ] = array(
        'Name' => 'Theme Name',
        'ThemeURI' => 'Theme URI',
        'Description' => 'Description',
        'Author' => 'Author',
        'AuthorURI' => 'Author URI',
        'Version' => 'Version',
        'Template' => 'Template',
        'Status' => 'Status',
        'Tags' => 'Tags',
        'TextDomain' => 'Text Domain',
        'DomainPath' => 'Domain Path',
        'Supported Features' => __( 'Supported Features', 'Flawless' ),
        'Disabled Features' => __( 'Disabled Features', 'Flawless' ),
        'Google Fonts' => __( 'Google Fonts', 'Flawless' )
      );

      $flawless[ 'default_header' ][ 'flawless_extra_assets' ] = array(
        'Name' => __( 'Name', 'Flawless' ),
        'Description' => __( 'Description', 'Flawless' ),
        'Author' => __( 'Author', 'Flawless' ),
        'Version' => __( 'Version', 'Flawless' ),
        'ThemeFeature' => __( 'Theme Feature', 'Flawless' )
      );

      /**
       * Add Color Scheme
       *
       * @author potanin@UD
       */
      add_filter( 'extra_theme_headers', function () {
        global $flawless;
        return (array) $flawless[ 'default_header' ][ 'themes' ];
      } );

      include_once( $flawless[ 'paths' ][ 'helpers' ] . '/template.php' );

      //** Get Core settings */
      $flawless[ 'theme_data' ] = array_filter( (array) get_file_data( TEMPLATEPATH . '/style.css', $flawless[ 'default_header' ][ 'themes' ], 'theme' ) );

      //** Define core version which is not affected by child theme version */
      // define( 'Flawless_Core_Version', $flawless[ 'theme_data' ][ 'Version' ] );

      //** If child theme is used, we combine the child theme version with the core version */
      if ( is_child_theme() ) {
        $flawless[ 'child_theme_data' ] = array_filter( (array) get_file_data( untrailingslashit( get_stylesheet_directory() ) . '/style.css', $flawless[ 'default_header' ][ 'themes' ], 'theme' ) );
        define( 'Flawless_Version', sanitize_file_name( implode( '-', array( Flawless_Core_Version, $flawless[ 'child_theme_data' ][ 'Version' ] ) ) ) );
      } else {
        define( 'Flawless_Version', Flawless_Core_Version );
      }

      //** Setup Primary Actions */
      add_action( 'after_setup_theme', array( 'Flawless', 'after_setup_theme' ) );
      add_action( 'init', array( 'Flawless', 'init_upper' ), 0 );
      add_action( 'init', array( 'Flawless', 'init_lower' ), 500 );

      //** Earliest available callback */
      do_action( 'flawless::loaded', $flawless );

      // Synchronize settings.
      // $this->settings = $flawless;

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
    static function after_setup_theme() {
      global $flawless, $wpdb;

      do_action( 'flawless::theme_setup', $flawless );

      //** Get default LESS options */
      $flawless = Flawless::parse_css_option_defaults( $flawless );

      //** Load Database Options, and repair serialized array if need be */
      $flawless_settings = get_option( 'flawless_settings' );

      //** In case serialize string was broken during export/import */
      if ( !is_array( $flawless_settings ) || empty( $flawless_settings ) ) {

        $flawless_settings = $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'flawless_settings' " );

        if ( is_array( $flawless_settings ) && !empty( $flawless_settings ) ) {
          update_option( 'flawless_settings', $flawless_settings );

        } elseif ( file_exists( untrailingslashit( get_stylesheet_directory() ) . '/default-configuration.json' ) && $default_configuration = file_get_contents( untrailingslashit( get_stylesheet_directory() ) . '/default-configuration.json' ) ) {
          update_option( 'flawless_settings', $flawless_settings = json_decode( $default_configuration, true ) );
        }
      }

      //** Merge default $flawless settings with database settings */
      $flawless = self::extend( $flawless, get_option( 'flawless_settings' ) );

      //** Apply earliest possible settings callback filter, verify that a valid array is returned */
      if ( is_array( $_theme_settings_loaded = apply_filters( 'flawless::theme_settings_loaded', $flawless ) ) ) {
        $flawless = $_theme_settings_loaded;
      }

      //** Clean up array and strip slashes */
      $flawless = stripslashes_deep( array_filter( (array) $flawless ) );

      Log::add( 'Theme settings loaded.' );

      //** Load theme's core assets */
      $flawless = Flawless::load_core_assets( $flawless );

      //** Load extra functionality */
      $flawless = Flawless::load_extend_modules( $flawless );

      //** Have to be run on after_setup_theme() level. */
      $flawless = Flawless::setup_theme_features( $flawless );

      //** Figure out which Widget Area Sections ( WAS ) are available for use in the theme */
      $flawless = Flawless::define_widget_area_sections( $flawless );

      do_action( 'flawless::theme_setup::after', $flawless );

      //** Global helper to fix local paths, should probably be moved somewhere else */
      add_filter( 'flawless::root_path', function ( $path ) {
        return Utility::fix_path( $path );
      } );

      //** Support for WP's code quality monitoring when debug mode is not enabled. */
      add_action( 'doing_it_wrong_run', function ( $function, $message, $version ) {
        Log::add( sprintf( __( 'Warning: %1$s was called incorrectly. %2$s %3$s' ), $function, $message, $version ), 'error' );
      }, 10, 3 );

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
    static function init_upper() {
      global $flawless;

      Log::add( 'Executed: Flawless::init();' );

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

      wp_register_script( 'flawless-core', get_bloginfo( 'template_url' ) . '/assets/js/flawless-core.js', array( 'twitter-bootstrap', 'require' ), Flawless_Version, true );
      wp_register_script( 'flawless-frontend', get_bloginfo( 'template_url' ) . '/assets/js/flawless-frontend.js', array( 'flawless-core' ), Flawless_Version, true );

      //** Admin Only Actions */
      add_action( 'admin_menu', array( 'Flawless', 'admin_menu' ) );
      add_action( 'admin_init', array( 'Flawless', 'admin_init' ) );

      //** Front-end Actions */
      add_action( 'template_redirect', array( 'Flawless', 'template_redirect' ), 0 );

      //** Admin AJAX Handler */
      add_action( 'wp_ajax_flawless_action', create_function( '', ' die( json_encode( Flawless::ajax_actions() )); ' ) );
      add_action( 'wp_ajax_nopriv_flawless_action', create_function( '', ' die( json_encode( Flawless::ajax_actions() )); ' ) );

      //** Frontend AJAX Handler */
      add_action( 'wp_ajax_frontend_ajax_handler', create_function( '', ' die( json_encode( Flawless::frontend_ajax_handler() )); ' ) );
      add_action( 'wp_ajax_nopriv_frontend_ajax_handler', create_function( '', ' die( json_encode( Flawless::frontend_ajax_handler() )); ' ) );

      add_action( 'wp_ajax_flawless_signup_field_check', array( 'Flawless', 'flawless_signup_field_check' ), 10, 3 );

      // Register Navigation Menus
      // @todo Migrate presentation-specific logic outside of this class; otherwise should make it easy to disable/enable in child theme.
      register_nav_menus(
        array(
          //'header-actions-menu' => __( 'Header Actions Menu', 'flawless' ),
          'header-menu' => __( 'Header Menu', 'flawless' ),
          //'header-sub-menu' => __( 'Header Sub-Menu', 'flawless' ),
          'footer-menu' => __( 'Footer Menu', 'flawless' ),
          //'bottom_of_page_menu' => __( 'Bottom of Page Menu', 'flawless' )
        )
      );

      $flawless = Flawless::setup_content_types( $flawless );

      //** Check if updates should be disabled */
      Flawless::maybe_disable_updates();

      /**
       * Determines if search request exists but it's empty, we do 'hack' to show Search result page.
       *
       * @author peshkov@UD
       */
      add_filter( 'request', function ( $query_vars ) {

        if ( isset( $_GET[ 's' ] ) && empty( $_GET[ 's' ] ) ) {
          $query_vars[ 's' ] = " ";
        }

        return $query_vars;

      }, 0 );

      add_filter( 'widget_text', 'do_shortcode' );

      //** Process Navbar ( has to be called early in case it needs to deregister the admin bar */
      if ( !is_admin() ) {
        Flawless::prepare_navbars();
      }

      do_action( 'flawless::init_upper' );

    }

    /**
     * Run on init hook, intended to load functionality towards the end of init.  Scripts are loaded here so they can be overwritten by regular init.
     *
     * 500 priority is ran pretty much after everything, to include widgets_init, which is ran @level 1 of init
     *
     * @filter init ( 500 )
     * @since 0.0.2
     */
    static function init_lower() {
      global $flawless;

      //** Enqueue front-end assets */
      add_action( 'wp_enqueue_scripts', array( 'Flawless', 'wp_enqueue_scripts' ), 100 );
      add_action( 'wp_print_styles', array( 'Flawless', 'wp_print_styles' ), 100 );

      add_action( 'admin_footer', array( 'Log', 'log_stats' ), 10 );
      add_action( 'wp_footer', array( 'Log', 'log_stats' ), 10 );

      //** Extra front-end assets ( such as Fancybox ) */
      add_action( 'flawless::extra_local_assets', array( __CLASS__, 'extra_local_assets' ), 5 );
      add_filter( 'wp_print_footer_scripts', array( __CLASS__, 'wp_print_footer_scripts' ), 100 );

      add_filter( 'post_link', array( 'Flawless', 'filter_post_link' ), 10, 2 );
      add_filter( 'post_type_link', array( 'Flawless', 'filter_post_link' ), 10, 2 );

      //** Has to be run every time for custom taxonomy URLs to work, when permalinks are used. */
      if ( $_REQUEST[ 'flush_rewrite_rules' ] == 'true' ) {
        flush_rewrite_rules();
      } elseif ( $flawless[ 'using_permalinks' ] ) {
        flush_rewrite_rules();
      }

      add_action( 'get_footer', function () {
        global $wp_query, $flawless;
        $wp_query->query_vars[ 'flawless' ] = $flawless;
      } );

      /* Check if .htaccess file is not there, and re-creates it */
      if ( $flawless[ 'using_permalinks' ] && method_exists( self, 'save_mod_rewrite_rules' ) ) {
        self::save_mod_rewrite_rules();
      }

      do_action( 'flawless::init_lower' );

    }

    /**
     * Admin Menu Handler
     *
     */
    static function admin_menu() {
      do_action( 'flawless::admin_menu' );
    }

    /**
     * Load extra front-end assets
     *
     * @todo Why are these not being registered? Does it matter? - potanin@UD 6/10/12
     * @since 0.0.3
     */
    static function extra_local_assets() {
      global $flawless;

      //** Fancybox Scripts and Styles - enabled by default */
      if ( $flawless[ 'disable_fancybox' ] != 'true' ) {
        wp_enqueue_script( 'jquery-fancybox' );
      }

      //** Masonry for galleries. */
      if ( $flawless[ 'disable_masonry' ] != 'true' ) {
        wp_enqueue_script( 'jquery-masonry' );
      }

      //** Masonry for galleries. */
      if ( $flawless[ 'disable_equalheights' ] != 'true' ) {
        wp_enqueue_script( 'jquery-equalheights' );
      }

      //** UD Form Helper - enabled on default */
      if ( $flawless[ 'disable_form_helper' ] != 'true' ) {
        wp_enqueue_script( 'jquery-ud-form_helper' );
      }

      /* Dynamic Filter - disabled on default */
      if ( $flawless[ 'enable_dynamic_filter' ] == 'true' ) {
        wp_enqueue_script( 'jquery-ud-dynamic_filter' );
      }

      /* Google Code Pretification - disabled on default */
      if ( $flawless[ 'enable_google_pretify' ] == 'true' ) {
        wp_enqueue_script( 'google-prettify' );
      }

      /* Lazyload for Images - disabled on default */
      if ( $flawless[ 'enable_lazyload' ] == 'true' ) {
        wp_enqueue_script( 'jquery-lazyload' );
      }

    }

    /**
     * Disables update notifications if set.
     *
     * @source Update Notifications Manager ( http://www.geekpress.fr/ )
     * @action after_setup_theme( 10 )
     * @since 0.0.2
     */
    static function maybe_disable_updates() {
      global $flawless;

      if ( $flawless[ 'disable_updates' ][ 'plugins' ] == 'true' ) {
        remove_action( 'load-update-core.php', 'wp_update_plugins' );
        add_filter( 'pre_site_transient_update_plugins', create_function( '', "return null;" ) );
        wp_clear_scheduled_hook( 'wp_update_plugins' );
      }

      if ( $flawless[ 'disable_updates' ][ 'core' ] == 'true' ) {
        add_filter( 'pre_site_transient_update_core', create_function( '', "return null;" ) );
        wp_clear_scheduled_hook( 'wp_version_check' );
      }

      if ( $flawless[ 'disable_updates' ][ 'theme' ] == 'true' ) {
        remove_action( 'load-update-core.php', 'wp_update_themes' );
        add_filter( 'pre_site_transient_update_themes', create_function( '', "return null;" ) );
        wp_clear_scheduled_hook( 'wp_update_themes' );
      }

    }

    /**
     * Defined which "Widget Area Sections" available for use in the Theme.
     *
     * These sections can have different Widget Areas associated with them, based on content type, home page, or blog page.
     * Definitions here are only configurable via API.
     *
     * @todo Add "Attention Grabber" via Feature
     *
     * @action after_setup_theme( 10 )
     * @since 0.0.2
     */
    static function define_widget_area_sections( $flawless ) {

      $flawless[ 'widget_area_sections' ][ 'left_sidebar' ] = array(
        'placement' => __( 'left', 'flawless' ),
        'class' => 'c6-12 sidebar-left span4 first',
        'label' => __( 'Left Sidebar', 'flawless' )
      );

      $flawless[ 'widget_area_sections' ][ 'right_sidebar' ] = array(
        'placement' => __( 'right', 'flawless' ),
        'class' => 'c6-56 sidebar-right span4 last',
        'label' => __( 'Right Sidebar', 'flawless' )
      );

      $flawless[ 'widget_area_sections' ] = apply_filters( 'flawless_widget_area_sections', $flawless[ 'widget_area_sections' ] );

      do_action( 'flawless_define_widget_area_sections' );

      return $flawless;

    }

    /**
     * Get Widget Titles and Instances in an area
     *
     * Currently not used, Denali 3.0 port.
     *
     * @since 0.0.2
     */
    static function widget_area_tabs( $widget_area = false ) {
      global $wp_registered_widgets;

      //** Check if widget are is active before doing anything else */
      if ( !Flawless::is_active_sidebar( $widget_area ) ) {
        return false;
      }

      $sidebars_widgets = wp_get_sidebars_widgets();

      if ( empty( $sidebars_widgets ) ) {
        return false;
      }

      $load_options = array();

      if ( empty( $sidebars_widgets[ $widget_area ] ) || !is_array( $sidebars_widgets[ $widget_area ] ) ) {
        return false;
      }

      foreach ( (array) $sidebars_widgets[ $widget_area ] as $count => $id ) {

        if ( !isset( $wp_registered_widgets[ $id ] ) ) {
          continue;
        }

        $callback = $wp_registered_widgets[ $id ][ 'callback' ];
        $number = $wp_registered_widgets[ $id ][ 'params' ][ 0 ][ 'number' ];
        $option_name = $callback[ 0 ]->option_name;
        $type = $wp_registered_widgets[ $id ][ 'name' ];
        $params = array( '', (array) $wp_registered_widgets[ $id ][ 'params' ] );
        $name = trim( $wp_registered_widgets[ $id ][ 'name' ] );

        if ( !isset( $load_options[ $option_name ] ) ) {
          $all_options = get_option( $option_name );
          $load_options[ $option_name ] = $all_options;
        }

        $these_settings = $load_options[ $option_name ][ $number ];

        $title = trim( $these_settings[ 'title' ] );

        $return[ $count ][ 'title' ] = ( !empty( $title ) ? $title : $name );
        $return[ $count ][ 'id' ] = $wp_registered_widgets[ $id ][ 'id' ];

        if ( is_callable( $callback ) ) {
          $return[ $count ][ 'callable' ] = true;
        }

      }

      if ( is_array( $return ) ) {
        return $return;
      }

      return false;

    }

    /**
     * Setup theme features using the WordPress API as much as possible.
     *
     * This function must run after all the post types are created and initialized to have effect.
     *
     * This function may be called more than once at different action levels ( ALs ) since taxonomy and post types may be added by plugins,
     * yet we want the admin to have full control over all the post types and taxonomies in one UI.
     *
     * @todo Need to update all labels for taxonomoies. - potanin@UD
     * @action init (0)
     * @since 0.0.2
     *
     */
    static function setup_content_types( $flawless = false ) {
      global $wp_post_types, $wp_taxonomies;

      if ( !$flawless ) {
        global $flawless;
      }

      Log::add( 'Executed: Flawless::setup_content_types();' );

      do_action( 'flawless::content_types', $flawless );

      //** May only be necessary temporarily since Attachments were included in version 0.0.6 by accident */
      unset( $flawless[ 'post_types' ][ 'attachment' ] );

      //** Create any new post types that are in our settings array, but not in the global $wp_post_types variable*/
      foreach ( (array) $flawless[ 'post_types' ] as $type => $data ) {

        if ( $data[ 'flawless_post_type' ] != 'true' ) {
          continue;
        }

        Log::add( sprintf( __( 'Adding custom post type: %1s', 'flawless' ), $type ) );

        $post_type_settings = array(
          'label' => $data[ 'name' ],
          'menu_position' => ( $data[ 'hierarchical' ] == "true" ? 21 : 6 ),
          'public' => true,
          'exclude_from_search' => $data[ 'exclude_from_search' ],
          'hierarchical' => $data[ 'hierarchical' ],
          'has_archive' => is_numeric( $data[ 'root_page' ] ) && $data[ 'root_page' ] > 0 ? false : true,
          'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'post-formats', 'author' ),
        );

        if ( !empty( $data[ 'rewrite_slug' ] ) ) {
          $post_type_settings[ 'rewrite' ] = array(
            'slug' => $data[ 'rewrite_slug' ],
            'with_front' => true
          );
        }

        //** 'has_archive' allows post_type entries list korotkov@ud */
        register_post_type( $type, $post_type_settings );

        do_action( 'flawless_content_type_added', array( 'type' => $type, 'data' => $data ) );

      }

      //** Create any Flawless taxonomoies an create them, or update existing ones with custom settings  */
      foreach ( (array) $flawless[ 'taxonomies' ] as $type => $data ) {
        if ( $data[ 'flawless_taxonomy' ] == 'true' ) {

          Log::add( sprintf( __( 'Adding custom flawless_taxonomy: %1s', 'flawless' ), $type ) );

          $flawless[ 'taxonomies' ][ $type ][ 'rewrite_tag' ] = '%' . $type . '%';

          if ( !taxonomy_exists( $type ) ) {

            $taxonomy_settings = array(
              'label' => $data[ 'label' ],
              'exclude_from_search' => $data[ 'exclude_from_search' ],
              'hierarchical' => $data[ 'hierarchical' ]
            );

            $taxonomy_settings[ 'rewrite' ] = array(
              'slug' => $data[ 'rewrite_slug' ] ? $data[ 'rewrite_slug' ] : $type,
              'with_front' => true
            );

            register_taxonomy( $type, '', $taxonomy_settings );
          }

          add_rewrite_tag( $flawless[ 'taxonomies' ][ 'rewrite_tag' ], '([^/]+)' );

          do_action( 'flawless_content_type_added', array( 'type' => $type, 'data' => $data ) );

        }

        //** Check to see if a taxonomy has disappeared ( i.e. plugin deactivated that was adding it ) */
        if ( !in_array( $type, array_keys( $wp_taxonomies ) ) ) {
          unset( $flawless[ 'taxonomies' ][ $type ] );
        }

        $data = apply_filters( 'flawless::generate_taxonomies', $data, $type );

        //** Save our custom settings to global taxononmy object */
        $wp_taxonomies[ $type ]->hierarchical = $data[ 'hierarchical' ] == 'true' ? true : false;
        $wp_taxonomies[ $type ]->exclude_from_search = $data[ 'exclude_from_search' ] == 'true' ? true : false;
        //$wp_taxonomies[ $type ]->show_tagcloud = $data[ 'show_tagcloud' ] == 'true' ? true : false;

        $wp_taxonomies[ $type ]->label = $data[ 'label' ] ? $data[ 'label' ] : $wp_taxonomies[ $type ]->label;

        //** Automatically try to get singular form if not set ( experimental ) */
        $wp_taxonomies[ $type ]->labels->singular_name = $data[ 'singular_label' ] ? $data[ 'singular_label' ] : self::depluralize( $data[ 'label' ] );
        $wp_taxonomies[ $type ]->labels->name = $data[ 'label' ] ? $data[ 'label' ] : $wp_taxonomies[ $type ]->label;

        //** Set singular labels */
        $wp_taxonomies[ $type ]->labels->add_new_item = sprintf( __( 'New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->new_item = sprintf( __( 'New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->edit_item = sprintf( __( 'Edit %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->update_item = sprintf( __( 'Update %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->view_item = sprintf( __( 'No %1s found.', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->new_item_name = sprintf( __( 'New %1s Name.', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->not_found = sprintf( __( 'Add New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->not_found_in_trash = sprintf( __( 'Add New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );

        //** Plural Labels */
        $wp_taxonomies[ $type ]->labels->search_items = sprintf( __( 'Search %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->not_found_in_trash = sprintf( __( 'No %1s found in trash.', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->popular_items = sprintf( __( 'Popular %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->add_or_remove_items = sprintf( __( 'Add ore remove %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->choose_from_most_used = sprintf( __( 'Choose from most used %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->all_items = sprintf( __( 'All %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->menu_name = $wp_taxonomies[ $type ]->labels->name;

      }

      //** Cycle through all existing taxonomies, and load their settings into FS settings */
      foreach ( (array) $wp_taxonomies as $type => $data ) {

        //** We do not do anything with non displayed taxononomies */
        if ( !$data->show_ui ) {
          continue;
        }

        if ( $flawless[ 'taxonomies' ][ $type ][ 'flawless_taxonomy' ] == 'true' ) {
          $flawless[ 'taxonomies' ][ $type ][ 'rewrite_slug' ] = $data->rewrite[ 'slug' ];
        }

        $flawless[ 'taxonomies' ][ $type ][ 'label' ] = $wp_taxonomies[ $type ]->labels->name;
        $flawless[ 'taxonomies' ][ $type ][ 'label' ] = $wp_taxonomies[ $type ]->labels->name;
        $flawless[ 'taxonomies' ][ $type ][ 'hierarchical' ] = $wp_taxonomies[ $type ]->hierarchical ? 'true' : 'false';
        $flawless[ 'taxonomies' ][ $type ][ 'exclude_from_search' ] = $wp_taxonomies[ $type ]->exclude_from_search ? 'true' : 'false';
        //$flawless[ 'taxonomies' ][ $type ][ 'show_tagcloud' ] = $wp_taxonomies[ $type ]->show_tagcloud ? 'true' : 'false';

        //** If Term Meta is supported, add callback function to render any UI we want to show on the taxonomy pages */
        if ( current_theme_supports( 'term-meta' ) || current_theme_supports( 'extended-taxonomies' ) ) {
          add_action( $type . '_edit_form_fields', array( 'Flawless_ui', 'taxonomy_edit_form_fields' ), 5, 2 );
          add_action( $type . '_pre_add_form', array( 'Flawless_ui', 'taxonomy_pre_add_form' ), 5 );
        }

      }

      //** Loop through post types and update the $flawless array */
      foreach ( (array) $wp_post_types as $type => $data ) {

        //** We don't do anything with any post types that are not displayed */
        if ( !$data->public || !$data->show_ui ) {
          continue;
        }

        $defaults = get_object_taxonomies( $type );

        //** Configure special settings if they are set, or use default settings */
        $flawless[ 'post_types' ][ $type ][ 'name' ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'name' ] ) ? $flawless[ 'post_types' ][ $type ][ 'name' ] : $data->labels->name );
        $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] ) ? $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] : ( $data->hierarchical ? 'true' : false ) );

        //** Cycle through all available taxonomies and add them back to post type. */
        foreach ( (array) $flawless[ 'taxonomies' ] as $tax => $tax_data ) {

          $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] ) ? $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] : ( in_array( $tax, $defaults ) ? 'enabled' : '' ) );

          if ( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] == 'enabled' ) {
            register_taxonomy_for_object_type( $tax, $type );
          }

          //** Remove blank values added as placeholders when FL taxonomies are initially registered */
          $wp_taxonomies[ $tax ]->object_type = array_filter( $wp_taxonomies[ $tax ]->object_type );
        }

        $flawless[ 'post_types' ][ $type ][ 'rewrite_slug' ] = $data->rewrite[ 'slug' ];

        @ksort( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ] );

        if ( $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] == 'true' ) {
          $wp_post_types[ $type ]->hierarchical = true;
          add_post_type_support( $type, 'page-attributes' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'disable_comments' ] == 'true' ) {
          remove_post_type_support( $type, 'comments' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'custom_fields' ] != 'true' ) {
          remove_post_type_support( $type, 'custom-fields' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'disable_author' ] == 'true' ) {
          remove_post_type_support( $type, 'author' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'exclude_from_search' ] == 'true' ) {
          $wp_post_types[ $type ]->exclude_from_search = true;
        }

        //** Rename post types. Do special stuff for post and page since they are built in, and Menu is hardcoded for some reason. */
        if ( $flawless[ 'post_types' ][ $type ][ 'name' ] != $data->labels->name || $flawless[ 'post_types' ][ $type ][ 'flawless_post_type' ] == 'true' ) {

          if ( $flawless[ 'post_types' ][ $type ][ 'name' ] != $data->labels->name ) {
            Log::add( sprintf( __( 'Changing labels for post type: %1s, from %2s to %3s', 'flawless' ), $type, $data->labels->name, $flawless[ 'post_types' ][ $type ][ 'name' ] ) );
          }

          $original_labels = ( !empty( $wp_post_types[ $type ]->labels ) ? (array) $wp_post_types[ $type ]->labels : array() );

          //** Update Post Type Labels */
          if ( empty( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ) {
            $flawless[ 'post_types' ][ $type ][ 'singular_name' ] = self::depluralize( $flawless[ 'post_types' ][ $type ][ 'name' ] );
          }

          $wp_post_types[ $type ]->labels = ( object ) array_merge( $original_labels, array(
            'name' => $flawless[ 'post_types' ][ $type ][ 'name' ], /* plural */
            'singular_name' => ucfirst( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'add_new_item' => sprintf( __( 'Add New %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'new_item' => sprintf( __( 'New %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'edit_item' => sprintf( __( 'Edit %1s', 'flawless' ), ucfirst( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ),
            'search_items' => sprintf( __( 'Search %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'name' ] ),
            'view_item' => sprintf( __( 'View %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'search_items' => sprintf( __( 'Search %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'name' ] ),
            'not_found' => sprintf( __( 'No %1s found.', 'flawless' ), strtolower( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ),
            'not_found_in_trash' => sprintf( __( 'No %1s found in trash.', 'flawless' ), strtolower( $flawless[ 'post_types' ][ $type ][ 'name' ] ) )
          ) );

          switch ( $type ) {
            case 'post':
              add_action( 'admin_menu', create_function( '', ' global $menu, $submenu, $flawless; $menu[5][0] = $flawless["post_types"]["post"]["name"]; $submenu["edit.php"][5][0] = "All " . $flawless["post_types"]["post"]["name"];  ' ) );
              break;
            case 'page':
              add_action( 'admin_menu', create_function( '', ' global $menu, $submenu, $flawless; $menu[20][0] = $flawless["post_types"]["page"]["name"]; $submenu["edit.php?post_type=page"][5][0] = "All " . $flawless["post_types"]["page"]["name"];  ' ) );
              break;
          }

        }

        //** If this post type can have an archive, we determine the URL */
        //** @todo This nees work, we are guessing that the permalink will be top level, need to check other factors */
        if ( $wp_post_types[ $type ]->has_archive ) {

          add_filter( 'nav_menu_items_' . $type, array( 'Flawless', 'add_archive_checkbox' ), null, 3 );

          $flawless[ 'post_types' ][ $type ][ 'archive_url' ] = get_bloginfo( 'url' ) . '/' . $type . '/';

        }

        //** Disable post type, and do work-around for built-in types since they are hardcoded into menu.*/
        if ( $flawless[ 'post_types' ][ $type ][ 'disabled' ] == 'true' ) {
          switch ( $type ) {
            case 'post':
              add_action( 'admin_menu', create_function( '', 'global $menu; unset( $menu[5] );' ) );
              break;
            case 'page':
              add_action( 'admin_menu', create_function( '', 'global $menu; unset( $menu[20] );' ) );
              break;
          }
          unset( $wp_post_types[ $type ] );
        }

      }

      return $flawless;

    }

    /**
     * Get default LESS variables from a file. These are later overwritten by theme settings.
     *
     * @todo Include a way of grouping variables together based on the CSS comments. - potanin@UD 6/11/12
     * @since 0.0.6
     * @author potanin@UD
     */
    static function parse_css_option_defaults( $flawless, $file = 'variables.less' ) {

      $flawless[ 'css_options' ] = array();

      if ( $_variables_path = Flawless::load( $file, 'less', array( 'return' => 'path' ) ) ) {
        $_content = file_get_contents( $_variables_path );
        $_lines = explode( "\n", $_content );
      }

      function de_camel( $str ) {
        $str[ 0 ] = strtolower( str_replace( '@', '', $str[ 0 ] ) );
        return ucwords( trim( preg_replace( '/([A-Z])/e', "' ' . strtolower('\\1')", $str ) ) );
      }

      foreach ( (array) $_lines as $line => $_line_string ) {

        if ( strpos( trim( $_line_string ), '@' ) === 0 ) {

          @list( $name, $value ) = (array) explode( ':', $_line_string );
          @list( $value, $description ) = explode( '//', $value );

          $name = str_replace( '@', '', $name );

          switch ( true ) {
            case strpos( $name, 'FontFamily' ):
              $type = 'font';
              break;
            case strpos( $name, 'fluidGrid' ):
              $type = 'percentage';
              break;
            case strpos( $name, 'Width' ):
              $type = 'pixels';
              break;
            case strpos( $name, 'Text' ):
            case strpos( $name, 'Color' ):
            case strpos( $name, 'Border' ):
            case strpos( $name, 'Background' ):
              $type = 'color';
              break;
            case strpos( $name, 'zindex' ):
              $type = 'hidden';
              break;
            case strpos( $name, 'Path' ):
              $type = 'url';
              break;
          }

          $flawless[ 'css_options' ][ $name ] = array_filter( array(
            'label' => str_replace( 'Btn', __( 'Button', 'flawless' ), de_camel( $name ) ),
            'value' => str_replace( ';', '', trim( $value ) ),
            'name' => trim( $name ),
            'type' => $type ? $type : false,
            'description' => trim( $description )
          ) );

        }

      }

      //$flawless[ 'css_options' ][ 'fluidGridColumnWidth' ] =

      //die( '<pre>' . print_r( $flawless[ 'css_options' ] , true ) . '</pre>' );

      $flawless[ 'css_options' ] = apply_filters( 'flawless::css_options', array_filter( $flawless[ 'css_options' ] ) );

      Log::add( sprintf( __( 'Flawless::parse_css_option_defaults() completed i n %2s seconds', 'flawless' ), timer_stop() ) );

      return $flawless;

    }

    /**
     * Setup theme features using the WordPress API as much as possible.
     *
     * @todo Should have some support for bootstrap content styles. - potanin@UD 6/10/12
     * @updated 0.0.6
     * @since 0.0.2
     */
    static function setup_theme_features( $flawless ) {
      global $wpdb;

      //** Load styles to be used by editor */
      add_editor_style( array(
        'ux/styles/flawless-content.css',
        'ux/styles/editor-style.css'
      ) );

      if ( $flawless[ 'color_scheme' ] ) {
        $flawless[ 'color_scheme_data' ] = Flawless::get_color_schemes( $flawless[ 'color_scheme' ] );
      }

      $flawless[ 'current_theme_options' ] = array_merge( (array) $flawless[ 'theme_data' ], (array) $flawless[ 'child_theme_data' ], (array) $flawless[ 'color_scheme_data' ] );

      if ( !empty( $flawless[ 'current_theme_options' ][ 'Google Fonts' ] ) ) {
        $flawless[ 'current_theme_options' ][ 'Google Fonts' ] = Flawless::trim_array( explode( ', ', trim( $flawless[ 'current_theme_options' ][ 'Google Fonts' ] ) ) );
      }

      if ( !empty( $flawless[ 'current_theme_options' ][ 'Supported Features' ] ) ) {
        $flawless[ 'current_theme_options' ][ 'Supported Features' ] = Flawless::trim_array( explode( ', ', trim( $flawless[ 'current_theme_options' ][ 'Supported Features' ] ) ) );
      }

      define( 'HEADER_TEXTCOLOR', '000' );
      define( 'HEADER_IMAGE', apply_filters( 'flawless::header_image', '' ) );
      define( 'HEADER_IMAGE_WIDTH', apply_filters( 'flawless::header_image_width', $flawless[ 'header_image_width' ] ? $flawless[ 'header_image_width' ] : 1090 ) );
      define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'flawless::header_image_height', $flawless[ 'header_image_height' ] ? $flawless[ 'header_image_height' ] : 314 ) );
      add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

      //** All Available Theme Features */
      $flawless[ 'available_theme_features' ][ 'custom-skins' ] = true;
      $flawless[ 'available_theme_features' ][ 'post-thumbnails' ] = true;
      $flawless[ 'available_theme_features' ][ 'custom-background' ] = true;
      $flawless[ 'available_theme_features' ][ 'custom-header' ] = true;
      $flawless[ 'available_theme_features' ][ 'automatic-feed-links' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-dropdowns' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-logo' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-navbar' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-search' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-text' ] = true;
      $flawless[ 'available_theme_features' ][ 'mobile-navbar' ] = true;
      $flawless[ 'available_theme_features' ][ 'footer-copyright' ] = true;
      $flawless[ 'available_theme_features' ][ 'extended-taxonomies' ] = true;
      $flawless[ 'available_theme_features' ][ 'term-meta' ] = true;
      $flawless[ 'available_theme_features' ] = apply_filters( 'flawless::available_theme_features', $flawless[ 'available_theme_features' ] );

      //** Load all Available Theme featurse */
      foreach ( (array) $flawless[ 'available_theme_features' ] as $feature => $always_true ) {
        add_theme_support( $feature );
      }

      //** Remove any explicitly disabled Features */
      foreach ( (array) $flawless[ 'disabled_theme_features' ] as $feature => $not_false ) {

        if ( $not_false !== 'false' ) {
          return;
        }

        remove_theme_support( $feature );

        if ( in_array( $feature, array( 'custom-background', 'custom-header', 'editor-style', 'widgets', 'menus' ) ) ) {

          switch ( $feature ) {

            case 'custom-background':
              remove_custom_background();
              break;

            case 'custom-header':
              remove_custom_image_header();
              break;

          }

        }

      }

      if ( current_theme_supports( 'term-meta' ) ) {
        $wpdb->taxonomymeta = $wpdb->prefix . 'taxonomymeta';
      }

      do_action( 'flawless::setup_theme_features::after' );

      return $flawless;

    }

    /**
     * Loads core assets of the theme
     *
     * Loaded after theme_features have been configured.
     *
     * @since 0.0.2
     */
    static function load_core_assets() {
      global $flawless;

      // @todo Utilize via Flawless\Loader()
      $_classes = array(
        'classmap' => array(),
        'files' => array(),
        'namespaced' => array(
          'Composer' => array(),
          'UDX' => array( $flawless[ 'paths' ][ 'vendor' ] ),
          'Flawless' => array( $flawless[ 'paths' ][ 'vendor' ] . '/core' ),
        )
      );

      //** Load logo if set */
      if ( is_numeric( $flawless[ 'flawless_logo' ][ 'post_id' ] ) && $image_attributes = wp_get_attachment_image_src( $flawless[ 'flawless_logo' ][ 'post_id' ], 'full' ) ) {
        $flawless[ 'flawless_logo' ][ 'url' ] = $image_attributes[ 0 ];
        $flawless[ 'flawless_logo' ][ 'width' ] = $image_attributes[ 1 ];
        $flawless[ 'flawless_logo' ][ 'height' ] = $image_attributes[ 2 ];
      }

      // die( '<pre>' . print_r( $flawless, true ) . '</pre>' );

      foreach ( (array) $flawless[ 'asset_directories' ] as $path => $url ) {

        $path = $path . '/core';

        if ( !is_dir( $path ) || !$resource = opendir( $path ) ) {
          continue;
        }

        while ( false !== ( $file_name = readdir( $resource ) ) ) {

          if ( substr( strrchr( $file_name, '.' ), 1 ) != 'php' ) {
            continue;
          }

          $file_data = @get_file_data( $path . '/' . $file_name, $flawless[ 'default_header' ][ 'flawless_extra_assets' ], 'flawless_extra_assets' );

          $file_data[ 'Location' ] = 'theme_functions';
          $file_data[ 'File Name' ] = $file_name;

          $flawless[ 'core_assets' ][ $file_data[ 'Name' ] ] = $file_data;
          include_once( $path . '/' . $file_name );

        }

      }

      return $flawless;

    }

    /**
     * Loads extra function files.
     *
     * @since 0.0.2
     */
    static function load_extend_modules( $flawless ) {

      function load_file( $file_data ) {

        if ( empty( $file_data ) ) {
          return false;
        }

        foreach ( (array) apply_filters( 'flawless::required_extra_resource_file_data', array( 'Name', 'Version' ) ) as $req_field ) {
          if ( !in_array( $req_field, array_keys( (array) $file_data ) ) ) {
            return false;
          }
        }

        $file_data[ 'Location' ] = 'theme_functions';

        $flawless[ 'flawless_extra_assets' ][ $file_data[ 'Name' ] ] = $file_data;

        include_once( $file_data[ 'path' ] );

      }

      foreach ( (array) $flawless[ 'asset_directories' ] as $path => $url ) {

        $path = $path . '/core/extensions';

        if ( !is_dir( $path ) ) {
          continue;
        }

        if ( !$functions_resource = opendir( $path ) ) {
          continue;
        }

        while ( false !== ( $file_name = readdir( $functions_resource ) ) ) {

          if ( $file_name == '.' || $file_name == '..' ) {
            continue;
          }

          //** Check if directory includes a with the same name as directory, AND there is no filename in root */
          if ( is_dir( $path . '/' . $file_name ) && file_exists( $path . '/' . $file_name . '/' . $file_name . '.php' ) && !file_exists( $path . '/' . $file_name . '.php' ) ) {
            $file_data = array_filter( (array) @get_file_data( $path . '/' . $file_name . '/' . $file_name . '.php', $flawless[ 'default_header' ][ 'flawless_extra_assets' ], 'flawless_extra_assets' ) );
            $file_data[ 'path' ] = $path . '/' . $file_name . '/' . $file_name . '.php';
            $file_data[ 'file_name' ] = $file_name . '.php';
            load_file( $file_data );
            continue;
          }

          if ( substr( strrchr( $file_name, '.' ), 1 ) != 'php' ) {
            continue;
          }

          $file_data = array_filter( (array) @get_file_data( $path . '/' . $file_name, $flawless[ 'default_header' ][ 'flawless_extra_assets' ], 'flawless_extra_assets' ) );

          $file_data[ 'file_name' ] = $file_name;
          $file_data[ 'path' ] = $path . '/' . $file_name;

          load_file( $file_data );

        }

      }

      return $flawless;

    }

    /**
     * Front-end script loading
     *
     * Loads all local and remote assets, checks conditionally loaded assets, etc.
     * Modifies body class based on loaded assets.
     *
     * @filter wp_enqueue_scripts ( 100 )
     * @since 0.0.2
     * @todo Scripts should be registered here, but enqueved at a differnet level. - potanin@UD
     */
    static function wp_enqueue_scripts( $args = '' ) {
      global $flawless, $wp_query, $is_IE, $_wp_theme_features;

      //** Do not load these styles if we are on admin side or the WP login page */
      if ( strpos( $_SERVER[ 'SCRIPT_NAME' ], 'wp-login.php' ) ) {
        return;
      }

      do_action( 'flawless::wp_enqueue_scripts' );

      do_action( 'flawless::extra_local_assets' );

      //** Load any existing assets for active plugins */
      foreach ( apply_filters( 'flawless::active_plugins', (array) Flawless::get_active_plugins() ) as $plugin ) {

        //** Get a plugin name slug */
        $plugin = dirname( plugin_basename( trim( $plugin ) ) );

        //** Look for plugin-specific scripts and load them */
        foreach ( (array) $flawless[ 'asset_directories' ] as $this_directory => $this_url ) {
          if ( file_exists( $this_directory . '/assets/js/' . $plugin . '.js' ) ) {
            $asset_url = apply_filters( 'flawless-asset-url', $this_url . '/assets/js/' . $plugin . '.js', $plugin );
            wp_enqueue_script( 'flawless-asset-' . $plugin, $asset_url, array(), Flawless_Version, true );
            Log::add( sprintf( __( 'JavaScript found for %1s plugin and loaded: %2s.', 'flawless' ), $plugin, $asset_url ) );
          }
        }
      }

      if ( wp_is_mobile() ) {
        wp_enqueue_script( 'jquery-touch-punch' );
      }

      if ( isset( $is_IE ) && $is_IE ) {
        wp_enqueue_script( 'html5shim', 'http://html5shim.googlecode.com/svn/trunk/html5.js' );
      }

      //** API Access */
      $flawless[ 'remote_assets' ] = apply_filters( 'flawless::remote_assets', (array) $flawless[ 'remote_assets' ] );

      //** Check and Load Remote Scripts */
      foreach ( (array) $flawless[ 'remote_assets' ][ 'script' ] as $asset_handle => $remote_asset ) {

        //** Remove prix if passed, we set them automatically */
        $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );

        if ( Flawless::can_get_asset( $flawless[ 'protocol' ] . $remote_asset, array( 'handle' => $asset_handle ) ) ) {
          wp_enqueue_script( $asset_handle, $flawless[ 'protocol' ] . $remote_asset, array(), Flawless_Version );

        } else {
          Log::add( sprintf( __( 'Could not load remote asset script: %1s.', 'flawless' ), $remote_asset ) );
        }
      }

      wp_enqueue_script( 'flawless-frontend' );

      if ( current_user_can( 'edit_theme_options' ) ) {
        wp_enqueue_script( 'customize-preview' );
      }

      do_action( 'flawless::wp_enqueue_scripts::end' );

    }

    /**
     * Enqueue Frontend Styles. Loaded late so plugin styles can be used for compiling and so theme CSS has more specificity.
     *
     * @todo Updates to /less/ files, other than bootstrap.less, are not monitored. - potanin@UD 6/11/12
     * @todo CSS added outside of here does not seem to honor dependencies - is there an issue with enqueueing CSS here? - potanin@UD 6/11/12
     * @filter wp_print_styles ( 100 )
     * @since 0.0.6
     */
    static function wp_print_styles( $args = '' ) {
      global $flawless, $wp_styles;

      $flawless[ 'remote_assets' ] = apply_filters( 'flawless::remote_assets', (array) $flawless[ 'remote_assets' ] );

      wp_enqueue_style( 'flawless-bootstrap-css', Flawless::load( 'bootstrap.less', 'less', array( 'return' => 'url' ) ), array(), '2.0.4', 'screen' );

      //** Enqueue core style.css (always). */
      if ( file_exists( TEMPLATEPATH . '/style.css' ) ) {
        wp_enqueue_style( 'flawless-style', get_bloginfo( 'template_url' ) . '/style.css', array( 'flawless-bootstrap-css' ), Flawless_Version, 'all' );
      }

      //** Enqueue remote styles if they are accessible */
      foreach ( (array) $flawless[ 'remote_assets' ][ 'css' ] as $asset_handle => $remote_asset ) {
        $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );
        if ( Flawless::can_get_asset( $flawless[ 'protocol' ] . $remote_asset, array( 'handle' => $asset_handle ) ) ) {
          wp_enqueue_style( $asset_handle, $flawless[ 'protocol' ] . $remote_asset, array(), Flawless_Version );
        }
      }

      //** Enqueue Google Fonts if specified by theme or skin */
      foreach ( (array) $flawless[ 'current_theme_options' ][ 'Google Fonts' ] as $google_font ) {
        wp_enqueue_style( 'google-font-' . sanitize_file_name( $google_font ), 'https://fonts.googleapis.com/css?family=' . str_replace( ' ', '+', ucfirst( trim( $google_font ) ) ), array( 'flawless-style' ) );
      }

      //** Enqueue Google Pretify */
      // https://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.css
      if ( $flawless[ 'enable_google_pretify' ] == 'true' ) {
        wp_enqueue_style( 'google-prettify', Flawless::load( 'prettify.css', 'css' ), array( 'flawless-style' ), Flawless_Version, 'screen' );
      }

      //** Enqueue Fancybox */
      if ( $flawless[ 'disable_fancybox' ] != 'true' ) {
        wp_enqueue_style( 'jquery-fancybox' );
      }

      //** Enqueue Maintanance CSS only when in Maintanance Mode */
      // if ( $wp_query->query_vars[ 'splash_screen' ] && Flawless::load( 'flawless-maintanance.css', 'css' ) ) {
      //  wp_enqueue_style( 'flawless-maintanance', Flawless::load( 'flawless-maintanance.css', 'css' ), array( 'flawless-style' ), Flawless_Version );
      // }

      //** Enqueue CSS for active plugins */
      foreach ( apply_filters( 'flawless::active_plugins', (array) Flawless::get_active_plugins() ) as $plugin ) {

        //** Get a plugin name slug */
        $plugin = dirname( plugin_basename( trim( $plugin ) ) );

        //** Look for plugin-specific scripts and load them */
        foreach ( (array) $flawless[ 'asset_directories' ] as $this_directory => $this_url ) {
          if ( file_exists( $this_directory . '/css/' . $plugin . '.css' ) ) {
            $asset_url = apply_filters( 'flawless-asset-url', $this_url . '/css/' . $plugin . '.css', $plugin );
            $file_data = get_file_data( $this_directory . '/css/' . $plugin . '.css', $flawless[ 'default_header' ][ 'flawless_style_assets' ], 'flawless_style_assets' );
            wp_enqueue_style( 'flawless-asset-' . $plugin, $asset_url, array( 'flawless-style' ), $file_data[ 'Version' ] ? $file_data[ 'Version' ] : Flawless_Version, $file_data[ 'Media' ] ? $file_data[ 'Media' ] : 'screen' );
            Log::add( sprintf( __( 'CSS found for %1s plugin and loaded: %2s.', 'flawless' ), $plugin, $asset_url ), 'info' );
          }
        }
      }

      //** Enqueue Content Styles - before child style.css and skins are loaded */
      if ( Flawless::load( 'content.css', 'css' ) ) {
        wp_enqueue_style( 'flawless-content', Flawless::load( 'content.css', 'css' ), array( 'flawless-style' ), Flawless_Version );
      }

      //** Enqueue Skin / Color Scheme CSS */
      if ( file_exists( $flawless[ 'loaded_color_scheme' ][ 'css_path' ] ) ) {
        wp_enqueue_style( 'flawless-colors', $flawless[ 'loaded_color_scheme' ][ 'css_url' ], array( 'flawless-style' ), Flawless_Version );
      }

      //** Enqueue child theme style.css */
      if ( is_child_theme() ) {
        wp_enqueue_style( 'flawless-child-style', get_bloginfo( 'stylesheet_directory' ) . '/style.css', array( 'flawless-style' ), Flawless_Version );
      }

      //** Check for and load conditional browser styles */
      foreach ( (array) apply_filters( 'flawless::conditional_asset_types', array( 'IE', 'lte IE 7', 'lte IE 8', 'IE 7', 'IE 8', 'IE 9', '!IE' ) ) as $type ) {

        //** Fix slug for URL - remove white space and lowercase */
        $url_slug = strtolower( str_replace( ' ', '-', $type ) );

        foreach ( (array) $flawless[ 'asset_directories' ] as $assets_path => $assets_url ) {

          if ( file_exists( $assets_path . "/css/conditional-{$url_slug}.css" ) ) {
            wp_enqueue_style( 'conditional-' . $url_slug, $assets_url . "/css/conditional-{$url_slug}.css", array( 'flawless-style' ), Flawless_Version );
            $wp_styles->add_data( 'conditional-' . $url_slug, 'conditional', $type );
          }

        }

      }

      do_action( 'flawless::wp_print_styles' );

      //** Analyze all Enqueued Styles for compiling */
      foreach ( (array) $wp_styles->queue as $key => $handle ) {

        $style_data = (array) $wp_styles->registered[ $handle ];

        $style_data[ 'file_name' ] = basename( $wp_styles->registered[ $handle ]->src );
        $style_data[ 'url' ] = $wp_styles->registered[ $handle ]->src;
        $style_data[ 'path' ] = untrailingslashit( apply_filters( 'flawless::root_path', ABSPATH ) ) . str_replace( untrailingslashit( home_url() ), '', $wp_styles->registered[ $handle ]->src );
        if ( !empty( $style_data[ 'args' ] ) && ( $style_data[ 'args' ] == 'print' ) ) {
          Log::add( sprintf( __( 'CSS Compiling: Excluding %1s because it is print only. ', 'flawless' ), $style_data[ 'file_name' ] ), 'info' );
          continue;
        }

        if ( defined( 'WP_PLUGIN_DIR' ) && $flawless[ 'do_not_compile_plugin_css' ] == 'true' && strpos( $style_data[ 'path' ], Utility::fix_path( WP_PLUGIN_DIR ) ) !== false ) {
          continue;
        }

        //** Add file to complication array if it is local and accessible*/
        if ( file_exists( $style_data[ 'path' ] ) ) {
          $flawless[ '_compilable_styles' ][ $handle ] = array_merge( $style_data, array( 'modified' => filemtime( $style_data[ 'path' ] ), 'file_size' => filesize( $style_data[ 'path' ] ) ) );
          $_modified_times[ $style_data[ 'file_name' ] ] = filemtime( $style_data[ 'path' ] );
        } else {

        }

      }

      if ( empty( $flawless[ '_compilable_styles' ] ) ) {
        Log::add( sprintf( __( 'CSS Compiling: No compilable styles were detected. ', 'flawless' ), $style_data[ 'file_name' ] ), 'error' );
      }

      //** If compiled CSS does not exist or is outdated, we re-generate */
      if ( !file_exists( $flawless[ '_bootstrap_compiled_path' ] ) ) {
        $_update_reason = 'initial';
      }

      if ( is_array( $_modified_times ) && !$_update_reason && file_exists( $flawless[ '_bootstrap_compiled_path' ] ) && ( filemtime( $flawless[ '_bootstrap_compiled_path' ] ) < max( (array) $_modified_times ) ) ) {
        $_update_reason = array_search( max( (array) $_modified_times ), $_modified_times );
      }

      if ( !$_update_reason && get_option( 'flawless::compiled_css_files' ) == '' ) {
        $_update_reason = 'system_trigger';
      }

      if ( $_update_reason ) {

        /*
       *
      // If compiled, enqueue the compiled CSS and remove the compiled styles
      if ( !is_wp_error( $_css_is_compiled = Flawless_LESS::build_compiled_css( $flawless[ '_compilable_styles' ] ) ) ) {

        if ( $_update_reason == 'initial' ) {
          flawless_add_notice( sprintf( __( 'Compiled CSS file has been successfully generated.', 'flawless' ), '#flawless_action#disable_notice=compiled_css_generation', 'hide' ));
        } elseif ( $_update_reason == 'system_trigger' ) {
          flawless_add_notice( sprintf( __( 'Compiled CSS file has been successfully generated, triggered by system.', 'flawless' ), '#flawless_action#disable_notice=compiled_css_generation', 'hide' ));
        } elseif ( !empty( $_update_reason ) ) {
          flawless_add_notice( sprintf( __( 'Compiled CSS automatically updated due to (%1s) having a more recent modified date than the compiled CSS.', 'flawless' ), array_search( max( (array) $_modified_times ), $_modified_times ), 'hide' ));
        }

      } else {

        if ( $flawless[ 'developer_mode' ] == 'true' ) {
          wp_die( '<h2>' . __( 'CSS Compile Error', 'flawless' ) . '</h2> ' . $_css_is_compiled->get_error_message(), '' );
        }

        Log::add( sprintf( __( 'CSS Compiling Error: %1s.', 'flawless' ), $_css_is_compiled->get_error_message() ? $_css_is_compiled->get_error_message() : __( 'Unknown Error.' ) ), 'error' );
      }

      */

      } else {
        Log::add( sprintf( __( 'CSS Compiling: Compiled file is up to date. ', 'flawless' ) ), 'info' );
      }

      //** We don't Enqueue this until now to exclude it from compiling */
      // wp_enqueue_style( 'flawless-compiled-css', $flawless[ 'developer_mode' ] == 'true' ? $flawless[ '_bootstrap_compiled_url' ] : $flawless[ '_bootstrap_compiled_minified_url' ], array(), Flawless_Version, 'screen' );

      foreach ( (array) $flawless[ '_compilable_styles' ] as $handle => $style_data ) {
        wp_dequeue_style( $handle );
      }

      $flawless_header_css = array();

      if ( current_theme_supports( 'custom-background' ) && get_header_image() ) {
        $flawless_header_css[ ] = ' .background_header_image { background-image: url( ' . get_header_image() . ' );  height: 100%; max-height: ' . HEADER_IMAGE_HEIGHT . 'px; }';
      }

      if ( $flawless[ 'layout_width' ] ) {
        $flawless_header_css[ ] = 'body div.container { max-width: ' . $flawless[ 'layout_width' ] . 'px; }';
      }

      //** Included fixed image sizes for faster rendering, and masonry support */
      foreach ( (array) self::image_sizes() as $size => $data ) {
        $flawless_header_css[ ] = '.gallery .gallery-item img.attachment-' . $size . ' { width: ' . $data[ 'width' ] . 'px; }';
        $flawless_header_css[ ] = 'img.fixed_size.attachment-' . $size . ' { width: ' . $data[ 'width' ] . 'px; }';
      }

      if ( is_array( $flawless_header_css ) ) {
        wp_add_inline_style( 'flawless_header_css', implode( '', (array) apply_filters( 'flawless::header_css', $flawless_header_css, $flawless ) ) );
      }

    }

    /**
     * Returns image sizes for a passed image size slug
     *
     * @method image_sizes
     * @version 0.1.1
     * @source UD_Functions
     * @since 0.5.4
     * @returns array keys: 'width' and 'height' if image type sizes found.
     */
    static function image_sizes( $type = false, $args = '' ) {
      global $_wp_additional_image_sizes;

      $image_sizes = (array) $_wp_additional_image_sizes;

      $image_sizes[ 'thumbnail' ] = array(
        'width' => intval( get_option( 'thumbnail_size_w' ) ),
        'height' => intval( get_option( 'thumbnail_size_h' ) )
      );

      $image_sizes[ 'medium' ] = array(
        'width' => intval( get_option( 'medium_size_w' ) ),
        'height' => intval( get_option( 'medium_size_h' ) )
      );

      $image_sizes[ 'large' ] = array(
        'width' => intval( get_option( 'large_size_w' ) ),
        'height' => intval( get_option( 'large_size_h' ) )
      );

      foreach ( (array) $image_sizes as $size => $data ) {
        $image_sizes[ $size ] = array_filter( (array) $data );
      }

      return array_filter( (array) $image_sizes );

    }

    /**
     * Return array of active plugins for current instance
     *
     * Improvement over wp_get_active_and_valid_plugins() which doesn't return any plugins when in MS
     *
     * @since 0.0.2
     */
    static function get_active_plugins() {

      $mu_plugins = (array) wp_get_mu_plugins();
      $regular_plugins = (array) wp_get_active_and_valid_plugins();

      if ( is_multisite() ) {
        $network_plugins = (array) wp_get_active_network_plugins();
      } else {
        $network_plugins = array();
      }

      return array_merge( $regular_plugins, $mu_plugins, $network_plugins );

    }

    /**
     * Load global vars for header template part.
     *
     * @todo Not sure if this is necessary - is there a reason for the flawless_header_links filter to be applied here? - potanin@UD
     * @since 0.0.2
     */
    static function get_template_part_header( $slug, $name ) {
      global $flawless, $wp_query;

      //** $flawless_header_links from filter which was set by different sections that will be in header drpdowns */
      $flawless[ 'header_links' ] = apply_filters( 'flawless_header_links', false );

      return $current;

    }

    /**
     * Adds Inline Cropping capability to an image.
     *
     * @todo Finish by initiating scripts when triggered. Right now causes a JS error because wp_image_editor() expects imageEdit() to already be loaded.  - potanin@UD
     * @since 0.3.4
     */
    static function inline_crop( $post_id ) {

      wp_enqueue_script( 'image-edit' );
      wp_enqueue_script( 'jcrop' );

      wp_enqueue_style( 'jcrop' );
      wp_enqueue_style( 'imgareaselect' );

      include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );
      ?>
      <script type="text/javascript"> var imageEdit = {
          init: function () {
            jQuery( document ).ready( function () {
              imageEdit.init();
            } );
          }
        };</script>
      <?php

      echo wp_image_editor( $post_id );

    }

    /**
     * Add "Theme Options" link to admin bar.
     *
     *
     * @since 0.0.3
     */
    static function admin_bar_menu( $wp_admin_bar ) {

      if ( current_user_can( 'switch_themes' ) && current_user_can( 'edit_theme_options' ) ) {

        $wp_admin_bar->add_menu( array(
          'parent' => 'appearance',
          'id' => 'theme-options',
          'title' => __( 'Theme Settings', 'flawless' ),
          'href' => Flawless_Admin_URL
        ) );

      }

    }

    /**
     * Frontend AJAX Handler.
     *
     * @since 0.6.0
     */
    static function frontend_ajax_handler() {
      global $flawless, $wpdb;

      nocache_headers();

      $return = array( 'success' => false );

      ob_start();

      switch ( $_REQUEST[ 'the_action' ] ) {

        /**
         *
         *
         * @todo Add  get_option('require_name_email') check / support.
         */
        case 'comment_submit':

          parse_str( $_POST[ 'form_data' ], $form_data );

          $args = wp_parse_args( $form_data, array(
            'comment_post_ID' => 0,
            'author' => null,
            'email' => null,
            'url' => null,
            'comment' => null,
            'comment_parent' => 0
          ) );

          foreach ( (array) $args as $key => $value ) {
            $args[ $key ] = trim( $value );
          }

          $args[ 'author' ] = strip_tags( $args[ 'author' ] );

          $post = get_post( $args[ 'comment_post_ID' ] );
          $status = get_post_status( $post );
          $status_obj = get_post_status_object( $status );

          if ( empty( $args[ 'comment' ] ) ) {
            $return[ 'message' ] = __( 'Please enter a comment.', 'flawless' );
            break;
          }

          if ( empty( $post->comment_status ) || !comments_open( $args[ 'comment_post_ID' ] ) ) {
            do_action( 'comment_id_not_found', $args[ 'comment_post_ID' ] );
            $return[ 'message' ] = __( 'Sorry, comments are closed for this item.' );
            break;
          } elseif ( 'trash' == $status ) {
            do_action( 'comment_on_trash', $args[ 'comment_post_ID' ] );
            break;
          } elseif ( !$status_obj->public && !$status_obj->private ) {
            do_action( 'comment_on_draft', $args[ 'comment_post_ID' ] );
            break;
          } elseif ( post_password_required( $args[ 'comment_post_ID' ] ) ) {
            do_action( 'comment_on_password_protected', $args[ 'comment_post_ID' ] );
            break;
          } else {
            do_action( 'pre_comment_on_post', $args[ 'comment_post_ID' ] );
          }

          $user = wp_get_current_user();

          if ( $user->ID ) {

            $args[ 'user_ID' ] = $user->ID;
            $args[ 'email' ] = $user->data->user_email;
            $args[ 'author' ] = $user->data->display_name;

          } else {

            if ( get_option( 'comment_registration' ) || 'private' == $status ) {
              $return[ 'message' ] = __( 'Sorry, you must be logged in to post a comment.' );
              break;
            }

          }

          if ( !is_email( $args[ 'email' ] ) ) {
            $return[ 'message' ] = __( 'Please enter a valid email address.' );
            break;
          }

          $comment_id = wp_new_comment( array(
            'comment_post_ID' => $args[ 'comment_post_ID' ],
            'comment_author' => $args[ 'author' ],
            'comment_author_email' => $args[ 'email' ],
            'comment_author_url' => $args[ 'url' ],
            'comment_content' => $args[ 'comment' ],
            'comment_parent' => $args[ 'comment_parent' ],
            'user_id' => $args[ 'user_id' ],
            'comment_type' => '',
          ) );

          $comment = get_comment( $comment_id );

          if ( $comment ) {

            if ( !$user->ID ) {
              $comment_cookie_lifetime = apply_filters( 'comment_cookie_lifetime', 30000000 );
              setcookie( 'comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
              setcookie( 'comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
              setcookie( 'comment_author_url_' . COOKIEHASH, esc_url( $comment->comment_author_url ), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
            }

            $return[ 'success' ] = true;

            $comments = get_comments( array(
              'post_id' => $args[ 'comment_post_ID' ],
              'status' => 'approve',
              'order' => 'ASC'
            ) );

            if ( $user->ID ) {
              $comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt", $args[ 'comment_post_ID' ], $args[ 'user_ID' ] ) );
            } else if ( empty( $args[ 'comment_author' ] ) ) {
              $comments = get_comments( array( 'post_id' => $args[ 'comment_post_ID' ], 'status' => 'approve', 'order' => 'ASC' ) );
            } else {
              $comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt", $args[ 'comment_post_ID' ], wp_specialchars_decode( $args[ 'comment_author' ], ENT_QUOTES ), $args[ 'comment_author_email' ] ) );
            }

            $return[ 'comment_list' ] = $comments;
            $return[ 'comment_count' ] = count( $comments );

            ob_start();
            wp_list_comments( array( 'callback' => 'flawless_comment' ), $comments );
            $comment_html = ob_get_contents();
            ob_end_clean();

            $return[ 'comment_html' ] = $comment_html;

          }

          break;

      }

      $output = ob_get_contents();
      ob_end_clean();

      $return[ 'output' ] = $output;

      return $return;

    }

    /**
     * Admin Flawless-specific ajax actions.
     *
     * Called when AJAX call with action:flawless_action is used.
     * Must return array, which is automatically converted into JSON.
     *
     * @todo May want to update nonce verification to something more impressive since used on back and front-end calls.
     * @since 0.0.2
     */
    static function ajax_actions() {
      global $flawless, $wpdb;

      nocache_headers();

      if ( !current_user_can( 'edit_theme_options' ) ) {
        die( '0' );
      }

      $flawless = stripslashes_deep( get_option( 'flawless_settings' ) );

      switch ( $_REQUEST[ 'the_action' ] ) {

        case 'delete_logo':

          //** Delete old logo */
          if ( is_numeric( $flawless[ 'flawless_logo' ][ 'post_id' ] ) ) {
            wp_delete_attachment( $flawless[ 'flawless_logo' ][ 'post_id' ], true );
            unset( $flawless[ 'flawless_logo' ] );
          } elseif ( !empty( $flawless[ 'flawless_logo' ][ 'url' ] ) ) {
            unset( $flawless[ 'flawless_logo' ] );
          }

          update_option( 'flawless_settings', $flawless );
          $return = array( 'success' => 'true' );

          break;

        case 'clean_up_revisions':

          if ( current_user_can( 'delete_posts' ) ) {

            $args[ 'max_revisions' ] = intval( defined( 'WP_POST_REVISIONS' ) ? WP_POST_REVISIONS : 3 );

            $revisions_over_limit = $wpdb->get_results( "SELECT post_parent, ID as revision_id, ( SELECT count(ID) FROM {$wpdb->posts} pp WHERE pp.post_parent = p.post_parent ) as revisions, ( SELECT post_date FROM {$wpdb->posts} last WHERE last.post_parent = p.post_parent ORDER BY last.post_date DESC LIMIT {$args[max_revisions]},1) as date_cutoff FROM {$wpdb->posts} p WHERE post_type = 'revision' AND post_date <= ( SELECT post_date FROM {$wpdb->posts} last WHERE last.post_parent = p.post_parent ORDER BY last.post_date DESC LIMIT " . ( $args[ max_revisions ] + 1 ) . ",1)" );

            $args[ 'revisions_over_limit' ] = count( $revisions_over_limit );

            foreach ( (array) $revisions_over_limit as $post_row ) {
              $args[ 'deleted' ][ ] = !is_wp_error( wp_delete_post_revision( $post_row->revision_id ) ) ? $post_row->revision_id : '';
            }

            $args[ 'deleted' ] = count( (array) array_filter( (array) $args[ 'deleted' ] ) );

            if ( $args[ 'deleted' ] ) {
              $wpdb->query( "OPTIMIZE TABLE {$wpdb->posts}" );
              $wpdb->query( "OPTIMIZE TABLE {$wpdb->postmeta}" );

              $return = array( 'success' => 'true', 'message' => sprintf( __( 'Success! We removed %1s post revisions and optimized your MySQL tables. ', 'flawless' ), $args[ 'deleted' ], $args[ 'max_revisions' ] ) );
            } else {
              $return = array( 'success' => 'false', 'message' => __( 'Does not look like there were any revisions to remove.', 'flawless' ) );
            }

          }

          break;

        case 'delete_all_settings':

          delete_option( 'flawless_settings' );

          $return = array(
            'success' => 'true',
            'message' => __( 'All Flawless settings deleted.', 'flawless' )
          );
          break;

        case 'show_permalink_structure':

          $return = array(
            'success' => 'true',
            'message' => '<pre class="flawless_class_pre">' . print_r( get_option( 'rewrite_rules' ), true ) . '</pre>'
          );

          break;

        case 'show_flawless_configuration':

          $return = array(
            'success' => 'true',
            'message' => '<pre class="flawless_class_pre">' . print_r( $flawless, true ) . '</pre>'
          );

          break;

        default:
          $return = apply_filters( 'flawless_ajax_action', array( 'success' => $false ), $flawless );
          break;

      }

      if ( empty( $return ) ) {

        $return = array(
          'success' => false,
          'message' => __( 'No action found.', 'flawless' )
        );

      }

      return $return;

    }

    /**
     * {need description}
     *
     *
     * @since 0.0.2
     *
     */
    static function add_archive_checkbox( $posts, $args, $post_type ) {
      global $_nav_menu_placeholder, $wp_rewrite, $flawless;

      $_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval( $_nav_menu_placeholder ) - 1 : -1;

      $archive_slug = $post_type[ 'args' ]->has_archive === true ? $post_type[ 'args' ]->rewrite[ 'slug' ] : $post_type[ 'args' ]->has_archive;

      if ( $post_type[ 'args' ]->rewrite[ 'with_front' ] ) {
        $archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
      } else {
        $archive_slug = $wp_rewrite->root . $archive_slug;
      }

      array_unshift( $posts, ( object ) array(
        'ID' => 0,
        '_add_to_top' => true,
        'object_id' => $_nav_menu_placeholder,
        'post_content' => '',
        'post_excerpt' => '',
        'custom_thing' => 'hola',
        'post_title' => sprintf( __( '%1s Archive Root', 'flawless' ), $post_type[ 'args' ]->labels->all_items ),
        'post_type' => 'nav_menu_item',
        'type' => 'custom',
        'url' => site_url( $archive_slug ),
      ) );

      return $posts;

    }

    /**
     * {need description}
     *
     * Adds a special class to menus that display descriptions for the individual menu items
     *
     * @since 0.0.2
     *
     */
    static function wp_nav_menu_args( $args ) {
      global $flawless;

      if ( $flawless[ 'menus' ][ $args[ 'theme_location' ] ][ 'show_descriptions' ] == 'true' ) {
        $args[ 'menu_class' ] = $args[ 'menu_class' ] . ' menu_items_have_descriptions';
      }

      return $args;

    }

    /**
     * {need description}
     *
     * @since 0.0.2
     *
     */
    static function walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
      global $flawless;

      //** Do not add description if this is not a top level menu item */
      if ( $item->menu_item_parent || $flawless[ 'menus' ][ $args->theme_location ][ 'show_descriptions' ] != 'true' ) {
        return $item_output;
      }

      $char_limit = 50;

      $description = substr( $item->description, 0, $char_limit ) . ( strlen( $item->description ) > $char_limit ? '...' : '' );

      $trigger = '</a>' . $args->after;

      //** Inject description HTML by identifying the $args->after */
      $item_output = str_replace( $trigger, $trigger . ( $description ? '<span class="menu_item_description">' . $description . '</span>' : '' ), $item_output );

      return $item_output;

    }

    /**
     * Modified front-end menus and adds extra classes
     *
     * @todo Find way to inexpensively figure out if current item is last and add a class.
     * @since 0.0.2
     */
    static function nav_menu_css_class( $classes, $item, $args ) {
      global $post, $flawless, $wpdb;

      $total_items = $wpdb->get_var( "SELECT count FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE object_id = {$item->db_id} AND taxonomy = 'nav_menu' " );

      if ( !$item->menu_item_parent ) {
        $classes[ ] = 'top_level_item';
      } else {
        $classes[ ] = 'sub_menu_level_item';
      }

      if ( $item->menu_order == 1 ) {
        $classes[ ] = 'first';

      }
      if ( $item->menu_order == $total_items ) {
        $classes[ ] = 'last';
      }

      //** Check if the currently rendered item is a child of this link */
      if ( untrailingslashit( $item->url ) == untrailingslashit( $flawless[ 'post_types' ][ $post->post_type ][ 'archive_url' ] ) ) {

        $classes[ ] = 'current-page-ancestor current-menu-ancestor current-menu-parent current-page-parent current_page_parent flawless_ad_hoc_menu_parent';

        //** This menu item is an ad-hoc parent of something, we need to update parent elements as well */
        if ( $item->menu_item_parent ) {

        }

      }

      return $classes;

    }

    /**
     * Handle upgrading the theme. Only displayed to users who can Update Themes.
     *
     * @since 0.0.2
     */
    static function handle_upgrade() {
      global $wpdb;

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

      $installed_version = get_option( 'flawless_version' );

      //** If new install. */
      if ( empty( $installed_version ) ) {
        $redirect = add_query_arg( 'admin_splash_screen', 'welcome', Flawless_Admin_URL );
      }

      //** If upgrading from older version */
      if ( version_compare( Flawless_Core_Version, $installed_version, '>' ) ) {
        $redirect = add_query_arg( 'admin_splash_screen', 'updated', Flawless_Admin_URL );
      }

      if ( current_theme_supports( 'term-meta' ) && $wpdb->taxonomymeta ) {

        $sql = "CREATE TABLE {$wpdb->taxonomymeta} (
        meta_id bigint(20) unsigned NOT NULL auto_increment,
        taxonomy_id bigint(20) unsigned NOT NULL default '0',
        meta_key varchar(255) default NULL,
        meta_value longtext,
        PRIMARY KEY  (meta_id),
        KEY taxonomy_id (taxonomy_id),
        KEY meta_key (meta_key)
      ) $charset_collate;";

        dbDelta( $sql );

      }

      //** Run the update now in case we have a redirection */
      update_option( 'flawless_version', Flawless_Core_Version );

      if ( $redirect ) {
        die( wp_redirect( $redirect ) );
      }

    }

    /**
     * Primary function for handling front-end actions
     *
     * @filter template_redirect ( 0 )
     * @since 0.0.2
     */
    static function template_redirect() {
      global $wp_styles, $is_IE, $flawless, $wp_query, $wp_rewrite, $post;

      do_action( 'flawless::template_redirect' );

      add_action( 'wp_head', array( 'Flawless', 'wp_head' ) );

      add_filter( 'wp_nav_menu_args', array( 'Flawless', 'wp_nav_menu_args' ), 5 );
      add_filter( 'walker_nav_menu_start_el', array( 'Flawless', 'walker_nav_menu_start_el' ), 5, 4 );
      add_filter( 'nav_menu_css_class', array( 'Flawless', 'nav_menu_css_class' ), 5, 3 );

      add_filter( 'post_class', array( 'Flawless', 'post_class' ), 10, 3 );
      add_filter( 'wp_title', array( 'Flawless', 'wp_title' ), 10, 3 );

      //** Load global variables into the "header" template_part
      add_filter( 'get_template_part_header-element', array( 'Flawless', 'get_template_part_header' ), 10, 2 );
      add_filter( 'get_template_part_footer-element', array( 'Flawless', 'get_template_part_header' ), 10, 2 );

      //** Load extra options into Admin Bar ( in header ) */
      add_action( 'admin_bar_menu', array( 'Flawless', 'admin_bar_menu' ), 200 );

      //** Disable default Gallery shortcode styles */
      add_filter( 'use_default_gallery_style', create_function( '', ' return false; ' ) );

      if ( get_post_meta( $post->ID, 'must_be_logged_in', true ) == 'true' && !is_user_logged_in() ) {
        die( wp_redirect( home_url() ) );
      }

      $wp_query->query_vars[ 'flawless' ] = & $flawless;

      add_action( 'body_class', array( 'Flawless', 'body_class' ), 200, 2 );

      /**
       * Display attention grabbing image. (Option to enable does not currently exist, for testing only )
       *
       * @since 0.6.0
       */
      add_action( 'flawless_ui::above_header', function () {
        global $post;
        if ( has_post_thumbnail( $post->ID ) && get_post_meta( 'display_header_featured_image', true ) == 'true' && $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large-feature' ) ) {
          $html[ ] = '<div class="row-c4-1234 row row-fluid"><div class="span12 full-width c4-1234 cfct-block">';
          $html[ ] = '<div class="cfct-module cfct-module-hero"><img src="' . $image[ 0 ] . '" class="cfct-module-hero-image fixed_size attachment-large-feature"></div>';
          $html[ ] = '</div></div>';
          echo implode( '', $html );
        }
      } );

      //** Load a custom color scheme if set last, so it supercedes all others */
      if ( !empty( $flawless[ 'color_scheme' ] ) && Flawless::get_color_schemes( $flawless[ 'color_scheme' ] ) ) {
        $flawless[ 'loaded_color_scheme' ] = Flawless::get_color_schemes( $flawless[ 'color_scheme' ] );
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'flawless_have_skin';
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'flawless_' . str_replace( array( '.', '-', ' ' ), '_', $flawless[ 'color_scheme' ] );
      } else {
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'flawless_no_skin';
      }

      add_action( 'flawless::content_container_top', function () {
        flawless_primary_notice_container( '' );
      } );

      Log::add( 'Executed: Flawless::template_redirect();' );

    }

    /**
     * Determines if we have a Navbar, and if so, the type.
     *
     * Loads information into global variables, setups body classes, loads scripts, etc.
     * current_theme_supports() is ran within the function to give child themes the opportunity to remove support.
     *
     * @todo Navbar depth level of 1 is temporary - a callback must be added to render WP menus in TB format. - potanin@UD
     * @filter init ( 500 )
     * @author potanin@UD
     * @since 0.3.5
     */
    static function prepare_navbars() {
      global $flawless;

      if ( apply_filters( 'flawless::use_navbar', $flawless[ 'disabled_theme_features' ][ 'header-navbar' ] ) ) {
        return;
      }

      if ( current_theme_supports( 'header-navbar' ) ) {

        //** Disable WordPress Toolbar unless it is selected */
        if ( $flawless[ 'navbar' ][ 'type' ] != 'wordpress' ) {
          remove_action( 'init', '_wp_admin_bar_init' );
        }

      }

      /**
       * Bind to Template Redirect for rest of Navbar Loading since we need the $post object.
       *
       * @author potanin@UD
       * @since 0.0.6
       */
      add_action( 'template_redirect', function () {
        global $flawless;

        if ( current_theme_supports( 'header-navbar' ) && $flawless[ 'navbar' ][ 'type' ] != 'wordpress' ) {

          $flawless[ 'navbar' ][ 'html' ] = array();

          if ( wp_get_nav_menu_object( $flawless[ 'navbar' ][ 'type' ] ) ) {
            $flawless[ 'navbar' ][ 'html' ][ 'left' ] = wp_nav_menu( array(
              'menu' => $flawless[ 'navbar' ][ 'type' ],
              'menu_class' => 'nav',
              'fallback_cb' => false,
              'echo' => false,
              'container' => false,
              'depth' => 1
            ) );
          }

          $flawless[ 'navbar' ][ 'html' ] = apply_filters( 'flawless::navbar_html', $flawless[ 'navbar' ][ 'html' ] );

          if ( is_array( $flawless[ 'navbar' ][ 'html' ] ) ) {

            /**Place edit layout in the navbar*/
            if ( $flawless[ 'navbar' ][ 'show_editlayout' ] == 'true' ) {
              if ( current_user_can( 'manage_options' ) && !is_admin() ) {

                array_push( $flawless[ 'navbar' ][ 'html' ], '<li><a class="" href="' . get_edit_post_link() . '">' . __( 'Edit Page', 'flawless' ) . '</a></li>' );

                array_push( $flawless[ 'navbar' ][ 'html' ], '<li><a class="flawless_edit_layout hidden" href="#flawless_action">' . __( 'Edit Layout', 'flawless' ) . '</a></li>' );

              }
            }

            foreach ( $flawless[ 'navbar' ][ 'html' ] as $key => &$value ) {

              if ( empty( $value ) ) {
                unset( $flawless[ 'navbar' ][ 'html' ][ $key ] );
              }

              if ( is_array( $value ) ) {
                $value = implode( '', $value );
              }

              $class = $key == "right" ? "pull-right" : "";
              $value = "<div class=\"nav-collapse {$class}\"><ul class=\"nav\">{$value}</ul></div>";

            }
          }

          //** Clean up Navbar */
          $flawless[ 'navbar' ][ 'html' ] = array_filter( (array) $flawless[ 'navbar' ][ 'html' ] );

        }

        if ( current_theme_supports( 'mobile-navbar' ) ) {

          /* Mobile Navbar is limited to one depth level */
          if ( wp_get_nav_menu_object( $flawless[ 'mobile_navbar' ][ 'type' ] ) ) {
            $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ] = wp_nav_menu( array(
              'menu' => $flawless[ 'mobile_navbar' ][ 'type' ],
              'menu_class' => 'nav',
              'fallback_cb' => false,
              'echo' => false,
              'depth' => 1
            ) );
          }
          $flawless[ 'mobile_navbar' ][ 'html' ] = apply_filters( 'flawless::mobile_navbar_html', $flawless[ 'mobile_navbar' ][ 'html' ] );

          if ( is_array( $flawless[ 'mobile_navbar' ][ 'html' ] ) ) {
            foreach ( $flawless[ 'mobile_navbar' ][ 'html' ] as $key => &$value ) {

              if ( empty( $value ) ) {
                unset( $flawless[ 'mobile_navbar' ][ 'html' ][ $key ] );
              }

              if ( is_array( $value ) ) {
                $value = implode( '', $value );
              }

              $class = $key == 'right' ? 'pull-right' : '';
              $value = "<div class=\"nav-collapse nav-collapse-mobile {$class}\"><ul class=\"nav\">{$value}</ul></div>";

            }
          }

          $flawless[ 'mobile_navbar' ][ 'html' ] = array_filter( (array) $flawless[ 'mobile_navbar' ][ 'html' ] );
        }

        if ( !empty( $flawless[ 'navbar' ][ 'html' ] ) ) {
          $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have-navbar';
        } else {
          unset( $flawless[ 'navbar' ][ 'html' ] );
        }

        if ( !empty( $flawless[ 'mobile_navbar' ][ 'html' ] ) ) {
          $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have-mobile-navbar';
        } else {
          unset( $flawless[ 'mobile_navbar' ][ 'html' ] );
        }

        if ( $flawless[ 'navbar' ][ 'html' ] || $flawless[ 'mobile_navbar' ][ 'html' ] ) {
          add_action( 'header-navbar', array( 'Flawless', 'render_navbars' ) );
        }

      } );

    }

    /**
     * Not Used. Adds an item to the Navbar.
     *
     * Needs to be called before init ( 500 )
     *
     * @todo Finish function and update the way Navbar items are added. - potanin@UD 4/17/12
     * @since 0.5.0
     */
    static function add_to_navbar( $html, $args = false ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'order' => 100,
        'position' => 'left',
        'navbar' => array( 'navbar' )
      ) );

      foreach ( (array) $args[ 'navbar' ] as $navbar_type ) {
        $flawless[ $navbar_type ][ 'html' ] .= 'start' . $html . '|';
      }

    }

    /**
     * Load a template part into a template.
     *
     * Overrides UD_API::get_template_part() which passes an array of known template names.
     * Same as default get_template_part() but returned as a variable.
     *
     * @version 0.d6
     */
    static function get_template_part( $slug, $name = null ) {

      do_action( "get_template_part_{$slug}", $slug, $name );

      $templates = array();
      if ( isset( $name ) )
        $templates[ ] = "{$slug}-{$name}.php";

      $templates[ ] = "{$slug}.php";

      ob_start();
      locate_template( $templates, true, false );
      $return = ob_get_clean();

      if ( empty( $return ) ) {
        return false;
      }

      return $return;

    }

    /**
     * Renders the Navbar form the template part.
     *
     * @since 0.3.5
     */
    static function render_navbars( $args = false ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'echo' => true
      ) );

      //** Prepare for rendering as a string. */
      $flawless[ 'navbar' ][ 'html' ] = implode( '', (array) $flawless[ 'navbar' ][ 'html' ] );
      $flawless[ 'mobile_navbar' ][ 'html' ] = implode( '', (array) $flawless[ 'mobile_navbar' ][ 'html' ] );

      ob_start();
      get_template_part( 'header-navbar' );
      $html = ob_get_contents();
      ob_end_clean();

      if ( !$args[ 'echo' ] ) {
        return $html;
      }

      echo $html;

    }

    /**
     * Add all the body classes
     *
     * @since 0.2.5
     * @author potanin@UD
     */
    static function body_class( $classes, $class ) {
      global $flawless;

      //** Added classes to body */
      foreach ( (array) $flawless[ 'current_view' ][ 'body_classes' ] as $class ) {
        $classes[ ] = $class;
      }

      if ( $flawless[ 'visual_debug' ] == 'true' ) {
        $classes[ ] = 'flawless_visual_debug';
      }

      $classes = apply_filters( 'flawless::body_class', $classes, $class );

      return array_unique( $classes );

    }

    /**
     * Tweaks the default title. In most cases a specialty plugin will be used.
     *
     * @since 0.3.7
     */
    static function wp_title( $current_title, $sep, $seplocation ) {

      $title = array();

      if ( is_home() || is_front_page() ) {
        $title[ ] = get_bloginfo( 'name' );

        if ( get_bloginfo( 'description' ) ) {
          $title[ ] = get_bloginfo( 'description' );
        }

      } else {
        $title[ ] = $current_title;
        $title[ ] = get_bloginfo( 'name' );
      }

      return trim( implode( ' - ', $title ) );

    }

    /**
     * Filters a post permalink to replace the tag placeholder with the first
     * used term from the taxonomy in question.
     *
     * @source http://www.viper007bond.com/2011/10/07/code-snippet-helper-class-to-add-custom-taxonomy-to-post-permalinks/
     * @since 0.5.0
     */
    static function filter_post_link( $permalink, $post ) {
      global $flawless;

      foreach ( (array) $flawless[ 'taxonomies' ] as $taxonomy => $data ) {

        if ( false === strpos( $permalink, $data[ 'rewrite_tag' ] ) ) {
          continue;
        }

        $terms = get_the_terms( $post->ID, $taxonomy );
        if ( empty( $terms ) ) {
          $permalink = str_replace( $data[ 'rewrite_tag' ], $taxonomy, $permalink );
        } else {
          $first_term = array_shift( $terms );
          $permalink = str_replace( $data[ 'rewrite_tag' ], $first_term->slug, $permalink );
        }

      }

      return $permalink;

    }

    /**
     * Adds content-specific classes
     *
     */
    static function post_class( $classes ) {

      if ( has_post_thumbnail() ) {
        $classes[ ] = 'has-img';
      } else {
        $classes[ ] = 'has-not-img';
      }

      return $classes;

    }

    /**
     * Front-end Header Things
     *
     * @since 0.0.2
     */
    static function wp_head() {
      global $flawless, $is_iphone, $is_IE;

      Log::add( 'Executed: Flawless::wp_head();' );

      //** Check for and load favico.ico */
      if ( file_exists( untrailingslashit( get_stylesheet_directory() ) . '/favicon.ico' ) ) {
        $html[ ] = '<link rel="shortcut icon" href="' . get_bloginfo( 'stylesheet_directory' ) . '/favicon.ico" type="image/x-icon" />';
      };

      //** Load JS Config */
      $js_config[ 'ajax_url' ] = admin_url( 'admin-ajax.php' );
      $js_config[ 'message_submission' ] = __( 'Thank you for your message.', 'flawless' );
      $js_config[ 'header' ] = $flawless[ 'header' ] ? $flawless[ 'header' ] : array();
      $js_config[ 'location_name' ] = !empty( $flawless[ 'name' ] ) ? $flawless[ 'name' ] : __( 'Our location.', 'flawless' );
      $js_config[ 'remove_empty_widgets' ] = $flawless[ 'do_not_remove_empty_widgets' ] == "true" ? false : true;
      $js_config[ 'location_coords' ] = array(
        'latitude' => $flawless[ 'latitude' ],
        'longitude' => $flawless[ 'longitude' ],
      );

      if ( $is_iphone ) {
        $js_config[ 'is_iphone' ] = true;
      }

      if ( $is_IE ) {
        $js_config[ 'is_ie' ] = true;
      }

      $js_config[ 'options' ] = array(
        'background_header_image_fade_in' => false /** @todo Experimental, no option to enable */
      );

      if ( $flawless[ 'developer_mode' ] == 'true' ) {
        $js_config[ 'developer_mode' ] = true;
        $js_config[ 'console_log_options' ][ 'show_log' ] = $flawless[ 'console_log_options' ][ 'show_log' ] == 'true' ? true : false;
      };

      if ( current_user_can( 'manage_options' ) ) {
        $js_config[ 'is_admin' ] = true;
        $js_config[ 'nonce' ] = wp_create_nonce( 'flawless_action' );
      };

      $js_config = apply_filters( 'Flawless_js_config', $js_config );

      if ( is_array( $js_config ) ) {
        $html[ ] = '<script type="text/javascript">var flawless = jQuery.extend( true, jQuery.parseJSON( ' . json_encode( json_encode( $js_config ) ) . ' ), typeof flawless === "object" ? flawless : {}); </script>';
      };

      if ( is_array( $html ) ) {
        echo implode( "\n", $html );
      };

      echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>';

      if ( file_exists( untrailingslashit( get_stylesheet_directory() ) . '/apple-touch-icon.png' ) ) {
        echo '<link type="image/png" rel="apple-touch-icon" href="' . untrailingslashit( get_stylesheet_directory_uri() ) . '"/>';
      }

    }

    /**
     * Scans asset directories for available color schemes, or can be used to get information about a specific skin.
     *
     * Scans child theme first.
     *
     * @since 0.0.2
     */
    static function get_color_schemes( $requested_scheme = false ) {
      global $flawless;

      $files = wp_cache_get( 'color_schemes', 'flawless' );

      if ( !$files ) {

        //** Reverse so child theme gets scanned first */
        $skin_directories = apply_filters( 'flawless::skin_directories', array_reverse( $flawless[ 'asset_directories' ] ) );

        foreach ( (array) $skin_directories as $path => $url ) {

          if ( !is_dir( $path ) || !$resource = opendir( $path ) ) {
            continue;
          }

          while ( false !== ( $file = readdir( $resource ) ) ) {

            if ( $file == "." || $file == ".." || strpos( $file, 'skin-' ) !== 0 || substr( strrchr( $file, '.' ), 1 ) != 'css' ) {
              continue;
            }

            $file_data = array_filter( (array) @get_file_data( $path . '/' . $file, $flawless[ 'default_header' ][ 'themes' ], 'themes' ) );

            if ( empty( $file_data ) ) {
              continue;
            }

            $file_data[ 'css_path' ] = $path . '/' . $file;
            $file_data[ 'css_url' ] = $url . '/' . $file;

            $potential_thumbnails = array(
              str_replace( '.css', '.jpg', $file ),
              str_replace( '.css', '.png', $file )
            );

            if ( !empty( $file_data[ 'Thumbnail' ] ) ) {
              $potential_thumbnails[ ] = $file_data[ 'Thumbnail' ];
              array_reverse( $potential_thumbnails );
            }

            foreach ( (array) $potential_thumbnails as $thumbnail_filename ) {
              foreach ( (array) $skin_directories as $thumb_path => $thumb_url ) {
                if ( file_exists( trailingslashit( $thumb_path ) . '/' . $thumbnail_filename ) ) {
                  $file_data[ 'thumb_url' ] = $thumb_url . '/' . $thumbnail_filename;
                  break;
                }
              }
            }

            if ( !isset( $files[ $file ] ) ) {
              $files[ $file ] = array_filter( ( array) $file_data );
            }

          }
        }

        $files = array_filter( (array) $files );

      }

      wp_cache_set( 'color_schemes', $files, 'flawless' );

      if ( $requested_scheme && $files[ $requested_scheme ] ) {
        return $files[ $requested_scheme ];
      }

      if ( empty( $files ) ) {
        return false;
      }

      return $files;

    }

    /**
     * Adds a widget to a sidebar.
     *
     * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
     *
     * Example usage:
     * Flawless::add_widget_to_sidebar( 'global_property_search', 'text', array( 'title' => 'Automatically Added Widget', 'text' => 'This widget was added automatically' ));
     *
     * @todo Some might exist that adds widgets twice.
     * @todo Consider moving functionality to UD Class
     *
     * @since 0.0.2
     */
    static function add_widget_to_sidebar( $sidebar_id = false, $widget_id = false, $settings = array(), $args = '' ) {
      global $wp_registered_widget_updates, $wp_registered_widgets;

      extract( wp_parse_args( $args, array(
        'do_not_duplicate' => 'true'
      ) ), EXTR_SKIP );

      require_once( ABSPATH . 'wp-admin/includes/widgets.php' );

      do_action( 'load-widgets.php' );
      do_action( 'widgets.php' );
      do_action( 'sidebar_admin_setup' );

      //** Need some validation here */
      if ( !$sidebar_id ) {
        return false;
      }

      if ( !$widget_id ) {
        return false;
      }

      if ( empty( $settings ) ) {
        return false;
      }

      //** Load sidebars */
      $sidebars = wp_get_sidebars_widgets();

      //** Get widget ID */
      $widget_number = next_widget_id_number( $widget_id );

      if ( is_array( $sidebars[ $sidebar_id ] ) ) {
        foreach ( (array) $sidebars[ $sidebar_id ] as $this_sidebar_id => $sidebar_widgets ) {

          //** Check if this sidebar already has this widget */
          if ( strpos( $sidebar_widgets, $widget_id ) === false ) {
            continue;
          }

          $widget_exists = true;

        }
      }

      if ( $do_not_duplicate == 'true' && $widget_exists ) {
        return true;
      }

      foreach ( (array) $wp_registered_widget_updates as $name => $control ) {

        if ( $name == $widget_id ) {
          if ( !is_callable( $control[ 'callback' ] ) ) {
            continue;
          }

          ob_start();
          call_user_func_array( $control[ 'callback' ], $control[ 'params' ] );
          ob_end_clean();
          break;
        }
      }

      //** May not be necessary */
      if ( $form = $wp_registered_widget_controls[ $widget_id ] ) {
        call_user_func_array( $form[ 'callback' ], $form[ 'params' ] );
      }

      //** Add new widget to sidebar array */
      $sidebars[ $sidebar_id ][ ] = $widget_id . '-' . $widget_number;

      //** Add widget to widget area */
      wp_set_sidebars_widgets( $sidebars );

      //** Get widget configuration */
      $widget_options = get_option( 'widget_' . $widget_id );

      //** Check if current widget has any settings ( it shouldn't ) */
      if ( $widget_options[ $widget_number ] ) {
      }

      //** Update widget with settings */
      $widget_options[ $widget_number ] = $settings;

      //** Commit new widget data to database */
      update_option( 'widget_' . $widget_id, $widget_options );

      return true;

    }

    /**
     * @author odokienko@UD
     */
    static function flawless_signup_field_check() {
      global $wpdb;

      $field_name = $_REQUEST[ 'field_name' ];
      $field_value = $_REQUEST[ 'field_value' ];
      $field_type = $_REQUEST[ 'field_type' ];
      $response = array(
        'success' => 'true'
      );

      switch ( $field_name ) {
        case "signup_username":
          $user_exists = $wpdb->get_row( "SELECT * FROM {$wpdb->users} WHERE `user_login` = '{$field_value}' limit 1" );

          if ( !empty( $user_exists ) ) {
            $response = array(
              'success' => 'false',
              'message' => __( 'Sorry, that username already exists!', 'flawless' )
            );
          }
          break;
        case "signup_email":
          $user_exists = $wpdb->get_row( "SELECT * FROM {$wpdb->users} WHERE `user_email` = '{$field_value}' limit 1" );

          if ( !empty( $user_exists ) ) {
            $response = array(
              'success' => 'false',
              'message' => __( 'Sorry, that email address is already used!', 'flawless' ),
              'setfocus' => '.flawless_login_form input[name=log]'
            );
          }
          break;

        default:

      }

      die( json_encode( $response ) );
    }

    /**
     * Return the allowed pages that $pagenow global is allowed to be.
     *
     */
    static function changeable_post_type( $post_type = false ) {
      global $post, $flawless;

      $post_type = $post_type ? $post_type : $post->post_type;

      if ( !$post_type ) {
        return false;
      }

      $changeable_post_types = (array) apply_filters( 'flawless::changeable_post_types', array_keys( (array) $flawless[ 'post_types' ] ) );

      if ( in_array( $post_type, $changeable_post_types ) ) {
        return true;
      }

      return false;
    }

    /**
     * Remove all instanced of a widget from a sidebar
     *
     * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
     *
     * @since 0.0.2
     */
    static function remove_widget_from_sidebar( $sidebar_id, $widget_id ) {
      global $wp_registered_widget_updates;

      //** Load sidebars */
      $sidebars = wp_get_sidebars_widgets();

      //** Get widget ID */
      if ( is_array( $sidebars[ $sidebar_id ] ) ) {
        foreach ( (array) $sidebars[ $sidebar_id ] as $this_sidebar_id => $sidebar_widgets ) {

          //** Check if this sidebar already has this widget */

          if ( strpos( $sidebar_widgets, $widget_id ) === 0 || $widget_id == 'all' ) {

            //** Remove widget instance if it exists */
            unset( $sidebars[ $sidebar_id ][ $this_sidebar_id ] );

          }

        }
      }

      //** Save new siebars */
      wp_set_sidebars_widgets( $sidebars );
    }

    /**
     * Displays first-time setup splash screen
     *
     *
     * @since 0.0.2
     */
    static function admin_init() {

      //** Load defaults on theme activation */
      if ( current_user_can( 'update_themes' ) ) {
        Flawless::handle_upgrade();
      }

      do_action( 'flawless::admin_init' );

    }

    /**
     * Uses back-trace to figure out which sidebar was called from the sidebar.php file
     *
     * WordPress does not provide an easy way to figure out the type of sidebar that was called from within the sidebar.php file, so we backtrace it.
     *
     * @since 0.0.2
     * @author potanin@UD
     */
    static function backtrace_sidebar_type() {

      $backtrace = debug_backtrace();

      if ( !is_array( $backtrace ) ) {
        return false;
      }

      foreach ( (array) $backtrace as $item ) {

        if ( $item[ 'function' ] == 'flawless_widget_area' ) {
          return $item[ 'args' ][ 0 ];
        } elseif ( $item[ 'function' ] == 'get_sidebar' ) {
          return $item[ 'args' ][ 0 ];
        }

      }

      return false;

    }

    /**
     * Checks if script or style have been loaded.
     *
     * @todo Add handler for styles.
     * @since 0.2.0
     *
     */
    static function is_asset_loaded( $handle = false ) {
      global $wp_scripts;

      if ( empty( $handle ) ) {
        return;
      }

      $footer = (array) $wp_scripts->in_footer;
      $done = (array) $wp_scripts->done;

      $accepted = array_merge( $footer, $done );

      if ( !in_array( $handle, $accepted ) ) {
        return false;
      }

      return true;

    }

    /**
     * Insert scripts into footer.
     *
     * flawless_render_in_footer() depends on this to render any scripts / styles.
     *
     * @version 0.50.0
     */
    static function wp_print_footer_scripts() {
      global $flawless;

      foreach ( (array) $flawless[ 'runtime' ][ 'footer_scripts' ] as $script ) {
        //echo $script;
      }


    }

    /**
     * Tests if remote script or CSS file can be opened prior to sending it to browser
     *
     *
     * @version 0.25.0
     */
    static function can_get_asset( $url = false, $args = array() ) {
      global $flawless;

      if ( empty( $url ) ) {
        return false;
      }

      $match = false;

      if ( empty( $args ) ) {
        $args[ 'timeout' ] = 10;
      }

      $result = wp_remote_get( $url, $args );

      if ( is_wp_error( $result ) ) {
        return false;
      }

      $type = $result[ 'headers' ][ 'content-type' ];

      if ( strpos( $type, 'javascript' ) !== false ) {
        $match = true;
      }

      if ( strpos( $type, 'css' ) !== false ) {
        $match = true;
      }

      if ( !$match || $result[ 'response' ][ 'code' ] != 200 ) {

        if ( $flawless[ 'developer_mode' ] == 'true' ) {
          Log::add( "Remote asset ( $url ) could not be loaded, content type returned: " . $result[ 'headers' ][ 'content-type' ] );
        }

        return false;
      }

      return true;

    }

    /**
     * Tests if remote image can be loaded.  Returns URL to image if valid.
     *
     * @version 0.25.0
     */
    static function can_get_image( $url = false ) {

      if ( !is_string( $url ) ) {
        return false;
      }

      if ( empty( $url ) ) {
        return false;
      }

      //** Test if post_id */
      if ( is_numeric( $url ) && $image_attributes = wp_get_attachment_image_src( $url, 'full' ) ) {
        $url = $image_attributes[ 0 ];
      }

      $result = wp_remote_get( $url, array( 'timeout' => 10 ) );

      if ( is_wp_error( $result ) ) {
        return false;
      }

      //** Image content types should always begin with 'image' ( I hope ) */
      if ( strpos( $result[ 'headers' ][ 'content-type' ], 'image' ) !== 0 ) {
        return false;
      }

      return $url;

    }

    /**
     * Checks if sidebar is active. Same as default function, but allows hooks
     *
     * @since 0.2.0
     */
    static function is_active_sidebar( $sidebar ) {
      return is_active_sidebar( $sidebar );
    }

    /**
     * Draws a dropdown of objects, much like the regular wp_dropdown_objects() but with custom objects
     *
     * @todo Perhaps update function to return an auto-complete or ID input field when there are too many objects to render ina  dropdown.
     *
     */
    static function wp_dropdown_objects( $args = '' ) {

      $defaults = array(
        'depth' => 0,
        'post_type' => 'page',
        'child_of' => 0,
        'selected' => 0,
        'echo' => 1,
        'name' => 'page_id',
        'id' => '',
        'show_option_none' => '',
        'show_option_no_change' => '',
        'option_none_value' => ''
      );

      $r = wp_parse_args( $args, $defaults );
      extract( $r, EXTR_SKIP );

      if ( is_array( $r[ 'post_type' ] ) ) {
        $content_types = $r[ 'post_type' ];
      } else {
        $content_types = array( $r[ 'post_type' ] );
      }

      foreach ( (array) $content_types as $type ) {
        $post_type_obj = get_post_type_object( $type );
        $this_query = $r;
        $this_query[ 'post_type' ] = $type;

        $these_pages = get_pages( $this_query );

        if ( $these_pages ) {
          $objects[ $post_type_obj->labels->name ] = $these_pages;
        }

      }

      if ( empty( $objects ) ) {
        return false;
      }

      $output = array();

      $output[ ] = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";

      if ( $show_option_no_change ) {
        $output[ ] = "\t<option value=\"-1\">$show_option_no_change</option>";
      }

      if ( $show_option_none ) {
        $output[ ] = "\t<option value=\"" . esc_attr( $option_none_value ) . "\">$show_option_none</option>\n";
      }

      foreach ( (array) $objects as $object_type => $pages ) {

        if ( count( $objects ) > 1 ) {
          $output[ ] = '<optgroup label="' . $object_type . '">';
        }

        $output[ ] = walk_page_dropdown_tree( $pages, $depth, $r );

        if ( count( $objects ) > 1 ) {
          $output[ ] = '</optgroup>';
        }

      }

      $output[ ] = "</select>\n";

      $output = apply_filters( 'wp_dropdown_pages', $output );

      if ( $echo ) {
        echo implode( ' ', $output );
      }

      return implode( ' ', $output );

    }

    /**
     * Scans Asset Directories for the requested assets and returns asset-specific result if found
     *
     * - lib - inclues the PHP library if it exists
     * - less - returns file path if file exists
     * - image - returns URL if exists
     * - js - returns URL if exists
     * - css - returns URL if exists
     *
     * @todo Add function to scan different image extensions if no extension is specified.
     * @updated 0.0.6
     *
     * @author peshkov@UD
     *
     * @param $name
     * @param string $type
     * @param string $args
     *
     * @return bool|mixed|string
     */
    static function load( $name, $type = 'lib', $args = '' ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'return' => false
      ) );

      if ( $return = wp_cache_get( md5( $name . $type . serialize( $args ) ), 'flawless_load_value' ) ) {
        return $return;
      }

      foreach ( (array) $flawless[ 'asset_directories' ] as $assets_path => $assets_url ) {
        switch ( $type ) {

          case 'lib':
            if ( file_exists( $assets_path . '/core/lib/' . $name . '.php' ) ) {
              include_once( $assets_path . '/core/lib/' . $name . '.php' );
              $return = true;
            }
            break;

          case 'less':
            if ( file_exists( $assets_path . '/assets/less/' . $name ) ) {
              $return = $args[ 'return' ] == 'path' ? $assets_path . '/assets/less/' . $name : $assets_url . '/assets/less/' . $name;
            }
            break;

          case 'img':
          case 'image':
            if ( file_exists( $assets_path . '/img/' . $name ) ) {
              $return = $assets_url . '/img/' . $name;
            }
            break;

          case 'js':
            if ( file_exists( $assets_path . '/assets/js/' . $name ) ) {
              $return = $assets_url . '/assets/js/' . $name;
            }
            break;

          case 'css':
            if ( file_exists( $assets_path . '/assets/css/' . $name ) ) {
              $return = $assets_url . '/assets/css/' . $name;
            }
            break;

        }

      }

      if ( !empty( $return ) ) {
        wp_cache_set( md5( $name . $type . serialize( $args ) ), $return, 'flawless_load_value' );
        return $return;
      }

      return false;
    }

  } /* end Flawless class */

  // Initialize the theme.
  new Flawless();

}
