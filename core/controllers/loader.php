<?php
/**
 * Flawless Loader
 *
 * @namespace Flawless
 * @module Loader
 * @version 0.0.3
 * @author potanin@UD
 */
namespace Flawless {

  // Get UsabilityDynamics Loader
  require_once( TEMPLATEPATH . DIRECTORY_SEPARATOR . '/core/vendor/usabilitydynamics/loader.php' );

  /**
   * Loader implements a PSR-0 class loader
   *
   *     $loader = new \Flawless\Loader();
   *
   *     // register classes with namespaces
   *     $loader->add( 'Symfony',           __DIR__ . '/framework' );
   *
   *     // register classes with namespaces by passing an array
   *     $loader->add( array(
   *        'UsabilityDynamics\\' => __DIR__ . '/usabilitydynamics'
   *        'JsonSchema\\' => __DIR__ . '/jsonschema/src'
   *     ));
   *
   *     // activate the autoloader
   *     $loader->register();
   *
   * This class is loosely based on the Symfony UniversalClassLoader.
   *
   * @author potanin@UD
   * @class Loader
   * @extend UsabilityDynamics\Loader
   *
   * @author Fabien Potencier <fabien@symfony.com>
   * @author Jordi Boggiano <j.boggiano@seld.be>
   */
  class Loader extends \UsabilityDynamics\Loader {

    /**
     * Loader Class version.
     *
     * @public
     * @static
     * @property $version
     * @type {Object}
     */
    public static $version = '0.0.3';

    // @parameter $headers Extra header parameters.
    public static $headers = array(
      'theme' => array(
        'Name' => 'Theme Name',
        'ThemeURI' => 'Theme URI',
        'Description' => 'Description',
        'Author' => 'Author',
        'AuthorURI' => 'Author URI',
        'Version' => 'Version',
        'Template' => 'Template',
        'Status' => 'Status',
        'Tags' => 'Tags',
        'TextDomain' => 'Text Domain',
        'DomainPath' => 'Domain Path',
        'Supported Features' => 'Supported Features',
        'Disabled Features' => 'Disabled Features',
        'Google Fonts' => 'Google Fonts'
      ),
      'style' => array(
        'Name' => 'Name',
        'Description' => 'Description',
        'Media' => 'Media',
        'Version' => 'Version'
      ),
      'module' => array(
        'Name' => 'Name',
        'Description' => 'Description',
        'Author' => 'Author',
        'Media' => 'Media',
        'ThemeFeature' => 'Theme Feature'
      ),
    );

    // @parameter $options Configuration.
    public $options = stdClass;

    /**
     * Constructor for the Loader class.
     *
     * @method __construct
     * @for Loader
     * @constructor
     *
     * @param $settings {Object|Array|boolean}
     *
     * @return \Flawless\Loader
     * @version 0.0.2
     * @since 0.0.2
     */
    function __construct( $settings = false ) {

      if( is_array( $settings ) || is_object( $settings ) ) {

        // Save Loader Settings.
        $this->$settings = (object) $settings;

        // Load libraries that use namespaces.
        $this->set_namespace( $this->$settings->controllers );

        // Loads libraries that do not use namespaces.
        $this->add_class_map( $this->$settings->helpers );

      }

      // Register Autoloader.
      $this->register( true );

      // Prepare Filters.
      add_filter( 'flawless::theme_setup',          array( $this, 'theme_setup' ) );
      add_filter( 'flawless::template_redirect',    array( $this, 'template_redirect' ) );

      // Utility.
      add_filter( 'extra_theme_headers',            array( $this, 'extra_theme_headers' ) );

      // @chainable
      return $this;

    }

    /**
     * Setup Theme Loader
     *
     * @method theme_setup
     * @for Loader
     *
     * @param $flawless object Flawless instance passed by reference.
     *
     * @return $this
     *
     * @author potanin@UD
     * @version 0.0.2
     * @since 0.0.2
     */
    function theme_setup( &$flawless ) {

      //** Load theme's core assets */
      $this->load_core_assets( $flawless );

      //** Load extra functionality */
      $this->load_extend_modules( $flawless );

      return $this;

    }

    /**
     * Fronted Setup
     *
     * @param $flawless object Flawless instance passed by reference.
     * @return $this
     *
     * @method template_redirect
     * @for Loader
     *
     * @author potanin@UD
     * @version 0.0.2
     * @since 0.0.2
     */
    function template_redirect( &$flawless ) {

      // Unrefister Autoloader.
      $this->unregister( $flawless );

      // @chainable
      return $this;

    }

    /**
     * Add Color Scheme
     *
     * @method extra_theme_headers
     * @for Loader
     *
     * @author potanin@UD
     * @version 0.0.2
     * @since 0.0.2
     */
    function extra_theme_headers() {
      return (array) $this->headers;
    }

