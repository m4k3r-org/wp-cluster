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
define( 'site', function() {
  console.log( 'site', 'loaded' );

  require.loadStyle( '/assets/styles/site.css' );

});

