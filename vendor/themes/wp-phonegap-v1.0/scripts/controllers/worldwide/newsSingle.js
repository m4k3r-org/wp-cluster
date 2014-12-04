define(
  [
    'global',
    'lodash',
    'knockout',
    'text!templates/shared/newsSingle.html',
    'model/post',
    'viewModel/shared/newsSingle'
  ],
  function( _ddp, _, ko, NewsSingleTemplate, PostModel, NewsSingleViewModel ){
    'use strict';
    return function( id ){
      var self = this;
      /** Ok, made sure our id is numeric, otherwise navigate out */
      id = parseInt( id );
      if( _.isNaN( id ) ){
        _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
      }
      var postModel = new PostModel( { _id: id } );
      postModel.fetch( {
        success: function( model, response, options ){
          /** Ok, lets init our view Model, and apply the bindings from our Model */
          var $page = $( '<div>' ).html( NewsSingleTemplate );
          /** Create our ViewModel, from our regular Model */
          var newsSingleViewModel = new NewsSingleViewModel( model );
          ko.applyBindings( newsSingleViewModel, $page[ 0 ] );
          /** Slide it into the DOM */
          _ddp.slidePage( $page );
        },
        error: function( model, error ){
          _ddp.log( 'There was an error getting the post: ' + error );
          _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
        }
      } );
    };
  }
);