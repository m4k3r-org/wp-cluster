/**
 * Global UD SaaS Interface
 *
 * @version 1.2
 * @description UD Loader. Initial global object extended by product-specific globals
 * @package UD
 * @author team@UD
 */
var ud = ( typeof ud === 'object' ) ? ud : {};

ud.saas = ({

  'scope': 'ud.saas',

  // ID of current connection
  'id': null,

  // Status of a connection. False for no connection, null when pending, true when connected and instance sent
  'connected': false,

  // URL of current socket connection
  'current': null,

  'socket': false,

  // Model of current instance (must be set by client)
  'instance': null,

  // Log of received messages
  'messages': [],

  'settings': {
    'query_timeout': 3000,
    'log': {
      'errors': true,
      'events': false,
      'procedurals': false,
      'all_data': false
    }
  },

  /**
   * Establish connection with UD Driver.
   *
   * Triggered by client-side functions when SaaS support is requested.
   *
   * @since 1.0.0
   * @author potanin@UD
   */
  connect: function( model, args ) {
    'use strict';
    var self = this;
    if( self.settings.log.procedurals ) { self.log( self.scope + '.connect()', arguments ); }

    args = jQuery.extend( true, { 'force new connection': false, 'secure': true }, args ? args :
    {}, { 'instance': self.instance } );
    var url = ( args.secure ? 'https://saas.usabilitydynamics.com:443/' : 'http://saas.usabilitydynamics.com:80/' ) + (
      typeof model === 'string' ? model : '' );

    /* Initialize Connection */
    if( typeof io !== 'object' ) {
      jQuery( document ).trigger( 'ud::saas::update', { message: 'Socket.io Not Loaded, connection not established.', args: arguments } );
      return false;
    }

    if( self.socket && !args[ 'force new connection' ] ) {
      self.emit( self.id + '::receive::instance', { instance: self.instance } );
      return self.socket;
    }

    self.socket = io.connect( url, args );

    self.on( 'connect', function( data ) {

      self.id = self.socket.socket.sessionid;
      self.current = url;

      jQuery( document ).trigger( 'ud::saas::connect', self );

      jQuery( document ).trigger( '' + self.id + '::init', self );

      /* Monitor all Updates */
      self.on( self.id + '::update', function( data ) {
        if( data.message ) { self.messages.push( { 'time': new Date().getTime(), 'message': data.message, 'screen': self.instance.screen } ); }
        jQuery( document ).trigger( '' + self.id + '::update', data );
      } );

      /* Send Instance / Authentication Data */
      self.on( self.id + '::request::instance', function( data ) {
        self.emit( self.id + '::receive::instance', { instance: self.instance } );

        self.connected = null;

        // Triggers to notify when an instance is authenticated and screen set
        self.on( self.id + '::update::screen_set', function( data ) {
          jQuery( document ).trigger( '' + self.id + '::update::screen_set::' + data.screen, { 'data': data, 'saas': self } );
        } );

        self.on( self.id + '::update::authentication', function( data ) {

          if( data.success ) {
            self.connected = true;
            jQuery( document ).trigger( '' + self.id + '::connected', self );
          } else {
            self.connected = false;
            self.current = null;
            self.log( new Error( self.scope + '.connect() update::authentication - Failure.' ) );
          }

        } );

      } );

      self.on( 'disconnect', function() { // if( self.settings.log.events ) { self.log( self.scope + ' -> disconnect from ' + self.id ); }

        jQuery( document ).trigger( '' + self.id + '::disconnected', self );

        self.connected = false;
        self.current = null;
        self.id = null;

        if( self.settings.log.all_data && typeof self.log_all === 'function' ) {
          jQuery( document ).unbind( 'io::data', self.log_all );
        }

      } );

      if( self.settings.log.all_data && typeof self.log_all === 'function' ) {
        jQuery( document ).bind( 'io::data', self.log_all );
      }

    } );

    return self.socket;

  },

  /**
   * Disconnect from SaaS
   *
   * @author peshkov@UD
   */
  disconnect: function() {
    'use strict';
    var self = this;
    if( self.settings.log.procedurals ) { self.log( self.scope + '.disconnect()', arguments ); }

    if( self.socket && typeof self.socket.disconnect == 'function' ) {
      self.socket.disconnect();
      self.socket.removeAllListeners();
      jQuery( document ).trigger( 'ud::saas::disconnect', self );
    }
  },

  /**
   * Programmatically Execute Emit
   *
   * Example: ud.saas.emit( 'get_capabilities' );
   *
   * @since 1.1.0
   * @author potanin@UD
   */
  emit: function( action, data ) {
    'use strict';
    var self = this;
    if( self.settings.log.events ) { self.log( self.scope + '.emit()', arguments ); }

    if( self.id ) {
      data.session = data.session ? data.session : self.id;
    }

    self.socket.emit( action, data );
  },

  /**
   * Programmatically Execute Emit
   *
   * Example: ud.saas.emit( 'get_capabilities' );
   *
   * @since 1.1.0
   * @author potanin@UD
   */
  on: function( action, callback ) {
    'use strict';
    var self = this;
    if( self.settings.log.events ) { self.log( self.scope + '.on()', arguments ); }

    if( self.socket ) {
      return self.socket.on( action, callback );
    }

    var interval = setInterval( function() {
      if( !self.socket ) { return ud.warning( 'Socket not ready. ' + action + ' called too early. Retrying in several seconds...' ); }
      self.socket.on( action, callback );
      clearInterval( interval );
    }, 2500 );

    window.setTimeout( function() {
      ud.warning( 'Socket attempts for ' + action + ' are up.' );
      clearInterval( interval );
    }, 10000 );

  },

  /**
   * Instance-specific emit/on handler
   *
   * A shorthand for a emit/on combination that tracks lookup instance.
   * The third argument can be a callback or a variable.
   *
   * @since 2.0
   * @author potanin@UD
   */
  query: function( action, request_data, callback ) {
    'use strict';
    var self = this;
    if( self.settings.log.events ) { self.log( self.scope + '.get()', arguments ); }

    if( !self.connected ) {
      ud.warning( self.scope + '.get() - Called too early. Scheduling re-try for ' + self.id + '::connected event.', arguments );
      jQuery( document ).one( 'ud::saas::connected', function() {
        ud.warning( self.scope + '.get() - Calling scheduled ud.saas.query() post ' + self.id + '::connected event.', arguments );
        self.query( action, request_data, callback );
      } );
    }

    // Create global variables for tracking instance listeners.
    window.ud.saas._instances = window.ud.saas._instances ? window.ud.saas._instances : {};

    // Create our custom hash for this request
    request_data._hash = parseInt( ( Math.random() ).toString().replace( '0.', '' ) );

    // Create timeout event - no response in X many seconds, request is dropped.
    window.ud.saas._instances[ request_data._hash ] = setTimeout( function() {
      delete window.ud.saas._instances[ request_data._hash ];
      if( typeof callback === 'function' ) {
        callback( new Error( 'ud.saas.query() - No response received, dropping listener: ' + 'receive::' + action + '::' + request_data._hash ), request_data );
      } else {
        self.log( new Error( 'ud.saas.query() - No response received, dropping listener: ' + 'receive::' + action + '::' + request_data._hash ), request_data );
      }
    }, self.settings.query_timeout );

    // Send request to SaaS
    self.socket.emit( 'request::' + action, request_data );

    // Create listener for response with unique hash
    self.socket.on( 'receive::' + action + '::' + request_data._hash, function( response_data ) {

      // Remove the timeout so it doesn't trigger the callback
      clearTimeout( window.ud.saas._instances[ request_data._hash ] );

      switch( typeof callback ) {

        case 'function':
          callback( null, response_data );
          break;

        default:
          callback = response_data;
          break;

      }

    } );

    return null;

  },

  /**
   * Internal logging function.
   *
   * @version 1.0.0
   * @author potanin@UD
   */
  log: function( data ) {
    var self = this;

    if( typeof window.console !== 'object' || /MSIE (\d+\.\d+);/.test( navigator.userAgent ) ) {
      return false;
    }

    if( typeof data === 'object' && data instanceof Error && !self.settings.log.errors ) {
      return;
    }

    console.log.apply( console, arguments );

    return typeof arguments[0] === 'boolean' ? arguments[0] : true;

  },

  /**
   * Enable Global IO Data Logger
   *
   * @since 2.0
   * @author potanin@UD
   */
  log_all: function( e, data ) {
    'use strict';
    var self = this;

    if( typeof window.console === 'object' || /MSIE (\d+\.\d+);/.test( navigator.userAgent ) ) {

      if( data.type === 'event' ) {
        return console.log( 'ud.saas.log_all()', data );
      }

      if( data.type === 'heartbeat' ) {
        // return console.log( 'ud.saas.log_all()', data );
      }

    }

    return false;

  }


});

