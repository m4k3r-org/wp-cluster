/**
 * flawless-core.js
 *
 * Global Flawless JavaScript functionality.
 *
 * @version 0.1.0
 * @copyright (c) 2012-2013 Usability Dynamics, Inc. (usabilitydynamics.com)
 */

var Emitter = require( 'emitter' );

module.exports = {

  test_thing: function () {

    console.log( 'Emitter:', Emitter );
  },

  developer_mode: false,

  /**
   * Internal logging function
   *
   * @author potanin@UD
   */
  log: function log ( notice, type, console_type, override_debug ) {

    /** Defaults */
    type = typeof type !== 'undefined' ? type : 'log';
    console_type = console_type ? console_type : 'log';

    /** Add Prefix */
    notice = ( typeof notice === 'string' || typeof notice === 'number' ? 'Flawless::' + notice : notice );

    /** If debugging is disabled, or the current browser does not support it, do nothing */
    if ( !override_debug && ( !flawless.developer_mode || !window.console ) ) {
      return notice;
    }

    if ( window.console && console.debug ) {

      switch ( console_type ) {

        case 'error':
          console.error( notice );
          break;

        case 'info':
          console.info( notice );
          break;

        case 'log':
          if ( typeof flawless.console_log_options === 'object' && flawless.console_log_options.show_log ) {
            console.log( notice );
          }
          break;

      }

    }

    return notice ? notice : false;

  }

}