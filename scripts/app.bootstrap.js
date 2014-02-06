
/**
 * HTML Picture Element
 *
 */
define( 'html.picture', ['require','exports','module'],function( exports, module ) {
  console.log( 'html.picture', 'loaded' );

});


/**
 * HTML Video Element
 *
 */
define( 'html.video', ['require','exports','module'],function( exports, module ) {
  console.log( 'html.video', 'loaded' );

});


/**
 * HTML Video Element
 *
 */
define( 'banner.poster', ['require','exports','module'],function( exports, module ) {
  console.log( 'banner.poster', 'loaded' );

});


/**
 * Application Bootstrap
 *
 * Loads initial non-blocking JavaScript.
 *
 */
define( 'app.bootstrap', [ 'html.picture', 'html.video' ], function Bootstrap() {
  console.log( 'app.bootstrap' );

  require.loadStyle( '/assets/styles/app.main.css' );

  // Load Main Application
  require( [ 'app.main' ] );

});