    /**
     * Loads core assets of the theme
     *
     * Loaded after theme_features have been configured.
     *
     * @method load_core_assets
     * @for Loader
     *
     * @author potanin@UD
     * @version 0.0.2
     * @since 0.0.2
     */
    function load_core_assets() {

      //** Load logo if set */
      /* if( is_numeric( $this->flawless_logo[ 'post_id' ] ) && $image_attributes = wp_get_attachment_image_src( $this[ 'flawless_logo' ][ 'post_id' ], 'full' ) ) {
        $flawless[ 'flawless_logo' ][ 'url' ] = $image_attributes[ 0 ];
        $flawless[ 'flawless_logo' ][ 'width' ] = $image_attributes[ 1 ];
        $flawless[ 'flawless_logo' ][ 'height' ] = $image_attributes[ 2 ];
      } */

      foreach( (array) $this->asset_directories as $path => $url ) {

        $path = $path . '/core';

        if( !is_dir( $path ) || !$resource = opendir( $path ) ) {
          continue;
        }

        while ( false !== ( $file_name = readdir( $resource ) ) ) {

          if( substr( strrchr( $file_name, '.' ), 1 ) != 'php' ) {
            continue;
          }

          $file_data = Loader::get_file_data( $path . '/' . $file_name, 'module' );

          $file_data[ 'Location' ] = 'theme_functions';
          $file_data[ 'File Name' ] = $file_name;

          $flawless[ 'core_assets' ][ $file_data[ 'Name' ] ] = $file_data;

          include_once( $path . '/' . $file_name );

        }

      }

    }

    /**
     * Loads extra function files.
     *
     * @method load_extend_modules
     * @for Loader
     *
     * @author potanin@UD
     * @version 0.0.2
     * @since 0.0.2
     */
    function load_extend_modules() {

      function load_file( $file_data ) {

        if( empty( $file_data ) ) {
          return false;
        }

        foreach ( (array) apply_filters( 'flawless::required_extra_resource_file_data', array( 'Name', 'Version' ) ) as $req_field ) {
          if( !in_array( $req_field, array_keys( (array) $file_data ) ) ) {
            return false;
          }
        }

        $file_data[ 'Location' ] = 'theme_functions';

        $flawless[ 'flawless_extra_assets' ][ $file_data[ 'Name' ] ] = $file_data;

        include_once( $file_data[ 'path' ] );

      }

      foreach ( (array) $this->asset_directories as $path => $url ) {

        $path = $path . '/core/extensions';

        if( !is_dir( $path ) ) {
          continue;
        }

        if( !$functions_resource = opendir( $path ) ) {
          continue;
        }

        while ( false !== ( $file_name = readdir( $functions_resource ) ) ) {

          if( $file_name == '.' || $file_name == '..' ) {
            continue;
          }

          //** Check if directory includes a with the same name as directory, AND there is no filename in root */
          if( is_dir( $path . '/' . $file_name ) && file_exists( $path . '/' . $file_name . '/' . $file_name . '.php' ) && !file_exists( $path . '/' . $file_name . '.php' ) ) {
            $file_data = array_filter( (array) @get_file_data( $path . '/' . $file_name . '/' . $file_name . '.php', $flawless[ 'default_header' ][ 'flawless_extra_assets' ], 'flawless_extra_assets' ) );
            $file_data[ 'path' ] = $path . '/' . $file_name . '/' . $file_name . '.php';
            $file_data[ 'file_name' ] = $file_name . '.php';
            load_file( $file_data );
            continue;
          }

          if( substr( strrchr( $file_name, '.' ), 1 ) != 'php' ) {
            continue;
          }

          $file_data = array_filter( (array) @get_file_data( $path . '/' . $file_name, $flawless[ 'default_header' ][ 'flawless_extra_assets' ], 'flawless_extra_assets' ) );

          $file_data[ 'file_name' ] = $file_name;
          $file_data[ 'path' ] = $path . '/' . $file_name;

          load_file( $file_data );

        }

      }

      //** Load any existing assets for active plugins */
      foreach ( apply_filters( 'flawless::active_plugins', (array) Utility::get_active_plugins() ) as $plugin ) {

        //** Get a plugin name slug */
        $plugin = dirname( plugin_basename( trim( $plugin ) ) );

        //** Look for plugin-specific scripts and load them */
        foreach ( (array) $this->asset_directories as $this_directory => $this_url ) {
          if( file_exists( $this_directory . '/assets/js/' . $plugin . '.js' ) ) {
            $asset_url = apply_filters( 'flawless-asset-url', $this_url . '/assets/js/' . $plugin . '.js', $plugin );
            wp_enqueue_script( 'flawless-asset-' . $plugin, $asset_url, array(), Flawless_Version, true );
            Log::add( sprintf( __( 'JavaScript found for %1s plugin and loaded: %2s.', 'flawless' ), $plugin, $asset_url ) );
          }
        }
      }

    }

    /**
     * Loader Helper Files
     *
     * @method load_helpers
     * @for Loader
     *
     * @author potanin@UD
     * @version 0.0.2
     * @since 0.0.2
     */
    function load_helpers() {

      // Load Helpers
      //include_once( $this->options->paths . '/template.php' );

    }

  }

}

