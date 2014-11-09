/**
 * Splash
 *
 */
require( [ 'skrollr', 'scrollReveal', 'udx.utility.imagesloaded' ], function( skrollr, scrollReveal, imagesloaded ) {
  console.log( 'ready' );

  var app = {
    body: document.body.className.split( ' ' ),
    height: window.screen.availHeight,
    width: window.screen.availWidth,
    reveal: undefined,
    skrollr: undefined
  };

  app.reveal = new scrollReveal({
    after: '0s',
    reset: false,
    init: true
  });

  imagesloaded( document.body, function() {
    console.log( 'have images', app.body );

    // app.skrollr = skrollr.init({ smoothScrolling: false });

  });


});