/**
 * Application Loader
 *
 * @example
 *
 *      // Some Locale String.
 *      require( 'festival.locale' ).someWord
 *
 *      // AJAX URL.
 *      require( 'festival.model' ).ajax
 *
 */
define( [ 'site.locale', 'site.model', 'jquery', 'skrollr', 'bootstrap' ], function( locale, model, jQuery ) {
  console.log( 'app', 'loaded', require( 'festival.model' ).domain );

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


});

