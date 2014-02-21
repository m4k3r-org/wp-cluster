/**
 * Main Application Scripts
 *
 * @example
 *
 *      // Some Locale String.
 *      require( 'site.locale' ).someWord
 *
 *      // AJAX URL.
 *      require( 'site.model' ).ajax
 *
 */
require( [ '/assets/models/locale/', '/assets/models/settings/', 'jquery', 'skrollr', 'twitter.bootstrap' ], function( _locale, _settings, jQuery ) {
  console.debug( 'app.main', 'loaded', _locale.domain );

  window.skrollr.init( {
    forceHeight: false
  });

  // Sticky elements implementation
  require( [ 'sticky' ], function() {
    var st = 0;

    if( jQuery( '#wpadminbar' ).length > 0 ) {
      st = jQuery( '#wpadminbar' ).height();
    }

    jQuery( ".navbar-top-home" ).sticky( {
      topSpacing: st
    });

  });

  /**
   * Ok for all eventbrite links, we're going to add the Google Analytics cross domain tracking code
   */
  jQuery( 'a' ).click( function( e ){
    //console.log( 'hi' );
    //debugger;
  } );

});

