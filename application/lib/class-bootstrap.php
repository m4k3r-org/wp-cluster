<?php
/**
 * EDM Cluster Application
 *
 * Veneer\Settings::add_content_type( trailingslashit( __DIR__ ) . 'config/settings.json' );
 * UI::create_dashboard_page();
 *
 * @version 1.0.0
 * @author potanin@UD
 * @namespace EDM
 *
 * Plugin Name: EDM Network
 * Description: EDM Network logic.
 * Version: 1.0.0
 * Plugin URI: http://usabilitydynamics.com
 * Author: Usability Dynamics, Inc.
 * Author URI: http://usabilitydynamics.com
 * Network: True
 */
namespace EDM\Application {

  // Localize UsabilityDynamics libraries and plugins
  use UsabilityDynamics;

  /**
   * EDMNetwork Application
   *
   * @property mixed init
   * @property mixed admin_init
   * @property mixed muplugins_loaded
   *
   * @class EDMNetwork
   * @author potanin@UD
   */
  class Bootstrap {

    /**
     * Version of child theme
     *
     * @public
     * @property $version
     * @var string
     */
    public $version = '2.0.4';

    /**
     * Textdomain String
     *
     * @public
     * @property $text_domain
     * @var string
     */
    public $text_domain = 'EDMApplication';

    /**
     * ID of instance, used for settings. Defaults to namespace.
     *
     * @public
     * @property id
     * @var string
     */
    private $id = null;

    /**
     * Root Directory.
     *
     * @public
     * @property $root
     * @var string
     */
    public $root = null;

    /**
     * Home URL.
     *
     * @public
     * @property $home
     * @var string
     */
    public $home = null;

