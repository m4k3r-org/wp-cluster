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

    // Class Version.
    public $version = '0.1.1';

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
     * @param array $options
     */
    public function __construct( $options = array() ) {
      add_action( 'flawless::extra_local_assets', array( __CLASS__, 'extra_local_assets' ), 5 );

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

    /**
     * Tests if remote script or CSS file can be opened prior to sending it to browser
     *
     *
     * @version 0.25.0
     */
    static function can_get( $url = false, $args = array() ) {
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
     * Checks if script or style have been loaded.
     *
     * Migrated from Flawless::is_asset_loaded()
     *
     * @todo Add handler for styles.
     * @since 0.2.0
     */
    static function is_loaded( $handle = false ) {
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
     * Load extra front-end assets
     *
     * @todo Why are these not being registered? Does it matter? - potanin@UD 6/10/12
     * @since 0.0.3
     */
    static function extra_local_assets( &$flawless ) {

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

  }

}