/**
 * Artist Profile Overlay
 *
 * @class artist profile
 */
define( ['jquery'], function( $ ){

  var artistProfileOverlay = {

    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventOpen: function(){

      var that = this;

      $( '#doc' ).on( 'click', '.artist-profile', function( e ){

        e.preventDefault();

        if ( $(window ).width() > 960 ){
          that.calculateArtistDescriptionHeight();
        }
        else{
          $( '.overlay-artist-post-content' ).height( 'auto' );
        }

        $( '.artist-profile-overlay' ).css( 'display', 'block' );
        $( 'html, body' ).addClass( 'overlay-open' );

      } );

    },

    /**
     * Event for closing the overlay
     *
     * @method eventClose
     */
    eventClose: function(){
      $( '.artist-profile-overlay' ).on( 'click', '.icon-close', function( e ){

        e.preventDefault();

        $( '.artist-profile-overlay' ).css( 'display', 'none' );
        $( 'html, body' ).removeClass( 'overlay-open' );

      } );

      // On ESC key close the overlay
      $( document ).keyup( function( e ){

        if( e.keyCode == 27 ){
          $( '.artist-profile-overlay' ).css( 'display', 'none' );
          $( 'html, body' ).removeClass( 'overlay-open' );
        }

      } );
    },

    calculateArtistDescriptionHeight: function(){

      $( '.overlay-artist-post-content' ).height( $(window).height() - 380 );

    },

    initCustomScrollBar: function(){

      $(window).load(function(){

        var $overlay = $(".overlay-artist-post-content");
        if( typeof $overlay.mCustomScrollbar == 'function' ){
          $overlay.mCustomScrollbar({
            theme:"dark"
          });
        }else {
          // Missing the .mCustomScrollbar function
        }

      });

    }

  };

  var artistProfileShareOverlay = {

    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventOpen: function(){
      $( '#doc' ).on( 'click', '.artist-share', function( e ){

        e.preventDefault();

        $( '.artist-profile-share-overlay' ).css( 'display', 'block' );
        $( 'html, body' ).addClass( 'overlay-open' );

      } );

    },

    /**
     * Event for closing the overlay
     *
     * @method eventClose
     */
    eventClose: function(){
      $( '.artist-profile-share-overlay' ).on( 'click', '.icon-close', function( e ){

        e.preventDefault();

        $( '.artist-profile-share-overlay' ).css( 'display', 'none' );
        $( 'html, body' ).removeClass( 'overlay-open' );

      } );

      // On ESC key close the overlay
      $( document ).keyup( function( e ){

        if( e.keyCode == 27 ){
          $( '.artist-profile-share-overlay' ).css( 'display', 'none' );
          $( 'html, body' ).removeClass( 'overlay-open' );
        }

      } );
    }

  };

  return {

    /**
     * Bootstrap the profile overlay
     *
     * @method initOverlay
     */
    initOverlay: function(){
      artistProfileOverlay.eventOpen();
      artistProfileOverlay.eventClose();
  //    artistProfileOverlay.initCustomScrollBar();

      artistProfileShareOverlay.eventOpen();
      artistProfileShareOverlay.eventClose();
    }
  }

} );

(function($){
  $(window).load(function(){

    if ( $(window).width() > 960 )
    {
      var $overlay = $(".overlay-artist-post-content");
      if( typeof $overlay.mCustomScrollbar == 'function' && $overlay.length ){
        $overlay.mCustomScrollbar();
      }else {
        // Missing the .mCustomScrollbar function
      }
    }

  });

  // Set the header to be always 100% height of the window
/*  $( window ).on( 'resizeEnd', function() {

    if ( $(window).width() > 960 )
    {
      $(".overlay-artist-post-content").mCustomScrollbar();
    }
    else
    {
      $(".overlay-artist-post-content").mCustomScrollbar('destroy');
    }

  } );*/
})(jQuery);
