/**
 * HTML Video Element
 *
 */
define( 'banner.poster', [ 'udx.storage' ], function( Storage ) {
  console.debug( 'banner.poster', 'loaded' );

  var storage = Storage.create( 'app.state' );

  console.dir( storage );

  var state = {
    purchasedTicket: storage.getItem( 'purchasedTicket' ) || false,
    watchedTrailer: storage.getItem( 'watchedTrailer' ) || false,
    startedTrailer: storage.getItem( 'watchedTrailer' ) || false
  };

  //setItem
  console.log( state );


});

