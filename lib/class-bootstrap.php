<?php
/**
 * UsabilityDynamics\Veneer Bootstrap
 *
 * ### Options
 * * cache.enabled
 * * scripts.outline.enabled
 *
 * * media.shard.enabled
 * * media.path.public
 * * media.shard.subdomain
 *
 * * assets.shard.enabled
 * * assets.shard.subdomain
 * * scripts.path.public
 * * html.minify.enabled
 *
 * @todo Use extract_from_markers and insert_with_markers methods to insert/udpate .htaccess rewrites.
 *
 * @verison 0.6.1
 * @author potanin@UD
 * @namespace UsabilityDynamics\Veneer
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Bootstrap' ) ) {

    /**
     * Bootstrap Veneer
     *
     * @class Bootstrap
     * @author potanin@UD
     * @version 0.0.1
     */
    class Bootstrap {

      /**
       * Veneer core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.7.0';

      /**
       * Textdomain String
       *
       * @public
       * @property text_domain
       * @var string
       */
      public static $text_domain = 'wp-veneer';

      /**
       * Site Domain
       *
       * @public
       * @property $site
       * @type {Object}
       */
      public $site = null;

      /**
       * Site Root Domain
       *
       * Used as basis for media, assets, cdn subdomains.
       *
       * @public
       * @property $apex
       * @type {Object}
       */
      public $apex = null;

      /**
       * Site ID
       *
       * @public
       * @property $site_id
       * @type {Integer}
       */
      public $site_id = null;

      /**
       * Network Domain
       *
       * @public
       * @property $network
       * @type {Object}
       */
      public $network = null;

      /**
       * Veneer Cache Instance.
       *
       * @public
       * @property $_cache
       * @type {Object}
       */
      private $_cache;

      /**
       * Settings Instance.
       *
       * @property $_settings
       * @type {Object}
       */
      private $_settings;

      /**
       * Veneer Documents Instance.
       *
       * @property $_documents
       * @type {Object}
       */
      private $_documents;

      /**
       * Veneer Log Instance.
       *
       * @property $_log
       * @type {Object}
       */
      private $_log;

      /**
       * Veneer Media Instance
       *
       * @property $media
       * @type {Object}
       */
      private $_media = null;

      /**
       * Veneer Security Instance
       *
       * @property $_security
       * @type {Object}
       */
      private $_security = null;

      /**
       * Veneer Varnish Instance.
       *
       * @property $_varnish
       * @type {Object}
       */
      private $_varnish = null;

      /**
       * CloudFront
       *
       * @property
       * @type {Object}
       */
      private $_cloud = null;

      /**
       * URL Rewrites
       *
       * @public
       * @property $_rewrites
       * @type {Object}
       */
      public $_rewrites = null;

      /**
       * Plugins handler
       *
       * @public
       */
      public $_plugins = null;

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
        global $wpdb, $current_blog, $wp_veneer;

	      // Return Singleton Instance
        if( self::$instance ) {
          return self::$instance;
        }

        // Check if being called too early, such as during Unit Testing.
        if( !function_exists( 'did_action' ) ) {
          return $this;
        }

        /** If we currently have a wp_veener object, we should copy it */
        if( isset( $wp_veneer ) && is_object( $wp_veneer ) && count( get_object_vars( $wp_veneer ) ) ) {
          foreach( array_keys( get_object_vars( $wp_veneer ) ) as $key ) {
            $this->{$key} = $wp_veneer->{$key};
          }
        }

        /** Set the singleton instance */
        $wp_veneer = self::$instance = & $this;

        /** Make sure we're not too late to init this */
        if( did_action( 'init' ) ) {
          _doing_it_wrong( 'UsabilityDynamics\Veneer\Bootstrap::__construct', 'Veneer should not be initialized before "init" filter.', '0.6.1' );
        }

	      // Requires $this->site to be defined, therefore being ignored on single-site installs.
        if( defined( 'MULTISITE' ) && MULTISITE && $wpdb->site ) {
          $this->site    = $wpdb->get_var( "SELECT domain FROM {$wpdb->blogs} WHERE blog_id = '{$wpdb->blogid}' LIMIT 1" );
          $this->network = $wpdb->get_var( "SELECT domain FROM {$wpdb->site}  WHERE id = {$wpdb->siteid}" );
        } else {
          $this->site    = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
          $this->network = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
          $this->apex    = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
        }

        $this->site_id = $wpdb->siteid;
        $this->apex    = isset( $current_blog->apex ) ? $current_blog->apex : $apex = str_replace( "www.", '', $this->site );

	      // Initialize Settings.
	      $this->_settings();

	      /** Initialize Components. */
        $this->_components();

        //add_action( 'setup_theme', array( $this, 'setup_theme' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_head', array( $this, 'wp_head' ), 0, 200 );
        // add_filter( 'theme_root', array( $this, 'theme_root' ), 10 );
	      // add_filter( 'flush_rewrite_rules_hard', array( $this, 'flush_rewrite_rules_hard' ));

        // Initialize Interfaces.
        $this->_interfaces();

	      // Enable Storage.
	      $this->_storage();

	      $this->_install();

	      ob_start( array( $this, 'ob_start' ) );

      }

      /**
       * Fix Vendor Theme Path
       *
       * This only seem to be an issue for the default (WP_DEFAULT_THEME) theme.
       *
       * @since 0.6.2
       * @author potanin@UD
       * @method theme_root
       *
       * @param $theme_root
       *
       * @return mixed
       */
      public function theme_root( $theme_root = '' ) {

        if( defined( 'WP_THEMES_DIR' ) && strpos( WP_THEMES_DIR, '/vendor' ) && !strpos( $theme_root, '/vendor' ) ) {
          $theme_root = str_replace( '/themes', '/vendor/themes', $theme_root );
        }

        return $theme_root;

      }

      /**
       *
       *
       *
       */
      public function _admin_menu() {

        // Site Only.
        if( current_filter() === 'admin_menu' ) {

          // Remove Native Site Sections.
          // remove_submenu_page( 'index.php', 'my-sites.php' );

          // Add Network Administration.
          add_options_page( __( 'Services', self::$text_domain ), __( 'Services', self::$text_domain ), 'manage_network', 'network-policy', array( $this, 'site_settings' ) );
          add_options_page( __( 'CDN', self::$text_domain ), __( 'CDN', self::$text_domain ), 'manage_network', 'network-policy', array( $this, 'site_settings' ) );

        }

        // Network Only.
        if( current_filter() === 'network_admin_menu' ) {
          // Remove Native Network Settings.
          // remove_menu_page( 'sites.php' );
        }

        // Add Network Administration to Network and Site.
        add_submenu_page( 'settings.php', __( 'API Settings', self::$text_domain ), __( 'API Settings', self::$text_domain ), 'manage_network', 'network-dns', array( $this, 'network_settings' ) );
        add_submenu_page( 'index.php', __( 'Reports', self::$text_domain ), __( 'Reports', self::$text_domain ), 'manage_network', 'network-policy', array( $this, 'network_settings' ) );

      }

      /**
       * Site Settings
       *
       */
      public function site_settings() {

        if( file_exists( dirname( __DIR__ ) . '/static/views/settings-site.php' ) ) {
          include( dirname( __DIR__ ) . '/static/views/settings-site.php' );
        }

      }

      /**
       * Network Settings
       *
       */
      public function network_settings() {

        if( file_exists( dirname( __DIR__ ) . '/static/views/settings-network.php' ) ) {
          include( dirname( __DIR__ ) . '/static/views/settings-network.php' );
        }

      }

      /**
       * Outline Scripts and Styles.
       *
       * https://developers.google.com/speed/pagespeed/module/filter-js-outline
       *
       * @param null   $buffer
       * @param string $type
       *
       * @return string
       */
      public function _outline( $buffer = null, $type = 'script' ) {

        // Will extract all JavaScript from page.
        if( class_exists( 'phpQuery' ) ) {

          $doc = \phpQuery::newDocumentHTML( $buffer );

          if( function_exists( 'pq' ) ) {
            $scripts = pq( 'script:not([data-main])' );
          }

          $_output = array();

          // @todo Write extracted Scripts to an /asset file to be served.
          foreach( $scripts as $script ) {
            $_output[ ] = $script;
          }

          // Remove all found <script> tags.
          $scripts->remove();

          if( function_exists( 'is_user_logged_in' ) && !is_user_logged_in() ) {
            // @todo Write generated app.config.js file to to static /assets cache.
          }

          // Return HTML without tags.
          return $doc->document->saveHTML();

        }

      }

      /**
       * Set Headers
       *
       * @todo Etag should be a lot more sophistiacted and take into account actual content changes.
       *
       * @author potanin@UD
       */
      public function wp_head() {

        $modified_since = ( isset( $_SERVER[ "HTTP_IF_MODIFIED_SINCE" ] ) ? strtotime( $_SERVER[ "HTTP_IF_MODIFIED_SINCE" ] ) : false );
        $etagHeader     = ( isset( $_SERVER[ "HTTP_IF_NONE_MATCH" ] ) ? trim( $_SERVER[ "HTTP_IF_NONE_MATCH" ] ) : false );

        $meta = array(
          'etag'          => md5( time() ),
          'x-server'      => 'wp-veneer/v' . self::$version,
          'public'        => 'true',
          'cache-control' => 'max-age=3600, must-revalidate',
          'last-modified' => gmdate( "D, d M Y H:i:s", time() ) . " GMT"
        );

        foreach( (array) $meta as $key => $value ) {
          //printf( "\n\t" . '<meta http-equiv="%s" content="%s" />', $key, $value );
        }

      }

	    /**
	     * Handle Caching and Minification
	     *
	     * @todo Add logging.
	     *
	     * @mehod cache
	     * @author potanin@UD
	     *
	     * @param $buffer
	     *
	     * @return mixed|void
	     */
      public function ob_start( &$buffer ) {
        global $post, $wp_query;

        // @note Trying to fix weird encoding issue in back-end <script> tag.
        $buffer = str_replace( '%5B%5D', '[]', $buffer );

        // @note thro exception to abort rest of ob_start from a filter.
        try {
          $buffer = apply_filters( 'wp-veneer:ob_start', $buffer, $this );
        } catch( \Exception $e ) {
          return $buffer;
        }

        // Remove W3 Total Cache generic text.
        $buffer = str_replace( "Performance optimized by W3 Total Cache. Learn more: http://www.w3-edge.com/wordpress-plugins/", 'Served from', $buffer );
        $buffer = str_replace( "\n\r\n Served from:", '', $buffer );
        $buffer = str_replace( 'by W3 Total Cache ', '', $buffer );

        // Never cached logged in users.
        if( function_exists( 'is_user_logged_in' ) && is_user_logged_in() ) {
          return $buffer;
        }

        // Ignore CRON requests.
        if( isset( $_GET[ 'doing_wp_cron' ] ) && $_GET[ 'doing_wp_cron' ] ) {
          return $buffer;
        }

        // Do not cache search results.
        if( is_search() ) {
          return $buffer;
        }

        // Ignore 404 pages.
        if( is_404() ) {
          return $buffer;
        }

        // Bail on Media and Assets.
        if( is_attachment() ) {
          return $buffer;
        }

        // Bypass non-get requests.
        if( $_SERVER[ 'REQUEST_METHOD' ] !== 'GET' ) {
          return $buffer;
        }

        // Always bypass AJAX and CRON Requests.
        if( ( defined( 'DOING_CRON' ) && DOING_CRON ) && ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
          return $buffer;
        }

        // Media Domain Sharding.
        if( $this->get( 'media.shard.enabled' ) ) {
          $buffer = str_replace( "//{$this->site}/" . $this->get( 'media.path.public' ) . "/", "//" . $this->get( 'media.shard.subdomain' ) . ".{$this->apex}/", $buffer );
        }

        // Asset Domain Sharding.
        if( $this->get( 'assets.shard.enabled' ) ) {
          $buffer = str_replace( "//{$this->site}/" . $this->get( 'scripts.path.public' ) . "/", "//" . $this->get( 'assets.shard.subdomain' ) . ".{$this->apex}/", $buffer );
        }

        // Outline and AMD JavaScript Assets.
        if( $this->get( 'scripts.outline.enabled' ) && $_response = $this->_outline( $buffer, 'scripts' ) ) {
          $buffer = $_response;
        }

        // Minify HTML Output.
        if( $this->get( 'html.minify.enabled' ) ) {
          $buffer = Cache::minify_html( $buffer );
        }

        // Save static HTML cache.
        if( $this->get( 'cache.enabled' ) && $this->get( 'cache.available' ) && $this->get( 'cache.path.disk' ) ) {

          $_info = pathinfo( $_SERVER[ 'REQUEST_URI' ] );

          $_parts = array(
            untrailingslashit( $this->get( 'cache.path.disk' ) ),
            trailingslashit( $_info[ 'dirname' ] ),
            $_info[ 'filename' ] ? $_info[ 'filename' ] : 'index',
            in_array( $_info[ 'extension' ], array( 'html', 'htm' ) ) ? $_info[ 'extension' ] : '.html'
          );

          $_path = implode( '', $_parts );

          if( !wp_mkdir_p( dirname( $_path ) ) ) {
            return $buffer;
          }

          // Don't cache blank results.
          if( !trim( $buffer ) ) {
            return $buffer;
          }

          // @todo Single post object detected.
          // if( is_single() ) { }

          // Write Cached Page.
          file_put_contents( $_path, $buffer );

        }

        return $buffer;

      }

	    /**
	     * Ran before theme is setup.
	     *
	     * This is the last action before wp_templating_constants() method is called in wp-settings.php which sets the following consants:
	     * - TEMPLATEPATH
	     * - STYLESHEETPATH
	     * - WP_DEFAULT_THEME (if not set)
	     *
	     * The theme's functions.php is loaded next, followed by "after_setup_theme" action.
	     *
	     * @method setup_theme
	     */
      public function setup_theme() {
	      global $wp_theme_directories;

	      if( defined( 'WP_THEME_DIR' ) && is_dir( WP_THEME_DIR ) ) {
		      //register_theme_directory( WP_THEME_DIR );
	      }

	      if( defined( 'WP_VENEER_THEME_DIR' ) && is_dir( WP_VENEER_THEME_DIR ) ) {
		      //register_theme_directory( WP_VENEER_THEME_DIR );
	      }

	      // Only register default theme directory if no other directories are registerd
	      if( !$wp_theme_directories || (is_array( $wp_theme_directories ) && empty( $wp_theme_directories ) ) && is_dir( ABSPATH . 'wp-content/themes' ) ) {
		      register_theme_directory( ABSPATH . 'wp-content/themes' );
	      }

	      // Only admin can see W3TC notices and errors
        // add_action('admin_notices', array( $this, 'admin_notices' ));
        // add_action('network_admin_notices', array( $this, 'admin_notices' ));

	      // @note Disabled until actual UIs are ready.
        // add_action( 'admin_menu', array( $this, '_admin_menu' ), 8 );
        // add_action( 'network_admin_menu', array( $this, '_admin_menu' ), 8 );

      }

      /**
       * Minify Output.
       */
      public function enqueue_scripts() {

        if( is_user_logged_in() ) {
          wp_enqueue_style( 'wp-veneer', plugins_url( '/static/styles/wp-veneer.css', dirname( __FILE__ ) ) );
        };

      }

	    /**
	     * Need to add logic to append BEFORE the WordPress markers are added, in the meantime need to manually reposition
	     *
	     * @param $setting
	     *
	     * @return mixed
	     */
	    public function flush_rewrite_rules_hard( $setting ) {

		    if( !is_admin() ) {
		      return;
		    }

		    $htaccess_file = wp_normalize_path( get_home_path() . '/.htaccess' );

		    insert_with_markers( $htaccess_file, 'Veneer Static Media', array(
			    "<IfModule mod_rewrite.c>",
			    " RewriteEngine On",
			    " RewriteBase /",

			    " RewriteRule ^wp-content/uploads/(.*)$ /media/$1 [L]",
			    " RewriteRule ^files/(.*)$ /media/$1 [L]",
			    " RewriteRule ^uploads/(.*)$ /media/$1 [L]",

			    " RewriteCond %{REQUEST_FILENAME} !-f",
			    " RewriteCond %{REQUEST_FILENAME} !-d",
			    " RewriteCond %{HTTP_HOST}::%{REQUEST_URI} ^(.*)::/(.*)$",
			    " RewriteCond %{DOCUMENT_ROOT}/wp-content/storage/%1/static/%2 -f",
			    " RewriteRule ^(.*) /wp-content/storage/%1/static/%2 [L]",

			    " RewriteCond %{REQUEST_FILENAME} !-f",
			    " RewriteCond %{REQUEST_FILENAME} !-d",
			    " RewriteCond %{HTTP_HOST}::%{REQUEST_URI} ^(.*)::/(.*)$",
			    " RewriteCond %{DOCUMENT_ROOT}/wp-content/storage/%1/%2 -f",
			    " RewriteRule ^.*$ /wp-content/storage/%1/%2 [L]",

			    " RewriteCond %{REQUEST_FILENAME} !-f",
			    " RewriteCond %{REQUEST_FILENAME} !-d",
			    " RewriteCond %{HTTP_HOST}::%{REQUEST_URI} ^(assets|media|cache|static)\\.(.*)::/(?:assets|media|cache|static)?/?(.*)$",
			    " RewriteCond %{DOCUMENT_ROOT}/wp-content/storage/%2/%1/%3 -f",
			    " RewriteRule ^.*$ /wp-content/storage/%2/%1/%3 [L]",

			    " RewriteCond %{REQUEST_FILENAME} !-f",
			    " RewriteCond %{REQUEST_FILENAME} !-d",
			    " RewriteCond %{HTTP_HOST}::%{REQUEST_URI} ^(.*)::/(.*)$",
			    " RewriteCond %{DOCUMENT_ROOT}/wp-content/storage/%1/static/%2index.html -f",
			    " RewriteRule ^.*$ /wp-content/storage/%1/static/%2index.html [NC,QSA,L]",

			    " RewriteCond %{REQUEST_FILENAME} !-f",
			    " RewriteCond %{REQUEST_FILENAME} !-d",
			    " RewriteCond %{HTTP_HOST}::%{REQUEST_URI} ^(.*)::/(.*)$",
			    " RewriteCond %{DOCUMENT_ROOT}/wp-content/storage/%1/static/%2.html -f",
			    " RewriteRule ^.*$ /wp-content/storage/%1/static/%2.html [L]",

			    " RewriteCond %{REQUEST_FILENAME} !-f",
			    " RewriteCond %{DOCUMENT_ROOT}/wp-content/storage/%{HTTP_HOST}/static/index\\.html -f",
			    " RewriteCond %{QUERY_STRING} ^$",
			    " RewriteRule ^$ /wp-content/storage/%{HTTP_HOST}/static/index.html [L]",
			    "</IfModule>"
		    ));

		    insert_with_markers( $htaccess_file, 'Veneer Assets', array(
			    "<IfModule mod_rewrite.c>",
			    " RewriteEngine On",
			    " RewriteBase /",
			    " RewriteRule ^assets/styles/(.*)$  /wp-includes/css/$1 [QSA,L]",
			    " RewriteRule ^assets/scripts/(.*)$ /wp-includes/js/$1 [QSA,L]",
			    " RewriteRule ^assets/images/(.*)$  /wp-includes/images/$1 [QSA,L]",
			    "</IfModule>"
		    ));

		    insert_with_markers( $htaccess_file, 'Veneer Management Access ', array(
			    "<IfModule mod_rewrite.c>",
			    " RewriteEngine On",
			    " RewriteBase /",
			    " RewriteRule ^manage$                         /manage/ [R=301,L]",
			    " RewriteRule ^manage/(login|signup)/?(.*)$    /wp-$1.php [QSA,L]",
			    " RewriteRule ^manage/?(.*)$                   /wp-admin/$1 [QSA,L]",
			    "</IfModule>"
		    ));

		    insert_with_markers( $htaccess_file, 'Veneer Public API', array(
			    "<IfModule mod_rewrite.c>",
			    " RewriteEngine On",
			    " RewriteBase /",
			    " RewriteCond %{HTTP_HOST}          ^api\\..*$",
			    " RewriteRule ^documents/v1/(.*)$   http://api.veneer.io/documents/$1 [P,L]",
			    " RewriteCond %{HTTP_HOST}          ^api\\..*$",
			    " RewriteRule ^search/v1/(.*)$      http://api.veneer.io/search/$1 [P,L]",
			    " RewriteCond %{HTTP_HOST}          ^api\\..*$",
			    " RewriteRule ^documents/(.*)$      http://api.veneer.io/documents/$1 [P,L]",
			    " RewriteCond %{HTTP_HOST}          ^api\\..*$",
			    " RewriteRule ^search/(.*)$         http://api.veneer.io/search/$1 [P,L]",
			    "</IfModule>"
		    ));

		    insert_with_markers( $htaccess_file, 'Veneer WordPress API', array(
			    "<IfModule mod_rewrite.c>",
			    " RewriteEngine On",
			    " RewriteBase /",
			    " RewriteCond %{REQUEST_FILENAME} !-f",
			    " RewriteCond %{REQUEST_FILENAME} !-d",
			    " RewriteCond %{REQUEST_URI} !^.*admin-ajax\\.php.*$",
			    " RewriteRule ^xmlrpc.php$    /xmlrpc.php [QSA,L]",
			    " RewriteRule ^api/rpc.xml$   /xmlrpc.php [QSA,L]",
			    " RewriteRule ^api/rpc.json$  /wp-admin/admin-ajax.php?action=/api/json-rpc [QSA,L]",
			    " RewriteRule ^api/(.*).xml$  /wp-admin/admin-ajax.php?action=/$1&format=xml [QSA,L]",
			    " RewriteRule ^api/(.*).json$ /wp-admin/admin-ajax.php?action=/$1&format=json [QSA,L]",
			    "</IfModule>"
		    ));

		    return $setting;

	    }

	    /**
	     * Copy Files.
	     *
	     * @todo Trigger this on plugin activation, not on every request.
	     * @return array
	     */
	    public function _install() {

		    if( is_admin() ) {
			    //wp_die('_install');
			    //define( 'WP_INSTALLING_NETWORK', true );
			    //require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			    //dbDelta( wp_get_db_schema( 'global' ) );
		    }

		    $_files = array(
			    // __DIR__ . '/class-config.php' => ABSPATH . '/wp-config.php',
			    // __DIR__ . '/class-advanced-cache.php' => WP_CONTENT_DIR . '/advanced-cache.php',
			    // __DIR__ . '/class-object-cache.php' => WP_CONTENT_DIR . '/object-cache.php'
		    );

		    $_errors = array();

		    foreach( $_files as $_source => $_destination ) {

			    if( file_exists( $_destination )  ) {
				    continue;
			    }

			    try {

				    if( !is_writable( dirname( $_destination  ) ) ) {
					    throw new \Exception( __( 'Destination is not writable, unable to install wp-cluster.', 'wp-veneer' ) );
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
       * Initialize Settings.
       *
       */
      private function _settings() {

	      if( class_exists( '\UsabilityDynamics\Settings' ) ) {

		      // Initialize Settings.
		      $this->_settings = new \UsabilityDynamics\Settings( array(
			      "store" => "options",
			      "key"   => "ud:veneer",
		      ));

	      }

        // ElasticSearch Service $instances.
        $this->set( 'documents', array(
          "active" => true,
          "host"   => "localhost",
          "port"   => 9200,
          "token"  => null,
        ) );

        // Varnish Service Settings.
        $this->set( 'varnish', array(
          "active" => false,
          "host"   => "localhost",
          "key"    => null
        ) );

        // CDN Service Settings.
        $this->set( 'media', array(
          "relative" => true
        ) );

        // CDN Service Settings.
        $this->set( 'cdn', array(
          "provider" => array(
            "active"   => false,
            "provider" => "cf",
            "key"      => null,
            "secret"   => null,
            "bucket"   => null
          )
        ) );

        $this->set( 'static.enabled', true );
        $this->set( 'cdn.enabled', true );
        $this->set( 'cache.enabled', true );

        $this->set( 'media.shard.enabled', false );
        $this->set( 'scripts.shard.enabled', false );
        $this->set( 'styles.shard.enabled', false );

        $this->set( 'media.shard.subdomain', 'media' );
        $this->set( 'scripts.shard.subdomain', 'assets' );
        $this->set( 'styles.shard.subdomain', 'assets' );

        $this->set( 'media.path.public', '/media' );
        $this->set( 'scripts.path.public', '/assets/scripts' );
        $this->set( 'styles.path.public', '/assets/styles' );

        $this->set( 'html.minify.enabled', false );

        $this->set( 'scripts.outline.enabled', false );

	      $this->set( 'rewrites.login', false );
	      $this->set( 'rewrites.assets', false );
	      $this->set( 'rewrites.manage', false );
	      $this->set( 'rewrites.api', false );

	      // Save Settings.
        $this->_settings->commit();

      }

      /**
       * Initialize Media, Varnish, etc.
       *
       */
      private function _components() {

        // Init our logging mechanism
        if( class_exists( 'UsabilityDynamics\Veneer\Log' ) && class_exists( 'Raygun4php\RaygunClient' ) ) {
          $this->_log = Log::init()->__construct();
        }

        // Init the api
        if( class_exists( 'UsabilityDynamics\Veneer\API' ) ) {
          $this->_api = API::init()->__construct();
        }

        // Init the plugin handler
        if( class_exists( 'UsabilityDynamics\Veneer\Plugins' ) ) {
          $this->_plugins = Plugins::init()->__construct();
        }

        // Initialize W3 Total Cachen Handlers.
        if( class_exists( 'UsabilityDynamics\Veneer\W3' ) ) {
          $this->_cache = new W3( $this->get( 'cache' ) );
        }

        // Enable CDN Media.
        if( class_exists( 'UsabilityDynamics\Veneer\Media' ) ) {
          $this->_media = new Media( $this->get( 'media' ) );
        }

        // Enable Varnish.
        if( class_exists( 'UsabilityDynamics\Veneer\Varnish' ) ) {
          $this->_varnish = new Varnish( $this->get( 'varnish' ) );
        }

        // Enable URL Rewrites.
        if( class_exists( 'UsabilityDynamics\Veneer\Rewrites' ) ) {
          $this->_rewrites = new Rewrites( $this->get( 'rewrites' ) );
        }

      }

	    private function _storage() {

		    // Create Public and Cache directories. Media directory created in Media class.
		    if( !defined( 'WP_VENEER_STORAGE' ) || !is_dir( WP_VENEER_STORAGE ) ) {
			    return;
		    }

		    // Use public directory if explicitly defined.
		    if( defined( 'WP_VENEER_PUBLIC' ) && WP_VENEER_PUBLIC ) {
			    $_public_path = trailingslashit( WP_VENEER_PUBLIC );
		    } else {
			    $_public_path = trailingslashit( WP_VENEER_STORAGE );
		    }

		    // Append the apex domain to storage path.
		    if( defined( 'MULTISITE' ) && MULTISITE ) {

			    if( !strpos( $_public_path, $this->site ) ) {
				    $_public_path = $_public_path . trailingslashit( $this->site );
			    }

		    }

		    $this->set( 'media.path.disk',    $_public_path . 'media' );
		    $this->set( 'assets.path.disk',   $_public_path . 'assets' );
		    $this->set( 'scripts.path.disk',  $_public_path . 'assets/scripts' );
		    $this->set( 'styles.path.disk',   $_public_path . 'assets/styles' );

		    if( $this->get( 'media.path.disk' ) && !wp_mkdir_p( $this->get( 'media.path.disk' ) ) ) {
			    $this->set( 'media.available', false );
		    }

		    if( $this->get( 'assets.path.disk' ) && !wp_mkdir_p( $this->get( 'assets.path.disk' ) ) ) {
			    $this->set( 'assets.available', false );
		    }

		    if( $this->get( 'scripts.path.disk' ) && !wp_mkdir_p( $this->get( 'scripts.path.disk' ) ) ) {
			    $this->set( 'scripts.available', false );
		    }

		    if( $this->get( 'styles.path.disk' ) && !wp_mkdir_p( $this->get( 'styles.path.disk' ) ) ) {
			    $this->set( 'styles.available', false );
		    }


	    }

      /**
       * Initialize Interface Compnents
       *
       */
      private function _interfaces() {

        // Render Toolbar.
        add_action( 'wp_before_admin_bar_render', array( $this, 'toolbar' ), 10 );

        $_SERVER[ 'REMOTE_ADDR' ] = isset( $_SERVER[ 'REMOTE_ADDR' ] ) ? $_SERVER[ 'REMOTE_ADDR' ] : '';
        
        add_action( 'wp_before_admin_bar_render', array( $this, 'toolbar_local' ), 100 );

      }

      /**
       * Administrative Toolbar.
       *
       * @author potanin@UD
       */
      public function toolbar_local(){
        global $wp_admin_bar;

        $_environment = defined( 'ENVIRONMENT' ) ? ENVIRONMENT : null;

        if( defined( 'WP_ENV' ) && !$_environment ) {
          $_environment = WP_ENV;
        }

        if( defined( 'PHP_ENV' ) && !$_environment ) {
          $_environment = PHP_ENV;
        }

	      $_ip = $_SERVER[ 'SERVER_ADDR' ];

	      if( $_ip === '::1' ) {
		      $_ip = 'localhost';
	      }

        if( current_user_can( 'manage_options' ) ) {
          /** Add the style we need */ ?>
          <style> #wp-admin-bar-server_name > a:before { content: "\f177"; top: 2px; }</style> <?php

          $wp_admin_bar->add_menu( array(
            'id' => 'server_name',
            'parent' => 'top-secondary',
            'title' => sprintf( __( 'Host: %s' ), gethostname() ),
            'href' => '#'
          ));

          $wp_admin_bar->add_menu( array(
            'id' => 'environment',
            'parent' => 'server_name',
            'title' => sprintf( __( 'Branch: %s' ), $_environment ),
            'href' => '#'
          ));

          $wp_admin_bar->add_menu( array(
            'id' => 'db_host',
            'parent' => 'server_name',
            'title' => sprintf( __( 'DB: %s/%s' ), DB_HOST, DB_NAME ),
            'href' => '#'
          ));

          $wp_admin_bar->add_menu( array(
            'id' => 'ip_address',
            'parent' => 'server_name',
            'title' => sprintf( __( 'IP: %s' ), $_ip ),
            'href' => '#'
          ) );

        }

      }

      /**
       * Shows Veneer Status (in dev)
       *
       * @method toolbar
       * @for Boostrap
       */
      public function toolbar() {
        global $wp_admin_bar;

        if( !$this->get( 'toolbar.menu.enabled' ) ) {
          return;
        }

        $wp_admin_bar->add_menu( array(
          'id'     => 'veneer',
          'parent' => 'top-secondary',
          'meta'   => array(
            'html'     => '<div class="veneer-toolbar-info"></div>',
            'target'   => '',
            'onclick'  => '',
            'title'    => 'Veneer',
            'tabindex' => 10,
            'class'    => 'veneer-toolbar'
          ),
          'title'  => 'Veneer',
          'href'   => network_admin_url( 'admin.php?page=veneer' )
        ) );

        $wp_admin_bar->add_menu( array(
          'parent' => 'veneer',
          'id'     => 'veneer-pagespeed',
          'meta'   => array(),
          'title'  => 'PageSpeed',
          'href'   => network_admin_url( 'admin.php?page=veneer#panel=cdn' )
        ) );

        $wp_admin_bar->add_menu( array(
          'parent' => 'veneer',
          'id'     => 'veneer-cloudfront',
          'meta'   => array(),
          'title'  => 'CloudFront',
          'href'   => network_admin_url( 'admin.php?page=veneer#panel=cdn' )
        ) );

        $wp_admin_bar->add_menu( array(
          'parent' => 'veneer',
          'id'     => 'veneer-varnish',
          'meta'   => array(),
          'title'  => 'Varnish',
          'href'   => network_admin_url( 'admin.php?page=veneer#panel=cdn' )
        ) );

      }

      /**
       * Get Setting.
       *
       *    // Get Setting
       *    Veneer::get( 'my_key' )
       *
       * @method get
       *
       * @for Flawless
       * @author potanin@UD
       * @since 0.1.1
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
       *    Veneer::set( 'my_key', 'my-value' )
       *
       * @method get
       * @for Flawless
       *
       * @author potanin@UD
       * @since 0.1.1
       */
      public static function set( $key, $value = null ) {
        return self::$instance->_settings ? self::$instance->_settings->set( $key, $value ) : null;
      }

      /**
       * Get the Veneer Singleton
       *
       * Concept based on the CodeIgniter get_instance() concept.
       *
       * @example
       *
       *      var settings = Veneer::get_instance()->Settings;
       *      var api = Veneer::$instance()->API;
       *
       * @static
       * @return object
       *
       * @method get_instance
       * @for Veneer
       */
      public static function &get_instance() {
        return self::$instance;
      }

    }

  }

  /**
   * Ok, create our shorthand function to get the veneer object
   */
  if( !class_exists( 'Veneeer' ) ) {
    class Veneer {
      /**
       * Get the Veneer Singleton
       *
       * Concept based on the CodeIgniter get_instance() concept.
       *
       * @example
       *
       *      var settings = Veneer::get_instance()->Settings;
       *      var api = Veneer::$instance()->API;
       *
       * @static
       * @return object
       *
       * @method get_instance
       * @for Veneer
       */
      public static function &get_instance() {
        return Bootstrap::$instance;
      }
    }
  }

}
