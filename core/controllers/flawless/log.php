<?php
/**
 * Flawless
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 * @module Flawless
 */
namespace Flawless {

  /**
   * Log
   *
   * -
   *
   * @submodule Log
   * @author potanin@UD
   * @version 0.1.0
   * @class Log
   */
  class Log {

    // Class Version.
    public $version = '0.1.1';

    /**
     * Constructor for the Log class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Log
     *
     * @param array
     */
    public function __construct( $options = array() ) {

      //** Add console log JavaScript in admin footer */
      add_filter( 'admin_print_footer_scripts', array( 'Log', 'render' ) );

      add_filter( 'wp_print_footer_scripts', array( __CLASS__, 'wp_print_footer_scripts' ), 100 );

    }

    /**
     * Renders scripts in footer.
     *
     * @method wp_print_footer_scripts
     * @for Log
     */
    static function wp_print_footer_scripts() {
      echo Log::render();
    }

    /**
     * Disables update notifications if set.
     *
     * @action after_setup_theme( 10 )
     * @since 0.0.2
     */
    static function log_stats() {
      __CLASS__::add( 'End of request, total execution: ' . timer_stop() . ' seconds.' );
    }

    /**
     * PHP function to echoing a message to JS console
     *
     * @todo This needs to be improved.
     * @since 0.2.0
     */
    static function add( $entry = false, $type = 'log' ) {
      global $flawless;

      if ( empty( $entry ) ) {
        return;
      }

      $new_entry = array(
        'timer' => timer_stop(),
        'entry' => $entry,
        'type' => $type
      );

      if ( function_exists( 'memory_get_peak_usage' ) && method_exists( 'Utility', 'format_bytes' ) ) {
        $new_entry[ 'memory_usage' ] = self::format_bytes( memory_get_peak_usage() );
      }

      $flawless[ 'add' ][ ] = $new_entry;

      return $entry;

    }

    /**
     * Prints JS for the console log when in debug mode in the footer.
     *
     * @todo Add Error logging and saving to DB even when not in developer mode. - potanin@UD
     * @author potanin@UD
     * @version 0.26.0
     */
    static function render() {
      global $flawless;

      if ( $flawless[ 'developer_mode' ] != 'true' ) {
        return;
      }

      $html = array();

      $html[ ] = '<script type="text/javascript"> if( typeof console == "object" && typeof console.log == "function" ) {';

      foreach ( (array) $flawless[ 'add' ] as $entry ) {

        if ( is_array( $entry[ 'entry' ] ) || is_object( $entry[ 'entry' ] ) ) {

          switch ( $entry[ 'type' ] ) {

            case 'info':
              $html[ ] = 'console.info( jQuery.parseJSON( ' . json_encode( json_encode( $entry[ 'entry' ] ) ) . ' ));';
              break;

            case 'error':
              $html[ ] = 'console.error( jQuery.parseJSON( ' . json_encode( json_encode( $entry[ 'entry' ] ) ) . ' ));';
              break;

            default:

              if ( $flawless[ 'add_options' ][ 'show_log' ] ) {
                $html[ ] = 'console.log( jQuery.parseJSON( ' . json_encode( json_encode( $entry[ 'entry' ] ) ) . ' ));';
              }

              break;

          }

        } else {

          $entry[ 'entry' ] = 'P: ' . $entry[ 'timer' ] . ' - ' . $entry[ 'entry' ];

          switch ( $entry[ 'type' ] ) {

            case 'info':
              $html[ ] = 'console.info( "' . $entry[ 'entry' ] . '" ); ';
              break;

            case 'error':
              $html[ ] = 'console.error( "' . $entry[ 'entry' ] . '" ); ';
              break;

            default:

              if ( $flawless[ 'add_options' ][ 'show_log' ] ) {
                $html[ ] = 'console.log( "' . $entry[ 'entry' ] . '" ); ';
              }

              break;

          }
        }

      }

      $html[ ] = '} </script>';

      echo implode( "\n", (array) $html );

    }

  }

}