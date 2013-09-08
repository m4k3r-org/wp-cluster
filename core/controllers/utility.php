<?php
/**
 * Flawless Utility
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Utility
   *
   * Description: WPP Utility implementation
   *
   * @extends \UsabilityDynamics\Utility
   * @author potanin@UD
   * @version 0.1.0
   * @class Utility
   */
  class Utility extends \UsabilityDynamics\Utility {

    /**
     * Utility Class version.
     *
     * @public
     * @static
     * @property $version
     * @type {Object}
     */
    public static $version = '0.2.1';

    /**
     * Constructor for the UD Utility class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Utility
     *
     * @param array $options
     */
    public function __construct( $options = array() ) {
      $this->options = $options;

      add_filter( 'template_directory', array( __CLASS__, 'fix_path' ) );
      add_filter( 'stylesheet_directory', array( __CLASS__, 'fix_path' ) );

      add_filter( 'flawless::theme_setup::after', array( __CLASS__, 'theme_setup_after' ) );

    }

    /**
     * Post Theme Setup Tweaks
     *
     * @method theme_setup_after
     * @for Utility
     */
    static function theme_setup_after() {

      // Global helper to fix local paths, should probably be moved somewhere else.
      add_filter( 'flawless::root_path', array( Utility, 'fix_path' ));

      // Support for WP's code quality monitoring when debug mode is not enabled.
      add_action( 'doing_it_wrong_run', function ( $function, $message, $version ) {
        Log::add( sprintf( __( 'Warning: %1$s was called incorrectly. %2$s %3$s' ), $function, $message, $version ), 'error' );
      }, 10, 3 );

    }

    /**
     * Uses back-trace to figure out which sidebar was called from the sidebar.php file
     *
     * WordPress does not provide an easy way to figure out the type of sidebar that was called from within the sidebar.php file, so we backtrace it.
     *
     * @since 0.0.2
     * @author potanin@UD
     */
    static function backtrace_sidebar_type() {

      $backtrace = debug_backtrace();

      if ( !is_array( $backtrace ) ) {
        return false;
      }

      foreach ( (array) $backtrace as $item ) {

        if ( $item[ 'function' ] == 'flawless_widget_area' ) {
          return $item[ 'args' ][ 0 ];
        } elseif ( $item[ 'function' ] == 'get_sidebar' ) {
          return $item[ 'args' ][ 0 ];
        }

      }

      return false;

    }

    /**
     * Load a template part into a template.
     *
     * Overrides UD_API::get_template_part() which passes an array of known template names.
     * Same as default get_template_part() but returned as a variable.
     *
     * @version 0.d6
     */
    static function get_template_part( $slug, $name = null ) {

      do_action( "get_template_part_{$slug}", $slug, $name );

      $templates = array();
      if ( isset( $name ) )
        $templates[ ] = "{$slug}-{$name}.php";

      $templates[ ] = "{$slug}.php";

      ob_start();
      locate_template( $templates, true, false );
      $return = ob_get_clean();

      if ( empty( $return ) ) {
        return false;
      }

      return $return;

    }

  }

}