    /**
     * Initializer.
     *
     * @return \EDM\Application\Bootstrap EDMNetwork
     */
    public function __construct() {

      register_theme_directory( WP_CONTENT_DIR . '/themes' );

      add_filter( 'wp_cache_themes_persistently', function( $current, $callee ) {
        return 43200; // 6 hours
      }, 10, 2);

      // Instantaite Settings.
      $this->settings   = class_exists( '\UsabilityDynamics\Settings' ) ? new \UsabilityDynamics\Settings : null;

      // Current Paths.
      $this->id         = 'edm'; // Utility::create_slug( __NAMESPACE__, array( 'separator' => '::' ) );
      $this->root       = defined( 'WP_BASE_DIR' ) ? WP_BASE_DIR : get_home_path();
      $this->home       = home_url();

      // Core Filters
      add_action( 'init', array( &$this, 'init' ), 100 );
      add_action( 'admin_init', array( &$this, 'admin' ), 100 );
      add_action( 'upload_mimes', array( &$this, 'upload_mimes' ), 100 );
      add_action( 'login_footer', array( &$this, 'login_footer' ), 30 );
      add_filter( 'login_headerurl', array( &$this, 'login_headerurl' ), 30 );
      add_filter( 'wp_mail_from', array( &$this, 'wp_mail_from' ), 10 );
      add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ), 30 );

    }

    /**
     * Initialize Application
     *
     * Load Domain Mapping at this level otherwise it willl break plugin_dir_url() and perhaps other urls.
     *
     * @author potanin@UD
     * @for EDMNetwork
     */
    public function init() {

      // Run Upgrade if version is missing or outdated.
      // if( !get_site_option( $this->id . '::version' ) || version_compare( self::$version, get_site_option( $this->id . '::version' ), '>' ) ) { $this->upgrade(); }

      // Dashboard Panels.
      // add_action( 'wp_network_dashboard_setup', array( &$this, 'wp_dashboard_setup' ), 0 );
      // add_action( 'wp_user_dashboard_setup', array( &$this, 'wp_dashboard_setup' ), 0 );

      // Fix hard-coded location of wp-content/bannner
      add_filter( 'option_adrotate_config', function( $value ) {
        $value[ 'jshowoff' ] = 'Y';
        $value[ 'banner_folder' ] = 'static/storage';
        return $value;
      });

      // Must be enabled for MS Media rewrite to work natively
      add_shortcode( 'wp_login_form', array( &$this, 'wp_login_form_shortcode' ) );

      // Enable JavaScript Library Loading.
      if( class_exists( 'UsabilityDynamics\Requires' ) ) {
        new \UsabilityDynamics\Requires( array( 'name'  => 'application', 'scope' => array( 'backend' ), 'debug' => true ) );
      }

      // Basic Frontend Security
      remove_action( 'wp_head', 'feed_links', 2 );
      remove_action( 'wp_head', 'feed_links_extra', 3 );
      remove_action( 'wp_head', 'rsd_link' );
      remove_action( 'wp_head', 'wlwmanifest_link' );
      remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
      remove_action( 'wp_head', 'wp_generator' );
      remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

      add_action( 'customize_controls_print_scripts', array( get_class(), 'admin_enqueue_scripts' ), 20 );
      add_action( 'admin_enqueue_scripts', array( get_class(), 'admin_enqueue_scripts' ), 20 );

      self::add_filters( array(
        'bloginfo_url',
        'the_permalink',
        'wp_list_pages',
        'wp_list_categories',
        'roots_wp_nav_menu_item',
        'the_content_more_link',
        'the_tags',
        'get_pagenum_link',
        'get_comment_link',
        'month_link',
        'day_link',
        'year_link',
        'tag_link',
        'the_author_posts_link',
        'script_loader_src',
        'style_loader_src'
      ), array( $this, 'relative_url' ) );

      if( class_exists( '\UsabilityDynamics\Utility' ) ) {
        add_filter( 'sanitize_file_name', array( '\UsabilityDynamics\Utility', 'hashify_file_name' ), 10 );
      }

      return;

      // migrated out of wp-festival
      $file = WP_BASE_DIR . '/static/schemas/default.settings.json';

      if( file_exists( $file ) ) {
        $settings = \UsabilityDynamics\Utility::l10n_localize( json_decode( file_get_contents( $file ), true ) );
        if( !empty( $settings[ 'structure' ] ) ) {
          $this->set( 'structure', $settings[ 'structure' ] );
        }
      }

    }

    /**
     * Admin Initializer
     *
     * @author potanin@UD
     * @for EDMNetwork
     */
    public function admin() {

      remove_action( 'welcome_panel', 'wp_welcome_panel' );
      add_action( 'welcome_panel', array( get_class(), 'welcome_panel' ), 0 );

      // Automatically setup htaccess file
      if( !file_exists( $this->root . '/.htaccess' ) && file_exists( 'application/defaults/.htaccess' ) ) {
        copy( 'application/defaults/.htaccess', $this->root . '/.htaccess' );
      }

      // Notify administrator if .htaccess was not copied
      if( current_user_can( 'administrator' ) && ( !is_writable( $this->root . '/.htaccess' ) || !is_writable( $this->root . '/.htaccess' ) ) ) {
        add_action( 'admin_notices', create_function( '', "echo '<div class=\"error\"><p>" . sprintf( __( 'Please make sure your <a href="%s">.htaccess</a> file is writable ', 'roots' ), admin_url( 'options-permalink.php' ) ) . "</p></div>';" ) );
      }

    }

    /**
     * Replace Default Sender Email
     *
     * @param $from_email
     *
     * @return mixed
     */
    public function wp_mail_from( $from_email ) {

      // Get the site domain and get rid of www.
      $sitename = strtolower( $_SERVER['SERVER_NAME'] );
      if ( substr( $sitename, 0, 4 ) == 'www.' ) {
        $sitename = substr( $sitename, 4 );
      }

      if( $from_email == 'wordpress@' . $sitename ) {
        return str_replace( 'wordpress', 'info', $from_email );
      }

      return $from_email;

    }

    /**
     * Admin Login Scripts
     *
     * @author potanin@UD
     */
    public function login_enqueue_scripts() {
      echo implode( '', array(
        '<link rel="stylesheet" id="network-styles" href="', self::application_url( '/static/styles/login.css' ), '" type="text/css" media="all" />'
      ));
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function login_headerurl( $url ) {
      return '';
    }

    /**
     * Render Informaiton on Login Screen.
     *
     * @method login_footer
     */
    public function login_footer() {
      global $interim_login;

      if( !$interim_login && is_file( 'templates/login-info.php' ) ) {
        include( 'templates/login-info.php' );
      }

    }

    /**
     * Login Shortcode
     * @param array $args
     */
    public function wp_login_form_shortcode( $args = array() ) {

      $args = shortcode_atts( $args, array(
        'echo' => true,
        'redirect' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], // Default redirect is back to the current page
        'form_id' => 'loginform',
        'label_username' => __( 'Username' ),
        'label_password' => __( 'Password' ),
        'label_remember' => __( 'Remember Me' ),
        'label_log_in' => __( 'Log In' ),
        'id_username' => 'user_login',
        'id_password' => 'user_pass',
        'id_remember' => 'rememberme',
        'id_submit' => 'wp-submit',
        'remember' => true,
        'value_username' => '',
        'value_remember' => false
      ));

      wp_login_form( $args );

    }

    /**
     * Welcome Dasboard
     *
     */
    public function wp_dashboard_setup() {
      include( __DIR__ . '/templates/welcome.php' );
    }

    /**
     * System Upgrade
     *
     * @author potanin@UD
     * @for EDMNetwork
     */
    public function upgrade() {
      global $wpdb;

      $_old_version = get_site_option( $this->id . '::version' ) || '0.0.0';

      // Good time to check of DB is fucked.
      foreach( $wpdb->get_results( "SELECT meta_id, meta_value from {$wpdb->postmeta} WHERE meta_key = '_cfct_build_data'" ) as $row ) {
        $fixed = Utility::repair_serialized_object( $row->meta_value );
        $wpdb->query("UPDATE {$wpdb->postmeta} SET meta_value = {$fixed} WHERE meta_key = {$row->meta_key}");
      }

      // update_user_meta( get_current_user_id(), 'show_welcome_panel', true );

      update_site_option( $this->id . '::version', self::$version );

      wp_die( '<h1>Updated</h1><p>Updated from ' . $_old_version  . ' to ' . self::$version . '.</p>' );

    }

    /**
     * Welcome Panel Content
     *
     * @author potanin@UD
     * @for EDMNetwork
     */
    public function welcome_panel() {

    }

    /**
     * Application Link
     *
     * @param string $path
     *
     * @return string
     */
    public function application_url( $path = '' ) {
      return home_url( 'application/' . $path );
    }

    /**
     * Admin Assets
     *
     *
     */
    static public function admin_enqueue_scripts() {
      wp_enqueue_script( 'jquery-ui-datepicker' );
      wp_enqueue_script( 'jquery-ui-sortable' );
      wp_enqueue_script( 'jquery-ui-widget' );
      wp_enqueue_script( 'jquery-ui-progressbar' );
    }

    /**
     * Allow Upload File Types
     *
     * Makes uploading of XML, XSD files possible for SEC files.
     *
     * @param $mimes
     *
     * @return mixed
     */
    public function upload_mimes( $mimes ) {
      $mimes[ 'xsd' ] = 'application/xsd';
      $mimes[ 'xml' ] = 'application/xml';

      return $mimes;
    }

    /**
     * Apply a method to multiple filters
     *
     * @param $tags
     * @param $function
     */
    public function add_filters( $tags, $function ) {

      foreach( $tags as $tag ) {
        add_filter( $tag, $function );
      }

    }

    /**
     * Root relative URLs
     *
     * WordPress likes to use absolute URLs on everything - let's clean that up.
     * Inspired by http://www.456bereastreet.com/archive/201010/how_to_make_wordpress_urls_root_relative/
     *
     * You can enable/disable this feature in config.php:
     * current_theme_supports('root-relative-urls');
     *
     * @souce roots
     * @author Scott Walkinshaw <scott.walkinshaw@gmail.com>
     */
    public function relative_url( $input ) {
      return $input;

      preg_match( '|https?://([^/]+)(/.*)|i', $input, $matches );

      if( isset( $matches[ 1 ] ) && isset( $matches[ 2 ] ) && $matches[ 1 ] === $_SERVER[ 'SERVER_NAME' ] ) {
        return wp_make_link_relative( $input );
      } else {
        return $input;
      }
    }

    /**
     * Network Application Error Handler
     *
     * Should be overwritten by themes.
     *
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     *
     * @return bool|void
     */
    public function error_handler( $code = null, $message = '', $file = '', $line = null ) {

      // Log error
      // error_log( $message );

      // AJAX Error
      if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        die( json_encode( array(
          "success" => false,
          "message" => $message
        ) ) );
      }

      // Output error
      wp_die( "<h1>Network Error</h1><p>We apologize for the inconvenience and will return shortly.</p><p>$message</p>" );

    }

  }

}