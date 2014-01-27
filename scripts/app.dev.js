/**
 * Application Loader
 *
 */
function Bootstrap( jQuery ) {

  // All events, functionality should be added here
  require( [ 'countdown' ], function(){
    /* TEMP */
    var liftoffTime = new Date();
    liftoffTime.setDate( liftoffTime.getDate() + 25 );

    jQuery( '#countdown' ).countdown( {
      until: liftoffTime,
      format: 'dHMS',
      labels: [ 'Years', 'Months', 'Weeks', 'Days', 'Hour', 'Min', 'Sec' ],
      labels1: [ 'Year', 'Month', 'Week', 'Day', 'Hour', 'Min', 'Sec' ]
    } );
  } );

  require( [ 'skrollr' ], function(){
    var s = skrollr.init({
      forceHeight:false
    });
  } );

  // Sticky elements implementation
  require( [ 'sticky' ], function(){
    var st = 0;
    if( jQuery( '#wpadminbar' ).length > 0 ) {
      st = jQuery( '#wpadminbar' ).height();
    }
    jQuery(".navbar-top-home").sticky({
      topSpacing:st
    });
    jQuery("#bottom-headings").sticky({
      topSpacing:st + jQuery(".navbar-top").height() + 4,
      wrapperClassName: 'container sticky-wrapper'
    });
    jQuery(".bottom-content .head .bottom-line").sticky({
      topSpacing:st + jQuery(".navbar-top").height() + 58,
      wrapperClassName: 'sticky-wrapper'
    });
  } );
  
  // Fix footer position at once if needed
  if ( jQuery(document).height() <= jQuery(window).height() ) {
    jQuery( "footer" ).addClass( "navbar-fixed-bottom" );
  }
    
}

require.config({
  "paths": {
    "html5shiv": "//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js",
    "bootstrap": "//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min",
    "jquery": "//codeorigin.jquery.com/jquery-2.0.3",
    "countdown": "utility/jquery.countdown",
    "skrollr": "utility/skrollr",
    "sticky": "utility/sticky"
  },
  "shim": {
    "bootstrap": {
      "deps": [ "jquery" ]
    }
  },
  "uglify": {
    "beautify": true,
    "max_line_length": 1000,
    "no_mangle": true
  },
  "waitSeconds": 15
});

require( [ 'jquery', 'bootstrap' ], Bootstrap );

