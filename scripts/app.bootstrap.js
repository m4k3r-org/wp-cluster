
/**
 * HTML Picture Element
 *
 */
define( 'html.picture', ['require','exports','module'],function( exports, module ) {
//  console.log( 'html.picture', 'loaded' );

});


/**
 * HTML Video Element
 *
 * @note Can not require "udx.storage" here.
 */
define( 'html.video', [],function() {
//  console.log( 'html.video', 'loaded' );

  var trailer = document.getElementById( 'trailer-video' );
  var banner = document.getElementById( 'banner' );

  if( !trailer ) {
    return;
  }

  return;

  trailer.addEventListener( 'click', function PLAY() {
    console.log( 'clicked!' );

    //var storage = Storage.create( 'app.state' );

    banner.appendChild(  trailer );
    jQuery( '.container', banner ).css( 'position', 'absolute' );
    jQuery( '.element-event-meta', banner ).fadeOut();
    jQuery( trailer ).css( 'width', '100%' );

    if( trailer.readyState  === 0 ) {
      trailer.load();
    }

    // trailer.readyState
    // trailer.videoWidth
    // trailer.videoHeight
    // trailer.hidden

    if( trailer.paused ) {
      trailer.play();
    } else {
      trailer.pause();
    }

    //trailer.load();
    if( trailer.currentTime === 0 ) {
    }

    //trailer.play();

    // banner.className = $( '#banner' ).className + ' ' + 'animated bounceOutLeft';
    // $( '#banner' ).className = 'banner-poster animated bounceInLeft';

    // container -> position absolute

  });

  trailer.addEventListener( 'canplay', function PLAY() {
    console.log( 'trailer', 'canplay' );
    trailer.play();
  })

  trailer.addEventListener( 'abort', function PLAY() {
    console.log( 'trailer', 'abort' );
  });

  trailer.addEventListener( 'loadstart', function PLAY() {
    console.log( 'trailer', 'loadstart' );
  })

  trailer.addEventListener( 'timeupdate', function PLAY() {
    console.log( 'trailer', 'timeupdate', trailer.currentTime );


    // @note when the motherfucking beat drops
    if( trailer.currentTime > 80 ) {
      //jQuery( '.element-event-meta', banner ).fadeIn();
    }

    //if( trailer.currentTime === )
  });

  trailer.addEventListener( 'ended', function PLAY() {
    console.log( 'trailer', 'ended' );

    jQuery( '.element-event-meta', banner ).fadeIn();
    //jQuery( trailer ).fadeOut();
  })

  trailer.addEventListener( 'playing', function PLAY() {
    console.log( 'trailer', 'playing' );

    jQuery( '.element-event-days' ).fadeTo( 'slow', 0.1 );
    jQuery( '.element-event-location' ).fadeTo( 'slow', 0.1 );

  });

  trailer.addEventListener( 'play', function PLAY() {
    console.log( 'trailer', 'play', trailer.readyState );

    if( trailer.readyState === 0 ) {
      trailer.load();
    }

  })

  trailer.addEventListener( 'error', function PLAY() {
    console.log( 'trailer', 'error' );
  })



});


/**
 * HTML Video Element
 *
 */
define( 'banner.poster', [ 'udx.storage' ], function( Storage ) {
//  console.debug( 'banner.poster', 'loaded' );

  var storage = Storage.create( 'app.state' );

  //console.dir( storage );

  var state = {
    purchasedTicket: storage.getItem( 'purchasedTicket' ) || false,
    watchedTrailer: storage.getItem( 'watchedTrailer' ) || false,
    startedTrailer: storage.getItem( 'watchedTrailer' ) || false
  };

  //setItem
  console.log( state );


});


/**
 * Application Bootstrap
 *
 * Loads initial non-blocking JavaScript.
 *
 */
require( [ 'html.picture', 'html.video' ], function Bootstrap() {
//  console.debug( 'app.bootstrap' );

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
