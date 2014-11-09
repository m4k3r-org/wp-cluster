/**
 * wpElastic Settings.
 *
 * @module wp-elastic
 * @author potanin@UD
 */
define( 'wp-elastic.settings', [ 'knockout', 'wp-elastic.api' ], function wpElasticSettings() {
  // console.debug( 'wp-elastic.settings' );

  var api = require( 'wp-elastic.api' );
  var ko  = require( 'knockout' );

  /**
   *
   */
  function heartbeatTick() {
    // console.log( 'tick tick', arguments )
  }

  /**
   *
   * @constructor
   */
  function ViewModel() {

    var context = this;

    this.title    = ko.observable();
    this.settings = ko.observable();

    if( 'function' === typeof jQuery ) {
      jQuery( document ).on( 'heartbeat-tick', heartbeatTick );
    }

    api.getSettings( null, function haveSettings( error, data ) {
      context.settings( JSON.stringify( data, null, 2 ) );
    });

    return this;

  }

  /**
   *
   */
  return function domReady() {
    // console.debug( 'wp-elastic.settings', 'domReady' );
    ko.applyBindings( new ViewModel, this );

  }

});