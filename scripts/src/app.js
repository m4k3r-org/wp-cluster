/**
 * Application Loader
 *
 */
define( [ 'festival.locale', 'festival.model', 'jquery', 'skrollr' ], function( locale, model, jQuery ) {
  console.log( 'wp-festival', 'loaded' );

  // All events, functionality should be added here
  require( [ 'jquery.countdown' ], function() {
    var liftoffTime = new Date();

    liftoffTime.setDate( liftoffTime.getDate() + 25 );

    jQuery( '#countdown' ).countdown({
      until: liftoffTime,
      format: 'dHMS',
      labels: [ 'Years', 'Months', 'Weeks', 'Days', 'Hour', 'Min', 'Sec' ],
      labels1: [ 'Year', 'Month', 'Week', 'Day', 'Hour', 'Min', 'Sec' ]
    });

  });

  console.log( 'festival.locale', require( 'festival.locale' ) );
  console.log( 'festival.model', require( 'festival.model' ) );

  window.skrollr.init({
    forceHeight: false
  });

});

