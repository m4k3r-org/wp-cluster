<?php
  /**
   * Flawless
   *
   * @author potanin@UD
   * @version 0.0.1
   * @namespace Flawless
   */
  namespace Flawless {

    /**
     * Asset
     *
     * -
     *
     * @author potanin@UD
     * @version 0.1.0
     * @class Asset
     */
    class Asset {

      /**
       * Asset Controller Version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.1.1';

      /**
       * Constructor for the Asset class.
       *
       * @author potanin@UD
       * @version 0.0.1
       * @method __construct
       *
       * @constructor
       * @for Asset
       *
       * @param array|bool $options
       */
      public function __construct( $options = false ) {

        // Load additional scripts and styles.
        add_action( 'flawless::wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 5 );
        add_action( 'flawless::wp_print_styles',    array( $this, 'wp_print_styles' ), 5 );
        add_action( 'flawless::extra_local_assets', array( $this, 'extra_local_assets' ), 5 );

      }

      /**
       * Load extra front-end assets
       *
       * @param $flawless {Object} Flawless instance.
       *
       * @method extra_local_assets
       * @for Asset
       *
       * @since 0.0.3
       */
      public function extra_local_assets( &$flawless ) {

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
       * Load extra front-end assets
       *
       * Cycles through $flawless->remote_assets->css and attempts to enqueue.
       *
       * @param $flawless {Object} Flawless instance.
       *
       * @method wp_print_styles
       * @for Asset
       *
       * @action flawless::extra_local_assets
       * @since 0.0.3
       */
      public function wp_print_styles( &$flawless ) {
        global $wp_styles;

        $flawless->remote_assets = apply_filters( 'flawless::remote_assets', (array) $flawless->remote_assets );

        //** Enqueue remote styles if they are accessible */
        foreach ( (array) $flawless->remote_assets[ 'css' ] as $asset_handle => $remote_asset ) {
          $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );
          if ( Asset::can_get( $this[ 'protocol' ] . $remote_asset, array( 'handle' => $asset_handle ) ) ) {
            wp_enqueue_style( $asset_handle, $this[ 'protocol' ] . $remote_asset, array(), Flawless_Version );
          }
        }

        // Enqueue Google Fonts if specified by theme or skin
        foreach ( $flawless->current_theme_options[ 'Google Fonts' ] as $google_font ) {
          wp_enqueue_style( 'google-font-' . sanitize_file_name( $google_font ), 'https://fonts.googleapis.com/css?family=' . str_replace( ' ', '+', ucfirst( trim( $google_font ) ) ), array( 'flawless-app' ) );
        }

        // Enqueue CSS for active plugins.
        foreach ( apply_filters( 'flawless::active_plugins', (array) Utility::get_active_plugins() ) as $plugin ) {

          // Get a plugin name slug
          $plugin = dirname( plugin_basename( trim( $plugin ) ) );

          // Look for plugin-specific scripts and load them
          foreach ( (array) $flawless->asset_directories as $this_directory => $this_url ) {
            if ( file_exists( $this_directory . '/css/' . $plugin . '.css' ) ) {
              $asset_url = apply_filters( 'flawless-asset-url', $this_url . '/css/' . $plugin . '.css', $plugin );
              $file_data = get_file_data( $this_directory . '/css/' . $plugin . '.css', $this[ 'default_header' ][ 'flawless_style_assets' ], 'flawless_style_assets' );
              wp_enqueue_style( 'flawless-asset-' . $plugin, $asset_url, array( 'flawless-app' ), $file_data[ 'Version' ] ? $file_data[ 'Version' ] : Flawless_Version, $file_data[ 'Media' ] ? $file_data[ 'Media' ] : 'screen' );
              Log::add( sprintf( __( 'CSS found for %1s plugin and loaded: %2s.', 'flawless' ), $plugin, $asset_url ), 'info' );
            }
          }
        }

        // Enqueue child theme style.css.
        if( file_exists( STYLESHEETPATH . '/ux/build/app.min.css' ) ) {
          //wp_enqueue_style( 'flawless-app', STYLESHEETPATH . '/ux/build/app.min.css', array(), Flawless_Version );
        }

      }

      /**
       * Load extra front-end assets
       *
       * Cycles through $flawless->remote_assets->script and attempts to enqueue.
       *
       * @param $flawless {Object} Flawless instance.
       *
       * @method wp_enqueue_scripts
       * @for Asset
       *
       * @action flawless::extra_local_assets
       * @since 0.0.3
       */
      public function wp_enqueue_scripts( &$flawless ) {

        // API Access
        $flawless->remote_assets = apply_filters( 'flawless::remote_assets', (array) $flawless->remote_assets );

        // Check and Load Remote Scripts
        foreach ( (array) $$flawless->remote_assets->script as $asset_handle => $remote_asset ) {

          //** Remove prix if passed, we set them automatically */
          $remote_asset = str_replace( array( 'http://', 'https://' ), '', $remote_asset );

          if ( Asset::can_get( $this[ 'protocol' ] . $remote_asset, array( 'handle' => $asset_handle ) ) ) {
            wp_enqueue_script( $asset_handle, $this[ 'protocol' ] . $remote_asset, array(), Flawless_Version );
          } else {
            Log::add( sprintf( __( 'Could not load remote asset script: %1s.', 'flawless' ), $remote_asset ) );
          }
        }

      }

      /**
       * Scans Asset Directories for the requested assets and returns asset-specific result if found
       *
       * @example
       *
       *      // Find Script and Style for Carrington
       *      $css  = Asset::load( 'carrington', 'css' );
       *      $js   = Asset::load( 'carrington', 'js' );
       *
       *      // Enqueue, assuming they are found.
       *      wp_enqueue_style( 'carrington', $css );
       *      wp_enqueue_script( 'carrington', $js );
       *
       * - lib - inclues the PHP library if it exists
       * - less - returns file path if file exists
       * - image - returns URL if exists
       * - js - returns URL if exists
       * - css - returns URL if exists
       *
       * @updated 0.0.6
       * @author peshkov@UD
       *
       * @param        $name
       * @param string $type
       * @param string $args
       *
       * @return bool|mixed|string
       */
      static public function load( $name, $type = 'lib', $args = '' ) {

        //die( '<pre>' . print_r( func_get_args(), true ) . '</pre>' );

        $args = wp_parse_args( $args, array(
          'return' => false
        ));

        /* if ( $return = wp_cache_get( md5( $name . $type . serialize( $args ) ), 'flawless_load_value' ) ) {
          return $return;
        } */

        foreach ( (array) $flawless->asset_directories as $assets_path => $assets_url ) {

          switch ( $type ) {

            case 'lib':
              if ( file_exists( $assets_path . '/controllers/' . $name . '.php' ) ) {
                include_once( $assets_path . '/controllers/' . $name . '.php' );
                $return = true;
              }
            break;

            case 'less':
              if ( file_exists( $assets_path . '/styles/' . $name . '.less' ) ) {
                $return = $args[ 'return' ] == 'path' ? $assets_path . '/styles/' . $name . '.less' : $assets_url . '/styles/' . $name . '.less';
              }
            break;

            case 'img':
            case 'image':
              if ( file_exists( $assets_path . '/img/' . $name ) ) {
                $return = $assets_url . '/img/' . $name;
              }
              break;

            case 'js':
              if ( file_exists( $assets_path . '/scripts/' . $name ) ) {
                $return = $assets_url . '/scripts/' . $name;
              }
            break;

            case 'css':
              if ( file_exists( $assets_path . '/styles/' . $name . '.css' ) ) {
                $return = $assets_url . '/styles/' . $name . '.css';
              }
            break;

          }

        }

        if ( !empty( $return ) ) {
          // wp_cache_set( md5( $name . $type . serialize( $args ) ), $return, 'flawless_load_value' );
          return $return;
        }

        return false;

      }

      /**
       * Tests if remote script or CSS file can be opened prior to sending it to browser
       *
       * @method can_get
       * @version 0.25.0
       *
       * @param bool  $url
       * @param array $args
       * @return bool
       */
      static public function can_get( $url = false, $args = array() ) {
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
       * Checks if script or style have been loaded.
       *
       * Migrated from Flawless::is_asset_loaded()
       *
       * @todo Add handler for styles.
       * @since 0.2.0
       */
      static public function is_loaded( $handle = false ) {
        global $wp_scripts;

        if ( empty( $handle ) ) {
          return;
        }

        $footer = (array) $wp_scripts->in_footer;
        $done   = (array) $wp_scripts->done;

        $accepted = array_merge( $footer, $done );

        if ( !in_array( $handle, $accepted ) ) {
          return false;
        }

        return true;

      }

    }

  }