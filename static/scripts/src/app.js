/**
 * Splash
 *
 */
require( [ 'skrollr', 'scrollReveal', 'udx.utility.imagesloaded' ], function( skrollr, scrollReveal, imagesloaded ) {
  console.log( 'ready'  );

  var _scroll = skrollr.init({
    smoothScrolling: false
  });

  var _reveal = new scrollReveal({
    after: '0s',
    //enter: 'right',
    //move: '24px',
    //over: '0.66s',
    //easing: 'ease-in-out',
    //viewportFactor: 0.33,
    reset: false,
    init: true
  });

  console.log( '_reveal', _reveal );

  imagesloaded( document.body, function() {
    console.log( 'have images' );
  });


});