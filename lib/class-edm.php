<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical {

  class EDM {

    /**
     * Version of child theme
     *
     * @public
     * @property $version
     * @var string
     */
    public $version = '2.0.4';

    function __construct() {

      // require_once( 'lib/class-edm.php' );
      // require_once( 'lib/class-edm-api.php' );
      // require_once( 'lib/class-edm-bootstrap.php' );
      // require_once( 'lib/class-edm-utility.php' );

      // Define Data Structure
      // - add_post_type_support( 'event', 'post-formats' );
      // - set_post_format();

      // Define User Roles and Capabilities
      // add_user_role( );

      // Define Post Object Callbacks
      // - add_filter( 'wp-elastic:websiteLink', array( 'wpCloud\Vertical\EDM::Utility', 'get_image_urls' ) );

      // migrated out of wp-festival
      $file = WP_BASE_DIR . '/static/schemas/default.settings.json';

      if( file_exists( $file ) ) {
        // $settings = \UsabilityDynamics\Utility::l10n_localize( json_decode( file_get_contents( $file ), true ) );
        // if( !empty( $settings[ 'structure' ] ) ) {
          // $this->set( 'structure', $settings[ 'structure' ] );
        // }
      }

    }

    /**
     * Admin Login Scripts
     *
     * @author potanin@UD
     */
    public function login_enqueue_scripts() {
      echo implode( '', array(
        '<link rel="stylesheet" id="network-styles" data-vertical="edm" href="', self::application_url( '/styles/login.css' ), '" type="text/css" media="all" />'
      ));
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
     * Load Network Plugins
     *
     * @author potanin@UD
     * @for EDMNetwork
     */
    public function muplugins_loaded() {

      // define( 'RWMB_URL', $this->vendor_url( 'rilwis/meta-box' ) . '/' );

      // Include plugins
      //require_once( $this->root . '/vendor/wordpress/regenerate-thumbnails/regenerate-thumbnails.php' );
      //require_once( $this->root . '/vendor/wordpress/rilwis/meta-box/meta-box.php' );

    }

    /**
     * Load Standard Plugins
     *
     * @method plugins_loaded
     * @author potanin@UD
     * @for EDMNetwork
     */
    public function plugins_loaded() {

      //define( 'RWMB_URL', $this->vendor_url( 'rilwis/meta-box' ) . '/' );

      // Include plugins
      // require_once( $this->root . '/vendor/usabilitydynamics/wp-adrotate/adrotate.php' );

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

  }

}