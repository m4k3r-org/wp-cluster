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
define( 'app.main', [ 'jquery', 'skrollr' ], function( jQuery ) {
  console.debug( 'app.main', 'loaded' );

  // ghetto fix because w/o it breaks mobile by preventing scrolling - potanin
  if( window.innerWidth > 700 ) {
    //console.log( 'window.screen.availWidth', window.screen.availWidth );

    //window.skrollr.init( { forceHeight: false });     

    // Sticky elements implementation
    require( [ 'sticky' ], function() {

      jQuery( ".navbar-top" ).sticky({
        //topSpacing: st
      });

    });

  }

  /**
   * Ok for all eventbrite links, we're going to add the Google Analytics cross domain tracking code
   */
  jQuery( 'a[data-track]' ).click( function( e ) {
    e.preventDefault();
    _gaq.push([ '_link', 'https://www.eventbrite.com/e/ume-2014-tickets-9467005067' ]);
    return true;
  });

  // Initialization the SPA.
  require( [ 'twitter.bootstrap', 'udx.wp.spa' ] );

});

