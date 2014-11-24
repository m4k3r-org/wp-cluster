<?php
/**
 * UsabilityDynamics\Cluster Bootstrap
 *
 * ### Options
 * * hide.toolbar.login
 *
 * @verison 0.4.1
 * @author potanin@UD
 * @namespace UsabilityDynamics\Cluster
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Bootstrap' ) ) {

    /**
     * Bootstrap Cluster
     *
     * @class Bootstrap
     * @author potanin@UD
     * @version 0.0.1
     */
    class Bootstrap {

      /**
       * Cluster core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.4.5';

      /**
       * Textdomain String
       *
       * @public
       * @property text_domain
       * @var string
       */
      public static $text_domain = 'wp-cluster';

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
       * Current Network ID
       *
       * @public
       * @static
       * @property $network_id
       * @type {Object}
       */
      public $network_id = null;

      /**
       * Current site (blog)
       *
       * @public
       * @static
       * @property $site_id
       * @type {Object}
       */
      public $site_id = null;

      /**
       * The literal domain of the current request.
       *
       * @public
       * @static
       * @property $site_id
       * @type {Object}
       */
      public $requested_domain = null;

      /**
       * The main mapped domain of a site.
       *
       * @public
       * @static
       * @property $domain
       * @type {Object}
       */
      public $domain = null;

      /**
       * The domain name of the network this site belongs to.
       *
       * @public
       * @static
       * @property $domain
       * @type {Object}
       */
      public $network_domain = null;

      /**
       * Original host, when proxied.
       *
       * @public
       * @property $original_host
       * @type {String}
       * @var null
       */
      public $original_host = null;

      /**
       * Settings.
       *
       * @public
       * @property $_settings
       * @type {Mixed}
       */
      public $_settings = null;
      public $_mapping = null;
      public $_developer = null;
      public $_api = null;
      public $_theme = null;

      /**
       * Constructor.
       *
       * UsabilityDynamics components should be avialable.
       * - class_exists( '\UsabilityDynamics\API' );
       * - class_exists( '\UsabilityDynamics\Utility' );
       *
       * @for Loader
       * @method __construct
       */
      public function __construct() {
        global $wpdb, $current_site, $current_blog, $wp_veneer, $wp_cluster;

        /** Return Singleton Instance */
        if( self::$instance ) {
          return self::$instance;
        }

        /** Ok, see if we have a wp_cluster object already */
        if( isset( $wp_veneer ) && is_object( $wp_veneer ) && @isset( $wp_veneer->cluster ) && count( get_object_vars( $wp_veneer->cluster ) ) ){
          foreach( array_keys( get_object_vars( $wp_veneer->cluster ) ) as $key ){
            $this->{$key} = $wp_veneer->cluster->{$key};
          }
        }

	      // Install plugin by copying files, got to do this before MS check.
	      register_activation_hook( dirname( __DIR__ ) . '/wp-cluster.php' , array( 'UsabilityDynamics\Cluster\Bootstrap', 'activation' ) );
	      register_deactivation_hook( dirname( __DIR__ ) . '/wp-cluster.php' , array( 'UsabilityDynamics\Cluster\Bootstrap', 'deactivation' ) );

	      if( !defined( 'MULTISITE' ) || !MULTISITE ) {
          return $this;
        }

        /** Set the singleton instance */
        if( !is_object( $wp_veneer ) ){
          $wp_veneer = new \stdClass();
        }

	      $wp_cluster = $wp_veneer->cluster = self::$instance = &$this;

        // Current Site.
        $this->site_name        = get_option( 'blogname' );
        $this->cluster_domain   = defined( 'WP_BASE_DOMAIN' ) ? WP_BASE_DOMAIN : null;
        $this->organization     = is_object( $current_site ) ? $current_site->site_name : null;
        $this->site_id          = $wpdb->blogid;
        $this->network_id       = $wpdb->siteid;
        $this->requested_domain = is_object( $current_blog ) ? $current_blog->domain : null;
        $this->domain           = isset( $wpdb->blogs ) ? $wpdb->get_var( "SELECT domain FROM {$wpdb->blogs} WHERE blog_id = '{$wpdb->blogid}' LIMIT 1" ) : null;
        $this->network_domain   = isset( $wpdb->site ) ? $wpdb->get_var( "SELECT domain FROM {$wpdb->site} WHERE id = {$this->network_id}" ) : null;
        $this->allowed_domains  = array( $this->domain, DOMAIN_CURRENT_SITE );
        $this->is_valid         = in_array( $this->requested_domain, $this->allowed_domains ) ? true : false;
        $this->is_public        = is_object( $current_blog ) && isset( $current_blog->public ) ? $current_blog->public : true;
        $this->is_main_site     = is_main_site();
        $this->is_multisite     = is_multisite();
        $this->is_main_network  = is_main_network();
        $this->original_host    = $_SERVER[ 'HTTP_HOST' ];

        if( !$this->is_valid ) {
          wp_die( '<h1>Cluster Network Error</h1><p>Your request is for an invalid domain.</p>', $this->domain );
        }

        // Fatal Error Handler.
        register_shutdown_function( array( $this, '_shutdown' ) );

        // Initialize Components and Settings.
        $this->_components();

        // Initialize Interfaces.
        $this->_interfaces();

        // Fix MultiSite URLs
        $this->_fix_urls();

        // Prepare Cookie Constants.
        $this->_cookie();

        // Sent common response headers.
        $this->_send_headers();

	      // Enable CLI, if in CLI mode
	      $this->_cli();

	      // Initialize all else.
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 20 );

      }

	    /**
	     * Copy Files.
	     *
	     * @return array
	     */
      static public function activation() {

	      $_files = array(
		      __DIR__ . '/class-sunrise.php' => WP_CONTENT_DIR . '/sunrise.php',
		      __DIR__ . '/class-database.php' => WP_CONTENT_DIR . '/db.php'
	      );

	      if( defined( 'SUNRISE' ) && SUNRISE ) {}

	      $_errors = array();

	      foreach( $_files as $_source => $_destination ) {

		      if( file_exists( $_destination )  ) {
			      continue;
		      }

		      try {

			      if( !is_writable( dirname( $_destination  ) ) ) {
				      throw new \Exception( __(  'Destination is not writable, unable to install wp-cluster.', 'wp-cluster' ) );
			      }

			      if( !function_exists( 'link' ) || !link( $_source, $_destination ) ) {

				      if( !copy( $_source, $_destination ) ) {
					      // Something went wrong?
				      }

			      }

		      } catch ( \Exception $_error ) {
			      $_errors[]  = $_error;
		      }

	      }

	      if( $_errors ) {
		      // die( '<pre>' . print_r( $_errors, true ) . '</pre>');
	      }

	      return $_errors;

      }

	    /**
	     *
	     */
	    static public function deactivation() {

	    }

	    /**
	     *
	     */
      public function _shutdown() {

        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        }

        if( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
        }

        $errfile = 'unknown file';
        $errstr  = 'shutdown';
        $errno   = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if( $error !== NULL ) {
          $errno   = $error[ 'type' ];
          $errfile = $error[ 'file' ];
          $errline = $error[ 'line' ];
          $errstr  = $error[ 'message' ];
        }

      }

      /**
       *
       */
      private function _cookie() {

        $siteurl = get_site_option( 'siteurl' );

        // Must set or long will not work
        if( !defined( 'COOKIE_DOMAIN' ) ) {
          $_requested_domain = str_replace( "www.", ".", $this->requested_domain );

          // Add "." prefix if not found, this is default WP.
          if( !substr($_requested_domain, 0, 1) !== '.' && ( defined( 'SUBDOMAIN_COOKIE' ) && SUBDOMAIN_COOKIE ) ) {
            $_requested_domain = '.' . $_requested_domain;
          }

          //if( strpos( $_requested_domain, '.', ))
          define( 'COOKIE_DOMAIN', $_requested_domain );
        }

        if( !defined( 'COOKIEHASH' ) ) {

          if( $siteurl ) {
            define( 'COOKIEHASH', md5( $siteurl ) );
          } else {
            define( 'COOKIEHASH', '' );
          }

        }

        if( !defined( 'USER_COOKIE' ) ) {
          define( 'USER_COOKIE', 'wordpressuser_' . COOKIEHASH );
        }

        if( !defined( 'LOGGED_IN_COOKIE' ) ) {
          define( 'LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH );
        }

        if( !defined( 'COOKIEPATH' ) ) {
          define( 'COOKIEPATH', preg_replace( '|https?://[^/]+|i', '', get_option( 'home' ) . '/' ) );
        }

        if( !defined( 'SITECOOKIEPATH' ) ) {
          define( 'SITECOOKIEPATH', preg_replace( '|https?://[^/]+|i', '', get_option( 'siteurl' ) . '/' ) );
        }

        if( !defined( 'ADMIN_COOKIE_PATH' ) ) {
          define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );
        }

        if( !defined( 'PLUGINS_COOKIE_PATH' ) ) {
          define( 'PLUGINS_COOKIE_PATH', preg_replace( '|https?://[^/]+|i', '', WP_PLUGIN_URL ) );
        }

      }

      /**
       * Initialize Media, Varnish, etc.
       *
       */
      private function _components() {

        // Initialize Controllers and Helpers
        $this->_developer = new Developer();
        $this->_settings  = new Settings();
        $this->_api       = new API();
        $this->_theme     = new Theme();
        //$this->_media = new Media( $this->get( 'media' ) );
        //$this->_varnish = new Varnish($this->get( 'varnish' ));

        // $this->_settings->commit();

        // Basic Frontend Security
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

      }

      /**
       * Modify Admin Footer Text
       *
       */
      public function admin_footer_text() {
        return '<span id="footer-thankyou">' . __( 'Provided by the <a href="http://' . $this->network_domain . '">' . $this->organization . '</a>' ) . ' network.</span>';
      }

      /**
       * Output Fatal Error Message
       *
       * @param $message
       */
      public function fatal( $message = null ) {
        wp_die( $message ? '<h1>Cluster Error</h1><p>' . $message . '</p>' : '<hr />' );
      }

      /**
       * Initialize Interface Compnents
       *
       */
      private function _interfaces() {

      }

      /**
       * Automatically fix MS URLs that get messed up
       *
       * UPLOADBLOGSDIR must be set in wp-config.php to take affect, UPLOADS is defined based on site's ID
       * This would be the place to overwrite the media/{ID}/files to something else.
       *
       * network_site_url - http://network.nightculture.loc/wp-admin/network/ -> http://network.nightculture.loc/system/wp-admin/network/
       *
       */
      private function _fix_urls() {

        // Add handling for /manage
        //add_filter( 'network_site_url', array( get_class(), 'network_site_url' ) );
        //add_filter( 'network_admin_url', array( get_class(), 'network_site_url' ) );

        // Add handling for /manage
        //add_filter( 'admin_url', array( get_class(), 'admin_url' ) );

      }

	    /**
       * Add Cluster Toolbar
       *
       * @method cluster_toolbar
       * @for Boostrap
       */
      public function toolbar_menu() {
        global $wp_admin_bar;

	      if( $this->get( 'toolbar.git.enabled' ) ) {

		      $wp_admin_bar->add_menu( array(
			      'id' => 'cluster_git',
			      'parent' => 'top-secondary',
			      'title' => sprintf( __( 'Branch: %s' ), Utility::get_git_branch() ),
			      'href' => '#'
		      ));

		      $wp_admin_bar->add_menu( array(
			      'id' => 'cluster_git_message',
			      'parent' => 'cluster_git',
			      'title' => sprintf( __( '%s' ), Utility::get_git_commit_message() ),
			      'href' => '#'
		      ));

		      $wp_admin_bar->add_menu( array(
			      'id' => 'cluster_git_version',
			      'parent' => 'cluster_git',
			      'title' => sprintf( __( 'Version: %s' ), Utility::get_git_version()->short ),
			      'href' => '#'
		      ));

	      }

	      if( $this->get( 'toolbar.menu.enabled' ) ) {

		      $wp_admin_bar->add_menu( array(
			      'id'    => 'cloud-manager',
			      'meta'  => array(
				      'html'     => '<div class="cluster-toolbar-info"></div>',
				      'target'   => '',
				      'onclick'  => '',
				      'tabindex' => 10,
				      'class'    => 'cluster-toolbar'
			      ),
			      'title' => 'Cloud',
			      'href'  => $this->cluster_domain . '/manage/admin.php?page=cluster#panel=networks'
		      ) );

		      $wp_admin_bar->add_menu( array(
			      'parent' => 'cloud-manager',
			      'id'     => 'cloud-policy',
			      'meta'   => array(),
			      'title'  => 'Policy',
			      'href'   => $this->cluster_domain . '/manage/admin.php?page=cluster#panel=policy'
		      ) );

		      $wp_admin_bar->add_menu( array(
			      'parent' => 'cloud-manager',
			      'id'     => 'cluster-dns',
			      'meta'   => array(),
			      'title'  => 'DNS',
			      'href'   => $this->cluster_domain . '/manage/admin.php?page=cluster#panel=dns'
		      ) );

	      }

      }

      /**
       * Added by anton.
       *
       * @note Could also use can_edit_network().
       */
      public function init() {

        // add_action( 'admin_menu', array( $this, '_admin_menu' ), 8 );
        // add_action( 'network_admin_menu', array( $this, '_admin_menu' ), 8 );

      }

      /**
       *
       */
      public function admin_enqueue_scripts() {
        wp_enqueue_style( 'wp-cluster', plugins_url( '/static/styles/wp-cluster.css', dirname( __FILE__ ) ), array(), self::$version );
      }

      /**
       * Add "ID" and "Thumbnail" columns to Network Sites table.
       *
       * @todo Use register_column_headers();
       *
       * @param $sites_columns
       *
       * @return mixed
       */
      public function wpmu_blogs_columns( $sites_columns ) {

        // Insert ID at position.
        $sites_columns = array_merge( array_slice( $sites_columns, 0, 1 ), array(
          'blog_id' => __( 'ID', self::$text_domain ),
        ), array_slice( $sites_columns, 1 ) );

        // Insert Thumbnail at position.
        $sites_columns = array_merge( array_slice( $sites_columns, 0, 10 ), array(
          'thumbnail' => __( 'Thumbnail', self::$text_domain )
        ), array_slice( $sites_columns, 10 ) );

        return $sites_columns;

      }

      /**
       * Dispaly "ID" and "Thumbnail" cells on Network Sites table.
       *
       * @param $column_name
       * @param $blog_id
       */
      public function manage_sites_custom_column( $column_name, $blog_id ) {

        switch( $column_name ) {

          case 'blog_id':
            echo '<p>' . $blog_id . '</p>';
            break;

          case 'thumbnail':
            echo '<img src="" class="cluster-site-thumbnail"/>';
            break;

        }

      }

      /**
       * Network Administration Menu.
       *
       * network_admin_url( 'cluster-icon.png' )
       */
      public function _admin_menu() {

        // Add Network Administration to Network and Site.
        add_submenu_page( 'settings.php', __( 'Site Provisioning', self::$text_domain ), __( 'Site Provisioning', self::$text_domain ), 'manage_network', 'network/settings.php?sites', array( $this, 'network_settings' ) );
        add_submenu_page( 'settings.php', __( 'Themes', self::$text_domain ), __( 'Themes', self::$text_domain ), 'manage_network', 'network/themes.php?security', array( $this, 'network_settings' ) );
        add_submenu_page( 'settings.php', __( 'Policy Delegation', self::$text_domain ), __( 'Policy Delegation', self::$text_domain ), 'manage_network', 'network/settings.php?policy', array( $this, 'network_settings' ) );
        add_submenu_page( 'settings.php', __( 'Security', self::$text_domain ), __( 'Security', self::$text_domain ), 'manage_network', 'network/settings.php?security', array( $this, 'network_settings' ) );

        // Site Only.
        if( current_filter() === 'admin_menu' ) {

          // Remove Native Site Sections.
          //remove_submenu_page( 'index.php', 'my-sites.php' );
	        //remove_submenu_page( 'index.php', 'my-networks' );
	        //remove_submenu_page( 'tools.php', 'ms-delete-site.php' );

          // Add Network Administration.
          add_options_page( __( 'DNS', self::$text_domain ), __( 'DNS', self::$text_domain ), 'manage_network', 'network-dns', array( $this, 'network_settings' ) );
          add_options_page( __( 'Domains', self::$text_domain ), __( 'Domains', self::$text_domain ), 'manage_network', 'network-dns', array( $this, 'network_settings' ) );

          // Add Network Administration to Network and Site.
          add_menu_page( 'Network', 'Network', 'manage_network', 'settings.php', array( $this, 'network_settings' ) );
          add_submenu_page( 'settings.php', __( 'Options', self::$text_domain ), __( 'Options', self::$text_domain ), 'manage_network', 'cloud-settings', array( $this, 'network_settings' ) );

        }

        // Network Only.
        if( current_filter() === 'network_admin_menu' ) {

	        // remove_submenu_page( 'settings.php', 'setup.php' );
          // remove_submenu_page( 'settings.php', 'settings.php' );

          // Remove Native Network Settings.
	        // remove_menu_page( 'update-core.php' );
	        // remove_menu_page( 'sites.php' );

          add_submenu_page( 'settings.php', __( 'Manage Users', self::$text_domain ), __( 'Manage Users', self::$text_domain ), 'manage_network', 'network-policy', array( $this, 'network_settings' ) );

        }


      }

      /**
       * Cluster Settings Page
       *
       */
      public function network_settings() {

        if( file_exists( dirname( __DIR__ ) . '/views/settings-network.php' ) ) {
          include( dirname( __DIR__ ) . '/views/settings-network.php' );
        }

      }

      /**
       * Site Settings Page
       *
       */
      public function site_settings() {

        if( file_exists( dirname( __DIR__ ) . '/views/settings-site.php' ) ) {
          include( dirname( __DIR__ ) . '/views/settings-site.php' );
        }

      }
      /**
       * Identify Request
       *
       * Currenty not used and handled by sunrise.php
       *
       * @method identify_site
       */
      public function identify_site() {
        global $site_id, $blog_id, $wpdb, $current_blog, $current_site;

        $_host = $_SERVER[ 'HTTP_HOST' ];

        $_lookup = $wpdb->get_row( "SELECT * FROM {$wpdb->blogs} WHERE domain = '{$_host}' LIMIT 1" );

        if( $_lookup ) {
          $blog_id               = $wpdb->blogid = $_lookup->blog_id;
          $site_id               = $wpdb->siteid = $_lookup->site_id;
          $current_site          = $wpdb->get_row( "SELECT * from {$wpdb->site} WHERE id = '{$site_id}' LIMIT 0,1" );
          $current_site->blog_id = $blog_id;
        }

      }

      /**
       * Initializer.
       *
       * @method plugins_loaded
       * @author potanin@UD
       */
      public function plugins_loaded() {

	      // Initialize Settings.
	      $this->set( array(
		      "store" => "options",
		      "key"   => "ud:cluster",
	      ) );

	      // ElasticSearch Service Settings.
	      $this->set( 'documents', array(
		      "active" => true,
		      "host"   => "localhost",
		      "port"   => 9200,
		      "token"  => null,
	      ) );


	      // Render Toolbar.
	      add_action( 'wp_before_admin_bar_render', array( $this, 'toolbar_menu' ), 10 );

	      add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 21 );
	      add_action( 'add_admin_bar_menus', array( $this, 'add_admin_bar_menus' ), 21 );

	      add_action( 'init', array( $this, 'init' ) );
	      add_action( 'admin_init', array( $this, 'admin_init' ) );

	      // Send Cluster Headers.
	      add_action( 'init', array( $this, '_send_headers' ) );

	      // Add Cluster Scripts & Styles.
	      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20 );

	      // Modify Core UI.
	      add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
	      add_action( 'manage_sites_custom_column', array( $this, 'manage_sites_custom_column' ), 10, 2 );

	      add_filter( 'pre_update_option_rewrite_rules', array( $this, '_update_option_rewrite_rules' ), 1 );

	      // /manage/admin-ajax.php?action=cluster_uptime_status
	      add_action( 'wp_ajax_cluster_uptime_status', array( $this, '_uptime_status' ) );
	      add_action( 'wp_ajax_nopriv_cluster_uptime_status', array( $this, '_uptime_status' ) );

	      add_action( 'wp_ajax_nopriv_varnish_test', array( $this, '_varnish_test' ) );
	      add_action( 'wp_ajax_varnish_test', array( $this, '_varnish_test' ) );

	      add_filter( 'wp_mail_from', array( 'UsabilityDynamics\Cluster\Utility', 'wp_mail_from' ), 10 );
	      add_filter( 'wp_mail_from_name', array( 'UsabilityDynamics\Cluster\Utility', 'wp_mail_from_name' ), 10 );

	      // remove_role( 'manage.stuff' );
        // $result = add_role( 'manage.stuff', __( 'Manage Stuff' ), array( "manage.stuff.view" => true, "manage.stuff.edit" => true ) );
        // $role = get_role( 'manage.stuff' );
        // $current_user = wp_get_current_user();
        // $current_user->add_role( 'administrator' );
        // $current_user->add_role( 'manage.stuff' );
        // die( '<pre>' . print_r( $current_user , true ) . '</pre>' );

        // add_action( 'shutdown', array( $this, 'shutdown' ) );
        // add_action( 'init', array( $this, 'init' ) );
        // add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        // add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
        // add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );

      }

      /**
       * Modify Rewrite Rules on Save.
       *
       * @todo Fix rule - does not seem to work.
       *
       * @param $rules
       *
       * @internal param $value
       * @return array
       */
      public function _update_option_rewrite_rules( $rules ) {

        // Define New Rules.
        $new_rules = array(
          '^api/uptime-status/?' => str_replace( trailingslashit( site_url() ), '', admin_url( 'admin-ajax.php?action=cluster_uptime_status' ) ),
        );

        // Return concatenated rules.
        return (array) $new_rules + (array) $rules;

      }

      /**
       *
       */
      public function _uptime_status() {

        ob_start( array( $this, '_render_status' ) );

      }

      /**
       * Renders on "shutdown" filter.
       *
       */
      public function _render_status( $buffer, $errstr, $errfile, $errline, $errcontext ) {
        global $wp, $wpdb;

        $have_error = false;

        //trigger_error("Cannot divide by zero", E_USER_ERROR);

        if( $error = error_get_last() ) {
          switch( $error[ 'type' ] ) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
              $have_error = true;
              break;
          }
        }

        header( "Content-type: application/json; charset: UTF-8" );
        header( "Cache-Control: private, must-revalidate" );

        if( !$have_error ) {
          header( ':', true, 200 );
        } else {
          header( ':', true, 200 );
        }

        $buffer = json_encode( array(
          "ok"      => true,
          "message" => $have_error ? sprintf( __( "Error occured. Message: %s", self::$text_domain ), $error[ 'message' ] ) : __( "Service is fully operational.", self::$text_domain ),
          "took"    => timer_stop(),
          "stats"   => array(
            "queries" => $wpdb->num_queries
          )
        ) );

        return $buffer;

      }

	    /**
	     * Enable CLI, if available.
	     *
	     * @method _cli
	     * @author potanin@UD
	     */
	    public function _cli() {

		    if( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' ) && class_exists( 'WP_CLI_Command' ) ) {

			    include_once( __DIR__ . DIRECTORY_SEPARATOR . 'class-cli.php' );

			    if( class_exists( 'UsabilityDynamics\Cluster\CLI' ) ) {
				    \WP_CLI::add_command( 'cluster', 'UsabilityDynamics\Cluster\CLI' );
			    }

		    }

	    }

	    /**
       * Add Frontend Headers
       *
       */
      public function _send_headers() {

        if( !headers_sent() ) {
          header( 'X-Server: WP-Veneer ' . Bootstrap::$version );
          header( 'X-Powered-By: WP-Cluster ' . Bootstrap::$version );
          header( 'X-Cluster-Version:' . Bootstrap::$version );
        }

      }

      /**
       * Initialize Admin
       *
       * @method admin_init
       * @author potanin@UD
       */
      public function admin_init() {
        remove_action( 'admin_notices', 'akismet_warning' );
        remove_action( 'admin_notices', 'bp_core_update_nag', 5 );
        remove_action( 'network_admin_notices', 'bp_core_update_nag', 5 );
      }

      /**
       * Modify Admin Bar Menus Hooks.
       *
       * @param bool $wp_admin_bar
       */
      public function add_admin_bar_menus( $wp_admin_bar = false ) {

        if( $this->get( 'hide.toolbar.login' ) ) {
          remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
          remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
        }

      }

	    /**
	     * Change My Account Toolbar Dropdown.
	     *
	     * @method admin_bar_menu
	     * @author potanin@UD
	     *
	     * @param bool $wp_admin_bar
	     */
      public function my_account_toolbar( $wp_admin_bar = false ) {

        $user_id      = get_current_user_id();
        $current_user = wp_get_current_user();
        $profile_url  = get_edit_profile_url( $user_id );

        $wp_admin_bar->remove_node( 'my-account' );

        $wp_admin_bar->add_menu( array(
          'id'     => 'my-account',
          'parent' => 'top-secondary',
          'title'  => sprintf( __( '%1$s' ), $current_user->display_name ),
          'href'   => $profile_url,
          'meta'   => array(
            'class' => isset( $class ) ? $class : '',
            'title' => __( 'My Account' ),
          ),
        ) );

      }

      /**
       * Update Amin Menu
       *
       * @method admin_bar_menu
       * @author potanin@UD
       */
      public function admin_bar_menu( $wp_admin_bar = false ) {

        if( !is_super_admin() || !is_multisite() || !$wp_admin_bar ) {
          return;
        }

        $wp_admin_bar->add_menu( array(
          'parent' => 'network-admin',
          'id'     => 'cloud-settings',
          'title'  => __( 'Settings', self::$text_domain ),
          'href'   => network_admin_url( 'settings.php' ),
        ) );

      }

      /**
       * Testing Varnish
       *
       * @temp
       */
      public static function _varnish_test() {

        // AJAX Request.
        if( Utility::requestHeaders()->{'X-Requested-With'} === "XMLHttpRequest" ) {

        }

        // Veneer/Varnish API Proxy.
        if( Utility::requestHeaders()->{'X-Veneer-Proxy'} === "true" ) {

        }

        // must have Cache-Control header
        header( "Cache-Control: public, must-revalidate, max-age=2592000" );

        // header( "Pragma: public" );
        // header( "Vary: Accept-Encoding" );
        // header( "Content-Type: application/json" );
        // header( 'Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000 ) . ' GMT' );
        // header( "Content-Length: application/json" );
        // header( "X-Api-Response: true" );

        die( json_encode( array(
          "varnish-request-id" => Utility::requestHeaders()->{'X-Varnish'},
          "request-headers"    => Utility::requestHeaders(),
          "ok"                 => true,
          "message"            => __( 'Hello!' ),
          "data"               => array(
            "key" => "value"
          )
        ) ) );

      }

      /**
       * Network URL
       *
       *
       * @author potanin@UD
       * @method network_site_url
       *
       * @param $url
       *
       * @return mixed
       */
      public static function network_site_url( $url ) {
        return str_replace( '/wp-admin', '/manage', $url );
      }

      /**
       * Manage URL
       *
       *
       * @author potanin@UD
       * @method network_site_url
       *
       * @param $url
       *
       * @return mixed
       */
      public static function admin_url( $url ) {

        if( defined( 'WP_SYSTEM_DIRECTORY' ) ) {
          return str_replace( '/system/wp-admin', '/manage', $url );
        } else {
          return str_replace( '/wp-admin', '/manage', $url );
        }

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

}
