/**
 * Event Countdown Timer.
 *
 */
define( 'countdown', [ '//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/1.6.3/jquery.countdown.min.js' ], function() {
  console.log( 'countdown', 'loaded' );

  return function domReady() {

    var _target = jQuery( jQuery(this).data('target'), this );

    var liftoffTime = new Date( 2014, 7, 30 );

    liftoffTime.setDate( liftoffTime.getDate() );

    _target.hide().fadeIn( 'slow' );

    _target.countdown({
      until: liftoffTime,
      format: jQuery(this).data('format') || 'dHMS',
      labels: [ 'Years', 'Months', 'Weeks', 'Days', 'Hour', 'Min', 'Sec' ],
      labels1: [ 'Year', 'Month', 'Week', 'Day', 'Hour', 'Min', 'Sec' ]
    });

    return this;

  }

});