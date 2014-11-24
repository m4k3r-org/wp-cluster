/**
 * Main entry point for Javascript. Loads several sub-modules and sets up the page.
 *
 * @module CHMF
 * @main CHMF
 */

require.config({

  baseUrl: "application/static/scripts/src",
  paths: {
    "jquery": "vendor/jquery/jquery"
  },

  shim: {
    "vendor/fancybox-2.1.5/jquery.fancybox": ["jquery"],
    "vendor/fancybox-2.1.5/helpers/jquery.fancybox-media": ["jquery", "vendor/fancybox-2.1.5/jquery.fancybox"],
    "vendor/caroufredsel-6.2.1/jquery.carouFredSel-6.2.1": ["jquery"]
  }

});


require( [
  'jquery',
  'lib/istouch',
  'vendor/fancybox-2.1.5/jquery.fancybox',
  'vendor/fancybox-2.1.5/helpers/jquery.fancybox-media',
  'lib/stream',
  'lib/countdown',
  'lib/contests'
], function( $, isTouch, fancybox, fancyboxMedia, stream, countDown, contests ){


  // Initialize top video popup
  if ( isTouch === false )
  {
    $('header .play-video' ).fancybox({
      maxWidth: 800,
      maxHeight: 600,
      fitToView: false,
      autoSize: false,
      closeClick: false,
      padding: 0,
      margin: 0,

      helpers: {
        media: true
      },

      youtube: {
        autoplay: 1,
        hd: 1
      }
    });
  }


  // Initialize stream
  stream.init();

  // Initialize countdown
  countDown.init();

  // Initialize the contests section
  contests.init();
} );
