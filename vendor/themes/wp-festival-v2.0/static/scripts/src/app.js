/**
 * WP-Festival 2 Main App File
 */
define(
  [
    'jquery',
    'lib/developer',
    'lib/equalheights',
    'lib/swipe',
    'lib/countdown',
    'lib/smoothscroll',
    'lib/stickem',
    'lib/dotdotdot',
    'lib/stickynav',
    'lib/buytickets',
    'lib/navigation',
    'lib/masonry',
    'lib/carousel',
    'lib/account',
    'lib/stream-filter',
    'lib/artist-profile',
    'lib/collapse',
    'lib/share',
    'lib/imagelightbox',
    'lib/stream',
    'lib/fancybox',
    'lib/blog-main',
    'lib/tabbed-content',
    'lib/module-video',
    'lib/contact-form',
    'lib/hotel-widget',
    'lib/artist-callout',
    'components/fitvids/fitvids-built',
		'lib/multi-language'
  ], function( $, developer, equalheights, swipe, countdown, ss, stickem, dotdotdot, stickynav, buytickets, navigation, masonry, carousel, account, streamFilter, artistProfile, collapse, share, imagelightbox, stream, fancybox, blogMain, tabbedContent, videoModule, contact, hotelWidget, artistCallout, fv, multiLanguage ){

    var self = this, resizeTo = null;

    // console.debug( 'developer', developer );

    // Performance optimization for window resize event
    $( window ).resize( function(){

      if( resizeTo !== null ){
        clearTimeout( resizeTo );
      }

      resizeTo = setTimeout( function(){
        $( this ).trigger( 'resizeEnd' );
      }, 250 );

    } );

    /** Only init lightbox if we have elements */
    if( $( 'a.imagelightbox' ).length > 0 ){
      imagelightbox.init();
    }

    /** Only init fancybox if we have elements */
    if( $( 'a.fancybox' ).length > 0 ){
      fancybox.init();
    }

    /** Only init the hotels widget if we have elements */
    if( $( '.hotel-widget' ).length > 0 ){
      hotelWidget.init();
    }

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

    collapse.init();

    share.init();


		// Multi-language overlay
		multiLanguage.init();


    if( $( '.video-module-container' ).length ){
      videoModule.init();
    }

    // Set the header to be always 100% height of the window
    $( window ).on( 'resizeEnd', function(){

      if( $( window ).height() > $( '#doc > header' ).height() ){
        $( '#doc > header' ).height( $( window ).height() );
      }

    } );

    $( window ).on( 'resizeEnd', function(){

      if( $( window ).height() > $( '.hero-container' ).height() ){
        $( '.hero-container' ).height( $( window ).height() );
      }

    } );

    $( window ).trigger( 'resize' );

    // Page specific function

    // Homepage
    var doc = $( '#doc' );

  if( doc.hasClass( 'page-home' ) ){
    // Set equal heights for various elements so it won't break the responsive design
    equalheights.equalize( $( '.main-artists .main-artist' ), 768 );
    equalheights.equalize( $( '.artist-lineup .callout, .artist-lineup.tier-one .main-artists' ), 768 );

    equalheights.equalize( $( '.location, .accommodations' ), 992 );

    //equalheights.equalize( $( '.posts-list-container .post .content' ), 582 );

    // Initialize swipe/scroller for the various slider
    if( $( '.news-slider-container' ).length ){
      swipe.init( '.news-slider-container', '.news-slider', '.card', '.news-slider-container .indicator-parent' );
    }
    if( $( '.accommodations-slider-container' ).length ){
      swipe.init( '.accommodations-slider-container', '.accommodations-slider', '.card', '.accommodations .indicator-parent' );
    }

    //Init masonry for the gallery
    if( $( '.page-photo-gallery' ).length ){
      masonry.init( '.photo-grid' );
    }

    // Initialize swipe for photos/videos is viewport is smaller than 768px wide
    var photoVideoScroller = null;

    if( $( '.photos-videos-strip-container' ).length ){
      $( window ).on( 'resizeEnd', function(){

        if( document.documentElement.clientWidth >= 768 ){
          swipe.destroy( photoVideoScroller );
          photoVideoScroller = null;

          $( '.photos-videos-strip-container' ).data( 'initswipe', false );
          $( '.photos-videos-strip' ).removeAttr( 'style' );
        } else{
          if( !$( '.photos-videos-strip-container' ).data( 'initswipe' ) ){
            photoVideoScroller = swipe.init( '.photos-videos-strip-container', '.photos-videos-strip', '.item', '.photos-videos-strip-container .indicator-parent' );

            $( '.photos-videos-strip-container' ).data( 'initswipe', true );
          }
        }
      } );
    }

    $( window ).trigger( 'resize' );

    if( document.documentElement.clientWidth >= 768 ){
      // Initialize artist lineup content stick
      stickem.init();
    }

    // Initialize text trim for news section
    dotdotdot.init();
  } // page-home

  else{
    if( doc.hasClass( 'page-artist-lineup' ) ){
      equalheights.equalize( $( '.main-artists .main-artist' ), 768 );
      equalheights.equalize( $( '.artist-lineup .callout, .artist-lineup.tier-one .main-artists' ), 768 );
      equalheights.equalize( $( '.artist-lineup2 .main-artists .main-artist' ), 768 );

      // Initialize swipe for tier2 artists
      var tier2ArtistScroller = null;

      $( window ).on( 'resizeEnd', function(){

        if( document.documentElement.clientWidth >= 992 ){
          swipe.destroy( tier2ArtistScroller );
          tier2ArtistScroller = null;

          $( '.artist-slider-container' ).data( 'initswipe', false );
          $( '.artist-slider' ).removeAttr( 'style' );
        } else{
          if( !$( '.artist-slider-container' ).data( 'initswipe' ) ){
            tier2ArtistScroller = swipe.init( '.artist-slider-container', '.artist-slider', '.tier2-artist', '.artist-slider-container .indicator-parent' );

            $( '.artist-slider-container' ).data( 'initswipe', true );
          }
        }
      } );

      $( window ).trigger( 'resize' );

      // Initialize artist lineup content stick
      stickem.init();

    } // page-artist-lineup

    else{
      if( doc.hasClass( 'page-features' ) ){
        equalheights.equalize( $( '.features-content .feature-item' ), 768 );

      } // page-features

      else{
        if( doc.hasClass( 'page-photo-gallery' ) ){
          masonry.init( '.photo-grid' );
        } // page-photo-gallery

        else{
          if( doc.hasClass( 'page-organizers' ) ){
            equalheights.equalize( $( '.organizers-content .organizer-item' ), 768 );
          }

          else{
            if( doc.hasClass( 'page-sponsors' ) ){
              equalheights.equalize( $( '.sponsors-content .sponsor-item' ), 768 );
            }
          }
        }
      }
    }
  }

    if( $( '#latest-blog-posts' ).length ){
      swipe.init( '#latest-blog-posts', '.posts', '.post', '#latest-blog-posts .indicator-parent' );
    }

    if( $( '.travel-packages-container' ).length ){

      // Swipe for Travel packages page header ( only when display width < 1200 )
      $( window ).on( 'resizeEnd', function(){

        if( document.documentElement.clientWidth >= 1199 ){

          if( typeof(bookHotelScroller) != 'undefined' ){
            swipe.destroy( bookHotelScroller );
            bookHotelScroller = null;
          }

          $( '.travel-packages-container' ).data( 'initswipe', false );
          $( '.travel-pcg-images' ).removeAttr( 'style' );
        } else{
          if( !$( '.travel-packages-container' ).data( 'initswipe' ) ){
            bookHotelScroller = swipe.init( '.travel-packages-container', '.travel-pcg-images', '.img-container', '.travel-packages-container .indicator-parent' );

            $( '.travel-packages-container' ).data( 'initswipe', true );
          }
        }

      } );


      if( document.documentElement.clientWidth >= 992 ){

        var header = $( '#doc > header' );

        var remainingSpace = header.height() - ( $( '.travel-packages-container' ).outerHeight( true ) + $( '.nav-arrows' ).outerHeight( true ) + $( '.travel-packages-container' ).position().top);

        if( remainingSpace < header.height() ){
          header.height( header.height() - remainingSpace + 105 );
        }

      }

    }

    // Initialize swipe for tier3 artists
    if( $( '.tier3-artists' ).length ){
      var tier3ArtistScroller = null;

      $( window ).on( 'resizeEnd', function(){

        if( document.documentElement.clientWidth >= 992 ){
          swipe.destroy( tier3ArtistScroller );
          tier3ArtistScroller = null;

          $( '.tier3-artists' ).data( 'initswipe', false );
          $( '.tier3-artists .the-list' ).removeAttr( 'style' );
        } else
          if( !$( '.tier3-artists' ).data( 'initswipe' ) ){
            tier3ArtistScroller = swipe.init( '.tier3-artists', '.the-list', '.tier2-artist', '.tier3-artists .indicator-parent' );

            $( '.tier3-artists' ).data( 'initswipe', true );
          }
      } );
    }

    // Initialize swipe for tier2 artists
    if( $( '.tier-two' ).length ){
      var tier2ArtistScroller = null;

      $( window ).on( 'resizeEnd', function(){

        if( document.documentElement.clientWidth >= 992 ){
          swipe.destroy( tier2ArtistScroller );
          tier2ArtistScroller = null;

          $( '.tier2-artists' ).data( 'initswipe', false );
          $( '.tier2-artists .tier-two' ).removeAttr( 'style' );
        } else
          if( !$( '.tier-two' ).data( 'initswipe' ) ){
            tier2ArtistScroller = swipe.init( '.tier2-artists', '.tier-two', '.main-artist', '.tier2-artists .indicator-parent' );

            $( '.tier2-artists' ).data( 'initswipe', true );
          }
      } );
    }

    if( $( '.single-artist' ).length ){

      // Initialize the carousel
      carousel.init();

      // Initialize artist profile overlay
      artistProfile.initOverlay();
    }

    if( $( '.feature-item' ).length ){

      equalheights.equalize( $( '.feature-item' ), 768 );
    }

    if( $( '.organizer-item' ).length ){
      equalheights.equalize( $( '.organizer-item' ), 768 );
    }

    if( $( '.posts-list-container' ).length ){
      blogMain.init();
    }

    if( $( '.single-post' ).length > 0 ){
      tabbedContent.init();
    }

    if( $( '.page-contact' ).length > 0 ){
      contact.init();
    }

    if ( $('.artist-callout' ).length > 0 ){

      artistCallout.init();

      $( window ).on( 'resizeEnd', function(){
        $( '.artist-callout-equal-height' ).css('min-height', '0px');
      });

      equalheights.equalize( $( '.artist-callout-equal-height' ), 200 );
    }

    // Panama Pages
    if( $( '.tpl-panama' ).length > 0 ){
      equalheights.equalize( $( '.equal-height > div' ), 768 );

      if( $( '.getting-there' ).length > 0 ){

        var gtContainer = $( '.getting-there' );

        $( '.tab-header a' ).click( function( e ){

          e.preventDefault();

          var id = $( this ).attr( 'href' );

          $( '.tab-content', gtContainer ).hide();
          $( id ).show();

          $( '.tab-header a' ).removeClass( 'selected' );
          $( this ).addClass( 'selected' );
        } );

        $( '.tab-header a:first' ).trigger( 'click' );
      }
    }

    // Fit Video in the blog content
    if( $( 'article.content' ).length > 0 ){
      $( 'article.content' ).fitVids();
    }

    // Hotel items adjustments
    equalheights.equalize( $( '.hotel-item .row > .col-xs-12' ), 768 );

    $( window ).on( 'resizeEnd', function(){
      $( '.hotel-item' ).each( function(){
        var t = $( this );
        var rtHeight = $( '.room-types', t ).outerHeight( true );
        var rtpHeight = $( '.room-types', t ).parents( '.col-xs-12' ).outerHeight( true );

        if( rtpHeight > rtHeight ){
          $( '.room-types', t ).outerHeight( rtpHeight );
        }
      } );
    } );

    $( window ).trigger( 'resize' );

  }
);