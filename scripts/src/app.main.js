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
//  console.debug( 'app.main', 'loaded', _locale.domain );

  // unfuck some WP stuff, i don't care.
  window.jQuery = window.jQuery || jQuery;
  window.jQuery.widget = window.jQuery.widget || {};
  window.mejs = window.mejs || {
    Utility: {}
  };

  // ghetto fix because w/o it breaks mobile by preventing scrolling - potanin
  if( window.innerWidth > 700 ) {
    //console.log( 'window.screen.availWidth', window.screen.availWidth );

    //window.skrollr.init( { forceHeight: false });     

    // Sticky elements implementation
    require( [ 'sticky' ], function() {
      // console.debug( 'app.main', 'sticky' );

      var st = 0;

      if( jQuery( '#wpadminbar' ).length > 0 ) {
        st = jQuery( '#wpadminbar' ).height();
      }

      jQuery( ".navbar-top" ).sticky( {
        //topSpacing: st
      } );

    } );

  }

  /**
   * Ok for all eventbrite links, we're going to add the Google Analytics cross domain tracking code
   */
  jQuery( 'a' ).click( function( e ) {
  } );

} );

