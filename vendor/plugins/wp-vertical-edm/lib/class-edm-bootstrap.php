<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical\EDM {

	use UsabilityDynamics\API;
	use UsabilityDynamics\Model;
	use UsabilityDynamics\Settings;

  class Bootstrap {

	  /**
	   * Cluster core version.
	   *
	   * @static
	   * @property $version
	   * @type {Object}
	   */
	  public static $version = '0.1.0';

	  /**
	   * Textdomain String
	   *
	   * @public
	   * @property text_domain
	   * @var string
	   */
	  public static $text_domain = 'wp-vertical-edm';

	  /**
	   * Singleton Instance Reference.
	   *
	   * @public
	   * @static
	   * @property $instance
	   * @type {Object}
	   */
	  public static $instance = false;

	  /**
	   * Settings.
	   *
	   * @public
	   * @property $_settings
	   * @type {Mixed}
	   */
	  public $_settings = null;

	  public function __construct() {

		  /** Return Singleton Instance */
		  if( self::$instance ) {
			  return self::$instance;
		  }

		  self::$instance = &$this;

		  $this->_settings  = new Settings();

      // Enable Vertical Features.
      $this->features();

	    add_action( 'plugins_loaded',         array( $this, 'plugins_loaded' ), 20 );

    }

    /**
     * System Ready.
     *
     * @url http://discodonniepresents.com/api/network/v1/sites
     * @url http://discodonniepresents.com/api/network/v1/routes
     * @url http://discodonniepresents.com/api/network/v1/upgrade
     * @url http://discodonniepresents.com/api/network/v1/install
     * @url http://discodonniepresents.com/api/site/v1/meta
     *
     */
    public function plugins_loaded() {

	    // load_plugin_textdomain( 'wp-vertical-edm', false, dirname( plugin_basename( dirname( __FILE__ ) ) ) . '/static/locale/' );

	    add_action('admin_head-tools_page_elastic_search', function(){
		    wp_enqueue_script('edm-elastic-index', plugins_url( '/static/scripts/src/manage-index.js', dirname( __FILE__ ) ), array('jquery'));
	    });

	    // Enable Standard Actions.
	    add_action( 'wp_before_admin_bar_render', array( $this, 'toolbar_menu' ), 10 );
	    add_action( 'login_enqueue_scripts',  array( $this, 'login_enqueue_scripts' ), 30 );
	    add_action( 'login_footer',           array( $this, 'login_footer' ), 30 );

	    // Enable Standard Filters.
	    add_filter( 'sanitize_file_name',     array( 'UsabilityDynamics\Utility', 'hashify_file_name' ), 10 );

	    if( !method_exists( 'UsabilityDynamics\API', 'define' ) )  {
		    return;
	    }

      // List Sites.
      API::define( '/network/v1/sites', array(
        'scopes' => array( 'read' ),
        'handler' => array( 'wpCloud\Vertical\EDM\API', 'listSites' )
      ));

      // System API
      API::define( '/network/v1/routes', array(
        'parameters' => array( 'name', 'version' ),
        'scopes' => array( 'read' ),
        'handler' => array( 'wpCloud\Vertical\EDM\API', 'listRoutes' )
      ));

      // System API
      API::define( '/network/v1/upgrade', array(
        'parameters' => array( 'version' ),
        'scopes' => array( 'install_plugins', 'activate_plugins' ),
        'handler' => array( 'wpCloud\Vertical\EDM\API', 'systemUpgrade' )
      ));

      API::define( '/network/v1/install', array(
        'parameters' => array( 'name', 'version' ),
        'scopes' => array( 'install_plugins', 'activate_plugins' ),
        'handler' => array( 'wpCloud\Vertical\EDM\API', 'pluginInstall' )
      ));

      // Single Site.
      API::define( '/site/v1/meta', array(
        'scopes' => array( 'manage_options' ),
        'handler' => array( 'wpCloud\Vertical\EDM\API', 'getSite' )
      ));

    }

	  /**
	   * Load JSON File into Post Type model.
	   *
	   * @param null $filePath
	   *
	   * @return mixed
	   */
	  static public function loadModel( $filePath = null ) {

		  if( did_action( 'wp_loaded' ) ) {
			  _doing_it_wrong( 'UsabilityDynamics\wpElastic\Bootstrap::define', __( 'Called too late.' ), '' );
		  }

		  if( !file_exists( $filePath ) ) {
			  return new \WP_Error( 'Unable to load model, given file path is not valid.' );
		  }

		  $fileContents = file_get_contents( $filePath );

		  if( is_string( $fileContents ) ) {
			  $parsedContents = json_decode( $fileContents, true );
		  }

		  $parsedContents = Utility::parse_args( $parsedContents, array(
			  'types' => array(),
			  'meta' => array(),
			  'taxonomies' => array()
		  ));

		  return Model::define( $parsedContents );


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

	  public function toolbar_menu() {
		  global $wp_admin_bar;

		  if( $this->get( 'toolbar.remove.comments' ) ) {
			  $wp_admin_bar->remove_menu( 'comments' );
		  }

		  if( $this->get( 'toolbar.remove.wp' ) ) {
			  $wp_admin_bar->remove_menu( 'wp-logo' );
		  }

		  if( $this->get( 'toolbar.remove.seo' ) ) {
			  $wp_admin_bar->remove_menu( 'wpseo-menu' );
		  }


	  }


		  /**
     * Render Informaiton on Login Screen.
     *
     * @method login_footer
     */
    public function login_footer() {
      global $interim_login;

	    if( !$interim_login && is_file( dirname (__DIR__ ) . '/static/templates/login-info.php'  ) ) {
        include( dirname (__DIR__ ) . '/static/templates/login-info.php' );
      }

    }

    /**
     * Welcome Dasboard
     *
     */
    public function wp_dashboard_setup() {
      include( dirname (__DIR__ ) . '/static/templates/welcome.php' );
    }

    /**
     * Admin Login Scripts
     *
     * @author potanin@UD
     */
    public function login_enqueue_scripts() {

	    echo implode( '', array(
        '<link rel="stylesheet" data-vertical="edm" href="', plugins_url( '/static/styles/login.css', dirname( __FILE__ ) ), '" type="text/css" media="all" />'
      ));

    }

	  /**
	   * Get Setting.
	   *
	   *    // Get Setting
	   *    Cluster::get( 'my_key' )
	   *
	   * @method get
	   *
	   * @for Flawless
	   * @author potanin@UD
	   * @since 0.1.1
	   *
	   * @param null $key
	   * @param null $default
	   *
	   * @return null
	   */
	  public static function get( $key = null, $default = null ) {
		  return self::$instance->_settings ? self::$instance->_settings->get( $key, $default ) : null;
	  }

	  /**
	   * Set Setting.
	   *
	   * @usage
	   *
	   *    // Set Setting
	   *    Cluster::set( 'my_key', 'my-value' )
	   *
	   * @method get
	   * @for Flawless
	   *
	   * @author potanin@UD
	   * @since 0.1.1
	   *
	   * @param null $key
	   * @param null $value
	   *
	   * @return null
	   */
	  public static function set( $key = null, $value = null ) {
		  return self::$instance->_settings ? self::$instance->_settings->set( $key, $value ) : null;
	  }

	  /**
	   * Get the Cluster Singleton
	   *
	   * Concept based on the CodeIgniter get_instance() concept.
	   *
	   * @example
	   *
	   *      var settings = Cluster::get_instance()->Settings;
	   *      var api = Cluster::$instance()->API;
	   *
	   * @static
	   * @return object
	   *
	   * @method get_instance
	   * @for Cluster
	   */
	  public static function &get_instance() {
		  return self::$instance;
	  }

  }

}
