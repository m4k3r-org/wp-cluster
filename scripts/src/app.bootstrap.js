/**
 * Application Bootstrap
 *
 * Loads initial non-blocking JavaScript.
 *
 */
define( 'app.bootstrap', [ 'html.picture', 'html.video' ], function Bootstrap() {
  console.log( 'app.bootstrap' );

  require.loadStyle( '/assets/styles/app.main.css' );
  require.loadStyle( '//cdn.udx.io/vendor/animate.css' );

  // Load Main Application
  require( [ 'app.main' ] );



});
