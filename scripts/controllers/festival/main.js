define(
  [
    'global',
    'lodash',
    'knockout',
    'text!templates/festival/main.html',
    'model/festival',
    'viewModel/festival/main'
  ],
  function( _ddp, _, ko, MainTemplate, FestivalModel, MainViewModel ){
    'use strict';
    return function( id ){
      var self = this;
      /** Ok, made sure our id is numeric, otherwise navigate out */
      id = parseInt( id );
      if( _.isNaN( id ) ){
        _ddp.router.navigate( 'festival/menu', { trigger: true } );
      }
      var festivalModel = new FestivalModel( { _id: id } );
      festivalModel.fetch( {
        success: function( model, response, options ){
          /** Ok, lets init our view Model, and apply the bindings from our Model */
          var $page = $( '<div>' ).html( MainTemplate );
          /** Create our ViewModel, from our regular Model */
          var mainViewModel = new MainViewModel( model );
          ko.applyBindings( mainViewModel, $page[ 0 ] );
          /** Slide it into the DOM */
          _ddp.slidePage( $page );
        },
        error: function( model, error ){
          _ddp.log( 'There was an error getting the festival: ' + error );
          _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
        }
      } );
    };
  }
);