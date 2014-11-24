/* =========================================================
 * flawless.js
 * Version 0.1.0
 * =========================================================
 *
 * Global Flawless JavaScript functionality.
 *
 * Copyright (c) 2012 Usability Dynamics, Inc. (usabilitydynamics.com)
 * ========================================================= */


  /* Declare global var if it not setup already */
  var flawless = jQuery.extend( true, {
    developer_mode: false
  }, typeof flawless === 'object' ? flawless : {} );

  
  /**
   * Internal logging function
   *
   * @author potanin@UD
   */
  flawless.log = function ( notice, type, console_type, override_debug ) {

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

        case 'debug':
          if( typeof console.debug != 'undefined' ) { console.debug( notice );  } else { console.log( notice ); }
        break;

        case 'dir':
          if( typeof console.dir != 'undefined' ) { console.dir( notice ); } else { console.log( notice ); }
        break;

        case 'log':
          console.log( notice );
        break;

      }

    }

    return notice ? notice : false;

  };