define(
  [
    'global',
    'lodash',
    'knockout',
    'text!templates/worldwide/event.html',
    'model/event',
    'viewModel/worldwide/event'
  ],
  function( _ddp, _, ko, EventTemplate, EventModel, EventViewModel ){
    'use strict';
    return function( id ){
      var self = this;
      /** Ok, made sure our id is numeric, otherwise navigate out */
      id = parseInt( id );
      if( _.isNaN( id ) ){
        _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
      }
      var eventModel = new EventModel( { _id: id } );
      eventModel.fetch( {
        success: function( model, response, options ){
          /** Ok, lets init our view Model, and apply the bindings from our Model */
          var $page = $( '<div>' ).html( EventTemplate );
          /** Create our ViewModel, from our regular Model */
          var eventViewModel = new EventViewModel( model );
          ko.applyBindings( eventViewModel, $page[ 0 ] );
          /** Slide it into the DOM */
          _ddp.slidePage( $page );
        },
        error: function( model, error ){
          _ddp.log( 'There was an error getting the event: ' + error );
          _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
        }
      } );
    };
  }
);