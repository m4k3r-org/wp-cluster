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
require( [ 'html.picture', 'html.video' ], function Bootstrap() {
  console.log( 'application bootstrapped' );

  require.loadStyle( '/assets/styles/app.main.css' );

  require( [ 'app.main' ] );

});