/**
 * Main entry point for Javascript. Loads several sub-modules and sets up the page.
 *
 * @module CHMF
 * @main CHMF
 */

require.config({

  baseUrl: pageMeta.baseUrl + "static/scripts/src",
  paths: {
    "jquery": "vendor/jquery/jquery"
  },

  shim: {
    "vendor/fancybox-2.1.5/jquery.fancybox": ["jquery"],
    "vendor/fancybox-2.1.5/helpers/jquery.fancybox-media": ["jquery", "vendor/fancybox-2.1.5/jquery.fancybox"],
    "vendor/caroufredsel-6.2.1/jquery.carouFredSel-6.2.1": ["jquery"],
    "vendor/jquery-dotdotdot/jquery.dotdotdot.min": {
      "deps": ["jquery"],
      "exports": "dotdotdot"
    }
  }
});


require( [
  'jquery',
  'lib/istouch',
  'vendor/fancybox-2.1.5/jquery.fancybox',
  'vendor/fancybox-2.1.5/helpers/jquery.fancybox-media',
  'lib/stream',
  'lib/countdown',
  'lib/contests',
  'lib/spectacle-tabs',
  'lib/equalheights',
  'lib/navigation',
  'lib/swipe',
  'lib/share',
  'lib/blog',
  'vendor/jquery-dotdotdot/jquery.dotdotdot.min'
], function( $, isTouch, fancybox, fancyboxMedia, stream, countDown, contests, spectacleTabs, equalHeights, navigation, swipe, share, blog, dotdotdot ){

  // Performance optimization for window resize event
  $( window ).resize( function() {

    if( resizeTo !== null ) {
      clearTimeout( resizeTo );
    }

    resizeTo = setTimeout( function() {
      $( this ).trigger( 'resizeEnd' );
    }, 250 );

  } );

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


  // Init navigation overlay
  navigation.init();

  // Initialize countdown
  countDown.init();

  // Initialize the contests section
  contests.init();

  // Initialize spectacle tabs
  spectacleTabs.init();

  //Initialize share overlay
  share.init();


  if ( $( '.equalize_col' ).length > 1 )
  {
    equalHeights.equalize( $( '.equalize_col' ), 768 );
  }

  // Initialize swipe/scroller for the winners
  if ( $('.winners-slider-container').length ) {

    // Go thorough the heights of the items and assign the container the biggest one
    var maxHeight = 0;

    $( '.winners-slider-container .winners-slider .item' ).each( function() {

      var height = $( this ).outerHeight( true );
      if( height > maxHeight ){
        maxHeight = height;
      }
    });

    // Add the max height to the container (+60 is the indicator gap)
    $('.winners-slider-container' ).height( maxHeight + 60 );

    // Init the swipe
    swipe.init( '.winners-slider-container', '.winners-slider', '.item', '.winners .indicator-parent' );
  }


  // Trim excerpt to be max 3 lines of text for the blog index
  if ( $('.blog-posts .card' ).length > 0 )
  {

    $( '.blog-posts .card .excerpt' ).dotdotdot( {
      height: 72
    });

    $('.blog-posts .card .title' ).dotdotdot({
      height: 160
    })
  }

  // Initialize the blog
  blog.init();


} );
