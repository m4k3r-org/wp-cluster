/**
 * Main entry point for Javascript. Loads several sub-modules and sets up the page.
 *
 * @module UMESouthPadre
 * @main UMESouthPadre
 */
require.config({

  baseUrl: "static/scripts/src",
  paths: {
    "jquery": "vendor/jquery/jquery"
  },
  shim: {
    "vendor/fancybox-2.1.5/jquery.fancybox": ["jquery"],
    "vendor/fancybox-2.1.5/helpers/jquery.fancybox-media": ["jquery", "vendor/fancybox-2.1.5/jquery.fancybox"]
  }
});


require( [
  'lib/share',
  'lib/countdown',
  'lib/istouch',
  'vendor/fancybox-2.1.5/jquery.fancybox',
  'vendor/fancybox-2.1.5/helpers/jquery.fancybox-media',
  '//wurfl.io/wurfl.js'
], function( share, countdown ){
  
  share.init();
  countdown.init();

  // Initialize top video popup
  if( !window.WURFL.is_mobile ) {

    var _video = $('header .video-popup' ).fancybox({
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

    // Store if video already seen in localStorage.
    if(typeof(Storage) !== "undefined") {
      if( localStorage.getItem( 'splashSeen' ) ) {
        // console.log( 'video seen' );
      } else {
        _video.click();
        localStorage.setItem( 'splashSeen', true );
      }

    } else {
      _video.click();
    }

  }

});
