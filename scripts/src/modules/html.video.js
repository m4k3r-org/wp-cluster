/**
 * HTML Video Element
 *
 * @note Can not require "udx.storage" here.
 */
define( 'html.video', function() {
  console.log( 'html.video', 'loaded' );

  var trailer = document.getElementById( 'trailer-video' );
  var banner = document.getElementById( 'banner' );


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

