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
define( [ 'festival.locale', 'festival.model', 'jquery', 'skrollr', 'bootstrap' ], function( locale, model, jQuery ) {
  console.log( 'wp-festival', 'loaded', require( 'festival.model' ).domain );

  window.skrollr.init({
    forceHeight: false
  });

});

