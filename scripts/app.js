/**
 * This basically bootstraps our app, and loads the appropriate page and JS based off the
 * 'module' variable in the URL GET string.
 */
define(
  [
    'loader',
    'lodash',
    'global',
    'router',
    'jquery',
    'knockout',
    'backbone'
  ],
  function( loader, _, _ddp, Router, $, ko, Backbone ){
    _ddp.log( 'Initializing the application' );
    var $app = $( '#app' );
    /** Register global events */
    _ddp.registerGlobalEvents();
    /** Setup our router */
    _ddp.router = new Router();
    /** Start our routing */
    Backbone.history.start();
  }
);