/**
 * Application Loader
 *
 */
require( [ 'jquery', 'skrollr', 'sticky' ], function Bootstrap( jQuery ) {
  console.log( 'wp-festival', 'loaded' );

  // All events, functionality should be added here
  require( [ 'countdown' ], function() {
    var liftoffTime = new Date();

    liftoffTime.setDate( liftoffTime.getDate() + 25 );

    jQuery( '#countdown' ).countdown({
      until: liftoffTime,
      format: 'dHMS',
      labels: [ 'Years', 'Months', 'Weeks', 'Days', 'Hour', 'Min', 'Sec' ],
      labels1: [ 'Year', 'Month', 'Week', 'Day', 'Hour', 'Min', 'Sec' ]
    });

  });

  var s = require( 'skrollr' ).init({
    forceHeight: false
  });

  // Sticky elements implementation
  require( [ 'sticky' ], function() {

    var st = 0;

    if( jQuery( '#wpadminbar' ).length > 0 ) {
      st = jQuery( '#wpadminbar' ).height();
    }

    jQuery( '.navbar-top-home' ).sticky({
      topSpacing: st
    });

    jQuery( '#bottom-headings' ).sticky({
      topSpacing: st + jQuery( '.navbar-top' ).height() + 4,
      wrapperClassName: 'container sticky-wrapper'
    });

    console.log( 'test', jQuery( '.bottom-content .head .bottom-line' ) );

    jQuery( '.bottom-content .head .bottom-line' ).sticky({
      topSpacing: st + jQuery( '.navbar-top' ).height() + 58,
      wrapperClassName: 'sticky-wrapper'
    });

  });

  // Fix footer position at once if needed
  if( jQuery( document ).height() <= jQuery( window ).height() ) {
    jQuery( 'footer' ).addClass( 'navbar-fixed-bottom' );
  }

});

