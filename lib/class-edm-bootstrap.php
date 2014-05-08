<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical\EDM {

  class Bootstrap {

    public function __construct() {

      // Enable Vertical Features.
      $this->features();

      // Enable Standard Actions.
      add_action( 'muplugins_loaded',       array( $this, 'muplugins_loaded' ), 0 );
      add_action( 'plugins_loaded',         array( $this, 'plugins_loaded' ), 100 );
      add_action( 'login_enqueue_scripts',  array( $this, 'login_enqueue_scripts' ), 30 );
      add_action( 'login_footer',           array( $this, 'login_footer' ), 30 );
      add_action( 'upload_mimes',           array( $this, 'upload_mimes' ), 100 );

      // Enable Standard Filters.
      add_filter( 'sanitize_file_name',     array( 'UsabilityDynamics\Utility', 'hashify_file_name' ), 10 );

    }

    /**
     * System Ready.
     *
     */
    public function plugins_loaded() {

      // Define EDM API Endpoints.
      API::define( '/v1/artists',            array( 'wpCloud\Vertical\EDM\API',     'getArtists' ) );
      API::define( '/v1/artist',             array( 'wpCloud\Vertical\EDM\API',     'getArtist' ) );
      API::define( '/v1/venues',             array( 'wpCloud\Vertical\EDM\API',     'getVenues' ) );
      API::define( '/v1/venue',              array( 'wpCloud\Vertical\EDM\API',     'getVenue' ) );
      API::define( '/v1/sites',              array( 'wpCloud\Vertical\EDM\API',     'listSites' ) );
      API::define( '/v1/site',               array( 'wpCloud\Vertical\EDM\API',     'getSite' ) );
      API::define( '/v1/routes',             array( 'wpCloud\Vertical\EDM\API',     'listRoutes' ) );
      API::define( '/v1/system/upgrade',     array( 'wpCloud\Vertical\EDM\API',     'systemUpgrade' ) );

      // System API
      API::define( '/v1/install/plugin',     array( 'wpCloud\Vertical\EDM\API',     'pluginInstall' ), array(
        'parameters' => array( 'name', 'version' ),
        'scopes' => array( 'install_plugins', 'activate_plugins' )
      ));

    }

    /**
     * Toggle Features.
     *
     * @todo Add a feature enabler based on Industry / Cloud / Network settings.
     *
     */
    private function features() {
      // if( wpCluster()->supports( '' ) ) {}
      // add_action( 'network_admin_menu',     'wpCloud\Modules\Intelligence::admin_menu', 20 );
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

    /**
     *
     */
    public function muplugins_loaded() {
      global $wp_theme_directories;


      // die( '<pre>' . print_r( $wp_theme_directories, true ) . '</pre>' );

    }

  }

}