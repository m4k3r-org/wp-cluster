<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical\EDM {

  class Bootstrap {

    public function __construct() {

      add_action( 'muplugins_loaded',       array( $this, 'muplugins_loaded' ), 0 );
      add_action( 'plugins_loaded',         array( $this, 'plugins_loaded' ) );
      add_action( 'login_enqueue_scripts',  array( $this, 'login_enqueue_scripts' ), 30 );

      add_action( 'login_footer',           array( $this, 'login_footer' ), 30 );
      add_action( 'init',                   array( $this, 'init' ), 30 );
      add_action( 'upload_mimes',           array( $this, 'upload_mimes' ), 100 );
      add_filter( 'sanitize_file_name',     array( 'UsabilityDynamics\Utility', 'hashify_file_name' ), 10 );

      add_action( 'network_admin_menu',     'wpCloud\Modules\Intelligence::admin_menu', 20 );
      // add_filter( 'wp_cache_themes_persistently', function( $current, $callee ) { return 43200;  }, 10, 2);

    }


    /**
     * Render Informaiton on Login Screen.
     *
     * @method login_footer
     */
    public function login_footer() {
      global $interim_login;

      if( !$interim_login && is_file( 'static/templates/login-info.php' ) ) {
        include( 'static/templates/login-info.php' );
      }

    }

    /**
     * Welcome Dasboard
     *
     */
    public function wp_dashboard_setup() {
      include( __DIR__ . '/static/templates/welcome.php' );
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
     * Admin Login Scripts
     *
     * @author potanin@UD
     */
    public function login_enqueue_scripts() {
      echo implode( '', array(
        '<link rel="stylesheet" id="network-styles" data-vertical="edm" href="', site_url( '/vendor/modules/vertical-edm/static/styles/login.css' ), '" type="text/css" media="all" />'
      ));
    }

    public function plugins_loaded() {

      // Define API Endpoints.
      API::define( '/artists',            array( 'wpCloud\Vertical\EDM\API',     'getArtists' ) );
      API::define( '/artist',             array( 'wpCloud\Vertical\EDM\API',     'getArtist' ) );
      API::define( '/venues',             array( 'wpCloud\Vertical\EDM\API',     'getVenues' ) );
      API::define( '/venue',              array( 'wpCloud\Vertical\EDM\API',     'getVenue' ) );
      API::define( '/site',               array( 'wpCloud\Vertical\EDM\API',     'getSite' ) );
      API::define( '/system/upgrade',     array( 'wpCloud\Vertical\EDM\API',     'systemUpgrade' ) );

    }

    public function init() {

    }

    public function muplugins_loaded() {
      global $wp_theme_directories;

      if( defined( 'WP_THEME_DIR' ) ) {
        register_theme_directory( WP_THEME_DIR );
      }

    }

  }

}