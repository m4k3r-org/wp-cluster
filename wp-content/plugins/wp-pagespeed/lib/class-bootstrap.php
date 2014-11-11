<?php
/**
 * UsabilityDynamics\PageSpeed Bootstrap
 *
 * @verison 0.1.0
 * @author potanin@UD
 * @namespace UsabilityDynamics\PageSpeed
 */
namespace UsabilityDynamics\PageSpeed {

	if( !class_exists( 'UsabilityDynamics\PageSpeed\Bootstrap' ) ) {

		/**
		 * Bootstrap PageSpeed
		 *
		 * @class Bootstrap
		 * @author potanin@UD
		 * @version 0.0.1
		 */
		class Bootstrap {

			/**
			 * PageSpeed core version.
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
			public static $text_domain = 'wp-pagespeed';

			/**
			 * Settings Instance.
			 *
			 * @property $_settings
			 * @type {Object}
			 */
			private $_settings;

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

				// Return Singleton Instance
				if( self::$instance ) {
					return self::$instance;
				}

				// Check if being called too early, such as during Unit Testing.
				if( !function_exists( 'did_action' ) ) {
					return $this;
				}

				add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

			}

			/**
			 *
			 */
			public function plugins_loaded() {

				add_action( 'template_redirect', array( $this, 'template_redirect' ) );

			}

			/**
			 *
			 */
			public function template_redirect() {

				if( headers_sent() ) {
					return;
				}

				ob_start( array( $this, 'ob_start' ) );

				if( defined( 'WP_PAGESPEED' ) && !WP_PAGESPEED ) {
					header( 'PageSpeed: off' );
				}

				if( defined( 'WP_PAGESPEED' ) && is_bool( WP_PAGESPEED ) ) {
					header( 'PageSpeed: on' );
					header( 'PageSpeedFilters:inline_images,remove_comments,recompress_images,minify_html,lazyload_images,-inline_images' );
				}

				if( defined( 'WP_PAGESPEED' ) && is_string( WP_PAGESPEED ) ) {
					header( 'PageSpeed: on' );
					header( 'PageSpeedFilters:' . WP_PAGESPEED );
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

				// @note thro exception to abort rest of ob_start from a filter.
				try {
					$buffer = apply_filters( 'wp-pagespeed:ob_start', $buffer, $this );
				} catch( \Exception $e ) {
					return $buffer;
				}

				// Media Domain Sharding.
				if( $this->get( 'minify' ) ) {

				}

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

				return $buffer;

			}

			/**
			 * Get Setting.
			 *
			 *    // Get Setting
			 *    PageSpeed::get( 'my_key' )
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
			 *    PageSpeed::set( 'my_key', 'my-value' )
			 *
			 * @method get
			 * @for Flawless
			 *
			 * @author potanin@UD
			 * @since 0.1.1
			 *
			 * @param $key
			 * @param null $value
			 *
			 * @return null
			 */
			public static function set( $key, $value = null ) {
				return self::$instance->_settings ? self::$instance->_settings->set( $key, $value ) : null;
			}

			/**
			 * Get the PageSpeed Singleton
			 *
			 * Concept based on the CodeIgniter get_instance() concept.
			 *
			 * @example
			 *
			 *      var settings = PageSpeed::get_instance()->Settings;
			 *      var api = PageSpeed::$instance()->API;
			 *
			 * @static
			 * @return object
			 *
			 * @method get_instance
			 * @for PageSpeed
			 */
			public static function &get_instance() {
				return self::$instance;
			}

		}

	}

}