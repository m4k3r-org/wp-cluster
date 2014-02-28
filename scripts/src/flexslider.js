/**
 * Event Countdown Timer.
 *
 */
define( 'flexslider', [ 'jquery.flexslider' ], function() {
  console.debug( 'flexslider', 'loaded' );

  return function domReady() {
    console.debug( 'flexslider', 'dom ready' );

    console.log( jQuery(this).flexslider() );

    return this;
  }

});