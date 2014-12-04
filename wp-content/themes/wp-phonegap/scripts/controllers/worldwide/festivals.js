define(
  [
    'global',
    'lodash',
    'collection/festivals',
    'viewModel/worldwide/festivals',
    'text!template/worldwide/festivals.html'
  ],
  function( _ddp, _, FestivalsCollection, FestivalsViewModel, FestivalsTemplate ){
    'use strict';
    return function(){
      var self = this, festivalsCollection = new FestivalsCollection( { } );
      festivalsCollection.fetch( {
        /** Setup our success function */
        success: function( collection, response, options ){
          /** Ok, lets init our view Model, and apply the bindings from our Model */
          var $page = $( '<div>' ).html( FestivalsTemplate );
          /** Create our ViewModel, from our collection */
          var festivalsViewModel = new FestivalsViewModel( collection );
          ko.applyBindings( festivalsViewModel, $page[ 0 ] );
          /** Slide it into the DOM */
          _ddp.slidePage( $page );
        },
        /** Setup our failure function */
        error: function( collection, error ){
          _ddp.log( 'There was an error getting the list of festivals: ' + error, 'error' );
          _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
        }
      } );
    };
  }
);