/**
 * Client Elastic
 *
 * @todo Add EventEmitter as a dependency to hand pub/sub.
 * @todo Add udx.saas.socket as a dependency and use as a client for Elastic.
 * @todo Perhaps add udx.storage to cache certain data sets, or use saving preferences/queries/whatever.
 *
 * @version 0.1.0
 * @returns {Object}
 */
define( 'udx.saas.elastic', [ 'udx.utility', 'elastic.client' ], function() {
  console.debug( 'udx.saas.elastic', 'loaded' );

  var _local = window.ejs;
  window.ejs = null;

  function Elastic( options ) {
    console.debug( 'udx.saas.elastic', 'Elastic' );

    // Event Store.
    this._events = {};

    return Object.create( _local, {
      emit: {
        value: function emit() {
          console.debug( 'Elastic', 'emit' );

        },
        enumerable: true,
        configurable: true,
        writable: true
      },
      on: {
        value: function on() {
          console.debug( 'Elastic', 'on' );

        },
        enumerable: true,
        configurable: true,
        writable: true
      },
      _events: {
        value: this._events,
        enumerable: true,
        configurable: true,
        writable: true
      },
      client: {
        value: {
          get: function() {
            console.log( 'get', arguments );
          },
          set: function() {
            console.log( 'set', arguments );
          }
        },
        enumerable: true,
        configurable: true,
        writable: true
      }
    });

  }

  /**
   * Elastic Instance Properties.
   *
   */
  Object.defineProperties( Elastic.prototype, {
    set: {
      value: function set( key, value ) {
        //return this.get( key, value );
      },
      enumerable: true,
      configurable: true,
      writable: true
    }
  });

  /**
   * Elastic Constructor Properties.
   *
   */
  Object.defineProperties( Elastic, {
    create: {
      /**
       *
       * @param options {Object|Null}
       */
      value: function create( options ) {
        return new Elastic( options )
      },
      enumerable: true,
      configurable: true,
      writable: true
    },
    version: {
      value: '1.0.0',
      enumerable: false,
      writable: false
    }
  });

  return Elastic;

});