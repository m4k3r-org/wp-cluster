/**
 * Application Bootstrap
 *
 * Loads initial non-blocking JavaScript.
 *
 */
require( [ 'html.picture', 'html.video' ], function Bootstrap() {
  console.debug( 'app.bootstrap' );

  require.config({
    paths: {
      scrollr: '/assets/scripts/scrollr',
      countdown: '/assets/scripts/countdown',
      sticky: '/assets/scripts/sticky'
    }
  });

  // require.loadStyle( '//cdn.udx.io/vendor/animate.css' );

  //require.loadStyle( '/assets/styles/app.main.css' );

  // Load Main Application
  //require( [ '/assets/scripts/app.main.js' ] );


});
