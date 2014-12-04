define(
  [
    'global',
    'lodash',
    'collection/events',
    'viewModel/worldwide/events',
    'text!template/worldwide/events.html'
  ],
  function( _ddp, _, EventsCollection, EventsViewModel, EventsTemplate ){
    'use strict';
    return function(){
      var self = this, eventsCollection = new EventsCollection( { } );
      eventsCollection.fetch( {
        /** Append the body here so we can add onto the default query */
        body: {
          query: { range: {
            start_date: {
              'gte': 'now'
            }
          } },
          sort: {
            start_date: {
              order: 'asc'
            }
          }
        },
        /** Setup our success function */
        success: function( collection, response, options ){
          /** Ok, lets init our view Model, and apply the bindings from our Model */
          var $page = $( '<div>' ).html( EventsTemplate );
          /** Create our ViewModel, from our collection */
          var eventsViewModel = new EventsViewModel( collection );
          ko.applyBindings( eventsViewModel, $page[ 0 ] );
          /** Slide it into the DOM */
          _ddp.slidePage( $page );
        },
        /** Setup our failure function */
        error: function( collection, error ){
          _ddp.log( 'There was an error getting the list of events: ' + error, 'error' );
          _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
        }
      } );
    };
  }
);