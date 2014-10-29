/**
 * Event Countdown Timer.
 *
 */
define( 'countdown', [ '//cdnjs.cloudflare.com/ajax/libs/jquery-countdown/1.6.3/jquery.countdown.min.js' ], function() {
  console.log( 'countdown', 'loaded' );

  return function domReady() {

    var _target = jQuery( jQuery(this).data('target'), this );

    var liftoffTime = new Date( jQuery(this).data('date') );

    liftoffTime.setDate( liftoffTime.getDate() );

    _target.hide().fadeIn( 'slow' );

    _target.countdown({
      until: liftoffTime,
      format: jQuery(this).data('format') || 'dHMS',
      labels: [ 'Years', 'Months', 'Weeks', 'Days', 'Hour', 'Min', 'Sec' ],
      labels1: [ 'Year', 'Month', 'Week', 'Day', 'Hour', 'Min', 'Sec' ],
      layout: jQuery(this).data('layout') || '<span class="countdown_row countdown_show4"><span class="countdown_section"><span class="countdown_amount">{dn}</span><br>{dl}</span><span class="colon">:</span>\n\
<span class="countdown_section"><span class="countdown_amount">{hn}</span><br>{hl}</span><span class="colon">:</span>\n\
<span class="countdown_section"><span class="countdown_amount">{mn}</span><br>{ml}</span><span class="colon">:</span>\n\
<span class="countdown_section"><span class="countdown_amount">{sn}</span><br>{sl}</span></span>',
      padZeroes: true
    });

    return this;

  }

});