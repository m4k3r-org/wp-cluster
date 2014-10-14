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
      $( '#doc' ).on( 'click', '.artist-profile', function( e ){

        e.preventDefault();

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

      artistProfileShareOverlay.eventOpen();
      artistProfileShareOverlay.eventClose();
    }
  }

} );