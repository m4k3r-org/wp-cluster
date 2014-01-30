/**
 * Event Countdown Timer.
 *
 */
define( 'countdown', [ '//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/1.6.3/jquery.countdown.min.js' ], function() {
  console.log( 'countdown', 'loaded' );

  return function domReady() {

    var _target = jQuery( this.getAttribute( 'data-target' ), this );

    var liftoffTime = new Date();

    liftoffTime.setDate( liftoffTime.getDate() + 25 );

    _target.hide().fadeIn( 'slow' );

    _target.countdown({
      until: liftoffTime,
      format: this.getAttribute( 'data-format' ) || 'dHMS',
      labels: [ 'Years', 'Months', 'Weeks', 'Days', 'Hour', 'Min', 'Sec' ],
      labels1: [ 'Year', 'Month', 'Week', 'Day', 'Hour', 'Min', 'Sec' ]
    });

    return this;

  }

});