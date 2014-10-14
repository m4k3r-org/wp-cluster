/**
 * Main entry point for Javascript. Loads several sub-modules and sets up the page.
 *
 * @module SCMF
 * @main SCMF
 */
require( [
  'jquery', 'lib/equalheights', 'lib/swipe', 'lib/countdown', 'lib/smoothscroll', 'lib/stickem', 'lib/dotdotdot',
  'lib/stickynav', 'lib/buytickets', 'lib/navigation', 'lib/masonry', 'lib/carousel', 'lib/account',
  'lib/stream-filter', 'lib/artist-profile'
], function( $, equalheights, swipe, countdown, ss, stickem, dotdotdot, stickynav, buytickets, navigation, masonry, carousel, account, streamFilter, artistProfile ){


  // Global functions that are available across all page

  // Performance optimization for window resize event
  var resizeTo = null;
  $( window ).resize( function(){

    if( resizeTo !== null ){
      clearTimeout( resizeTo );
    }

    resizeTo = setTimeout( function(){
      $( this ).trigger( 'resizeEnd' );
    }, 250 );

  } );

  // Initialize the countdown timer
  countdown.init();

  // Initialize smooth scroll
  ss.init();

  // Initialize sticky navigation
  stickynav.init();

  // Init buy tickets
  buytickets.init();

  // Init navigation
  navigation.init();

  // Account overlay
  account.init();

  // Set the header to be always 100% height of the window
  $( window ).on( 'resizeEnd', function(){

    if( $( window ).height() > $( 'header' ).height() ){
      $( 'header' ).height( $( window ).height() );
    }

  } );

  $( window ).trigger( 'resize' );

  // Page specific function

  // Homepage
  var doc = $( '#doc' );

  if( doc.hasClass( 'page-home' ) ){
    // Set equal heights for various elements so it won't break the responsive design
    equalheights.equalize( $( '.main-artists .main-artist' ), 768 );
    equalheights.equalize( $( '.artist-lineup .callout, .artist-lineup .main-artists' ), 768 );

    equalheights.equalize( $( '.location, .accommodations' ), 992 );

    // Initialize swipe/scroller for the various slider
    swipe.init( '.news-slider-container', '.news-slider', '.card', '.news-slider-container .indicator-parent' );
    swipe.init( '.accommodations-slider-container', '.accommodations-slider', '.card', '.accommodations .indicator-parent' );

    // Initialize swipe for photos/videos is viewport is smaller than 768px wide
    var photoVideoScroller = null;

    $( window ).on( 'resizeEnd', function(){

      if( document.documentElement.clientWidth >= 768 ){
        swipe.destroy( photoVideoScroller );
        photoVideoScroller = null;

        $( '.photos-videos-strip-container' ).data( 'initswipe', false );
        $( '.photos-videos-strip' ).removeAttr( 'style' );
      } else
        if( !$( '.photos-videos-strip-container' ).data( 'initswipe' ) ){
          photoVideoScroller = swipe.init( '.photos-videos-strip-container', '.photos-videos-strip', '.item', '.photos-videos-strip-container .indicator-parent' );

          $( '.photos-videos-strip-container' ).data( 'initswipe', true );
        }
    } );

    $( window ).trigger( 'resize' );

    // Initialize artist lineup content stick
    stickem.init();

    // Initialize text trim for news section
    dotdotdot.init();
  } // page-home

  else
    if( doc.hasClass( 'page-artist-lineup' ) ){
      equalheights.equalize( $( '.main-artists .main-artist' ), 768 );
      equalheights.equalize( $( '.artist-lineup .callout, .artist-lineup .main-artists' ), 768 );
      equalheights.equalize( $( '.artist-lineup2 .main-artists .main-artist' ), 768 );

      // Initialize swipe for tier2 artists
      var tier2ArtistScroller = null;

      $( window ).on( 'resizeEnd', function(){

        if( document.documentElement.clientWidth >= 992 ){
          swipe.destroy( tier2ArtistScroller );
          tier2ArtistScroller = null;

          $( '.artist-slider-container' ).data( 'initswipe', false );
          $( '.artist-slider' ).removeAttr( 'style' );
        } else
          if( !$( '.artist-slider-container' ).data( 'initswipe' ) ){
            tier2ArtistScroller = swipe.init( '.artist-slider-container', '.artist-slider', '.tier2-artist', '.artist-slider-container .indicator-parent' );

            $( '.artist-slider-container' ).data( 'initswipe', true );
          }
      } );

      $( window ).trigger( 'resize' );

      // Initialize artist lineup content stick
      stickem.init();

    } // page-artist-lineup

    else
      if( doc.hasClass( 'page-features' ) ){
        equalheights.equalize( $( '.features-content .feature-item' ), 768 );

      } // page-features

      else
        if( doc.hasClass( 'page-photo-gallery' ) ){
          masonry.init( '.photo-grid' );
        } // page-photo-gallery

        else
          if( doc.hasClass( 'page-organizers' ) ){
            equalheights.equalize( $( '.organizers-content .organizer-item' ), 768 );
          }

          else
            if( doc.hasClass( 'page-artist-single' ) ){

              // Initialize the carousel
              carousel.init();

              // Initialize the stream filter
              streamFilter.init( $( '.stream-filters' ) );

              // Initialize artist profile overlay
              artistProfile.initOverlay();
            }

            else
              if( doc.hasClass( 'page-sponsors' ) ){
                equalheights.equalize( $( '.sponsors-content .sponsor-item' ), 768 );
              }

} );
